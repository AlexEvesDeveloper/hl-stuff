<?php
/**
 * Wrapper to handle interaction with Options datasource
 */
class Manager_Product_Options
{
    /**
     * Get product by product id, policy options id and optional date
     * 
     * @param int $prodId
     * @param int $polOptId
     * @param str $date
     * @return object Model_Core_Product_ProductOptions
     * @see Datasource_Core_Product_Options
     */
    public function getProductOptionsByProdAndOpt($prodId, $polOptId, $date = null)
    {
        $pSource = new Datasource_Core_Product_Options(); 
        return $pSource->getProductByProdAndOpt($prodId, $polOptId, $date);
    }
    
    /**
     * Get product by policy option name
     * 
     * @param int $polOptName
     * @return object Model_Core_Product_ProductOptions
     * @see Datasource_Core_Product_Options
     */
    public function getProductOptByPolicyOptName($polOptName)
    {
        $pSource = new Datasource_Core_Product_Options(); 
        return $pSource->getProductOptByPolicyOptName($polOptName);
    }
}