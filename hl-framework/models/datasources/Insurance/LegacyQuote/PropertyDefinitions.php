<?php

class Datasource_Insurance_LegacyQuote_PropertyDefinitions extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'POLICYVARIABLEDEF';
    protected $_primary = 'policyVariableDefID';

    /**
     * Get list of property definitions
     *
     * @return array List of property definition
     */
    public function getPropertyDefinitions()
    {
        return $this->fetchAll($this->select());
    }
}
