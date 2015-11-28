<?php
class Manager_Product_OptionDiscount
{
    /**
     * 
     * @param $product
     * @param $option
     */
    public function fetchListByProductAndOptionName($product, $option)
    {
        $prodOptDisc = new Datasource_Core_Product_OptionDiscount();
        return $prodOptDisc->getProductOptionDiscountListByProductAndOptionName(
            $product, $option
        );
    }
    
    /**
     * Gets the default product option discount id
     * 
     * @param int $prodOptId Product Option Id
     */
    public function getDefaultOption($prodOptId)
    {
        $prodOptDisc = new Datasource_Core_Product_OptionDiscount($prodOptId);
        return $prodOptDisc->getDefaultProductOptionDiscountId($prodOptId);
    }
}