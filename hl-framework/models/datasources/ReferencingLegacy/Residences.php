<?php

/**
* Model definition for the residences datasource.
*/
class Datasource_ReferencingLegacy_Residences extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'tenant_address';
    protected $_primary = 'refno';
    /**#@-*/
    
    
	/**
	 * Inserts a residence into the tenant_address table.
	 *
	 * @param Model_Referencing_Residence
	 * Holds the residence details.
	 *
	 * @param boolean $isLast
	 * Indicates if the residence is a last residence of the ReferenceSubject.
	 */
    public function insertResidence($residence, $isLast) {
    
        $data = array(
			'flat' => empty($residence->address->flatNumber) ? '' : $residence->address->flatNumber,
			'house' => empty($residence->address->houseName) ? '' : $residence->address->houseName,
			'address1' => empty($residence->address->addressLine1) ? '' : $residence->address->addressLine1,
			'address2' => empty($residence->address->addressLine2) ? '' : $residence->address->addressLine2,
			'town' => empty($residence->address->town) ? '' : $residence->address->town,
			'postcode' => empty($residence->address->postCode) ? '' : $residence->address->postCode,
			'monthshere' => $residence->durationAtAddress,
			'last' => ($isLast) ? 'Yes' : 'No',
			'isForeign' => ($residence->address->isOverseasAddress) ? 'yes' : 'no',
			'country' => empty($residence->address->country) ? '' : $residence->address->country
		);
		
		return $this->insert($data);
    }
    
    
    /**
     * Retrieves all residence details against a specific Enquiry.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * An array of Model_Referencing_Residence objects, or null if no
     * residences found.
     * 
     * @todo
     * The residence referencing details are not yet captured by this method.
     */
    public function getByEnquiry($enquiryId) {
        
        $returnArray = array();
		
		//Use the Enquiry datasource to retrieve the identifiers to the reference subject's
		//residences.
		$enquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
		$currentResidenceId = $enquiryDatasource->getResidenceId(
			$enquiryId, Model_Referencing_ResidenceChronology::CURRENT);
		$firstPreviousResidenceId = $enquiryDatasource->getResidenceId(
			$enquiryId, Model_Referencing_ResidenceChronology::FIRST_PREVIOUS);
		$secondPreviousResidenceId = $enquiryDatasource->getResidenceId(
			$enquiryId, Model_Referencing_ResidenceChronology::SECOND_PREVIOUS);
		
		
		//Now identify the residences recorded in the tenant_address table.
		for($i = 0; $i < 3; $i++) {
			
			switch($i) {
				
				case 0: $residenceId = $currentResidenceId; break;
				case 1: $residenceId = $firstPreviousResidenceId; break;
				case 2: $residenceId = $secondPreviousResidenceId; break;
			}
			
			if(empty($residenceId)) {
				
				continue;
			}
			
			
			$select = $this->select();
			$select->where('refno = ?', $residenceId);
			$residenceRow = $this->fetchRow($select);
			
			if(!empty($residenceRow)) {
					
				$residence = new Model_Referencing_Residence();
				
				//Set the ID.
				$residence->id = $residenceRow->refno;
				
				
				//Set the address.
				$address = new Model_Core_Address();
				$address->flatNumber = $residenceRow->flat;
				$address->houseName = $residenceRow->house;
				$address->addressLine1 = $residenceRow->address1;
				$address->addressLine2 = $residenceRow->address2;
				$address->town = $residenceRow->town;
				$address->postCode = $residenceRow->postcode;
				$address->country = $residenceRow->country;
				$residence->address = $address;
				
				
				//Set the chronology.
				switch($i) {
				
					case 0: $residence->chronology = Model_Referencing_ResidenceChronology::CURRENT; break;
					case 1: $residence->chronology = Model_Referencing_ResidenceChronology::FIRST_PREVIOUS; break;
					case 2: $residence->chronology = Model_Referencing_ResidenceChronology::SECOND_PREVIOUS; break;
				}
				
				
				//Set the duration of occupancy.
				$residence->durationAtAddress = $residenceRow->monthshere;

				
				//Set the residential status (owner, tenant, living with relatives).
				if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
					
					$referenceSubjectDatasource = new Datasource_ReferencingLegacy_ReferenceSubject();
					$residentialStatus = $referenceSubjectDatasource->getLegacyResidentialStatus($enquiryId);
					switch($residentialStatus) {
						
						case 'Owner':
							$residence->status = Model_Referencing_ResidenceStatus::OWNER;
							break;
						case 'Tenant':
							$residence->status = Model_Referencing_ResidenceStatus::TENANT;
							break;
						case 'Living with Relative':
							$residence->status = Model_Referencing_ResidenceStatus::LIVING_WITH_RELATIVES;
							break;
						default:
							$residence->status = null;
							break;
					}
				}
				else {
					
					$residence->status = null;
				}
				
				
				//Set the referee and referencing details, if applicable. We only obtain a referee and
				//reference against the current residence, and even then only if the applicant is a
				//tenant at the current residence.
				$residence->refereeDetails = null;
				$residence->referencingDetails = null;
					
				if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
					
					if($residence->status == Model_Referencing_ResidenceStatus::TENANT) {
						
						$refereeDatasource = new Datasource_ReferencingLegacy_ResidentialReferees();
						$residence->refereeDetails = $refereeDatasource->getByEnquiry($enquiryId);
						
						$residentialReferenceDatasource = new Datasource_ReferencingLegacy_ResidentialReferences();
						$residence->referencingDetails = $residentialReferenceDatasource->getByEnquiry($enquiryId);
					}
				}

				$returnArray[] = $residence;
			}
		}
        
		
		//Finalize
		if(empty($returnArray)) {
			
			$returnVal = null;
		}
		else {
			
			$returnVal = $returnArray;
		}
        return $returnVal;
    }
}

?>