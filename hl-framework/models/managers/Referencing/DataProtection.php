<?php

/**
 * Data protections manager class.
 */
class Manager_Referencing_DataProtection {	
	
	/**#@+
     * Internal datasource references.
     */
	protected $_dpaDatasource;
    /**#@-*/
    
    
    /**
     * Instantiates internal datasource references.
     */
    public function __construct() {
        
        $this->_dpaDatasource = new Datasource_Referencing_DataProtections();
    }
	

	/**
     * Inserts new data protection answers into the datasource.
     *
     * @param array $dpaList
     * The list of data protection identifiers and their corresponding answers given
     * against a particular reference. Each answer is encapsulated in a
     * Model_Referencing_DataProtectionItem object.
     *
     * @return void
     */
    public function createNewDataProtections(array $dpaList) {

		return $this->_dpaDatasource->createNewDataProtections($dpaList);
	}
	
	
	/**
     * Deletes all data protection answers given against a specified reference ID.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return void
     */
	public function deleteDataProtections($referenceId) {
		
		$this->_residenceDatasource->deleteDataProtections($referenceId);
	}
}

?>