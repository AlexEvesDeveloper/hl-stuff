<?php

/**
 * Business rules class to provide a sharers service for TCI+ policies.
 */
class Manager_Insurance_TenantsContentsPlus_Sharers {

	protected $_sharersModel;
    
	
	/**
	 * Gets the number of sharers allowed.
	 *
	 * Method which returns the number of sharers that are permitted
	 * given a specified cover amount.
	 *
	 * @param Zend_Currency $coverAmount
	 * The main cover amount on the TCI+ policy.
	 *
	 * @return integer
	 * Returns the number of sharers allowed on the $coverAmount given.
	 */
	public function getNoOfSharersAllowed($coverAmount) {
        
        $params = Zend_Registry::get('params');
		
		
		//Read in the lower contents bands.
		$bandLower = array();
		$bandLower[] = new Zend_Currency(
			array(
				'value' => $params->sharers->band0->lower,
				'precision' => 0
			));
		
		$bandLower[] = new Zend_Currency(
			array(
				'value' => $params->sharers->band1->lower,
				'precision' => 0
			));
		
		$bandLower[] = new Zend_Currency(
			array(
				'value' => $params->sharers->band2->lower,
				'precision' => 0
			));
		
		$bandLower[] = new Zend_Currency(
			array(
				'value' => $params->sharers->band3->lower,
				'precision' => 0
			));
		
		
		//Read in the upper contents bands.		
		$bandUpper = array();
		$bandUpper[] = new Zend_Currency(
			array(
				'value' => $params->sharers->band0->upper,
				'precision' => 0
			));
		
		$bandUpper[] = new Zend_Currency(
			array(
				'value' => $params->sharers->band1->upper,
				'precision' => 0
			));
		
		$bandUpper[] = new Zend_Currency(
			array(
				'value' => $params->sharers->band2->upper,
				'precision' => 0
			));
		
		$bandUpper[] = new Zend_Currency(
			array(
				'value' => $params->sharers->band3->upper,
				'precision' => 0
			));
		
		$numberPermitted = array();
		$numberPermitted[] = $params->sharers->numberPermitted->band0;
		$numberPermitted[] = $params->sharers->numberPermitted->band1;
		$numberPermitted[] = $params->sharers->numberPermitted->band2;
		$numberPermitted[] = $params->sharers->numberPermitted->band3;		
		
		
		//Zero sharers by default until the cover amount is understood.
		$returnVal = 0;
		for($i = 0; $i < count($bandLower); $i++) {

			$bandFound = false;
			
			if($coverAmount->isMore($bandLower[$i]) && $coverAmount->isLess($bandUpper[$i])) {
				
				$bandFound = true;
			}
			else if($coverAmount->equals($bandLower[$i]) || $coverAmount->equals($bandUpper[$i])) {
				
				$bandFound = true;
			}
			
			if($bandFound) {

				$returnVal = $numberPermitted[$i];
				break;
			}
		}
		
		return $returnVal;
    }
    

	/**
	 * Gets all the sharer occupations.
	 *
	 * Retrieves all the sharer occupations, encapsulates them in individual
	 * Model_Insurance_TenantsContentsPlus_SharerOccupation objects, and returns them
	 * in an array. The objects can be used to populate forms and drop-downs
	 * text with sharer occupation types.
	 *
	 * @return array
	 * Returns an array of Model_Insurance_TenantsContentsPlus_SharerOccupation objects.
	 */
    public function getOccupations() {
        
		$sharerOccupations = new Datasource_Insurance_TenantsContentsPlus_SharerOccupations();
		return $sharerOccupations->getOccupations();
    }
	
	
	/**
     * Inserts sharers into the database.
     *
     * This method provides a convenient way of inserting sharers into the database.
     * Due to the existing data source being poorly designed, this method will
     * first delete the existing sharers data for the policy defined by
     * $sharers->getPolicyNumber(), prior to writing the new sharers data.
     *
     * @param Model_Insurance_TenantsContentsPlus_Sharers $sharers
     * The Model_Insurance_TenantsContentsPlus_Sharers object containing all the sharers
     * information to be inserted.
     *
     * @return void
     */
    public function insertSharers($sharers) {
        
        if(empty($this->_sharersModel)) {
            
            $this->_sharersModel = new Datasource_Insurance_TenantsContentsPlus_Sharers();
        }
        
		$this->removeAllSharers($sharers->getPolicyNumber());
        $this->_sharersModel->insertSharers($sharers);
    }
	
	
	/**
	 * Removes sharers data.
	 *
	 * This method removes all sharers data corresponding to the $policyNumber
	 * passed in.
	 *
	 * @param string $policyNumber
	 * The identifier for the sharers data to be deleted.
	 */
	public function removeAllSharers($policyNumber) {
        
        if(empty($this->_sharersModel)) {
            
            $this->_sharersModel = new Datasource_Insurance_TenantsContentsPlus_Sharers();
        }
        
        return $this->_sharersModel->removeAllSharers($policyNumber);
	}
	

    /**
     * Convenience method, determines if a sharers are already stored.
     *
     * Provides a convenient way to determine if sharers data has already been
     * stored, and returns true or false to indicate this.
	 *
     * @param Model_Insurance_TenantsContentsPlus_Sharers $sharers
     * A Model_Insurance_TenantsContentsPlus_Sharers object containing all the sharers
     * information to compare against the data store.
	 *
	 * @return boolean
	 * Returns true if a identical sharers data has already been stored,
	 * false otherwise.
	 */
    public function getIsSharersAlreadyStored($sharers) {
		
		$sharersStored = $this->getSharers($sharers->getPolicyNumber());
		
		$isSharersAlreadyStored = false;
		
		if(!empty($sharersStored)) {
			
			if($sharersStored->getPolicyNumber() == $sharers->getPolicyNumber()) {
				
				if($sharersStored->getSharerOccupation(Model_Insurance_TenantsContentsPlus_Sharers::SHARER_01)
					== $sharers->getSharerOccupation(Model_Insurance_TenantsContentsPlus_Sharers::SHARER_01)) {
					
					if($sharersStored->getSharerOccupation(Model_Insurance_TenantsContentsPlus_Sharers::SHARER_02)
						== $sharers->getSharerOccupation(Model_Insurance_TenantsContentsPlus_Sharers::SHARER_02)) {
						
						$isSharersAlreadyStored = true;
					}
				}
			}
		}
		
		return $isSharersAlreadyStored;
	}
	
    
    /**
	 * Returns sharers data.
	 *
	 * Method which retrieves sharers data for a quote / policy, if any exists,
	 * encapsulated in a Model_Insurance_TenantsContentsPlus_Sharers object.
	 *
	 * @param string $policyNumber
	 * The full identifier for the sharers data to be retrieved
	 *
	 * @return Model_Insurance_TenantsContentsPlus_Sharer_DomainObjects_Sharers
	 * Returns a Model_Insurance_TenantsContentsPlus_Sharers object encapsulating
	 * the sharers data for the $policyNumber, or null if none found.
	 */
	public function getSharers($policyNumber) {
		
        if(empty($this->_sharersModel)) {
            
            $this->_sharersModel = new Datasource_Insurance_TenantsContentsPlus_Sharers();
        }

        return $this->_sharersModel->getSharers($policyNumber);
	}
}

?>