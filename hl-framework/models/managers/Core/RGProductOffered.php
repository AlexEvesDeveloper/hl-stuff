<?php
class Manager_Core_RGProductOffered
{
    /**
     * Returns true if the given combinations allow RG products
    * @param string $agentschemeno
     * @param int $letType
     * @param int $howRgOffere
     * @return bool
     */
    public function canOfferRentGuanantee($fsa_status, $letType, $howRgOffered){
        
        if((strtoupper($fsa_status) == "AR" || strtoupper($fsa_status) == "DIRECT" || strtoupper($fsa_status) == "NAR") && $howRgOffered != 4){
        
            return true;
        }
        else {
            
            if ($howRgOffered == 4 || (($letType == "1" || $letType == "3") && $howRgOffered != "1")){
            
                return false;
            }
            else {
                
                return true;
            }
        }
    }
     /**
     * Fetches all the products
     *
     * @param string $fsa_status
     * @param int $letType
     * @param int $howRgOffered
     * @param bool $isCompanyApplication bool
     *
     * @return array
     */
     public function fetchProducts($fsa_status,$letType,$howRgOffered, $isCompanyApplication = false){
        
        $productList = array();
        
        //Suppress all products for agents with an FSA status of none.
        if(preg_match("/^$|none/i", $fsa_status)) {

            return $productList;
        }
        
        $productManager = new Manager_Referencing_Product();
        if(!$this->canOfferRentGuanantee($fsa_status, $letType, $howRgOffered)) {
            
            //Offer only non-rent-guarantee products only.
            $productVariable = Model_Referencing_ProductVariables::NON_RENT_GUARANTEE;
            $products = $productManager->getByVariable($productVariable);
            foreach($products as $product) {
                
                $productList[] = array('value'=>$product->key, 'name' => strtoupper($product->name));
            }
            return $productList;
        }
        
        if((strtoupper($fsa_status) == "AR" || strtoupper($fsa_status) == "DIRECT" || strtoupper($fsa_status) == "NAR") && $howRgOffered != 4){
            
            // Get ALL products
            $productVariable = Model_Referencing_ProductVariables::RENT_GUARANTEE;
            $products = $productManager->getByVariable($productVariable);
            foreach($products as $product) {
                
                $productList[] = array('value'=>$product->key, 'name' => strtoupper($product->name));
            }
            $productVariable = Model_Referencing_ProductVariables::NON_RENT_GUARANTEE;
            $products = $productManager->getByVariable($productVariable);
            foreach($products as $product) {
                
                $productList[] = array('value'=>$product->key, 'name' => strtoupper($product->name));
            }

        }
        else {
            
            if ($howRgOffered == 4 || (($letType == 1 || $letType == 3) && $howRgOffered != 1)){
                // Get NON RG products only
                $productVariable=Model_Referencing_ProductVariables::NON_RENT_GUARANTEE;
                $products = $productManager->getByVariable($productVariable);
                foreach($products as $product) {
                    $productList[] = array('value'=>$product->key, 'name' => strtoupper($product->name));
                }
            }else{
                // Get ALL products
                $productVariable = Model_Referencing_ProductVariables::RENT_GUARANTEE;
                $products = $productManager->getByVariable($productVariable);
                foreach($products as $product) {
                    $productList[] = array('value'=>$product->key, 'name' => strtoupper($product->name));
                }
                $productVariable = Model_Referencing_ProductVariables::NON_RENT_GUARANTEE;
                $products = $productManager->getByVariable($productVariable);
                foreach($products as $product) {
                    $productList[] = array('value'=>$product->key, 'name' => strtoupper($product->name));
                }
            }
        }
        return $productList;
    }
}
