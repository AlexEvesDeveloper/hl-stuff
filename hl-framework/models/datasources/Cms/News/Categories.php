<?php
/**
* Model definition for the page meta fields table
* 
*/
class Datasource_Cms_News_Categories extends Zend_Db_Table_Multidb {
    protected $_name = 'news_categories';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
    /**
     * Returns an array of all the categories a news article can be linked to
     *
     * @return array
     *
     */
    public function getAll() {
        $rows = $this->fetchAll();
        
        $returnArray = array();
        foreach ($rows as $row) {
            $returnArray[] = array(
                'categoryID'    => $row->id,
                'category'      => $row->category,
                'niceName'      => $row->nice_name
            );
        }
        
        return $returnArray;
    }   
}
?>