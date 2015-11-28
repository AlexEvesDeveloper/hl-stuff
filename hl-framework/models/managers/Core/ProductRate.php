<?php
class Manager_Core_ProductRate
{
    /**
     * Wrapper for Datasource_Core_Product_Rates::getRate
     * 
     * @param int $productOptionsId
     * @param int $agentsRateID
     * @param int $riskarea
     * @param str $date
     */
    public function getRate($productOptionsId, $agentsRateId = 0, $riskarea = 0,
        $date = null) 
    {
        $prodRate = new Datasource_Core_Product_Rates();
        return $prodRate->getRate(
            $productOptionsId, $agentsRateId, $riskarea, $date
        );
    }
}