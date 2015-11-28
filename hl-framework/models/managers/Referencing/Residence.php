<?php

/**
 * Encapsulates referencing residence business logic.
 * 
 * All access to the residence datasources should be through this class.
 */
class Manager_Referencing_Residence {	

	protected $_residenceDatasource;
    
    protected function _loadSources() {
    	
    	if(empty($this->_residenceDatasource)) {
    		
			$this->_residenceDatasource = new Datasource_Referencing_Residences();
		}
    }

	/**
     * Creates a new, empty Residence and corresponding record in the datasource.
     *
     * @param integer $referenceId
     * The unique Reference identifier. This is used to link the new Residence
     * record to the Reference record.
     *
     * @param integer $chronology
     * Indicates the residential chronology. Must corresond to one of the consts
     * exposed by the Model_Referencing_ResidentialChronology class.
     *
     * @return Model_Referencing_Residence
     * Returns the newly created, empty Residence.
     */
    public function insertPlaceholder($referenceId, $chronology) {

    	$this->_loadSources();
		return $this->_residenceDatasource->insertPlaceholder($referenceId, $chronology);
	}
	
	/**
     * Save an existing residence.
     * 
     * @param Model_Referencing_Residence $residence
     * The residence to save.
     * 
     * @return void
     */
    public function save($residence) {
		
		$this->_loadSources();
    	$this->_residenceDatasource->updateResidence($residence);
    }
    
    /**
     * Retrieve the residences applicable to a reference.
     * 
     * @param integer $referenceId
     * The unique reference identifier.
     * 
     * @return mixed
     * An array of Model_Referencing_Residence objects, or null if no
     * residences found.
     */
    public function retrieve($referenceId) {
		
	    $this->_loadSources();
    	return $this->_residenceDatasource->getByReferenceId($referenceId);
    }
	
	/**
	 * Determines if a residence is allowed.
	 *
	 * Business rules specify that an applicants first previous residence cannot match
	 * their current residence, and that their second previous residence cannot match their
	 * first previous. This is implemented in this method.
	 *
	 * @param integer $referenceId
	 * The unique reference identifier.
	 *
	 * @param integer $proposedChronology
	 * The proposed chronology of the proposed residence. Must correspond to one of the consts
	 * exposed by the Model_Referencing_ResidentialChronology class.
	 *
	 * @param Model_Core_Address $proposedAddress
	 * Encapsulates the details of the proposed residence.
	 *
	 * @return boolean
	 * True if the residence is allowed, false otherwise.
	 */
	public function isResidenceAllowed($referenceId, $proposedChronology, $proposedAddress) {
		$this->_loadSources();
		if($proposedChronology == Model_Referencing_ResidenceChronology::CURRENT) {
			
			//The proposed address is the current address, so no comparisons with other addresses
			//needs to be made.
			return true;
		}
		
		
		//Retrieve all residences against the referenceId.
		$residences = $this->_residenceDatasource->getByReferenceId($referenceId);
		
		if($proposedChronology == Model_Referencing_ResidenceChronology::FIRST_PREVIOUS) {
			
			//Ensure the proposed address is not the same as the reference subject's current residence.
			$currentResidence = $this->findSpecificResidence($residences, Model_Referencing_ResidenceChronology::CURRENT);
			if($proposedAddress->equals($currentResidence->address)) {
				
				//The addresses are the same, so reject the proposed address.
				return false;
			}
		}
		
		if($proposedChronology == Model_Referencing_ResidenceChronology::SECOND_PREVIOUS) {
			
			//Ensure the proposed address is not the same as the reference subject's first previous residence.
			$firstPreviousResidence = $this->findSpecificResidence($residences, Model_Referencing_ResidenceChronology::FIRST_PREVIOUS);
			if($proposedAddress->equals($firstPreviousResidence->address)) {
	
				//The addresses are the same, so reject the proposed address.
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Utility method that allows calling code to retrieve a particular residence from a set of residences stored against a reference subject.
	 *
	 * @param array $residenceArray
	 * The array of residences.
	 *
	 * @param integer $chronology
	 * The residential chronology. Must correspond to one of the consts exposed by the
	 * Model_Referencing_ResidentialChronology class.
	 *
	 * @return mixed
	 * Returns null if the residence cannot be found. Else returns a Model_Referencing_Residence
	 * from the $residenceArray matching the $chronology specified in the arguments.
	 */
	public function findSpecificResidence($residenceArray, $chronology) {
		
		if(empty($residenceArray)) {
			
			return null;
		}
		
		
		$returnVal = null;
		foreach($residenceArray as $residence) {
			
			if($residence->chronology == $chronology) {
				
				$returnVal = $residence;
				break;
			}
		}
		
		return $returnVal;
	}
}

?>