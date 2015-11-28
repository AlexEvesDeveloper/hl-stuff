<?php
/**
* Model definition for the page meta table
* 
*/
class Datasource_Cms_Page_Meta extends Zend_Db_Table_Multidb {
    protected $_name = 'page_template_meta';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    protected $_dependentTables = array ('Datasource_Cms_Page_MetaFields');
    protected $_referenceMap = array(
        'Meta' => array(
            'columns'       =>  array('page_id'),
            'refTableClass' =>  'Datasource_Cms_Pages',
            'refColumns'    =>  array('id')
    ));
}

?>