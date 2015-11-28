<?php

/**
 * ResidenceReferee manager class.
 */
class Manager_Referencing_ResidenceReferee {	
	
	/**#@+
     * Internal datasource references.
     */
	protected $_refereeDatasource;
    /**#@-*/
    
    
    /**
     * Instantiates internal datasource references.
     */
    public function __construct() {
        
        $this->_refereeDatasource = new Datasource_Referencing_ResidenceReferees();
    }
	
	
	/**
	 * Creates a new, empty ResidenceReferee in the datasource and returns an object representation of this.
	 *
	 * @param integer $residenceId
	 * The unique Residence identifier, used this to link the objects together.
	 * 
	 * @return Model_Referencing_ResidentialReferee
	 * The newly created, empty ResidenceReferee.
	 */
	public function createReferee($residenceId) {

		return $this->_refereeDatasource->createReferee($residenceId);
	}
}

?>