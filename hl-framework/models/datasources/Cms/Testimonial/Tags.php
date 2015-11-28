<?php
/**
 *
 * Model definition for the cms testimonial tags table
 * 
 */
class Datasource_Cms_Testimonial_Tags extends Zend_Db_Table_Multidb {
    protected $_name = 'testimonial_tags';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
    function __construct() {
        // Make this model use the homelet database connection
        $this->_db = Zend_Registry::get('db_homelet_cms');
    }
    
    
    /**
     * Gets all tags in an array
     *
     * @return string
     *
     */
    public function getAll() {
        $select = $this->select();
        
        $rows = $this->fetchAll($select);
        $quotesArray = $rows->toArray();
        
        $returnArray = array();
        foreach ($quotesArray as $quote) {
            array_push($returnArray, array(
                'id'    =>  $quote['id'],
                'tag'   =>  $quote['tag']
            ));
        }
        
        return $returnArray;
    }
    
    
    
    /**
     * Takes an array of tags and makes sure they exist, if they don't it will insert the new ones
     *
     * @param array tags An array of tags
     */
    public function upsert($tags) {
        // Otherwise known as the "I really wish Zend had a 'replace into'" function!!
        
        // Firstly we get an array of current tags
        $currentTags = $this->getAll();
        // Then we strip this down to just a raw array of tags (we don't need the id's)
        $currentTagArray = array();
        foreach ($currentTags as $currentTag) {
            $currentTagArray[] = $currentTag['tag'];
        }
        
        // Now we can loop through the tag list and remove ones we already have to create a list of tags we need to create
        foreach ($tags as $tag) {
            if (!in_array(trim($tag), $currentTagArray)) {
                $this->insert(array('tag' => trim($tag)));
            }
        }
    }
    
    
    
    public function getID($tag) {
        $select = $this->select();
        $select->where('tag = ?', $tag);
        
        echo "finding tag '" . $tag . "'<br>";
        $row = $this->fetchRow($select);
        return $row->id;
    }
}
?>