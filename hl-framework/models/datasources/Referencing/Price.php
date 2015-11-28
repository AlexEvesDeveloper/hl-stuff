<?php

/**
* Model definition for the Enquiry datasource. The Enquiry links together all aspects of the
* referencing process. The Enquiry identifier can be used to identify all related data,
* not just that in the Enquiry datasource.
*/
class Datasource_Referencing_Price extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'Price';
    protected $_primary = 'ID';
    /**#@-*/
    
    
	/**
	 * Retrieves the PLL price for a referencing product.
	 *
	 * @param Model_Referencing_ProductSelection
	 * Holds the product id and duration, both of which are needed to retrieve
	 * the price.
	 *
	 * @return mixed
	 * Returns a Zend_Currency price if the price can be found, else returns null.
	 */
    public function getPrice($productId, $duration) {
        if(is_null($duration)){
        	$duration=0;
        }
        $select = $this->select();
		$args = array($productId, 0, $duration, 'A', '0', 3);
		$where = $this->quoteInto(
			'ProductID = ? AND
			Renewal = ? AND
			Duration = ?
			AND Band = ?
			AND Guarantor = ?
			AND AgentTypeID =?', $args);
		$select->where($where);
		      
        $priceRow = $this->fetchRow($select);
		
		if(empty($priceRow)) {
			
			Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ': Unable to find Price.');
			$returnVal = null;
		}
		else {
			
			$returnVal = new Zend_Currency(
				array(
					'value' => $priceRow['Price'],
					'precision' => 2
				)
			);
		}
		
		return $returnVal;
    }
}

?>