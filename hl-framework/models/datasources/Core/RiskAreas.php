<?php
/**
* Model definition for the RiskAreas
* 
*/
class Datasource_Core_RiskAreas extends Zend_Db_Table_Multidb {
    protected $_name = 'risk_areas';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet';
    
    /**
    * Finds a specific riskarea by postcode outcode
    *
    * @param string $postcode The full UK postcode for the property
    * @param int $productID A valid homelet product ID
    * @return array
    */    
    function findByPostcode($postcode, $productID)
    {
        // First we need to check that the postcode is valid, but only run this
        // check if the global.ini parameter test.filterPostcode is set to true
        $params = Zend_Registry::get('params');
	if (is_object($params->test) && $params->test->filterPostcode) {
            $postcodeUtil = new Application_Core_Postcode();
            $postcode = $postcodeUtil->validate($postcode);
            if ($postcode == '') return false;
        }

        $postcodeArray = explode(' ', $postcode);
        $outcode = $postcodeArray[0]; // This should give us the outcode
        
        $select = $this->select()
            ->from($this->_name, array('risk_area'))
            ->where('product_id=?', $productID)
            ->where('postcode = ?', $outcode)
            ->where('start_date <= ?', date("Y-m-d"))
            ->where('end_date >= ? OR end_date IS NULL', date("Y-m-d"));
        $row = $this->fetchRow($select);
                
        if(!empty($row))
        {
            $returnVal = $row->risk_area;
        }
        else {
            
            $returnVal = null;
        }
        return $returnVal;
    }
}

?>
