<?php

/**
 * Represents a sharers policy addition.
 */
class Model_Insurance_TenantsContentsPlus_Sharers extends Model_Abstract {

	const SHARER_01 = 1;
	const SHARER_02 = 2;
	
	
	protected $_policyNumber;
	protected $_sharer1;
	protected $_sharer2;
	
	
	public function __construct($policyNumber = null, $sharerOccupation1 = '', $sharerOccupation2 = '') {
		
		$this->_policyNumber = $policyNumber;
		$this->_sharer1 = $sharerOccupation1;
		$this->_sharer2 = $sharerOccupation2;
	}
	
	
	public function getPolicyNumber() {
		
		return $this->_policyNumber;
	}
	
	
	public function getSharerOccupation($sharerNumber) {
		
		switch($sharerNumber){
			
			case self::SHARER_01:
				$returnVal = $this->_sharer1;
				break;
			
			case self::SHARER_02:
				$returnVal = $this->_sharer2;
				break;
			
			default:
				throw new Exception(get_class() . __FUNCTION__ . ": invalid argument.");
		}
		return $returnVal;
	}
	
	
	public function setPolicyNumber($policyNumber) {
		
		$this->_policyNumber = $policyNumber;
	}
	
	
	public function setSharerOccupation($sharerNumber, $occupation) {
		
		switch($sharerNumber){
			
			case self::SHARER_01:
				$this->_sharer1 = $occupation;
				break;
			
			case self::SHARER_02:
				$this->_sharer2 = $occupation;
				break;
			
			default:
				throw new Exception(get_class() . __FUNCTION__ . ": invalid argument.");
		}
	}
}

?>