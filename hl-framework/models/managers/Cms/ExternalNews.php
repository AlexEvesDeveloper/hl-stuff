<?php

/**
 * Manager class responsible for implementing external news-related business
 * logic, and for binding together the external news domain objects and
 * datasources.
 *
 * @category   Manager
 * @package    Manager_Cms
 * @subpackage ExternalNews
 *
 * @todo: Switch DB I/O over to MySQL 5 - for now it's easier to use the legacy
 * DB as agents and agent users will have already set up their news preferences.
 */
class Manager_Cms_ExternalNews {

    /**#@+
     * References to common aspects stored in the datasources.
     */
    protected $_externalNewsSourceDatasource;
    protected $_externalNewsCategoryDatasource;
    protected $_externalNewsItemDatasource;
    private $_categoryList;
    /**#@-*/

    /**
     * Fetch list of all available news categories, and their source names
     */
    public function fetchCategories($sortBySourceAndCategory = true) {

        $this->_useSourceDatasource();
        $this->_useCategoryDatasource();

        $allCategories = $this->_externalNewsCategoryDatasource->getAllCategories();

        // Add source name for each category
        $allSources = $this->_externalNewsSourceDatasource->getAllSources();
        foreach($allCategories as $id => $category) {
            $allCategories[$id]->source = $allSources[$allCategories[$id]->sourceId]->name;
        }

        if ($sortBySourceAndCategory) {

            // Order categories alphabetically by source name and category name
            usort($allCategories, array('Manager_Cms_ExternalNews', '_sortBySourceAndCategory'));
        }

        return $allCategories;
    }

    /**
     * Private sort function used by fetchCategories()'s call to usort().
     *
     * @param Model_Cms_ExternalNews_Category $a Category to compare.
     * @param Model_Cms_ExternalNews_Category $b Category to compare.
     *
     * @return int Comparison result.
     */
    private function _sortBySourceAndCategory($a, $b) {
        // Compare sources
        $sourceCmp = strcmp($a->source, $b->source);
        if ($sourceCmp != 0) {
            return $sourceCmp;
        }
        // Sources same, compare category names
        return strcmp($a->name, $b->name);
    }

    /**
     * Fetch a set of news from local data, filtered by news categories.
     *
     * Raw item list is cached - the returned result of the filtering should
     * ideally be secondarily cached by calling code on a per-end-user basis.
     *
     * Intended to be used as part of an AJAX request for the external news
     * ticker.
     *
     * @param array $categoryIds
     *
     * @return array Array of Model_Cms_ExternalNews_Item objects.
     */
    public function fetchNews($categoryIds = array()) {

        $params = Zend_Registry::get('params');

        // Get all news items from DB, result cached with Zend_Cache, and filter/return required categories

        // Initialise the all items cache
        $frontendOptions = array(
            'lifetime' => $params->cms->extnews->fetchAllItemsCacheLifetime, // cache lifetime of x minutes
            'automatic_serialization' => true
        );
        $backendOptions = array(
            'cache_dir' => $params->cms->extnews->cachePath // Directory where to put the cache files
        );

        $cache = Zend_Cache::factory('Core',
                                     'File',
                                     $frontendOptions,
                                     $backendOptions);

        if (($externalNews = $cache->load('externalNews')) === false) {
            // Cache miss, get new results
            $this->_useItemDatasource();
            $externalNews = $this->_externalNewsItemDatasource->getAllItems();

            $cache->save($externalNews, 'externalNews');

        } else {
            // Cache hit
        }

        // Filter news items by category IDs
        $returnVal = array();
        $count = 0;

        foreach ($externalNews as $item) {

            // If item passes category filter, include it
            if (in_array($item->categoryId, $categoryIds)) {
                $returnVal[] = $item;
                $count++;
            }

            // Quit filtering once we have all x display items
            if ($count >= $params->cms->extnews->displayItemsMax) break;
        }

        return $returnVal;
    }

    /**
     * Get fresh news from external data sources and store it locally.
     *
     * Intended to be run as a cron task.
     *
     * @return void
     *
     * @todo Doesn't dynamically add categories, always uses a source's default
     * category - fix to extract categories from feed.
     */
    public function updateNews() {

        $this->_useSourceDatasource();

        // Find up to x random news sources that are earmarked to be updated
        $sources = $this->_externalNewsSourceDatasource->getNewsSourcesToUpdate();

        // Early exit if there are no sources to update
        if (count($sources) == 0) return;

        $this->_useCategoryDatasource();
        $this->_useItemDatasource();

        $updatedCategories = array(); // For noting down which categories got updated, for later garbage collection pruning

        // Run through sources, fetch latest content
        foreach($sources as $source) {
            try {
                $feed = Zend_Feed::import($source->sourceUrl);
            } catch (Zend_Feed_Exception $e) {
                // Feed import failed
                // TODO: log failure!
            }

            // We don't really use this bit just yet, it's already assumed from $source
            $channel = array(
                'title'         => $feed->title(),
                'link'          => $feed->link(),
                'description'   => $feed->description()
            );

            // Run through items and process into database
            foreach ($feed as $feedItem) {
                $item = new Model_Cms_ExternalNews_Item();

                $item->linkUrl = $feedItem->link();
                $item->guidHash = md5($feedItem->guid());
                $item->publishDate = new Zend_Date($feedItem->pubDate());

                $item->title = htmlentities($feedItem->title(), ENT_QUOTES, 'UTF-8');
                $item->summary = htmlentities($feedItem->description(), ENT_QUOTES, 'UTF-8');
                // If title and summary are the same, this is probably a from a
                //   Twitter feed and needs special treatment
                if ($item->title == $item->summary) {
                    // Check if there's a "[username]: " start to the title to split out
                    if (preg_match('/^(\w+): (.*)$/', $item->title, $matches) > 0) {
                        // Definitely a Twitter item, put Twitter username in title and remainder in summary
                        $item->title = $matches[1];
                        $item->summary = $matches[2];

                        // Detect if a Twitter URL is present (only takes the
                        //   first one) and extract it if so
                        if (preg_match('/(https?:\/\/t\.co\/\w+)/', $item->summary, $matches) > 0) {
                            $item->linkUrl = $matches[1];
                        }
                    } else {
                        // Can't split item, just empty summary
                        $item->summary = '';
                    }
                } else {
                    // Strip any HTML junk from summary, eg, like that which Reuters adds in (see http://mf.feeds.reuters.com/reuters/UKTopNews)
                    $item->summary = preg_replace('/&lt;(.*?)&gt;/', '', $item->summary);
                    // Strip any newlines and tabs
                    $item->summary = preg_replace('/(\n|\t)/', '', $item->summary);
                    // Trim what's left
                    $item->summary = trim($item->summary);
                }
                // Sort out ampersands in category names, as the BBC likes to mix and match
                $categoryName = $source->defaultCategory; //isset($item['category']) ? str_replace('&', 'and', $item['category']) : $ns_defaultCategory;
                // Filter missing categories or blanket categories
                if ($categoryName == '' || $categoryName == 'topNews') {
                    $categoryName = 'Top Stories';
                }
                // Make category name web safe
                $categoryName = htmlentities($categoryName, ENT_QUOTES);

                // Get category ID by category name and source ID
                $item->categoryId = $this->_existingCategory($categoryName, $source->id);

                // Is this category new for this feed?
                if ($item->categoryId === false) {
                    // Add new category
                    $this->_externalNewsCategoryDatasource->addCategory($categoryName, $source->id);
                    // Refresh running categories list
                    $this->_categoryList = $this->_externalNewsCategoryDatasource->getAllCategories();
                    // Set category ID for item
                    $item->categoryId = $this->_existingCategory($categoryName, $source->id);
                }

                // Note down that this category has recently been updated from feed (for later garbage collection pruning)
                $updatedCategories[$item->categoryId] = $item->categoryId;

                // INSERT or UPDATE news item
                $this->_externalNewsItemDatasource->upsertItem($item);
            }

            // Mark updated categories as updated - holds off garbage collection
            foreach ($updatedCategories as $updatedCategoryId) {
                $this->_externalNewsCategoryDatasource->markCategoryUpdated($updatedCategoryId);
            }

            // Mark news source as updated - holds off refetch
            $this->_externalNewsSourceDatasource->markSourceUpdated($source->id);
        }
    }

    /**
     * Clean up locally stored news - prunes old entries and removes dead
     * categories (and any mapping links to them) that are tagged as
     * non-permanent.
     *
     * Intended to be run as a cron task.
     *
     * @return void
     */
    public function cleanupNews() {

        $this->_useCategoryDatasource();
        $this->_useItemDatasource();

        // Delete old news items
        $this->_externalNewsItemDatasource->prune();

        // Identify non-permanent categories that haven't been updated in a while
        $unusedCategories = $this->_externalNewsCategoryDatasource->getPrunable();

        // Remove mapping links between agent users and old non-permanent categories
        $mappingDatasource = new Datasource_Cms_ExternalNews_CategoriesAgentsMap();
        $mappingDatasource->pruneByCategories($unusedCategories);

        // Remove old non-permanent categories
        $this->_externalNewsCategoryDatasource->prune();
    }

    /**
     * Resets source updated fields, trashes all stored news items, leaves
     * categories and category mappings intact.
     *
     * Intended for testing purposes only.
     *
     * @return void
     */
    public function eraseAllNews() {

        $this->_useSourceDatasource();
        $this->_useItemDatasource();

        // Mark all sources as if they've never been updated
        $this->_externalNewsSourceDatasource->markAllSourcesNotUpdated();

        // Delete all news items
        $this->_externalNewsItemDatasource->eraseAll();
    }

    /**
     * Instantiate external news source datasource.  Not in constructor as not
     * all methods in class need it - this way it can be lazy-loaded as
     * appropriate.
     *
     * @return void
     */
    protected function _useSourceDatasource() {

        // Instantiate source datasource if it's not already instantiated
        if (is_null($this->_externalNewsSourceDatasource)) {
            $this->_externalNewsSourceDatasource = new Datasource_Cms_ExternalNews_Source();
        }
    }

    /**
     * Instantiate external news category datasource.  Not in constructor as not
     * all methods in class need it - this way it can be lazy-loaded as
     * appropriate.
     *
     * @return void
     */
    protected function _useCategoryDatasource() {

        // Instantiate category datasource if it's not already instantiated
        if (is_null($this->_externalNewsCategoryDatasource)) {
            $this->_externalNewsCategoryDatasource = new Datasource_Cms_ExternalNews_Category();
        }
    }

    /**
     * Instantiate external news item datasource.  Not in constructor as not all
     * methods in class need it - this way it can be lazy-loaded as appropriate.
     *
     * @return void
     */
    protected function _useItemDatasource() {

        // Instantiate item datasource if it's not already instantiated
        if (is_null($this->_externalNewsItemDatasource)) {
            $this->_externalNewsItemDatasource = new Datasource_Cms_ExternalNews_Item();
        }
    }

    /**
     * Checks to see if category name already exists for a given data source.
     *
     * @return mixed Returns (int)Category ID or (bool)false if non-existent
     */
    protected function _existingCategory($categoryName, $sourceId) {

        // Fetch category list if it's not already known
        if (is_null($this->_categoryList)) {

            // Instantiate category datasource if it's not already instantiated
            if (is_null($this->_externalNewsCategoryDatasource)) {
                $this->_externalNewsCategoryDatasource = new Datasource_Cms_ExternalNews_Category();
            }

            $this->_categoryList = $this->_externalNewsCategoryDatasource->getAllCategories();
        }

        // Run through category objects looking for a match
        foreach ($this->_categoryList as $category) {
            if ($categoryName == $category->name && $sourceId == $category->sourceId) {
                // Found category, return its ID
                return $category->id;
            }
        }

        return false;
    }

}
