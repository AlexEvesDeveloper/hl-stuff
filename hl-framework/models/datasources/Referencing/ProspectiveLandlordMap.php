<?php

/**
* Model definition for the ProspectiveLandlordMap datasource.
*/
class Datasource_Referencing_ProspectiveLandlordMap extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_referencing';
    protected $_name = 'prospective_landlord_map';
    protected $_primary = 'reference_id';    
    
    /**
     * Inserts a new ProspectiveLandlordMap into the datasource.
     *
     * To create a map, there must already be an existing ProspectiveLandlord and an
     * existing Enquiry.
     *
     * @param integer $prospectiveLandlordId
     * The unique ProspectiveLandlord identifier.
     *
     * @param integer $referenceId
     * The unique Referene identifer.
     *
	 * @return void
     */
    public function insertPlaceholder($prospectiveLandlordId, $referenceId) {    
    
        $data = array(
            'reference_id' => $referenceId,
            'prospective_landlord_id' => $prospectiveLandlordId);
        
        if (!$this->insert($data)) {
            
            // Failed insertion
            Application_Core_Logger::log("Can't create record in table {$this->_name}", 'error');
        }
    }
    
    /**
     * Returns the ProspectiveLandlord identifier associated with the Reference identifier passed in.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return mixed
     * Returns the integer ProspectiveLandlord identifier, if found. Else returns null.
     */
    public function getProspectiveLandlordId($referenceId) {
        
        $select = $this->select();
        $select->where('reference_id = ?', $referenceId);
        $mapRow = $this->fetchRow($select);
        
        if(empty($mapRow)) {
            
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ':Unable to find ProspectiveLandlordMap.');
            $returnVal = null;
        }
        else {
            
            $returnVal = $mapRow->prospective_landlord_id;
        }
        
        return $returnVal;
    }
}

?>