<?php

/**
 * ContactDetails manager class providing contact details services.
 */
class Manager_Core_ContactDetails {	
	
	/**#@+
     * Internal datasource references.
     */
	protected $_contactDatasource;
    /**#@-*/
    
    
    /**
     * Instantiates internal datasource references.
     */
    public function __construct() {

		$this->_contactDatasource = new Datasource_Core_ContactDetails();
    }
	
	
	/**
	 * Creates a new, empty ContactDetails in the datasource and returns an object representation of this.
	 * 
	 * @return Model_Core_ContactDetails
	 * The new, empty ContactDetails.
	 */
	public function createContactDetails() {

		return $this->_contactDatasource->createContactDetails();
	}
}

?>