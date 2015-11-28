<?php

/**
* Model definition for the residential references datasource.
*/
class Datasource_Referencing_ResidenceReferences extends Zend_Db_Table_Multidb {
	
    protected $_multidb = 'db_referencing';
    protected $_name = 'residence_reference';
    protected $_primary = 'residence_id';
    
    public function upsertReference($reference) {
    	
    	if(empty($reference)) {
    		
    		return;
    	}
    	
        //Delete existing reference, as its easier than checking for an
        //existing one then deciding whether to update or insert.
        $where = $this->quoteInto('residence_id = ? ', $reference->residenceId);
        $this->delete($where);
        
    	//Insert the new.
		$data = array(
			'residence_id' => $reference->residenceId,
			'submission_type_id' => $reference->submissionType,
			'duration_at_address' => $reference->durationAtAddress,
			'current_rent' => $reference->currentRent->getValue(),
			'is_rent_paid_promptly' => ($reference->isRentPaidPromptly) ? 1 : 0,
			'is_satisfactory' => ($reference->isSatisfactory) ? 1 : 0,
			'is_good_tenant' => ($reference->isGoodTenant) ? 1 : 0
        );
        
        $this->insert($data);
    }
    
	/**
     * Retrieves the residential reference for a specific Residence.
     *
     * @param integer $residenceId
     * The unique Residence identifier.
     *
     * @return mixed
     * Returns a Model_Referencing_ResidentialReference object, or null if no
     * reference is found.
     */
    public function getByResidenceId($residenceId) {
		
		$select = $this->select();
		$select->where('residence_id = ?', $residenceId);
		$referenceRow = $this->fetchRow($select);
		
		if(empty($referenceRow)) {
			
			$returnVal = null;
		}
		else {
			
			$residentialReference = new Model_Referencing_ResidenceReference();
			$residentialReference->residenceId = $referenceRow->residence_id;
			$residentialReference->submissionType = $referenceRow->submission_type_id;
            $residentialReference->durationAtAddress = $referenceRow->duration_at_address;
            $residentialReference->currentRent = new Zend_Currency(
            	array('precision' => 0, 'value' => $referenceRow->current_rent)
            );
			
            $residentialReference->isRentPaidPromptly = ($referenceRow->is_rent_paid_promptly) ? true : false;
            $residentialReference->isSatisfactory = ($referenceRow->is_satisfactory) ? true : false;
            $residentialReference->isGoodTenant = ($referenceRow->is_good_tenant) ? true : false;
            
			$returnVal = $residentialReference;
		}
		
		return $returnVal;
    }
}

?>