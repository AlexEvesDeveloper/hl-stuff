<?php

/**
 * Factory for generating Manager_Referencing_DataEntry_Flow_AbstractFlow objects.
 *
 * The AbstractFlow objects expose methods which can be used to control and navigate
 * through the data entry process.
 */
class Manager_Referencing_DataEntry_Flow_FlowFactory {

    /**
     * Returns a Manager_Referencing_DataEntry_Flow_FlowManager object.
     *
     * The object returned will correspond to the $productName passed in, and
     * can be used to control and navigate through the data entry process for
     * that product.
     *
     * @param string $productName
     * Must correspond to one of the conts exposed by the Model_Referencing_ProductNames
     * class.
     *
     * @return Manager_Referencing_DataEntry_Flow_FlowManager
     *
     * @deprecated
     * Use createFlowManagerByProduct() instead, which provides a way of identifying
     * FlowManagers without having to change when new products are added.
     */
    public static function createFlowManager($productName) {
        
        $flowManager = null;
        
        switch($productName) {
            
            case Model_Referencing_ProductKeys::INSIGHT:
                $flowManager = new Manager_Referencing_DataEntry_Flow_CreditFlow();
                break;
            
            case Model_Referencing_ProductKeys::ENHANCE:
            case Model_Referencing_ProductKeys::OPTIMUM:
                $flowManager = new Manager_Referencing_DataEntry_Flow_FullFlow();
                break;
            
            case Model_Referencing_ProductKeys::XPRESS:
                $flowManager = new Manager_Referencing_DataEntry_Flow_CreditRentGuaranteeFlow();
                break;
            
            case Model_Referencing_ProductKeys::EXTRA:
            case Model_Referencing_ProductKeys::ADVANTAGE:
            case Model_Referencing_ProductKeys::INTERNATIONAL_EXTRA:
            case Model_Referencing_ProductKeys::INTERNATIONAL_ADVANTAGE:
                $flowManager = new Manager_Referencing_DataEntry_Flow_FullRentGuaranteeFlow();
                break;
        }
        
        return $flowManager;
    }
    
    
    /**
     * Returns a Manager_Referencing_DataEntry_Flow_FlowManager object.
     *
     * The object returned will correspond to the $product passed in, and can be
     * used to control and navigate through the data entry process for that product.
     *
     * This method provides a way of identifying FlowManagers without having to change when new
     * products are added.
     *
     * @param Model_Referencing_Product $product
     * Encapsulates the product details.
     *
     * @return Manager_Referencing_DataEntry_Flow_FlowManager
     */
    public static function createFlowManagerByProduct($product) {

        if($product->referencingType == Model_Referencing_ProductReferencingTypes::CREDIT_REFERENCE) {
            
            $isCreditReference = true;
        }
        else {
            
            $isFullReference = true;
        }
        
        
        //Instantiate and return the appropriate FlowManager.
        if($isCreditReference && (!$product->isRentGuarantee)) {
            
            $returnVal = new Manager_Referencing_DataEntry_Flow_CreditFlow();
        }
        else if($isCreditReference && $product->isRentGuarantee) {
            
            $returnVal = new Manager_Referencing_DataEntry_Flow_CreditRentGuaranteeFlow();
        }
        else if($isFullReference && (!$product->isRentGuarantee)) {
            
            $returnVal = new Manager_Referencing_DataEntry_Flow_FullFlow();
        }
        else if($isFullReference && $product->isRentGuarantee) {
            
            $returnVal = new Manager_Referencing_DataEntry_Flow_FullRentGuaranteeFlow();
        }
        else {
            
            throw new Zend_Exception('Product type undefined.');
        }
        
        return $returnVal;
    }
}

?>