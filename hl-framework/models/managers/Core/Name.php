<?php

/**
 * Name manager class providing personal name services.
 */
class Manager_Core_Name {	
	
	/**#@+
     * Internal datasource references.
     */
	protected $_nameDatasource;
    /**#@-*/
    
    
    /**
     * Instantiates internal datasource references.
     */
    public function __construct() {

		$this->_nameDatasource = new Datasource_Core_Names();
    }
	
	
	/**
	 * Creates a new, empty Name in the datasource and returns an object representation of this.
	 * 
	 * @return Model_Core_Name
	 * The new, empty Name.
	 */
	public function createName() {

		return $this->_nameDatasource->createName();
	}
}

?>