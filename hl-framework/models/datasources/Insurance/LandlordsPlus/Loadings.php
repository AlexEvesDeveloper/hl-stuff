<?php

/**
 * Model definition for the policyLoadingHist table.
 * 
 * @todo
 * Remove that stinking $optionDiscounts method parameter which expects its value to be
 * a pipe-delimited string.
 */
class Datasource_Insurance_LandlordsPlus_Loadings extends Zend_Db_Table_Multidb {
    
	protected $_multidb = 'db_legacy_homelet';    
	protected $_name = 'policyLoadingHist';
    protected $_primary = 'id';
    

    /**
	 * Reflects in the datasource the loading(s) to be applied to a particular policy.
	 *
	 * @param string $policyNumber
	 * The quote or policy number.
	 *
	 * @param string $optionsdiscounts
	 * The pipe-delimited loadings to insert into the record. This will reflect the
	 * loading applied to the particular coverages.
	 *
	 * @param Zend_Date $date
	 * The date on which the loading applies.
	 *
	 * @return boolean
	 * Returns true if loading is successfully inserted.
	 */
	public function addLoadingHistory($policyNumber, $optionDiscounts, $date) {

		
		$data = array(
            'policynumber' => $policyNumber,
            'optiondiscounts' => $optionDiscounts,
            'date' => $date->toString(Zend_Date::ISO_8601),
        );
        
        if($this->insert($data)) {
			
			$returnVal = true;
		}
		else {
        
			// Failed insertion
        	Application_Core_Logger::log("Can't insert loading in table {$this->_name}", 'error');
			$returnVal = false;
		}
		
		return $returnVal;
	}
	
	
	public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		
		//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if (empty ( $policyNumber )) {
			
			$policyNumber = preg_replace ( '/^Q/', 'P', $quoteNumber );
		}
		
		$where = $this->quoteInto ( 'policyNumber = ?', $quoteNumber );
		$updatedData = array ('policyNumber' => $policyNumber );
		return $this->update ( $updatedData, $where );
	}
}

?>