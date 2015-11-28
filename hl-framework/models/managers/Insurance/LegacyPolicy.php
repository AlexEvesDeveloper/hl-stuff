<?php

/**
 * Manager for legacy policies
 */
class Manager_Insurance_LegacyPolicy {
	protected $_dataSource;
	
	public function __construct() {
		$this->_dataSource = new Datasource_Insurance_LegacyPolicies();
	}
	
	/**
	 * Get a specific policy
	 *
	 * @param string policyNumber
	 */
	public function getByPolicyNumber($policyNumber) {
		return $this->_dataSource->getByPolicyNumber($policyNumber);
		
	}
}

?>