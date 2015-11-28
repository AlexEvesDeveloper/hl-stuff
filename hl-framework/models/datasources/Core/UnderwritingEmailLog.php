<?php

/**
 * Model definition for the UnderwritingEmailLog table.
 * 
 */
class Datasource_Core_UnderwritingEmailLog extends Zend_Db_Table_Multidb {
    
	/**#@+
     * Mandatory attributes
     */
	protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'UnderwritingEmailLog';
    protected $_primary = 'id';
    /**#@-*/
    	
	
	/**
	 * Inserts a log entry in the table.
	 *
	 * @param Zend_Date $timeStamp
	 * Date against which the entry should be recorded.
	 *
	 * @param string $policyNumber
	 * The full quote/policynumber.
	 *
	 * @param string $reason
	 * The reason being logged.
	 *
	 * @return boolean
	 * Returns true if the record was inserted successfully, false otherwise.
	 */
    public function insertNotification($timeStamp, $policyNumber, $reason) {
    	               
        $data = array(
            'timestamp' => $timeStamp->toString('YYYY-MM-dd'),
            'policyNumber' => $policyNumber,
            'reason' => $reason
        );
        
		
        if(is_int($this->insert($data))) {
			
			return true;
		}
        return false;
    }
	
	
	/**
	 * Description given in the IChangeable interface.
	 */
	public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		
		//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if(empty($policyNumber)) {
			
			$policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
		}
		
		$where = $this->quoteInto('policyNumber = ?', $quoteNumber);
		$updatedData = array('policyNumber' => $policyNumber);
		return $this->update($updatedData, $where);	
	}
}

?>