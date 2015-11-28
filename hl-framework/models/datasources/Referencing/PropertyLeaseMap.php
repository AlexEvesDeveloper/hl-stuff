<?php

/**
* Model definition for the property lease map datasource.
*/
class Datasource_Referencing_PropertyLeaseMap extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_referencing';
    protected $_name = 'property_lease_map';
    protected $_primary = 'reference_id';    
    
    /**
     * Inserts a new PropertyLeaseMap into the datasource.
     *
     * To create a map, there must already be an existing PropertyLease and an
     * existing Reference.
     *
     * @param integer $propertyLeaseId
     * The unique property lease identifier.
     *
     * @param integer $referenceId
     * The unique Reference identifer.
     *
	 * @return void
     */
    public function insertPlaceholder($propertyLeaseId, $referenceId) {    
    
        $data = array(
            'property_lease_id' => $propertyLeaseId,
            'reference_id' => $referenceId);
        
        if (!$this->insert($data)) {
            
            // Failed insertion
            throw new Zend_Exception("Can't create record in table {$this->_name}");
        }
    }
    
    /**
     * Returns the PropertyLease identifier associated with the Reference identifier passed in.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return mixed
     * Returns the integer PropertyLease identifier, if found. Else returns null.
     */
    public function getPropertyLeaseId($referenceId) {
        
        $select = $this->select();
        $select->where('reference_id = ?', $referenceId);
        $mapRow = $this->fetchRow($select);
        
        if(empty($mapRow)) {
            
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ': Unable to find PropertyLeaseMap.');
            $returnVal = null;
        }
        else {
            
            $returnVal = $mapRow->property_lease_id;
        }
        
        return $returnVal;
    }
}

?>