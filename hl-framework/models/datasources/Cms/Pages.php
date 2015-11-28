<?php
/**
* Model definition for the cms pages table
* 
*/
class Datasource_Cms_Pages extends Zend_Db_Table_Multidb
{
    protected $_name = 'pages';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    protected $_dependentTables = array ('Datasource_Cms_Page_Meta');
    
    /**
     * Loads main content to display a specific page
     *
     * @param string url
     * @return string
     *
     */
    public function getByUrl($url)
    {
        $select = $this->select();
        $select->where('url = ?', $url);
        $row = $this->fetchRow($select);
        
        if (count($row)>0) {
            $templateRows = $row->findDependentRowset('Datasource_Cms_Page_Template');
            $templateRow = $templateRows->current()->toArray();
            
            $meta = $this->getMeta($row->id);
            $metaFieldData = $this->getMetaFields($row->id);
            
            $metaArray = array();
            foreach ($metaFieldData as $metaRow) {
                if (isset($meta[$metaRow['metaName']])) {
                    $metaArray[$metaRow['metaName']] = $meta[$metaRow['metaName']];
                } else {
                    $metaArray[$metaRow['metaName']] = '';
                }
            }
            
            $returnArray = array(
                'template'      =>  $templateRow['template_file'],
                'content'       =>  $row->content,
                'meta'          =>  $metaArray,
                'title'         =>  $row->title,
                'description'   =>  $row->description,
                'keywords'      =>  $row->keywords,
                'urlEditable'   =>  $row->url_editable
                );
            
            return $returnArray;
        } else {
            return array();
        }
    }
    
    /**
     * Returns a list of all the pages in the system (used by the admin system)
     *
     * @param string $siteHandle Optional site handle to filter pages by.
     *
     * @return array
     *
     */
    public function getPageList($siteHandle = '')
    {
        $select = $this->select();
        if ($siteHandle != '') {
            $select
            ->setIntegrityCheck(false)
            ->from(
                array('p' => 'pages')
            )
            ->join(
                array('s' => 'sites'),
                'p.site_id = s.id',
                array('s.handle')
            )
            ->where('s.handle = ?', $siteHandle);
        }
        $select->order('site_id');
        $select->order('url');
        $rows = $this->fetchAll($select);
        return $rows->toArray();
    }
    
    /**
     * Returns an array of page data for a specific page
     *
     * @param int pageID
     * @return array
     *
     */
    public function getByID($pageID)
    {
        $select = $this->select();
        $select->where('id = ?',$pageID);
        $row = $this->fetchAll($select);
        $pageArray = $row->current()->toArray();
        return (array(
            'pageContent'       =>  $pageArray['content'],
            'pageTitle'         =>  $pageArray['title'],
            'url'               =>  $pageArray['url'],
            'keywords'          =>  $pageArray['keywords'],
            'description'       =>  $pageArray['description'],
            'layoutID'          =>  $pageArray['template_id'],
            'urlEditable'       =>  $pageArray['url_editable']
        ));
    }
    
    /**
     * This function retrieves all the meta data for a page (if no meta has been entered a blank array row will be returned)
     *
     * @param int pageID
     * @return array
     *
     */
    public function getMeta($pageID)
    {
        $select = $this->select();
        $select->where('id = ?',$pageID);
        $pageRow = $this->fetchRow($select);
        
        $metaRows = $pageRow->findDependentRowset('Datasource_Cms_Page_Meta');
        
        $returnArray = array();
        
        foreach ($metaRows as $metaRow) {
            $metaRowField = $metaRow->findDependentRowset('Datasource_Cms_Page_MetaField');
            if (count($metaRowField)>0)
            {
                $metaRowFieldArray = $metaRowField->current()->toArray();
                $metaRowArray = $metaRow->toArray();
                if ($metaRowFieldArray['meta_type']=='html') {
                    $returnArray[$metaRowFieldArray['meta_name']] = $metaRowArray['html_value'];
                } else {
                    $returnArray[$metaRowFieldArray['meta_name']] = $metaRowArray['string_value'];
                }
            }
        }
        
        return $returnArray;
    }
    
    /**
     * Saves meta data for a specific page
     *
     * @param int pageID
     * @param array metaData
     * @param return boolean
     *
     */
    public function saveMeta($pageID, $metaData)
    {
        // Bit dangerous this but we have to delete the old meta data and then insert the new
        // Could do with being altered to a database transaction that can be rolled back at some point
        
        $pageMeta = new Datasource_Cms_Page_Meta();
        $where = $pageMeta->getAdapter()->quoteInto('page_id = ?', $pageID);
        $pageMeta->delete($where);
        $metaData['page_id'] = $pageID;
        
        /*
        // Delete any attached meta expando data too
        $pageMetaExpando = new Datasource_Cms_Page_MetaExpando();
        $pageMetaExpando->delete($pageID);
        */
        
        $metaFields = $this->getMetaFields($pageID);
        foreach ($metaFields as $metaField) {
            if ($metaField['metaType']=='html') {
                $htmlValue = $metaData[$metaField['metaName']];
                $stringValue = '';
            } else {
                $htmlValue = '';
                $stringValue = $metaData[$metaField['metaName']];
            }
            $data = array(
                'meta_field_id' =>  $metaField['id'],
                'html_value'    =>  $htmlValue,
                'string_value'  =>  $stringValue,
                'page_id'       =>  $pageID
            );
            $pageMeta->insert($data);
        }
        return true;
    }
    
    /**
     * This function retrieves all the meta fields for a page
     *
     * @param int pageID
     * @return array
     *
     */
    public function getMetaFields($pageID)
    {
        $select = $this->select();
        $select->where('id = ?',$pageID);
        $pageRow = $this->fetchRow($select);
        
        $templateRows = $pageRow->findDependentRowset('Datasource_Cms_Page_Template');
        $templateRow = $templateRows->current();
        
        $metaField = $templateRow->findDependentRowset('Datasource_Cms_Page_MetaField');
        $metaFieldArray = $metaField->toArray();
        
        $returnArray = array();
        foreach ($metaFieldArray as $metaField)
        {
            array_push($returnArray, array(
                'id'            =>  $metaField['id'],
                'metaName'       =>  $metaField['meta_name'],
                'metaType'       =>  $metaField['meta_type'],
                'niceName'       =>  $metaField['nice_name'],
                'description'    =>  $metaField['description']
            ));
        }
        
        return $returnArray;
    }
    
    /**
     * Adds a new page
     *
     * @param string $siteId
     * @param string $title
     * @param string $url
     * @param string $content
     * @param string $keywords
     * @param string $description
     * @param int $templateID
     *
     * @return boolean
     */
    public function addNew($siteId, $title, $url, $content, $keywords, $description, $templateID)
    {
        $data = array(
            'title'         =>  $title,
            'url'           =>  $url,
            'content'       =>  $content,
            'keywords'      =>  $keywords,
            'description'   =>  $description,
            'site_id'       =>  $siteId,
            'template_id'   =>  $templateID,
            'editable'      =>  1
        );
        
        return $this->insert($data);
    }
    
    /**
     * Update an existing page
     *
     * @param int pageID
     * @param string title
     * @param string url
     * @param string content
     * @return boolean
     */
    public function saveChanges($pageID, $title, $url, $content, $keywords, $description, $templateID)
    {
        // TODO : Copy a snapshot to the page history table first
        $data = array(
            'id'            =>  $pageID,
            'title'         =>  $title,
            'url'           =>  $url,
            'content'       =>  $content,
            'keywords'      =>  $keywords,
            'description'   =>  $description,
            'template_id'   =>  $templateID
        );
        
        $where = $this->quoteInto('id = ?', $pageID);
        $this->update($data, $where);
    }
    
    public function remove($pageID)
    {
        $where = $this->quoteInto('id = ?', $pageID);
        $this->delete($where);
    }
}
?>