<?php

/**
 * Business rules class which provides 'Minimum Security Protection' flag 
 * 
 */
class Manager_Insurance_MinimumSecurityProtection {

	public $_minimumSecurityProtectionModel;

	/**
	 * Returns MinimumSecurityProtection object.
	 *
	 * This method will retrieve and initialise MSP object stored
	 * for the $postcode in the database, 	 
	 *
	 */
	public function isHighRiskPostcode($postcode) {
							
		$this->_minimumSecurityProtectionModel = new Datasource_Insurance_MinimumSecurityProtection();
		$msp = $this->_minimumSecurityProtectionModel->getMinimumSecurityProtection($postcode);
		
		return $msp;
	}	
}

?>
