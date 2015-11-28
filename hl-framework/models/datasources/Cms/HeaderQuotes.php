<?php
/**
 *
 * Model definition for the cms quotes table
 *
 */
class Datasource_Cms_HeaderQuotes extends Zend_Db_Table_Multidb {
    protected $_name = 'header_quotes';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';

    /**
     *
     * Gets all quotes in an array
     *
     * @return string
     *
     */
    public function getAll() {
        $select = $this->select();

        $quotesArray = $this->fetchAll($select);

        $returnArray = array();
        foreach ($quotesArray as $quote) {
            // Now we have the quote - we need to build a list of tags it's currently linked to
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(array('qt' => 'header_quote_tags'), array('tag'));
            $select->joinInner(array('qtm' => 'header_quote_tags_map'), 'qtm.quote_tag_id = qt.id', array());
            $select->where('qtm.quote_id = ?', $quote->id);
            $tags = $this->fetchAll($select);
            $tagList = '';
            foreach ($tags as $tag) {
                $tagList .= $tag->tag . ', ';
            }
            array_push($returnArray, array(
                'id'        =>  $quote->id,
                'title'     =>  $quote->title,
                'subtitle'  =>  $quote->subtitle,
                'tags'      =>  $tagList
            ));
        }

        return $returnArray;
    }


    /**
     * Get a specific quote by category
     *
     * @param string tags
     * @param bool includeGlobal
     * @return array
     */
    public function getByTags($tags, $includeGlobal = true) {
        $tags = trim($tags);
        $tags = trim($tags,',');
        $tags = str_replace(' ','',$tags);

        $tagsArray = explode(',',$tags);
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('q' => 'header_quotes'), array('id','title','subtitle'));
        $select->joinInner(array('qtm' => 'header_quote_tags_map'), 'qtm.quote_id = q.id', array());
        $select->joinInner(array('qt' => 'header_quote_tags'), 'qt.id = qtm.quote_tag_id', array());
        $select->where('qt.tag = ?', $tagsArray[0]);
        unset ($tagsArray[0]);
        foreach ($tagsArray as $tag) {
            $select->orWhere('qt.tag = ?', $tag);
        }
        if ($includeGlobal) {
            $select->orWhere('qt.tag = ?', 'global');
        }

        $quotes = $this->fetchAll($select);

        $returnArray = array();
        foreach ($quotes as $quote) {
            array_push($returnArray, array(
                'id'        =>  $quote->id,
                'title'     =>  $quote->title,
                'subtitle'  =>  $quote->subtitle
            ));
        }

        return $returnArray;
    }



    /**
     * Get a specific quote by ID
     *
     * @param int quoteID
     * @return array
     */
    public function getById($quoteID) {
        $select = $this->select();
        $select->where('id = ?', $quoteID);

        $quote = $this->fetchRow($select);

        // Now we have the quote - we need to build a list of tags it's currently linked to
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('qt' => 'header_quote_tags'), array('tag'));
        $select->joinInner(array('qtm' => 'header_quote_tags_map'), 'qtm.quote_tag_id = qt.id', array());
        $select->where('qtm.quote_id = ?', $quote->id);
        $tags = $this->fetchAll($select);
        $tagList = '';
        foreach ($tags as $tag) {
            $tagList .= $tag->tag . ', ';
        }

        return(array(
            'id'            =>  $quote->id,
            'title'         =>  $quote->title,
            'subtitle'         =>  $quote->subtitle,
            'tags'          =>  $tagList
        ));
    }



    /**
     * Links a specific quote to a series of tags
     *
     * @param int quoteID
     * @param string tags Comma seperated list of tags
     */
    public function linkToTags($quoteID, $tags) {
        // Create an array of tags and trim any extra spaces out
        $tagArray = explode(',',$tags);

        // Do an upsert to make sure all tags actually exist in the lookup table
        $quoteTags = new Datasource_Cms_HeaderQuote_Tags();
        $quoteTags->upsert($tagArray);

        // Delete current tag links for this testimonial
        $this->clearLinksToTags($quoteID);

        $quoteTagMap = new Datasource_Cms_HeaderQuote_TagMap();
        // Now loop through and link the testimonial to the tag

        foreach ($tagArray as $tag) {
            $tagID = $quoteTags->getId($tag);
            $quoteTagMap->addMap($quoteID, $tagID);
        }
    }




    /**
     * Clears current links between testimonial and tags
     *
     * @param int testimonialID
     */
    public function clearLinksToTags($quoteID) {
        $quoteTagMap = new Datasource_Cms_HeaderQuote_TagMap();

        $where = $quoteTagMap->getAdapter()->quoteInto('quote_id = ?', $quoteID);
        $quoteTagMap->delete($where);
    }



    /**
     * Save changes to an existing header quote
     *
     * @param int testimonialID
     * @param string person
     * @param string quote
     * @param string tags
     */
    public function saveChanges($quoteID, $title, $subtitle, $tags) {
        $where = $this->quoteInto('id = ?', $quoteID);

        $data = array(
            'id'        =>  $quoteID,
            'title'     =>  $title,
            'subtitle'  =>  $subtitle
        );

        // Link quote to tags
        $tags = str_replace(' ','',$tags);
        $this->linkToTags($quoteID, $tags);

        $this->update($data, $where);
    }


    /**
     * Adds a new quote
     *
     * @param string person
     * @param string quote
     * @param string tags
     */
    public function addNew($title, $subtitle, $tags) {
        $data = array(
            'title'     =>  $title,
            'subtitle'  =>  $subtitle
        );

        $quoteID = $this->insert($data);

        // Link testimonial to tags
        $this->linkToTags($quoteID, $tags);

        return $quoteID;
    }



    /**
     * Delete an existing quote
     *
     * @param int testimonialID
     */
    public function remove($quoteID) {
        $where = $this->quoteInto('id = ?', $quoteID);
        $this->delete($where);
    }



    /**
     * Returns a comma seperated list of possible tags for the admin system
     *
     * @return string
     */
    public function getPossibleTags() {
        $quoteTags = new Datasource_Cms_HeaderQuote_Tags();

        $currentTags = $quoteTags->getAll();
        $tagList = array();
        foreach ($currentTags as $currentTag) {
            $tagList[] = '"' . $currentTag['tag'] . '"';
        }
        return implode(',',$tagList);
    }

}
?>