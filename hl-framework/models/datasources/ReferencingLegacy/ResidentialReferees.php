<?php

/**
* Model definition for the residential referees datasource.
*/
class Datasource_ReferencingLegacy_ResidentialReferees extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'landlordref';
    protected $_primary = 'refno';
    /**#@-*/
    
    
    /**
     * Not implemented in this release.
     */
    public function insertResidentialReferee($id) {
    
        throw new Zend_Exception(get_class() . __FUNCTION__ . ': not implemented in this release.');
    }
    
    
    /**
     * Not implemented in this release.
     */
    public function updateResidentialReferee($id) {
        
        throw new Zend_Exception(get_class() . __FUNCTION__ . ': not implemented in this release.');
    }
    
    
	/**
     * Retrieves the residential referee for a specific residence.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * Returns a Model_Referencing_ResidentialReferee object, or null if no
     * referee is found.
     */
    public function getByEnquiry($enquiryId) {
		
    	$enquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
    	return $this->getResidentialReferee($enquiryDatasource->getCurrentLandlordId($enquiryId));
    }
	
	
	/**
     * Retrieves the residential referee for a specific residence.
     *
     * @param int $referenceId
     * The unique residential referee identifier.
     *
     * @return mixed
     * Returns a Model_Referencing_ResidentialReferee object, or null if no
     * referee is found.
     */
	public function getResidentialReferee($refereeId) {

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
			
			$residentialReferee = new Model_Referencing_ResidenceReferee();
			$residentialReferee->name = new Model_Core_Name();
			$residentialReferee->name->firstName = $refereeRow->name;
			
			$residentialReferee->contactDetails = new Model_Core_ContactDetails();
			$residentialReferee->contactDetails->telephone1 = $refereeRow->telday;
			$residentialReferee->contactDetails->telephone2 = $refereeRow->televe;
			$residentialReferee->contactDetails->fax1 = $refereeRow->fax;
			$residentialReferee->contactDetails->email1 = $refereeRow->email;
			
			$residentialReferee->address = new Model_Core_Address();
			$residentialReferee->address->addressLine1 = $refereeRow->address1;
			$residentialReferee->address->addressLine2 = $refereeRow->address2;
			$residentialReferee->address->town = $refereeRow->town;
			$residentialReferee->address->postCode = $refereeRow->postcode;
			
			switch($refereeRow->type) {
				
				case 'Letting/Estate Agent':
					$residentialReferee->type = Model_Referencing_ResidenceRefereeTypes::LETTING_AGENT;
					break;
				case 'Solicitor':
					$residentialReferee->type = Model_Referencing_ResidenceRefereeTypes::SOLICITOR;
					break;
				case 'Managing Agent':
					$residentialReferee->type = Model_Referencing_ResidenceRefereeTypes::MANAGING_AGENT;
					break;
				case 'Landlord':
					$residentialReferee->type = Model_Referencing_ResidenceRefereeTypes::PRIVATE_LANDLORD;
					break;
			}
			
			$returnVal = $residentialReferee;
		}
		
		return $returnVal;
	}
}

?>