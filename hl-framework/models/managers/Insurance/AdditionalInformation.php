<?php

/**
 * Business rules class which provides 'additional information' services during
 * the underwriting assessment process.
 */
class Manager_Insurance_AdditionalInformation {

	protected $_additionalInformationModel;
	
	
	/**
	 * Identifies if additional information has already been stored.
	 *
	 * This method will look up additional information stored in the dbase
	 * against the $policyNumber specified. If found, it will return true,
	 * else will return false.
	 *
	 * @param string $policyNumber
	 * The additional information identifier.
	 *
	 * @return boolean
	 * True if the additional information has already been stored, false
	 * otherwise.
	 */
	public function getIsAdditionalInformationAlreadyStored($policyNumber) {
	
		$additionalInformation = $this->getAdditionalInformation($policyNumber);
		if(empty($additionalInformation)) {
			
			return false;
		}
		return true;
	}
	
	
	/**
	 * Returns additional information.
	 *
	 * This method will retrieve additional underwriting information stored
	 * against the $policyNumber in the database, and return this encapsulated
	 * in a Model_Insurance_AdditionalInformation object.
	 *
	 * @param string $policyNumber
     * Will search for records matching this quote / policy number.
	 *
	 * @return Model_Insurance_AdditionalInformation
	 * Returns this object populated with relevant information, or null if no
	 * relevant information has been stored.
	 */
	public function getAdditionalInformation($policyNumber) {
	
		if(empty($this->_additionalInformationModel)) {
			
			$this->_additionalInformationModel = new Datasource_Insurance_AdditionalInformation();
		}
		
		return $this->_additionalInformationModel->getAdditionalInformation($policyNumber);
	}
	
	
	/**
	 * Inserts new additional underwriting information into the data storage.
	 *
	 * This method is responsible for storing additional information against a quote or policy,
	 * to supplement the underwriting answers given by the customer.
	 *
	 * @param Model_Insurance_AdditionalInformation $additionalInformation
	 * An object containing all the additional information provided by the user when applying
	 * for a policy / renewal etc.
	 *
	 * @return void
	 */
	public function insertAdditionalInformation($additionalInformation) {
	
		if(empty($this->_additionalInformationModel)) {
			
			$this->_additionalInformationModel = new Datasource_Insurance_AdditionalInformation();
		}
		
		$this->_additionalInformationModel->insertAdditionalInformation($additionalInformation);
	}
	
	
	/**
	 * Removes additional underwriting information from the data storage.
	 *
	 * Method responsible for removing additional information stored against the
	 * $policyNumber passed in.
	 *
	 * @param string $policyNumber
	 * Identifier for the additional underwriting information.
	 *
	 * @return void
	 */
	public function removeAdditionalInformation($policyNumber) {
		
		if(empty($this->_additionalInformationModel)) {
			
			$this->_additionalInformationModel = new Datasource_Insurance_AdditionalInformation();
		}
		
		$this->_additionalInformationModel->removeAdditionalInformation($policyNumber);
	}
}

?>