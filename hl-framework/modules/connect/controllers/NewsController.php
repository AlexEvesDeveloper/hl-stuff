<?php

require_once('ConnectAbstractController.php');
class Connect_NewsController extends ConnectAbstractController {

    private $_blogManager;

    public function init() {

        $this->view->headLink()->appendStylesheet('/assets/connect/css/news.css');
        $this->_blogManager = new Manager_Connect_Blog();

        parent::init();
    }

    /**
     * Fetch and display a news items.  For single news items, the incoming ID
     * is translated from the temporary ones assigned by the index action.
     *
     * @return void
     */
    public function indexAction() {

        $blogEntries = null;

        // Check what type of search request this is based on the POST
        // parameters OR the Earl
        $searchType = null;
        $param1 = null;
        $param2 = null;

        // Is this a POST?
        if ($this->getRequest()->isPost()) {
            $postParams = $this->getRequest()->getParams();
            $searchType = (isset($postParams['searchType'])) ? $postParams['searchType'] : null;
            $param1 = (isset($postParams['param1'])) ? $postParams['param1'] : null;
            $param2 = (isset($postParams['param2'])) ? $postParams['param2'] : null;
        } else {
            // Not a POST, check Earl vars
            $url = $this->getRequest()->getRequestUri();
            if (preg_match('/.*\/search\/(\d\d\d\d)\/(\w+)\/?$/i', $url, $matches) !== 0) {
                $searchType = 'date';
                $param1 = $matches[1];
                $param2 = $matches[2];
            } elseif (preg_match('/.*\/search\/keyword\/(\w+)\/?$/i', $url, $matches) !== 0) {
                $searchType = 'keyword';
                $param1 = $matches[1];
            }
        }

        // Perform search based on type
        switch ($searchType) {
            case 'date':
                // This is a date-based search
                if ($param1 != '' && $param2 != '') {
                    $blogEntries = $this->_blogManager->getBlogEntriesByDate($param2, $param1);
                    $this->view->searchTitle = "News Articles from {$param2} {$param1}";
                } else {
                    // Month or year not supplied, revert to showing all articles
                    $blogEntries = $this->_blogManager->getAllBlogEntries();
                    $this->view->searchTitle = 'All News Articles';
                }
                break;
            case 'keyword':
                // This is a keyword-based search
                $blogEntries = $this->_blogManager->getBlogEntriesByTagName($param1);
                $this->view->searchTitle = "News Articles tagged &quot;{$param1}&quot;";
                break;
        }
        
        $this->_displayResults($blogEntries);
    }

    public function singleAction() {

        $url = $this->getRequest()->getRequestUri();
        if (preg_match('/.*\/single\/(\d+)\/?$/i', $url, $matches) !== 0) {
            $param1 = $matches[1];
        }
        $blogEntries = array(
            $this->_blogManager->getSummaryBlog($param1)
        );
        $this->view->searchTitle = 'News Article';
        $this->_displayResults($blogEntries);
    }

    public function searchAction() {

        return $this->indexAction();
    }

    public function allAction() {

        $blogEntries = $this->_blogManager->getAllBlogEntries();
        $this->view->searchTitle = 'All News Articles';

        $this->_displayResults($blogEntries);
    }

    public function articletitleAction() {

        $blogEntries = $this->_blogManager->getAllBlogEntries();
        $this->view->searchTitle = 'Main Article';

        // Retrieve the main blog details
        $mainBlog = $this->_blogManager->getMainBlog();
        if (!empty($mainBlog)) {

            $this->_displayResults(array($mainBlog));
        }
    }

    private function _displayResults($blogEntries) {

        $this->_helper->viewRenderer('index');

        $blogSummaries = array();

        // Set up output
        if (!is_null($blogEntries) && isset($blogEntries[0])) {
            foreach ($blogEntries as $blogEntry) {
                $blogSummaries[] = array(
                    'title'         => $blogEntry->getTitle(),
                    'description'   => $blogEntry->getSummary(),
                    'content'       => $blogEntry->getContent(),
                    'image'         => $blogEntry->getIcon(),
                    'lastUpdated'   => $blogEntry->getLastUpdatedDate()
                );
            }
        }

        if (count($blogSummaries) == 0) {
            $blogSummaries[] = array(
                'title'         => 'No news articles matching your search criteria',
                'description'   => 'Please try searching again.',
                'content'       => 'Please try searching again.',
                'image'         => '',
                'lastUpdated'   => 'None found'
            );
            $this->view->searchTitle = 'No News Article';
        }

        // Output search results
        $this->view->blogSummaries = $blogSummaries;

        // Get list of months and years that have news, for sidebar
        $this->view->blogMonthsYears = $this->_blogManager->getAllBlogYearsMonths();

        // Decide what criteria to offer for further searching blog entries
        $this->view->blogSearchKeywords = $this->_blogManager->getAllTagsInUse();
        $this->view->blogSearchMonths   = $this->_blogManager->getAllBlogMonths();
        $this->view->blogSearchYears    = $this->_blogManager->getAllBlogYears();
    }

}