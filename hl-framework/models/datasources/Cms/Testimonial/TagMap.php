<?php
/**
 *
 * Model definition for the cms testimonials tag map table
 * 
 */
class Datasource_Cms_Testimonial_TagMap extends Zend_Db_Table_Multidb {
    protected $_name = 'testimonial_tags_map';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
    
    public function addMap($testimonialID, $tagID) {
        $data = array(
            'testimonial_id'        =>  $testimonialID,
            'testimonial_tag_id'    =>  $tagID
        );
        $this->insert($data);
    }
}
    