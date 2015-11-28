<?php
/**
 * Manages interaction with the various Product data sources.
 */
class Manager_Core_Product
{
    /**
     * Returns a list of product option discount objects related to the 
     * supplied product option identifier.
     * 
     * *****Warning*****
     * Datasource only provides those options which are valid = 1 in the table.
     * 
     * @param int $optionId
     * @return array List of product option discount objects
     * @see Datasource_Core_Product_OptionDiscount
     */
    public function getOptionDiscountsById($optionId)
    {
        $pdsDataSource = new Datasource_Core_Product_OptionDiscount();
        return $pdsDataSource->getProductOptionDiscountList($optionId);
    }
    
    /**
     * Returns a single product option discount object identified by the
     * supplied optionId
     * 
     * @param int $optionId
     * @return Model_Core_Product_ProductOptionDiscount object
     * @see Datasource_Core_Product_OptionDiscount
     */
    public function getOptionDiscountById($optionId)
    {
        $pdsDataSource = new Datasource_Core_Product_OptionDiscount();
        return $pdsDataSource->getProductOptionDiscountById($optionId);
    }
    
    /**
     * Returns the product id from a given product name
     * 
     * @param str $prodName
     */
    public function getProductIdByName($prodName)
    {
        $pdsDatasource = new Datasource_Core_Product();
        $product = $pdsDatasource->getProductByName($prodName);
        return $product->getId();
    }
}