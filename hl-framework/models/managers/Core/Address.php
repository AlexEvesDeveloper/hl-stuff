<?php

/**
 * Address manager class providing address services.
 */
class Manager_Core_Address {	
	
	/**#@+
     * Internal datasource references.
     */
	protected $_addressDatasource;
    /**#@-*/
    
    
    /**
     * Instantiates internal datasource references.
     */
    public function __construct() {

		$this->_addressDatasource = new Datasource_Core_Addresses();
    }
	
	
	/**
	 * Creates a new, empty address in the datasource and returns an object representation of this.
	 * 
	 * @return Model_Core_Address
	 * The new, empty address.
	 */
	public function createAddress() {

		return $this->_addressDatasource->createAddress();
	}
}

?>