<?php

/**
 * Datasource definition for external news items.
 *
 * @category   Datasource
 * @package    Datasource_Cms
 * @subpackage ExternalNews
 *
 * @todo Add some error-trapping
 */

class Datasource_Cms_ExternalNews_Item extends Zend_Db_Table_Multidb {
    protected $_name = 'NewsItem';
    protected $_primary = 'guidHash';
    protected $_multidb = 'db_legacy_homelet';

    private $_params;

    public function __construct() {

        $params = Zend_Registry::get('params');
        $this->_params = $params->cms->extnews;

        parent::__construct();
    }

    /**
     * Find a single news item by its GUID.
     *
     * @param string $guidHash Globally Unique Identifier hash.
     *
     * @return mixed Model_Cms_ExternalNews_Item or null if none found.
     */
    public function getItemByGuid($guidHash) {

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('ni' => $this->_name)
            )
            ->join(
                array('nc' => 'NewsCategory'),
                'ni.id_nc = nc.id',
                array('nc_id' => 'nc.id', 'nc_name' => 'nc.name')
            )
            ->join(
                array('ns' => 'NewsSource'),
                'nc.id_ns = ns.id',
                array('ns_id' => 'ns.id', 'ns_name' => 'ns.name')
            )
            ->where('guidHash = ?', $guidHash);

        $itemRow = $this->fetchRow($select);
        if ($itemRow) {
            return $this->_populateItemObject($itemRow);
        }

        return null;
    }

    /**
     * Fetch all news items, limited to a parameterised maximum.
     *
     * @return array Array of Model_Cms_ExternalNews_Item objects.
     */
    public function getAllItems() {

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('ni' => $this->_name)
            )
            ->join(
                array('nc' => 'NewsCategory'),
                'ni.id_nc = nc.id',
                array('nc_id' => 'nc.id', 'nc_name' => 'nc.name')
            )
            ->join(
                array('ns' => 'NewsSource'),
                'nc.id_ns = ns.id',
                array('ns_id' => 'ns.id', 'ns_name' => 'ns.name')
            )
            ->order('pubDate DESC')
            ->limit($this->_params->fetchItemsMax);

        $newsItems = $this->fetchAll($select);
        $returnVal = array();
        foreach ($newsItems as $newsItemRow) {
            $returnVal[] = $this->_populateItemObject($newsItemRow);
        }

        return $returnVal;
    }

    /**
     * INSERT or UPDATE a news item.
     *
     * @param Model_Cms_ExternalNews_Item
     *
     * @return void
     */
    public function upsertItem($item) {

        // Place item data into an array that maps to the DB fields
        $data = array(
            'guidHash' => $item->guidHash,
            'id_nc' => $item->categoryId,
            'pubDate' => $item->publishDate->toString('YYYY-MM-dd HH:mm:ss'),
            'title' => $item->title,
            'summary' => $item->summary,
            'link' => $item->linkUrl,
            'thumbnail' => $item->thumbnailUrl,
        );

        // See if item already exists
        if (is_null($this->getItemByGuid($item->guidHash))) {
            // Do an INSERT
            return $this->insert($data);
        } else {
            // Do an UPDATE
            $where = $this->getAdapter()->quoteInto('guidHash = ?', $item->guidHash);
            return $this->update($data, $where);
        }
    }

    /**
     * Prune old news items from DB.
     */
    public function prune() {

        /*
         * DELETE FROM `NewsItem`
         *   WHERE pubDate <= DATE_SUB(NOW(), INTERVAL $this->_params->itemLifetime MINUTE);
         */
        $where = $this->getAdapter()->quoteInto('pubDate <= DATE_SUB(NOW(), INTERVAL ? MINUTE)', $this->_params->itemLifetime);
        return $this->delete($where);
    }

    /**
     * Erase all news items from DB.
     *
     * Intended for testing purposes only.
     */
    public function eraseAll() {
        $where = '1';
        return $this->delete($where);
    }

    /**
     * Attempts to put a row of news item data from the DB into a Model_Cms_ExternalNews_Item and returns it.
     *
     * @param Zend_Db_Table_Row $itemData
     *
     * @return mixed Model_Cms_ExternalNews_Item or null on failure.
     */
    private function _populateItemObject($itemData) {

        if (!empty($itemData)) {
            $item = new Model_Cms_ExternalNews_Item();

            $item->guidHash     = $itemData->guidHash;
            $item->categoryId   = $itemData->nc_id;
            $item->categoryName = $itemData->nc_name;
            $item->sourceId     = $itemData->ns_id;
            $item->sourceName   = $itemData->ns_name;
            // Following line should be using
            //   "new Zend_Date($itemData->pubDate)", except it's TOO SLOOOW.
            $item->publishDate  = $itemData->pubDate;
            $item->title        = $itemData->title;
            $item->summary      = $itemData->summary;
            $item->linkUrl      = $itemData->link;
            $item->thumbnailUrl = $itemData->thumbnail;

            $returnVal = $item;
        } else {
            $returnVal = null;
        }

        return $returnVal;

    }
}