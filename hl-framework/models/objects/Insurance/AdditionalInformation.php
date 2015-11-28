<?php

/**
 * Holds a additional underwriting information relating to a single quote or
 * policy.
 */
class Model_Insurance_AdditionalInformation extends Model_Abstract {

	protected $_text;
	protected $_policyNumber;
	
	
	public function __construct() {
	
		$this->_text = null;
		$this->_policyNumber;
	}
	
	public function getPolicyNumber() {
		
		return $this->_policyNumber;
	}
	
	public function getAdditionalInformation() {
	
		return $this->_text;
	}
	
	public function setAdditionalInformation($text) {
	
		if(empty($this->_text)) {
			
			$this->_text = $text;
		}
		else {
		
			$this->_text .= $text;
		}
	}
	
	public function setPolicyNumber($policyNumber) {
		
		$this->_policyNumber = $policyNumber;
	}
}

?>