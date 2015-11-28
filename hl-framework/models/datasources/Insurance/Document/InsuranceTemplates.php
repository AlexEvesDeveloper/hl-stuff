<?php

final class Datasource_Insurance_Document_InsuranceTemplates extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'doc_template_names';
    protected $_primary = 'template_id';
    
    /**
     * Retrieve a templates Id number
     *
     * @param string $templatename Template name
     * @return int Template Id number
     */
    public function getTemplateId($templatename)
    {
        $queueselect = $this->select()->where('template_name = ?', $templatename);
        $row = $this->fetchRow($queueselect);
        
        if (isset($row['template_id']))
            return $row['template_id'];
            
        return null;
    }
    
    /**
     * Retrieve the template name
     */
    public function getTemplateName($templateid)
    {
        $select = $this->select()->where('template_id = ?', $templateid);
        $row = $this->fetchrow($select);
        
        if (isset($row['template_name']))
            return $row['template_name'];
        
        return null;
    }

    /**
     * Retrieves the customers description
     */
    public function getCustomersDescription($templateid)
    {
        $select = $this->select()->where('template_id = ?', $templateid);
        $row = $this->fetchrow($select);
        
        if (isset($row['customers_description']))
            return $row['customers_description'];
        
        return null;
    }
}
