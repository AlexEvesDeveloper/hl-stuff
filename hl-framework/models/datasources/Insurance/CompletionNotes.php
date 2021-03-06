<?php

/**
* Model definition for Campaign datasource.
*/
class Datasource_Insurance_CompletionNotes extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'completionnotes';
    protected $_primary = 'cnid';
    /**#@-*/
    
	/**
	 * Description given in the IChangeable interface.
	 */
    public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		
		//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if(empty($policyNumber)) {
			
			$policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
		}
		
		$where = $this->quoteInto('policynumber = ?', $quoteNumber);
		$updatedData = array('policynumber' => $policyNumber);
		return $this->update($updatedData, $where);
	}
}

?>
