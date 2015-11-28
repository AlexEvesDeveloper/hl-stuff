<?php
/**
 * Wrapper to handle interaction with Datasource_Core_Discount_CsuDiscount
 * 
 * @author chris.ramsay
 *
 */
class Manager_Core_Discount
{
    /**
     * Returns a single Model_Core_Discount_CsuDiscount object related to the 
     * given csu discount id.
     * 
     * @param int $csuDiscountId
     * @return object Model_Core_Discount_CsuDiscount
     * @see Datasource_Core_Discount_CsuDiscount
     */
    public function getCsuDiscountById($csuDiscountId)
    {
        $csuDiscDataSource = new Datasource_Core_Discount_CsuDiscount();
        return $csuDiscDataSource->getCsuDiscountByID($csuDiscountId);
    }
    
    /**
     * Returns an array of formatted csu/discount strings.
     * 
     * @param int $csuId CSU identifier
     */
    public function getFormattedCsuDiscountById($csuId)
    {
        $csuDs = new Datasource_Core_Discount_CsuDiscount();
        $discounts = $csuDs->getCsuDiscountsByCsuId($csuId);
        $ret = array();
        if ($discounts !== false) {
            foreach ($discounts as $discount) {
            	$ret[] = sprintf(
                    "%d_%d", $discount->getCsuId(),
            	    $discount->getProductOptionDiscountId()
            	);
            }
        }
        return $ret;
    }
    
    /**
     * Updates the csu discount table. Removes existing records pertaining to
     * the csu and product option ids first and then updates accordingly.
     * 
     * @param int $managerCsuId
     * @param array $productOptions
     * @param int $optionId
     */
    public function updateCsuDiscounts($managerCsuId, $productOptions, 
        $optionId) {
        // first split the array values into csu and prod opt id.
        $csuOpts = $this->splitCsusAndOptions($productOptions);
        $csuDisc = new Datasource_Core_Discount_CsuDiscount();
        foreach ($csuOpts as $csuid => $options) {
        	// Delete existing policy opts for this products
        	$this->clearCsuDiscount($optionId, $csuid);
        	// Add in new policy opts
        	$csuDisc->setDiscount($csuid, $managerCsuId, $options);
        }
        
    }
     
    /**
     * Utility function which clears the csu discount by csu id and product 
     * option id
     * 
     * @param int $prodOptId
     * @param int $csuId
     */
    private function clearCsuDiscount($prodOptId, $csuId)
    {
        $ds = new Datasource_Core_Discount_CsuDiscount();
        $ds->clearCsuDiscountByCsuIdAndProdOptId($prodOptId, $csuId);
    }
    
    /**
     * A utility function to map form data into something useful for the csu
     * discount table.
     * 
     * Turns: 
     *      Array (
     *               [0] => 381_1
     *               [1] => 381_2
     *               [2] => 1501_2
     *            )
     * Into:
     *      Array (
     *               [381] => Array
     *                   (
     *                       [0] => 1
     *                       [1] => 2
     *                   )
     *               [1501] => Array
     *                   (
     *                       [0] => 2
     *                   )
     *            )
     *          
     * @param array $productOptions
     * @return array
     */
    private function splitCsusAndOptions($productOptions)
    {
        $splits = array();
        $final = array();
        foreach ($productOptions as $options) {
            $splits = preg_split('/\_/', $options);
            $final[$splits[0]][] = $splits[1];
        }
        return $final;
    }
    
}