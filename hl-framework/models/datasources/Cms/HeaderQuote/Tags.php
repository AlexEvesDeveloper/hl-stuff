<?php
/**
 *
 * Model definition for the cms quote categories table
 * 
 */
class Datasource_Cms_HeaderQuote_Tags extends Zend_Db_Table_Multidb {
    protected $_name = 'header_quote_tags';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
    
    /**
     *
     * Gets all quote tags in an array
     *
     * @return string
     *
     */
    public function getAll() {
        $select = $this->select();
        
        $quoteTagsArray = $this->fetchAll($select);
        
        $returnArray = array();
        foreach ($quoteTagsArray as $quoteTag) {
            array_push($returnArray, array(
                'id'        =>  $quoteTag->id,
                'tag'       =>  $quoteTag->tag
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