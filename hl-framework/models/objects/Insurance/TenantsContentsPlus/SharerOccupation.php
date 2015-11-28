<?php

/**
 * Represents a single, sharer occupation.
 */
class Model_Insurance_TenantsContentsPlus_SharerOccupation extends Model_Abstract {

	protected $_id;
	protected $_type;
	protected $_label;
	
	
	public function __construct($id = null, $type = null, $label = null) {
	
		$this->_id = $id;
		$this->_type = $type;
		$this->_label = $label;
	}
	
	
	public function getID() {
		
		return $this->_id;
	}
	
	
	/**
	 * Returns the human readable occupation.
	 *
	 * Method which returns an occupation description in title casing,
	 * useful for form displays, drop-downs etc.
	 *
	 * @return string
	 * The occupation description in title casing.
	 */
	public function getType() {
		
		return $this->_type;
	}
	
	
	/**
	 * Returns the software readable occupation.
	 *
	 * Method which returns an occupation description all in lower casing
	 * and with spaces removed - intended for use in code so the developer
	 * does not have to worry about capitalization.
	 *
	 * @return string
	 * The occupation description in lower casing with spaces removed.
	 */
	public function getLabel() {
		
		return $this->_label;
	}
	
	
	public function setID($id) {
		
		$this->_id = $id;
	}
	
	
	public function setType($type) {
		
		$this->_type = $type;
	}
	
	
	public function setLabel($label) {
		
		$this->_label = $label;
	}
}

?>