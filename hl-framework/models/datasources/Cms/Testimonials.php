<?php
/**
 *
 * Model definition for the cms testimonials table
 * 
 */
class Datasource_Cms_Testimonials extends Zend_Db_Table_Multidb {
    protected $_name = 'testimonials';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
    /**
     *
     * Gets all testimonial items in an array
     *
     * @return string
     *
     */
    public function getAll() {
        $select = $this->select();
        
        $rows = $this->fetchAll($select);
        $testimonialsArray = $rows;
        
        $returnArray = array();
        foreach ($testimonialsArray as $testimonial) {
            // Now we have the testimonial - we need to build a list of tags it's currently linked to
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(array('tt' => 'testimonial_tags'), array('tag'));
            $select->joinInner(array('ttm' => 'testimonial_tags_map'), 'ttm.testimonial_tag_id = tt.id', array());
            $select->where('ttm.testimonial_id = ?', $testimonial->id);
            $tags = $this->fetchAll($select);
            $tagList = '';
            foreach ($tags as $tag) {
                $tagList .= $tag->tag . ', ';
            }
            
            array_push($returnArray, array(
                'id'            =>  $testimonial->id,
                'person'        =>  $testimonial->person,
                'quote'         =>  $testimonial->quote,
                'shortQuote'    =>  Application_Core_Utilities::word_split($testimonial->quote,10) . '&hellip;',
                'tags'          =>  $tagList
            ));
        }
        
        return $returnArray;
    }
    
    
    
    
    /**
     * Get a specific testimonial by ID
     *
     * @param int testimonialID
     * @return array
     */
    public function getByID($testimonialID) {
        $select = $this->select();
        $select->where('id = ?', $testimonialID);
        
        $testimonial = $this->fetchRow($select);
        
        // Now we have the testimonial - we need to build a list of tags it's currently linked to
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('tt' => 'testimonial_tags'), array('tag'));
        $select->joinInner(array('ttm' => 'testimonial_tags_map'), 'ttm.testimonial_tag_id = tt.id', array());
        $select->where('ttm.testimonial_id = ?', $testimonial->id);
        
        $tags = $this->fetchAll($select);
        $tagList = '';
        foreach ($tags as $tag) {
            $tagList .= $tag->tag . ', ';
        }
        
        return(array(
            'id'            =>  $testimonial->id,
            'person'        =>  $testimonial->person,
            'quote'         =>  $testimonial->quote,
            'shortQuote'    =>  Application_Core_Utilities::word_split($testimonial->quote,10) . '&hellip;',
            'tags'          =>  $tagList
        ));
    }
    
    
    /**
     * Get all testimonials that match any entry in a list of tags
     *
     * @param int testimonialID
     * @return array
     */
    public function getByTags($tagsArray) {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('t' => 'testimonials'), array('id','person','quote'));
        $select->joinInner(array('ttm' => 'testimonial_tags_map'), 'ttm.testimonial_id = t.id', array());
        $select->joinInner(array('tt' => 'testimonial_tags'), 'tt.id = ttm.testimonial_tag_id', array());
        $select->where('tt.tag = ?', $tagsArray[0]);
        unset ($tagsArray[0]);
        foreach ($tagsArray as $tag) {
            $select->orWhere('tt.tag = ?', $tag);
        }
        $select->orWhere('tt.tag = ?', 'global');
        
        $testimonials = $this->fetchAll($select);
        
        $returnArray = array();
        foreach ($testimonials as $testimonial) {
            $returnArray[] = array(
                'id'            =>  $testimonial->id,
                'person'        =>  $testimonial->person,
                'quote'         =>  $testimonial->quote,
                'shortQuote'    =>  Application_Core_Utilities::word_split($testimonial->quote,10) . '&hellip;'
            );
        }
        
        return $returnArray;        
    }
    
    
    
    /**
     * Links a specific testimonial to a series of tags
     *
     * @param int testimonialID
     * @param string tags Comma seperated list of tags
     */
    public function linkToTags($testimonialID, $tags) {
        // Create an array of tags and trim any extra spaces out
        $tagArray = explode(',',$tags);
        
        // Do an upsert to make sure all tags actually exist in the lookup table
        $testimonialTags = new Datasource_Cms_Testimonial_Tags();
        $testimonialTags->upsert($tagArray);
        
        // Delete current tag links for this testimonial
        $this->clearLinksToTags($testimonialID);
        
        $testimonialTagMap = new Datasource_Cms_Testimonial_TagMap();
        // Now loop through and link the testimonial to the tag
        
        foreach ($tagArray as $tag) {
            $tagID = $testimonialTags->getId($tag);
            $testimonialTagMap->addMap($testimonialID, $tagID);
        }
    }
    
    
    
    
    /**
     * Clears current links between testimonial and tags
     *
     * @param int testimonialID
     */
    public function clearLinksToTags($testimonialID) {
        $testimonialTagMap = new Datasource_Cms_Testimonial_TagMap();
        
        $where = $testimonialTagMap->getAdapter()->quoteInto('testimonial_id = ?', $testimonialID);
        $testimonialTagMap->delete($where);
    }
    
    
    
    
    /**
     * Save changes to an existing testimonial
     *
     * @param int testimonialID
     * @param string person
     * @param string quote
     * @param string tags
     */
    public function saveChanges($testimonialID, $person, $quote, $tags) {
        $where = $this->quoteInto('id = ?', $testimonialID);

        $data = array(
            'id'        =>  $testimonialID,
            'person'    =>  $person,
            'quote'     =>  $quote
        );
        
        // Link testimonial to tags
        $tags = str_replace(' ','',$tags);
        $this->linkToTags($testimonialID, $tags);
        
        $this->update($data, $where);
    }
    
    
    /**
     * Adds a new testimonial
     *
     * @param string person
     * @param string quote
     * @param string tags
     */
    public function addNew($person, $quote, $tags) {
        $data = array(
            'person'    =>  $person,
            'quote'     =>  $quote
        );
        
        $testimonialID = $this->insert($data);
        
        // Link testimonial to tags
        $this->linkToTags($testimonialID, $tags);
        
        return $testimonialID;
    }
    
    
    /**
     * Delete an existing testimonial
     *
     * @param int testimonialID
     */
    public function remove($testimonialID) {
        $where = $this->quoteInto('id = ?', $testimonialID);
        $this->delete($where);
    }
    
    
    /**
     * Returns a comma seperated list of possible tags for the admin system
     *
     * @return string
     */
    public function getPossibleTags() {
        $testimonialTags = new Datasource_Cms_Testimonial_Tags();
        
        $currentTags = $testimonialTags->getAll();
        $tagList = array();
        foreach ($currentTags as $currentTag) {
            $tagList[] = '"' . $currentTag['tag'] . '"';
        }
        return implode(',',$tagList);
    }
}
?>