<?php
/**
* Model definition for the page meta fields table
* 
*/
class Datasource_Cms_News_CategoriesMap extends Zend_Db_Table_Multidb {
    protected $_name = 'news_categories_map';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
}

?>