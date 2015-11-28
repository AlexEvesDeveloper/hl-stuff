<?php

/**
 * Model definition for the BlogEntryTags tags table. As the name implies, the
 * table holds tags associated with blog entries, allowing the end user to search
 * for specific blog entries.
 */
class Datasource_Cms_Connect_BlogEntryTags extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_homelet_connect';
    protected $_name = 'blog_entry_tags';
    protected $_primary = 'id';
    
    /**
     * Retrieves all blog entry tags in an array.
     *
     * @return array
     * Array of all blog entry tags.
     */
    public function getAll()
    {
        $select = $this->select();
        
        $rows = $this->fetchAll($select);
        $blogEntriesTagsArray = $rows->toArray();
        
        $returnArray = array();
        foreach ($blogEntriesTagsArray as $tag) {
            
            array_push($returnArray, array(
                'id'    =>  $tag['id'],
                'tag'   =>  $tag['tag']
            ));
        }
        
        return $returnArray;
    }
    
    /**
     * Retrieves all blog entry tags IN USE in an array.
     *
     * @return array
     * Array of all blog entry tags.
     */
    public function getAllInUse()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('blog_entry_tags', array('id','tag'));
        $select->joinInner('blog_entry_tag_map', 'blog_entry_tag_map.blog_entry_tag_id = blog_entry_tags.id', array());
        $select->joinInner('blog_entries', 'blog_entries.id = blog_entry_tag_map.blog_entry_id', array());
        $select->where('blog_entries.is_archived = 0');
        $select->group('tag');
        $select->order('tag ASC');
        $rows = $this->fetchAll($select);
        $blogEntriesTagsArray = $rows->toArray();
        
        $returnArray = array();
        foreach ($blogEntriesTagsArray as $tag) {
            array_push($returnArray, array(
                'id'    =>  $tag['id'],
                'tag'   =>  $tag['tag']
            ));
        }
        
        return $returnArray;
    }
    
    /**
     * Takes an array of tags and makes sure they exist, if they don't it will
     * insert the new ones.
     *
     * @param array $tags
     * An array of blog entry tags
     */
    public function upsert($tags)
    {
        //First get all the current tags.
        $currentTags = $this->getAll();
        
        //Then strip this down to just a raw array of tags (we don't need the id's)
        $currentTagArray = array();
        foreach ($currentTags as $currentTag) {
            
            $currentTagArray[] = $currentTag['tag'];
        }
        
        //Now loop through the tag list and remove ones we already have to create
        //a list of tags we need to create
        foreach ($tags as $tag) {
            
            if (!in_array(trim($tag), $currentTagArray)) {
                
                $this->insert(array('tag' => trim($tag)));
            }
        }
    }
    
    /**
     * Returns the ID associated with the blog entry tag string passed in.
     *
     * @param string $tag
     * The blog entry tag string.
     *
     * @return int
     * The ID of the blog entry.
     */
    public function getID($tag) {
        
        $select = $this->select();
        $select->where('tag = ?', $tag);
        $row = $this->fetchRow($select);
        return $row->id;
    }
}
?>