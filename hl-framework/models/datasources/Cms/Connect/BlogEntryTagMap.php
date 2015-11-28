<?php

/**
 * Model definition for the BlogEntryTagMap table.
 */
class Datasource_Cms_Connect_BlogEntryTagMap extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_homelet_connect';
    protected $_name = 'blog_entry_tag_map';
    protected $_primary = 'id';
    
    public function getByTagId($tagId)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this->_name);
        $select->joinInner('blog_entries', 'blog_entries.id = blog_entry_tag_map.blog_entry_id');
        $select->where('blog_entry_tag_id = ?', $tagId);
        $select->where('is_archived = 0');
        $select->order('last_updated DESC');
        $maps = $this->fetchAll($select);
        
        //Finalise the output consistent with this function's contract.
        if(count($maps) == 0) {
            
            $returnVal = null;
        }
        else {
            
            $returnVal = $maps;
        }
        
        return $returnVal;
    }
    
    /**
     * Function which adds a map (association) between a blog entry and a
     * blog entry tag.
     *
     * @param int $blogEntryID
     * The blog entry ID.
     *
     * @param int $tagID
     * The blog entry tag ID.
     */
    public function addMap($blogEntryID, $tagID)
    {
        $data = array(
            'blog_entry_id'        =>  $blogEntryID,
            'blog_entry_tag_id'    =>  $tagID
        );
        $this->insert($data);
    }
    
    /**
    * Removes all maps corresponding to a particular blog entry, as specified by
    * the $blogEntryID passed in.
    *
    * @param int $blogEntryID
    * The blog entry ID against which all associated maps should be deleted.
    */
    public function removeAllMaps($blogEntryID)
    {
        $where = $this->quoteInto('blog_entry_id = ?', $blogEntryID);
        $this->delete($where);
    }
}
?>