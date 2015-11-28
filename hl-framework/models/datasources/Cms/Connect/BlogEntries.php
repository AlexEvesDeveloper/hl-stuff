<?php

/**
 * Model definition for the BlogEntries table
 *
 * @todo
 * Remove the 'is_archived' field and use the status field instead.
 */
class Datasource_Cms_Connect_BlogEntries extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_homelet_connect';
    protected $_name = 'blog_entries';
    protected $_primary = 'id';
    
    public function getByID($id)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('blog_entries');
        $select->joinInner(array('betm' => 'blog_entry_tag_map'), 'betm.blog_entry_id = blog_entries.id', array());
        $select->joinInner(array('bet' => 'blog_entry_tags'), 'bet.id = betm.blog_entry_tag_id', array('tag'));
        $select->where('blog_entries.id = ?', $id);
        $select->where('is_archived = 0');
        
        $blogEntry = $this->fetchRow($select);
        
        //Put it all together and return.
        if ($blogEntry) {
            $returnArray = array(
                'id' => $blogEntry->id,
                'lastUpdated' => $blogEntry->last_updated,
                'title' => $blogEntry->title,
                'summary' => $blogEntry->summary,
                'article' => $blogEntry->article,
                'status' => $blogEntry->status,
                'imageName' => $blogEntry->image_name,
                'tagString' => $blogEntry->tag
            );
        } else {
            Zend_Debug::dump($select->__toString());
            $returnArray = null;
        }        
        return $returnArray;
    }
    
    /**
    */
    public function getByStatus($status)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('blog_entries');
        $select->joinInner(array('betm' => 'blog_entry_tag_map'), 'betm.blog_entry_id = blog_entries.id', array());
        $select->joinInner(array('bet' => 'blog_entry_tags'), 'bet.id = betm.blog_entry_tag_id', array('tag'));
        $select->where('status = ?', $status);
        $select->where('is_archived = 0');
        $select->group('blog_entries.id');
        $select->order('last_updated DESC');
        $blogEntriesArray = $this->fetchAll($select);
        
        $returnArray = array();
        foreach($blogEntriesArray as $currentBlogEntry) {
            $returnArray[] = array(
                'id' => $currentBlogEntry->id,
                'lastUpdated' => $currentBlogEntry->last_updated,
                'title' => $currentBlogEntry->title,
                'summary' => $currentBlogEntry->summary,
                'article' => $currentBlogEntry->article,
                'status' => $currentBlogEntry->status,
                'imageName' => $currentBlogEntry->image_name,
                'tagString' => $currentBlogEntry->tag
            );
        }
        
        //Finalise the output consistent with this function's contract.
        if(empty($returnArray)) {
            $returnVal = null;
        } else {
            $returnVal = $returnArray;
        }
        
        return $returnVal;
    }
    
    public function getByTagId($tagId)
    {
        $map = new Datasource_Cms_Connect_BlogEntryTagMap();
        $mapArray = $map->getByTagId($tagId);
        
        $returnArray = array();
        foreach($mapArray as $currentMap) {
            $returnArray[] = $this->getByID($currentMap['blog_entry_id']);
        }
        
        //Provide a return value consistent with this function's contract.
        if(empty($returnArray)) {
            $returnVal = null;
        }
        else {
            $returnVal = $returnArray;
        }
        return $returnVal;
    }
    
    /**
     * Returns blog entries matching the date specified.
     *
     * @param string $month
     * The full month name to search for. E.g. February
     *
     * @param integer $year
     * The year to search for. E.g. 2011
     *
     * @return mixed
     * Returns an array of blog entry data corresponding to the date specified,
     * or null if no matches found.
     */
    public function getByDate($month, $year)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('blog_entries');
        $select->joinInner(array('betm' => 'blog_entry_tag_map'), 'betm.blog_entry_id = blog_entries.id', array());
        $select->joinInner(array('bet' => 'blog_entry_tags'), 'bet.id = betm.blog_entry_tag_id', array('tag'));
        $select->where('is_archived = 0');
        $select->where('DATE_FORMAT(last_updated, "%Y") = ?', $year);
        $select->where('DATE_FORMAT(last_updated, "%M") = ?', ucfirst(strtolower($month)));
        $select->group('blog_entries.id');
        $select->order('last_updated DESC');
        $blogEntriesArray = $this->fetchAll($select);
        
        $returnArray = array();
        foreach($blogEntriesArray as $currentBlogEntry) {
            $returnArray[] = array(
                'id' => $currentBlogEntry['id'],
                'lastUpdated' => $currentBlogEntry['last_updated'],
                'title' => $currentBlogEntry['title'],
                'summary' => $currentBlogEntry['summary'],
                'article' => $currentBlogEntry['article'],
                'status' => $currentBlogEntry['status'],
                'imageName' => $currentBlogEntry['image_name'],
                'tagString' => $currentBlogEntry['tag']
            );
        }
        
        // Provide a return value consistent with this function's contract.
        if(empty($returnArray)) {
            $returnVal = null;
        } else {
            $returnVal = $returnArray;
        }
        return $returnVal;
    }
    
    /**
     */
    public function getAll()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('blog_entries');
        $select->joinInner(array('betm' => 'blog_entry_tag_map'), 'betm.blog_entry_id = blog_entries.id', array());
        $select->joinInner(array('bet' => 'blog_entry_tags'), 'bet.id = betm.blog_entry_tag_id', array('tag'));
        $select->where('is_archived = 0');
        $select->group('blog_entries.id');
        $select->order('last_updated DESC');
        $blogEntriesArray = $this->fetchAll($select);
        
        $returnArray = array();
        foreach($blogEntriesArray as $currentBlogEntry) {
            //Add the record to the return array.
            $returnArray[] = array(
                'id' => $currentBlogEntry['id'],
                'lastUpdated' => $currentBlogEntry['last_updated'],
                'title' => $currentBlogEntry['title'],
                'summary' => $currentBlogEntry['summary'],
                'article' => $currentBlogEntry['article'],
                'status' => $currentBlogEntry['status'],
                'imageName' => $currentBlogEntry['image_name'],
                'tagString' => $currentBlogEntry['tag']
            );
        }
        
        return $returnArray;
    }
    
    public function saveChanges($id, $lastUpdated, $title, $summary, $article, $status, $imageName, $tags)
    {
        $where = $this->quoteInto('id = ?', $id);
        $data = array(
            'id' => $id,
            'last_updated' => $lastUpdated->toString('YYYY-MM-dd'),
            'title' => $title,
            'summary' => $summary,
            'article' => $article,
            'status' => $status,
            'image_name' => $imageName
        );
        $this->update($data, $where);
        
        //Now insert the tags, if any have been provided.
        $this->_saveTags($id, $tags);
    }

    public function addNew($lastUpdated, $title, $summary, $article, $status, $imageName, $tags)
    {
        $data = array(
            'last_updated' => $lastUpdated->toString('YYYY-MM-dd'),
            'title' => $title,
            'summary' => $summary,
            'article' => $article,
            'status' => $status,
            'image_name' => $imageName
        );
        
        $id = $this->insert($data);
        
        //Now insert the tags, if any have been provided.
        $this->_saveTags($id, $tags);
        
        return $id;
    }
    
    /**
     * Saves tags associated with the blog entry ID passed in. Also maps
     * the tags to the entry.
     *
     * @param int $blogEntryID
     * The blog entry ID to which the tags should be mapped.
     *
     * @param array $tags
     * The array of tags to associate with the blog entry ID.
     */
    protected function _saveTags($blogEntryID, $tags)
    {
        //Add any tags from $tags which are unique.
        $blogEntryTags = new Datasource_Cms_Connect_BlogEntryTags();
        if(!empty($tags)) {
            $blogEntryTags->upsert($tags);
        }
        
        //Delete all associations with the blogentry
        $blogEntryTagMap = new Datasource_Cms_Connect_BlogEntryTagMap();
        $blogEntryTagMap->removeAllMaps($blogEntryID);
        
        //Now associate the blog entry with each tag in $tags
        if(!empty($tags)) {
            foreach($tags as $currentTag) {
                $tagID = $blogEntryTags->getID($currentTag);
                $blogEntryTagMap->addMap($blogEntryID, $tagID);
            }
        }
    }
    
    /**
     * Sets the status of the blog entry, which can be 'main', 'summary' or
     * 'pool'.
     */
    public function setStatus($id, $status)
    {
        if($status == 1) {
            //If there is currently a blog entry with a status of 1 (main blog entry),
            //change its status to 3 (pool blog entry).
            $where = $this->quoteInto('status = ?', $status);
            $data = array('status' => 3);
            $this->update($data, $where);
        } else if($status == 2) {
            $select = $this->select();
            $select->where('status = ?', $status);
            $summaryBlogs = $this->fetchAll($select);
            
            if(count($summaryBlogs) >= 4) {
                //Change the status of the oldest summary blog to 3 (pool).
                $select = $this->select()->where('status = ?', $status)
                    ->order('last_updated DESC')
                    ->limit(1, 0);
                $row = $this->fetchRow($select);
                
                $where = $this->quoteInto('id = ? ', $row->id);
                $data = array('status' => 3);
                $this->update($data, $where);
            }
        } else if($status == 3) {
            //No preliminary work necessary.
        } else {
            throw new Exception("Invalid status specified.");
        }
        
        //Now set the status of the blog entry to that passed in.
        $where = $this->quoteInto('id = ?', $id);
        $data = array('status' => $status);
        $this->update($data, $where);
    }
    
    /**
     * Function which sets the archived status of a specified blog entry.
     *
     * If a blog is archived, it will no longer appear on the screen, and will
     * no longer be returned in search results.
     *
     * @param int $id
     * The unique blog identifier.
     *
     * @param boolean $status
     * True to archive the blog, false otherwise.
     *
     * @return void
     *
     * @todo
     * Remove the 'is_archived' field and use the status field instead.
     */
    public function setArchivedStatus($id, $status)
    {
        $where = $this->quoteInto('id = ?', $id);
        
        //Convert the boolean to integers that are required by the datasource.
        if($status) {
            //When a blog entry is archived, its status has to be set to 4 to ensure
            //that we continue to allow 1 main blog and 4 summary blogs.
            
            //The 'is_archived' field should be deleted and replaced entirely by
            //a status of 4.
            $data = array('is_archived' => 1, 'status' => 4);
        }
        else {
            $data = array('is_archived' => 0);
        }
        
        $this->update($data, $where);
    }
    
    /**
     */
    public function remove($blogEntryID)
    {
        //First delete from the blog entry.
        $where = $this->quoteInto('id = ?', $blogEntryID);
        $this->delete($where);
        
        //Delete all associations with the blogentry
        $blogEntryTagMap = new Datasource_Cms_Connect_BlogEntryTagMap();
        $blogEntryTagMap->removeAllMaps($blogEntryID);
    }
    
    /**
     * Returns a comma seperated list of possible tags for the admin system
     *
     * @return string
     */
    public function getPossibleTags()
    {
        $blogTags = new Datasource_Cms_Connect_BlogEntryTags();
        
        $currentTags = $blogTags->getAll();
        $tagList = array();
        foreach ($currentTags as $currentTag) {
            $tagList[] = '"' . $currentTag['tag'] . '"';
        }
        return implode(',', $tagList);
    }
}
?>