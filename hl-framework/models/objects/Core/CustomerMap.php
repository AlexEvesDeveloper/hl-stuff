<?php

/**
 * Represents a customer map, associating the customer in the DataStore with
 * the LegacyDataStore.
 */
class Model_Core_CustomerMap extends Model_Abstract {
    
	/**#@+
	 * Map attributes.
	 */
	protected $_id;
	protected $_identifier;
	protected $_legacyIdentifier;
	/**#@-*/
	
	
	public function __construct($id = null, $identifier = null, $legacyIdentifier = null) {
		
		$this->_id = $id;
		$this->_identifier = $identifier;
		$this->_legacyIdentifier = $legacyIdentifier;
	}
	
	
	/**
	 * Gets the map identifier.
	 *
	 * @return integer
	 * The map identifier.
	 */
	public function getId() {
		
		return $this->_id;
	}
	
	
	/**
	 * Gets the customer's identifier.
	 *
	 * @return integer
	 * The customer's identifier.
	 */
	public function getIdentifier() {
		
		return $this->_identifier;
	}
	
	
	/**
	 * Gets the customer's legacy identifier.
	 *
	 * @return string
	 * The customer's legacy identifier.
	 */
	public function getLegacyIdentifier() {
		
		return $this->_legacyIdentifier;
	}
	
	
	/**
	 * Sets the map identifier.
	 *
	 * @param $id
	 * The map identifier.
	 *
	 * @return void
	 * The map identifier.
	 */
	public function setId($id) {
		
		$this->_id = $id;
	}
	
	
	/**
	 * Sets the customer's identifier.
	 *
	 * @param integer $identifier
	 * The customer's identifier.
	 *
	 * @return void
	 */
	public function setIdentifier($identifier) {
		
		$this->_identifier = $identifier;
	}
	
	
	/**
	 * Gets the customer's occupation.
	 *
	 * @return string
	 * The customer's occupation.
	 */
	public function setLegacyIdentifier($legacyIdentifier) {
		
		$this->_legacyIdentifier = $legacyIdentifier;
	}
}

?>