<?php

/**
 * Encapsulates referencing product selection business logic.
 * 
 * All access to the product selection datasource should be through this class.
 */
class Manager_Referencing_ProductSelection {	

	protected $_productSelectionDatasource;
	protected $_productManager;
    
    protected function _loadSources() {
    	
    	if(empty($this->_productSelectionDatasource)) {
    		
			$this->_productSelectionDatasource = new Datasource_Referencing_ProductSelection();
    	}
    	
        if(empty($this->_productManager)) {
    		
			$this->_productManager = new Manager_Referencing_Product();
    	}
    }

    /**
     * Inserts a new, empty ProductSelection into the datasource and returns a corresponding object.
     *
     * @param integer $referenceId
     * The unqiue reference identifier.
     *
	 * @return Model_Referencing_ProductSelection
	 * Encapsulates the details of the newly inserted ProductSelection.
     */
    public function insertPlaceholder($referenceId) {

		$this->_loadSources();
    	return $this->_productSelectionDatasource->createProductSelection($referenceId);
	}
	
	/**
     * Save a referencing product selection.
     * 
     * @param Model_Referencing_ProductSelection $productSelection
     * The referencing product selection to save.
     * 
     * @return void
     */
	public function save($productSelection) {
		
		$this->_loadSources();
		$this->_productSelectionDatasource->updateProductSelection($productSelection);
	}
	
	/**
     * Retrieve a referencing product selection.
     * 
     * @param integer $referenceId
     * The unique reference identifier.
     * 
     * @return Model_Referencing_ProductSelection
     * The reference product selection, or null if this has not yet been set.
     */
	public function retrieve($referenceId) {
		
		$this->_loadSources();
		
		$productSelection = $this->_productSelectionDatasource->getByReferenceId($referenceId);
		if(!empty($productSelection)) {
			
			//Get the objects linked to product selection.
			$productSelection->product = $this->_productManager->getByReferenceId($referenceId);
		}
		return $productSelection;
	}
	
	/**
 
42 	 * Retrieves the PLL price for a referencing product.
 
43 	 *
 
44 	 * @param Model_Referencing_ProductSelection
 
45 	 * Holds the product id and duration, both of which are needed to retrieve
 
46 	 * the price.
 
47 	 *
 
48 	 * @return mixed
 
49 	 * Returns a Zend_Currency price if the price can be found, else returns null.
 
50 	 */
 
	 	public function getPrice($productSelection) {
 
		
 			$this->_priceDatasource = new Datasource_Referencing_Price();
	 		
		//Use the product id and duration to determine the product price.
 
			return $this->_priceDatasource->getPrice($productSelection->product->key, $productSelection->duration);
 	 	}
 
	
}

?>