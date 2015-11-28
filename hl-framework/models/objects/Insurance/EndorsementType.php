<?php

/**
 * Holds a single underwriting endorsement type. 
 * 
 * An endorsement type is comprised of and ID, name and description.
 */
class Model_Insurance_EndorsementType extends Model_Abstract {

	protected $_id;
	protected $_name;
	protected $_description;
	
	
	public function getID() {
		
		return $this->_id;
	}
	
	public function getName() {
		
		return $this->_name;
	}
	
	public function getDescription() {
		
		return $this->_description;
	}
	
	public function setID($id) {
		
		$this->_id = $id;
	}
	
	public function setName($name) {
		
		$this->_name = $name;
	}
	
	public function setDescription($description) {
		
		$this->_description = $description;
	}
}

?>