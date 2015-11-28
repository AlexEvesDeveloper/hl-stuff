<?php

/**
* Model definition for the ProductDurationMap datasource.
*/
class Datasource_Referencing_ProductDurationMap extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'product_duration_map';
    protected $_primary = array('product_id', 'product_duration_id');
    /**#@-*/    
    
    
    /**
     * Returns an array of durations permitted for the product.
     *
     * @param integer $productId
     * Identifies the product.
     *
     * @return mixed
     * Returns an array of permitted durations, or null if the durations
     * cannot be found.
     */
    public function getPermittedDurations($productId) {
            
        $select = $this->select();
        $select->where('product_id = ?', $productId);
        $products = $this->fetchAll($select);
        
        $durations = array();
        if(count($products) > 0) {

            $productDurations = new Datasource_Referencing_ProductDurations();            
            foreach($products as $product) {
                
                $durations[] = $productDurations->getById($product->product_duration_id);
            }
        }
        
        
        //Finalise the outputs.
        if(empty($durations)) {
            
            $returnVal = null;
        }
        else {
            
            $returnVal = $durations;
        }
        return $returnVal;
    }
}

?>