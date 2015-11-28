<?php
/**
* Model definition for the cms header table
* 
*/
class Datasourc_Cms_SiteLinks extends Zend_Db_Table_Multidb {
    protected $_name = 'header_links';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
    /**
    * Loads main content for a specific page
    *
    * @param string url
    * @return string
    * 
    */
    public function getLinks($parentID = 0) {
        $select = $this->select();
        $select->where('parent_menu_id = ? ',$parentID);
        $select->order('sort_order');
        $rows = $this->fetchAll($select);
        return $rows->toArray();
    }
}
?>