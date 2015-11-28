<?php

/**
 * Manager class responsible for implementing internal blog-related logic, and
 * for binding together the internal blog domain objects and datasources.
 * Provides local equivalent to Ben's BlogManager class in legacy PHP4 Connect.
 *
 * @uses Service_Connect_BlogAccessor
 *
 * @category   Manager
 * @package    Manager_Connect
 * @subpackage Blog
 */
class Manager_Connect_Blog {

    /**#@+
     * 'Constants' used to specify which summary blog is required
     * in function calls
     */
    protected $SUMMARY_BLOG_01 = 1;
    protected $SUMMARY_BLOG_02 = 2;
    protected $SUMMARY_BLOG_03 = 3;
    protected $SUMMARY_BLOG_04 = 4;
    /**#@-*/

    private $_params;
    private $_blogClient;
    private $_mainBlog;
    private $_summaryBlog;
    private $_tags;
    private $_blogMonths;
    private $_blogYears;
    private $_blogYearsMonths;

    public function __construct() {

        $this->_params = Zend_Registry::get('params');

        $this->_blogClient = new Service_Connect_BlogAccessor();
    }

    /**
     * Returns the main blog.
     *
     * @return Blog
     * Object encapsulating the main blog details.
     */
    public function getMainBlog() {

        if(empty($this->_mainBlog)) {

            $this->_initMainBlog();
        }

        return $this->_mainBlog;
    }

    /**
     * Internal method, loads the main blog entry.
     *
     * @return void
     *
     * @todo Icons not working yet.
     */
    protected function _initMainBlog() {

        $result = $this->_blogClient->getMainBlogEntry();

        $this->_mainBlog = new Model_Cms_Connect_Blog(
            $result[0]['id'],
            $result[0]['title'],
            $result[0]['article'],
            $result[0]['summary'],
            $this->_params->cms->imageDisplayPath . $result[0]['imageName'],
            $this->_formatBlogDate($result[0]['lastUpdated'])
        );
    }

    /**
     * Returns the specified summary blog.
     *
     * @return Model_Cms_Connect_Blog|null If the summary blog is available,
     * then it will be returned in a Model_Cms_Connect_Blog object.  Otherwise
     * this method will return null.
     */
    public function getSummaryBlog($blogNumber) {

        if(empty($this->_summaryBlog)) {

            $this->_initSummaryBlogs();
        }

        if(empty($this->_summaryBlog[$blogNumber - 1])) {

            $returnVal = null;
        }
        else {

            $returnVal = $this->_summaryBlog[$blogNumber - 1];
        }

        return $returnVal;
    }

    /**
     * Internal method, loads the summary blog entries.
     *
     * @return void
     */
    protected function _initSummaryBlogs() {

        $summaryBlogArray = $this->_blogClient->getSummaryBlogEntries();

        $this->_summaryBlog = $this->_convertToBlogArray($summaryBlogArray);
    }

    /**
     * Returns all blog entries.
     *
     * @return Model_Cms_Connect_Blog|null An array of Model_Cms_Connect_Blog
     * objects, or null if no blog entries were found.
     */
    public function getAllBlogEntries() {

        $matchingBlogsArray = $this->_blogClient->getAllBlogEntries();

        return $this->_convertToBlogArray($matchingBlogsArray);
    }

    /**
     * Returns blog entries associated with the $tagName string passed in.
     *
     * @param string $tagName
     * The tag-name search criteria.
     *
     * @return array|null An array of Model_Cms_Connect_Blog objects matching
     * the search criteria, or null if no matches were found.
     */
    public function getBlogEntriesByTagName($tagName) {

        $matchingBlogsArray = $this->_blogClient->getBlogEntriesByTagName($tagName);

        return $this->_convertToBlogArray($matchingBlogsArray);
    }

    /**
     * Internal method, converts raw blog arrays to Model_Cms_Connect_Blog
     * arrays, for easier processing.
     *
     * @param array $blogArray Array of raw blog details.
     *
     * @return array A corresponding array of Model_Cms_Connect_Blog objects.
     */
    protected function _convertToBlogArray($blogArray) {

        $returnArray = array();

        for($i = 0; $i < count($blogArray); $i++) {

            $id = $blogArray[$i]['id'];
            $title = $blogArray[$i]['title'];
            $content = $blogArray[$i]['article'];
            $summary = $blogArray[$i]['summary'];
            $icon = $this->_params->cms->imageDisplayPath . $blogArray[$i]['imageName'];
            $lastUpdated = $this->_formatBlogDate($blogArray[$i]['lastUpdated']);

            $returnArray[$i] = new Model_Cms_Connect_Blog($id, $title, $content, $summary, $icon, $lastUpdated);
        }

        return $returnArray;
    }

    /**
     * Formats the SQL date returned from the client call and formats it into a
     * user-friendly date.
     *
     * @param string $blogDate The date to format.  Must be in the format
     * yyyy-mm-dd
     *
     * @return string The formatted date string: Jan 1st 2012
     */
    protected function _formatBlogDate($blogDate) {

        $date = new Zend_Date($blogDate, Zend_Date::ISO_8601);
        return $date->toString("MMM d" . Zend_Date::DAY_SUFFIX . " yyyy");
    }

    /**
     * Returns an array of all months against which there are one or more blog
     * entries.
     *
     * @return array An array of month strings, or null if no blog entries were
     * found.
     */
    public function getAllBlogMonths() {

        if (empty($this->_blogMonths)) {

            $this->_blogMonths = $this->_blogClient->getAllBlogMonths();

            // Remove any duplicates.
            if (!empty($this->_blogMonths)) {
                $this->_blogMonths = array_unique($this->_blogMonths);
            }
        }

        return $this->_blogMonths;
    }

    /**
     * Returns an array of all years against which there are one or more blog
     * entries.
     *
     * @return array An array of year strings, or null if no blog entries were
     * found.
     */
    public function getAllBlogYears() {

        if (empty($this->_blogYears)) {

            $this->_blogYears = $this->_blogClient->getAllBlogYears();

            // Remove any duplicates.
            if (!empty($this->_blogYears)) {
                $this->_blogYears = array_unique($this->_blogYears);
            }
        }

        return $this->_blogYears;
    }

    /**
     * Returns an array of all years/month against which there are one or more
     * blog entries.
     *
     * @return array An array of "year month" strings, or null if no blog
     * entries were found.
     */
    public function getAllBlogYearsMonths() {

        if(empty($this->_blogYearsMonths)) {

            $this->_blogYearsMonths = $this->_blogClient->getAllBlogYearsMonths();

            //Remove any duplicates.
            $this->_blogYearsMonths = array_unique($this->_blogYearsMonths);
        }

        return $this->_blogYearsMonths;
    }

    public function getBlogEntriesByDate($month, $year) {

        $matchingBlogsArray = $this->_blogClient->getBlogEntriesByDate($month, $year);

        return $this->_convertToBlogArray($matchingBlogsArray);
    }

    /**
     * Returns all blog tags that are currently associated with at least one
     * blog entry.
     *
     * @return array An array of string tags, or null if no tags are found.
     */
    public function getAllTagsInUse() {

        if(empty($this->_tags)) {

            $this->_loadAllTagsInUse();
        }
        return $this->_tags;
    }

    /**
     * Internal method, loads all the blog tags into an internal variable.
     *
     * @return void.
     */
    protected function _loadAllTagsInUse() {

        $this->_tags = $this->_blogClient->getAllTagsInUse();
    }
}