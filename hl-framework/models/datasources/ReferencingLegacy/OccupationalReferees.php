<?php

/**
* Model definition for the occupational referees datasource.
*/
class Datasource_ReferencingLegacy_OccupationalReferees extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'employment';
    protected $_primary = 'refno';
    /**#@-*/
    
    
    /**
     * Not implemented in this release.
     */
    public function insertOccupationalReferee($id) {
    
        throw new Zend_Exception(get_class() . __FUNCTION__ . ': not implemented in this release.');
    }
    
    
    /**
     * Not implemented in this release.
     */
    public function updateOccupationalReferee($id) {
        
        throw new Zend_Exception(get_class() . __FUNCTION__ . ': not implemented in this release.');
    }
    
    
    /**
     * Not implemented in this release.
     */
    public function getByEnquiry($enquiryId, $chronology) {		
		
    	throw new Zend_Exception(get_class() . __FUNCTION__ . ': not implemented in this release.');
    }
	
	
	/**
     * Retrieves the occupational referee for a specific occupation.
     *
     * @param int $referenceId
     * The unique occupational referee identifier.
     *
     * @return mixed
     * Returns a Model_Referencing_OccupationalReferee object, or null if no
     * occupational referee is found.
     */
	public function getOccupationalReferee($refereeId) {
		
		if(empty($refereeId)) {
			
			return null;
		}
		
		
		$select = $this->select();
		$select->where('refno = ?', $refereeId);
		$refereeRow = $this->fetchRow($select);
		
		if(empty($refereeRow)) {
			
			$returnVal = null;
		}
		else {
			
			$referee = new Model_Referencing_OccupationReferee();
			$referee->name = new Model_Core_Name();
			$referee->name->firstName = $refereeRow->contactname;
			$referee->position = $refereeRow->contactposition;
			
			$referee->contactDetails = new Model_Core_ContactDetails();
			$referee->contactDetails->telephone1 = $refereeRow->tel;
			$referee->contactDetails->fax1 = $refereeRow->fax;
			$referee->contactDetails->email1 = $refereeRow->email;
			
			$referee->organisationName = $refereeRow->companyname;
			$referee->address = new Model_Core_Address();
			$referee->address->addressLine1 = $refereeRow->address1;
			$referee->address->addressLine2 = $refereeRow->address2;
			$referee->address->town = $refereeRow->town;
			$referee->address->postCode = $refereeRow->postcode;
			
			$returnVal = $referee;
		}
		
		return $returnVal;
	}
}

?>