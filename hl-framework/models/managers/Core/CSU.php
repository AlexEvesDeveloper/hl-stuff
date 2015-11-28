<?php

class Manager_Core_CSU
{
    /**
     * Retrieves the product option discount for a given CSU and associated
     * given product option identifier.
     * 
     * @param int $csuId
     * @param int $productOptionId
     */
    public function getProductOptionDiscount($csuId, $productOptionId)
    {
        $csuDiscDatasource = new Datasource_Core_Discount_CsuDiscount();
        return $csuDiscDatasource->getProductDiscByCsuProductOptionId(
            $csuId, $productOptionId
        );
    }
    
    /**
     * 
     * @param $csuId
     * @param $productId
     */
    public function getProductDiscountOptionsByName($csuId, $productId)
    {
        $productDatasource = new Datasource_Core_Product();
        return $this->getProductOptionDiscount(
            $csuId,
            $productDatasource->getProductByName($productId)
        ); 
    }
    
    /**
     * Returns LIKE matched CSUs by CSU name.
     * 
     * @param str $csuName
     * @return array Model_Core_Csu object(s)
     */
    public function getCsuByName($csuName)
    {
        $csuDs = new Datasource_Core_Csu();
        return $csuDs->getCsuByName($csuName);
    }
    
    /**
     * Updates the CSUs product option availability for a given CSU
     * 
     * @param int $csuid
     * @param int $supervisorId
     * @param array $productOptDiscIds
     */
    public function updateProductDiscountOptions($csuID, $supervisorID, 
        $productOptDiscIds)
    {
        $csuDiscDatasource = new Datasource_Core_Discount_CsuDiscount();
        $csuDiscDatasource->setDiscount(
            $csuID, $supervisorID, $productOptDiscIds
        );
    }
    
    /**
     * 
     * @param $csuID
     * @param $productOptDiscIDs
     */
    public function removeProductDiscountOptions($csuID, $productOptDiscIDs)
    {
        $csuDiscDatasource = new Datasource_Core_Discount_CsuDiscount();
        $csuDiscDatasource->removeDiscount($csuID, $productOptDiscIDs);   
    }
}