<?php

/**
 * Occupational referee manager class.
 */
class Manager_Referencing_OccupationReferee {	
	
	/**#@+
     * Internal datasource references.
     */
	protected $_refereeDatasource;
    /**#@-*/
    
    
    /**
     * Instantiates internal datasource references.
     */
    public function __construct() {
        
        $this->_refereeDatasource = new Datasource_Referencing_OccupationReferees();
    }
	
	
	/**
	 * Creates a new, empty OccupationReferee in the datasource and returns an object representation of this.
	 *
	 * @param integer $occupationId
	 * The unique Occupation identifier, used this to link the objects together.
	 * 
	 * @return Model_Referencing_OccupationalReferee
	 * The newly created, empty OccupationReferee.
	 */
	public function createReferee($occupationId) {

		return $this->_refereeDatasource->createReferee($occupationId);
	}
}

?>