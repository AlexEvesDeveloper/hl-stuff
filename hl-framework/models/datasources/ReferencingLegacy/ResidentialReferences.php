<?php

/**
* Model definition for the residential references datasource.
*/
class Datasource_ReferencingLegacy_ResidentialReferences extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'experian';
    protected $_primary = 'refno';
    /**#@-*/
    
	
	/**
	 * Indicates if the residential reference is applicable.
	 *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return boolean
     * True if the reference is applicable, false otherwise.
     */
    public function getIsReferenceApplicable($enquiryId) {
		
		$enquiry = new Datasource_ReferencingLegacy_Enquiry();
		$legacyLandlordKey = $enquiry->getCurrentLandlordId($enquiryId);
		
		if(empty($legacyLandlordKey)) {
			
			$returnVal = false;
		}
		else {
			
			$returnVal = true;
		}
		
		return $returnVal;
	}
	
	
	/**
     * Indicate if the residential reference is complete.
     * 
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return boolean
     * True if the residence is complete or N/A, false otherwise.
     */
    public function getIsReferenceComplete($enquiryId) {
        
        $select = $this->select();
		
        $select->where('refno = ?', $enquiryId);
        $experianRow = $this->fetchRow($select);
        
        if(empty($experianRow)) {
            
            $returnVal = false;
        }
        else {

			if(preg_match("/^accept/i", $experianRow->acceptlandlord)) {
            
				$returnVal = true;
			}
			else if(preg_match("/n\/a/i", $experianRow->acceptlandlord)) {
				
				$returnVal = true;
			}
			else {
				
				$returnVal = false;
			}
        }
        
        return $returnVal;
    }
	
	
	/**
     * Retrieves the residential reference for a specific Enquiry.
     *
     * The residential reference, if applicable, will only be given for the
     * applicant's current residence. This is why the reference can be retrieved
     * using the $enquiryId, as opposed to a residence id.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * Returns a Model_Referencing_ResidentialReference object, or null if no
     * reference is found.
     */
    public function getByEnquiry($enquiryId) {
		
		if(empty($enquiryId)) {
			
			return null;
		}
		
		
		$select = $this->select();
		$select->where('refno = ?', $enquiryId);
		$referenceRow = $this->fetchRow($select);
		
		if(empty($referenceRow)) {

			$returnVal = null;
		}
		else {
			
			$residentialReference = new Model_Referencing_ResidenceReference();
			switch($referenceRow->pllhowconf) {
				
				case 'Phone':
					$residentialReference->submissionType  = Model_Referencing_ReferenceSubmissionTypes::PHONE;
					break;
				case 'Fax':
					$residentialReference->submissionType = Model_Referencing_ReferenceSubmissionTypes::FAX;
					break;
				case 'Email':
					$residentialReference->submissionType = Model_Referencing_ReferenceSubmissionTypes::LETTER;
					break;
				case 'Letter':
					$residentialReference->submissionType = Model_Referencing_ReferenceSubmissionTypes::EMAIL;
					break;
			}
			
			$residentialReference->durationAtAddress = ($referenceRow->howlongyrs * 12) + $referenceRow->howlongmths;
			$residentialReference->currentRent = new Zend_Currency(
				array(
					'value' => empty($referenceRow->currentrent) ? 0 : $referenceRow->currentrent,
					'precision' => 0
				)
			);
			
			if(empty($referenceRow->rentprompt) || $referenceRow->rentprompt == 'No') {
				
				$residentialReference->isRentPaidPromptly = false;
			}
			else {
				
				$residentialReference->isRentPaidPromptly = true;
			}
			
			if(empty($referenceRow->satisfactoryten) || $referenceRow->satisfactoryten == 'No') {
				
				$residentialReference->isSatisfactory = false;
			}
			else {
				
				$residentialReference->isSatisfactory = true;
			}
			
			if(empty($referenceRow->goodtenant) || $referenceRow->goodtenant == 'No') {
				
				$residentialReference->isGoodTenant = false;
			}
			else {
				
				$residentialReference->isGoodTenant = true;
			}

			$returnVal = $residentialReference;
		}
		
		return $returnVal;
	}
}

?>