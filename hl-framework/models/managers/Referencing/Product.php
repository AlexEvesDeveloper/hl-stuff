<?php

class Manager_Referencing_Product {

	protected $_productDatasource;
	protected $_productVariablesMap;
	protected $_priceDatasource;
    
	/**
	 * Implements lazy loading of datasources.
	 */
    protected function _loadSources() {
    	
    	if(empty($this->_productDatasource)) {
    		
			$this->_productDatasource = new Datasource_Referencing_Product();
    	}
    	
        if(empty($this->_productVariablesMap)) {
    		
			$this->_productVariablesMap = new Datasource_Referencing_ProductVariablesMap();
    	}
    	
    	if(empty($this->_priceDatasource)) {
    		
			$this->_priceDatasource = new Datasource_Referencing_Price();
    	}
    }
    
	/**
	 * Retrieves the PLL price for a referencing product.
	 *
	 * @param Model_Referencing_ProductSelection
	 * Holds the product id and duration, both of which are needed to retrieve
	 * the price.
	 *
	 * @return mixed
	 * Returns a Zend_Currency price if the price can be found, else returns null.
	 */
	public function getPrice($productSelection) {
		
		//Use the product id and duration to determine the product price.
		$this->_loadSources();
		return $this->_priceDatasource->getPrice($productSelection->product->key, $productSelection->duration);
	}

    /**
     * Retrieves the product recorded against the Reference identifier.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return mixed
     * The Product details, encapsulated in a Model_Referencing_Product object,
     * or null if the Product cannot be found.
     */
    public function getByReferenceId($referenceId) {

		$this->_loadSources();
    	$product = $this->_productDatasource->getByReferenceId($referenceId);
		if(!empty($product)) {
		
			$product->variables = $this->_productVariablesMap->getByProductId($product->key);
		}
		return $product;
	}

    /**
    * Returns a specific product located by its identifier.
    *
    * @param integer $productId
    * The product identifier
    *
    * @return mixed
    * Returns a Model_Referencing_Product encapsulating the product details,
    * or null if no matching product found.
    */
    public function getById($productId) {
		
		$this->_loadSources();
    	$product = $this->_productDatasource->getById($productId);
		if(!empty($product)) {
		
			$product->variables = $this->_productVariablesMap->getByProductId($product->key);
		}
		return $product;
	}

	/**
    * Returns a specific product located by its name.
    *
    * @param string $productName
    * The product's name. Case insensitive.
    *
    * @return mixed
    * Returns a Model_Referencing_Product encapsulating the product details,
    * or null if no matching product found.
    */
    public function getByName($productName) {

        $this->_loadSources();
    	$product = $this->_productDatasource->getByName($productName);
        if(!empty($product)) {
		
			$product->variables = $this->_productVariablesMap->getByProductId($product->key);
		}
		return $product;
    }

   /**
    * Returns all products, with a choice of sorting by their display order
    * value.
    *
    * @param bool $orderProducts Optional flag to sort results by display order
    * value.
    *
    * @return mixed An array of Model_Referencing_Product encapsulating all the
    * product details, or null if no products found.
    */
    public function getAll($orderProducts = false) {

        $this->_loadSources();
    	$productArray = $this->_productDatasource->getAll($orderProducts);
        
        //Attach the product variables. Reference the elements so that the original
        //array is modified.
        foreach($productArray as &$currentProduct) {
        	
        	$currentProduct->variables = $this->_productVariablesMap->getByProductId($currentProduct->key);
        }
        //Destroy the reference of $currentProduct.
        unset($currentProduct);
        
        return $productArray;
    }
	
    /**
    * Returns a specific product located by its name.
    *
    * @param string $productvariable
    * The product's name. Case insensitive.
    *
    * @return mixed
    * Returns an array of Model_Referencing_Product encapsulating the product details,
    * or null if no matching product found.
    */
 	public function getByVariable($variable) {

        $this->_loadSources();
    	$productArray = $this->_productDatasource->getByVariable($variable);
        
		//Attach the product variables. Reference the elements so that the original
        //array is modified.
        foreach($productArray as &$currentProduct) {
        	
        	$currentProduct->variables = $this->_productVariablesMap->getByProductId($currentProduct->key);
        }
		
        //Destroy the reference of $currentProduct.
        unset($currentProduct);
        return $productArray;
    }
}

?>