<?php
/**
 *
 * Model definition for the cms testimonials tag map table
 * 
 */
class Datasource_Cms_HeaderQuote_TagMap extends Zend_Db_Table_Multidb {
    protected $_name = 'header_quote_tags_map';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
    public function addMap($quoteID, $tagID) {
        $data = array(
            'quote_id'        =>  $quoteID,
            'quote_tag_id'    =>  $tagID
        );
        $this->insert($data);
    }
}
    