<?php
/**
 *
 * Model definition for the cms pages table
 * 
 */
class Datasource_Cms_Menu extends Zend_Db_Table_Multidb {
    protected $_name = 'header_links';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
       
    /**
     *
     * Gets all menu items in a sorted array
     *
     * @param string sortBy
     * @return string
     *
     */
    public function getAll($sortBy) {
        $select = $this->select();
        $select->order($sortBy);
        
        $row = $this->fetchAll($select);
        return $row->toArray();
    }
        
    /**
     *
     * Gets a specific menu item
     *
     * @param int menuID
     * @return array
     *
     */
    public function get($menuID) {
        $select = $this->select();
        $select->where('id = ?' ,$menuID);
        $row = $this->fetchRow($select);
        
        return array(
            'title' =>  $row->link,
            'url'   =>  $row->url
        );
    }
        
    /**
     *
     * Updates a specific menu item
     *
     * @param int menuID
     * @param string title
     * @param string url
     * @return boolean
     *
     */
    public function saveChanges($menuID, $title, $url) {
        $data = array(
            'link'  =>  $title,
            'url'   =>  $url
        );
        
        $where = $this->quoteInto('id = ?', $menuID);
        $this->update($data, $where);
        
        return true;
    }
    
    
    /**
     * 
     * Updates the sort order of menu items
     *
     * @param array sortOrder
     * @return string
     *
     */
    public function resort($sortOrder) {
        $menuOrder = explode(',',$sortOrder);
        $i=1;
        foreach ($menuOrder as $menuID) {
            $where = $this->quoteInto('id = ?', $menuID);
            $data = array(
                'sort_order'    =>  $i
            );
            $this->update($data, $where);
            $i++;
        }
        
        return true;
    }
    
    
    /**
     * Add a new menu item - will always add it to the end of current menu items
     *
     * @param string title
     * @param string url
     * @return boolean
     *
     */
    public function addNew($title, $url) {
        
        if ($title=='' || $url=='') return false;
        
        // First we need to grab the current highest sort order
        $select = $this->select();
        $select->from('header_links',array(
            'maxSortOrder' =>  'max(sort_order)'
        ));
        
        $row = $this->fetchRow($select);
        $sortOrder = $row->maxSortOrder+1;
        
        $data = array(
            'link'         =>  $title,
            'url'           =>  $url,
            'sort_order'    =>  $sortOrder
        );
        $this->insert($data);
        return true;
    }
    
    
    /**
     * Delete an existing menu item
     *
     * @param int menuID
     * @return boolean
     *
     */
    public function remove($menuID) {       
        $where = $this->quoteInto('id = ?', $menuID);
        return $this->delete($where);
    }
}
?>