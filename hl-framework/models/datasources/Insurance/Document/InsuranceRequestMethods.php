<?php

final class Datasource_Insurance_Document_InsuranceRequestMethods extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'doc_request_methods';
    protected $_primary = 'method_id';
    
    /**
     * Get the Id of a request method label
     *
     * @param string $methodlabel Request method label
     * @return integer Request method Id
     */
    public function getRequestMethodId($methodlabel)
    {
        $select = $this->select()->where('requestname = ?', $methodlabel);
        return $this->fetchRow($select)->method_id;
    }
}
