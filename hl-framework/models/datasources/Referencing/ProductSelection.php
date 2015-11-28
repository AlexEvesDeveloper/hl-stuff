<?php

/**
* Model definition for the product_selection datasource.
*/
class Datasource_Referencing_ProductSelection extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'product_selection';
    protected $_primary = 'reference_id';
    /**#@-*/
    
    
    /**
     * Inserts a new, empty ProductSelection into the datasource and returns a corresponding object.
     *
     * @param integer $referenceId
     * Links the new ProductSelection to the Reference.
     *
	 * @return Model_Referencing_ProductSelection
	 * Encapsulates the details of the newly inserted ProductSelection.
     */
    public function createProductSelection($referenceId) {
        
        $data = array('reference_id' => $referenceId);
        
        if (!$this->insert($data)) {
            
            // Failed insertion
            Application_Core_Logger::log("Can't create record in table {$this->_name}", 'error');
            $returnVal = null;
        }
        else {
         
            $productSelection = new Model_Referencing_ProductSelection();
            $productSelection->referenceId = $referenceId;
            $returnVal = $productSelection;
        }
        
        return $returnVal;
    }
    
    
    /**
     * Updates an existing ProductSelection.
     *
     * @param Model_Referencing_ProductSelection
     * The ProductSelection object to update in the datasource.
     *
     * @return void
     */
    public function updateProductSelection($productSelection) {
        
        if(empty($productSelection)) {
            
            return;
        }
        
        
        //Obtain the product id.
        if(!empty($productSelection->product)) {
            
            $productId = $productSelection->product->key;
        }
        else {
            
            $productId = 0;
        }
        
        
        //Update.
        $data = array(
            'product_id' => $productId,
            'duration' => empty($productSelection->duration) ? 0 : $productSelection->duration
        );
        
        $where = $this->quoteInto('reference_id = ?', $productSelection->referenceId);
        $this->update($data, $where);
    }
    
    
    /**
     * Retrieves the specified ProductSelection.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return mixed
     * The ProductSelection details, encapsulated in a Model_Referencing_ProductSelection object,
     * or null if the ProductSelection cannot be found.
     */
    public function getByReferenceId($referenceId) {
        
        $select = $this->select();            
        $select->where('reference_id = ?', $referenceId);
        $row = $this->fetchRow($select);
        
        
        if(empty($row)) {
            
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ':Unable to find ProductSelection.');
            $returnVal = null;
        }
        else {

            //Populate the details into an Enquiry object.
            $productSelection = new Model_Referencing_ProductSelection();
            $productSelection->referenceId = $referenceId;
            $productSelection->duration = $row->duration;         
            $returnVal = $productSelection;
        }
        
        return $returnVal;
    }
}

?>