<?php

/**
 * Encapsulates referencing property aspect business logic.
 * 
 * All access to the property aspect datasources should be through this class.
 */
class Manager_Referencing_PropertyAspect {

	protected $_propertyAspectDatasource;    

	protected function _loadSources() {
        
        if(empty($this->_propertyAspectDatasource)) {
		
        	$this->_propertyAspectDatasource = new Datasource_Referencing_PropertyAspects();
        }
    }

    /**
     * Save reference property aspects.
     *
     * @param array
     * An array of Model_Referencing_PropertyAspects_PropertyAspectItem
     * objects.
     *
     * @return void
     */
    public function saveCollective($propertyAspectItems) {

		$this->_loadSources();
    	$this->_propertyAspectDatasource->upsertAspects($propertyAspectItems);
	}
	
/**
     * Save reference property aspects.
     *
     * @param array
     * An object of Model_Referencing_PropertyAspects_PropertyAspectItem
     * objects.
     *
     * @return void
     */
    public function save($propertyAspectItem) {

		$this->_loadSources();
    	$this->_propertyAspectDatasource->upsertAspect($propertyAspectItem);
	}
    /**
     * Retrieve reference property aspects.
     * 
     * @param integer $referenceId
     * The unique reference identifier.
     * 
	 * @return mixed
	 * An array of Model_Referencing_PropertyAspects_PropertyAspectItem objects,
	 * or null if none found.
     */
    public function retrieve($referenceId) {
		
	    $this->_loadSources();
	    return $this->_propertyAspectDatasource->getAspects($referenceId);
    }
}

?>