<?php
/**
* Model definition for the page meta fields table
* 
*/
class Datasource_Cms_Page_MetaField extends Zend_Db_Table_Multidb {
    protected $_name = 'page_template_meta_fields';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    protected $_referenceMap = array(
        'MetaField' => array(
            'columns'       =>  array('id'),
            'refTableClass' =>  'Datasource_Cms_Page_Meta',
            'refColumns'    =>  array('meta_field_id')
        ),
        'TemplateMeta' => array(
            'columns'       =>  array('page_template_id'),
            'refTableClass' =>  'Datasource_Cms_Page_Template',
            'refColumns'    =>  array('id')
        )
    );
    
}

?>