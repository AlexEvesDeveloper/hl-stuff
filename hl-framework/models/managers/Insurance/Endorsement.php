<?php

/**
 * Business rules class which provides generic underwriting endorsement services.
 */
abstract class Manager_Insurance_Endorsement {

    protected $_endorsementsModel;
	
	
	/**
     * Returns the endorsements required by a quote or policy.
     *
     * This method will identify the endorsements that should be applied to
     * the quote or policy identified by $policyNumber. If any endorsements are
     * identified, they will be detailed in one or more Model_Insurance_Endorsement 
     * objects, which will then be returned in an array. If the quote / policy does
     * not merit any endorsements, then null will be returned.
     *
     * @param string $policyNumber
     * The quote or policy number.
     *
     * @return mixed
     * Returns an array of Model_Insurance_Endorsement objects,
     * or null if no endorsements are applicable.
     */
    public abstract function getEndorsementsRequired($policyNumber);
        
		
	/**
     * Convenience method, determines if an endorsement is already applied.
     *
     * Provides a convenient way to determine if a specific endorsement has already
     * been applied to a quote/policy, and returns true or false to indicate this.
     * Useful to prevent duplicate insertions.
	 *
     * @param Model_Insurance_Endorsement $endorsement
     * An Model_Insurance_Endorsement object containing all the
     * endorsement information.
	 *
	 * @return boolean
	 * Returns true if an endorsement has already been applied, false otherwise.
	 */
    public function getIsEndorsementAlreadyApplied($endorsement) {
		
        if(empty($this->_endorsementsModel)) {
            
            $this->_endorsementsModel = new Datasource_Insurance_Endorsements();
        }

        return $this->_endorsementsModel->getEndorsement($endorsement);
	}
	
	
	/**
     * Inserts multiple policy endorsements.
     *
     * This method provides a convenient way of inserting multiple policy endorsements
     * into the data storage.
     *
     * @param array $endorsements
     * An array of Model_Insurance_Endorsement objects containing
     * all the endorsement information.
     */
    public function insertEndorsements($endorsements) {
        
        foreach($endorsements as $currentEndorsement) {
            
            $this->insertEndorsement($currentEndorsement);
        }
    }
	
	
	/**
     * Inserts an endorsement.
     *
     * This method provides a convenient way of inserting a policy endorsement on
     * a quote or policy.
     *
     * @param Model_Insurance_Endorsement $endorsement
     * An Model_Insurance_Endorsement object containing all the
     * endorsement information.
     *
     * @return boolean
     * True if the endorsement was successfully inserted, false otherwise.
     */
    public function insertEndorsement($endorsement) {
        
        if(empty($this->_endorsementsModel)) {
            
            $this->_endorsementsModel = new Datasource_Insurance_Endorsements();
        }
        
        return $this->_endorsementsModel->insertEndorsement($endorsement);
    }
	
	
	/**
	 * Removes endorsements.
	 *
	 * This method removes all endorsements associated with the $policyNumber
	 * passed in.
	 *
	 * @param string $policyNumber
	 * The quote / policy number used to identify the endorsments to delete,
	 * if any.
	 *
	 * @return void
	 */
	public function removeAllEndorsements($policyNumber) {
		
        if(empty($this->_endorsementsModel)) {
            
            $this->_endorsementsModel = new Datasource_Insurance_Endorsements();
        }
        
        $this->_endorsementsModel->removeAllEndorsements($policyNumber);
	}
}

?>