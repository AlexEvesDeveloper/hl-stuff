<?php
class Datasource_Core_Product_Rates extends Zend_Db_Table_Multidb
{
    protected   $_name = 'productRates';
    protected   $_primary = 'rateID';
    protected   $_multidb = 'db_legacy_homelet';
    
    /**
     * Fetch a rate 
     *
     * @param productOptionsID,agentsRateID,riskarea,date 
     * @return array (annual rate) ['grossRate'], ['netRate']
     */
    public function getRate($productOptionsID, $agentsRateID= 0, $riskarea = 0,
        $date = null) 
    {
    	if (is_null($date)) {
    	    $date = date("Y-m-d");    	
    	}
        $select = $this->select();
        $select->where("productOptionsID = ?", $productOptionsID )
               ->where("agentsRateID = ?",$agentsRateID)
               ->where("riskarea = ? ", $riskarea)
               ->where("startDate <= ?", $date)
               ->where("endDate >=? or endDate='0000-00-00'", $date);
        $row = $this->fetchRow($select);
        if (count($row)) {
            $prodRate = new Model_Core_Product_ProductRate();
            $prodRate->populate($row->toArray());
            $ret = $prodRate;
        } else {
        	$ret = false;
        }
        return $ret;
    }
}
?>

