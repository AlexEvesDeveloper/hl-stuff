<?php

class Datasource_Insurance_LegacyQuote_Properties extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'POLICYVARIABLE';
    protected $_primary = 'policyVariableID';

    /**
     * Get a single property for the policy
     *
     * @param $policyNumber string Policy number affected
     * @param $propertyId int Property id
     * @return mixed
     */
    public function getProperty($policyNumber, $propertyId)
    {
        $select = $this->select()
                       ->from($this->_name)
                       ->where('policynumber = ?', $policyNumber)
                       ->where('policyVariableDefID = ?', $propertyId)
                       ->order('timestamp DESC')
                       ->limit(1);

        return $this->fetchRow($select);
    }
}
