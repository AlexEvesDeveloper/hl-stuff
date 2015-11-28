<?php
/**
 * Model definition for the page templates table
 * 
 */
class Datasource_Cms_Page_Template extends Zend_Db_Table_Multidb
{
    protected $_name = 'page_templates';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    protected $_referenceMap = array(
        'Template' => array(
            'columns'       =>  array('id'),
            'refTableClass' =>  'Datasource_Cms_Pages',
            'refColumns'    =>  array('template_id')
        )
    );
    
    /**
     * This function will return an array of all available page templates for a site.
     *
     * @param int $siteId The site ID to filter templates by.
     *
     * @return array
     */
    public function getAll($siteId) {
        // Filter out any templates which don't have editable content as they are used just for static CMS pages
        $select = $this->select();
        $where = $this->quoteInto('site_id = ? AND content_editable = ?', $siteId, 1);
        $select->where($where);
        $select->order('sort_order ASC');
        
        $templateArray = $this->fetchAll($select)->toArray();
        
        $returnArray = array();
        foreach ($templateArray as $template)
        {
            array_push($returnArray,array(
                'id'            =>  $template['id'],
                'templateFile'  =>  $template['template_file'],
                'description'   =>  $template['description']
            ));
        }
        
        return $returnArray;
    }
}
?>