<?php

/**
 * Encapsulates referencing customer map business logic.
 * 
 * All access to the customer map datasources should be through this class.
 * 
 * @todo
 * Merge this manager and the Customer manager.
 */
class Manager_Referencing_CustomerMap
{
	protected $_customerMapDatasource;
    
    protected function _loadSources()
    {
    	if(empty($this->_customerMapDatasource)) {
			$this->_customerMapDatasource = new Datasource_Referencing_CustomerMap();
		}
    }

    /**
     * Used to determine whether or not to insert a placeholder.
     *
     * A customer map MUST be inserted into the datasource before
     * save operations can be performed. Therefore, calling code should use this
     * method to determine if a placeholder needs to be created.
     *
     * @throws Zend_Exception
     * @return boolean
     * Returns true if a placeholder has already been created, false otherwise.
     */
    public function getPlaceholderExists()
    {
    	throw new Zend_Exception(__FUNCTION__ . ' : not yet implemented');
    }

    /**
     * Creates a new customer map and corresponding record in the datasource.
     *
     * @param integer $reference
     * The unique reference identifier.
     *
     * @return Model_Referencing_CustomerMap
     * Returns the newly created customer.
     */
    public function insertPlaceholder($reference)
    {
		return $this->_customerMapDatasource->upsertReferenceCustomer($reference);
	}
	
    /**
     * Save a referencing customer map.
     * 
     * @param Model_Referencing_Reference $reference
     * The reference object, which encapsulates the customer
     * map details.
     * 
     * @return void
     */
    public function save($reference)
    {
		$this->_loadSources();
    	$this->_customerMapDatasource->upsertReferenceCustomer($reference);
    }
    
    /**
     * Retrieve a referencing customer map.
     * 
     * @param integer $referenceId
     * The unique reference identifier.
     * 
     * @return Model_Referencing_CustomerMap
     * The reference customer, or null if not set.
     */
    public function retrieve($referenceId)
    {
	    $this->_loadSources();
	    return $this->_customerMapDatasource->getReferenceCustomer($referenceId);
    }
}
