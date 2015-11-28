<?php
/**
* Model definition for the cms page code snippets
* 
*/
class Datasource_Cms_Page_Snippets extends Zend_Db_Table_Multidb {
    protected $_name = 'page_code_snippets';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
    /**
     * Returns an associative array of all keys => replacement code
     *
     * @return array
     */
    public function getAll() {
        $select = $this->select();
        $select->from($this->_name, array('tag', 'code'));
        
        $snippets = $this->fetchAll($select);
        $return = array();
        foreach ($snippets as $snippet) {
            $return [] = array(
                'tag'   =>  $snippet->tag,
                'code'  =>  $snippet->code
            );
        }
        
        return $return;        
    }
}
?>