<?php

/**
 * Class used to identify missing product selection information.
 */
class Manager_Referencing_DataEntry_MissingInformation_ProductSelection {

	/**
     * Identifies the missing information in the product data entry flow.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * Returns an array of missing information strings, if missing information is
     * found. Else returns null.
     */
    public function getMissingInformation($enquiryId) {
        
		$referenceManager = new Manager_Referencing_Reference();
		$enquiry = $referenceManager->getReference($enquiryId);
        $productSelection = $enquiry->productSelection;

        $missingInfo = array();
        
        
        //Make sure product selected, and if rent guarantee then a duration is selected
        if(empty($productSelection)) {
            
            $missingInfo[] = 'Product not selected';
        }
        else {
            
            $product = $productSelection->product;
            if($product->durationType == Model_Referencing_ProductDurationTypes::VARIABLE) {
                
                //The duration must be specified
                if(empty($productSelection->duration)) {
                    
                    $missingInfo[] = 'Product duration not selected';    
                }
            }
        }
        
        
        //Finalize
        if(empty($missingInfo)) {
            
            $returnVal = null;
        }
        else {
            
            $returnVal = $missingInfo;
        }
        
        return $returnVal;
    }
}

?>