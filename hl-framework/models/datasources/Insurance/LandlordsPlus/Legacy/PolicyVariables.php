<?php

class Datasource_Insurance_LandlordsPlus_Legacy_PolicyVariables extends Zend_Db_Table_Multidb {
	protected $_multidb = 'db_legacy_homelet';    
	protected $_name = 'POLICYVARIABLE';
    protected $_primary = 'policyVariableID';
    
    public function save($data) {
		// Delete anything that's currently saved for this policy number
		$where = $this->quoteInto('policynumber = ?', $data[0]['policyNumber']);
		$this->delete($where);
		
		// Now insert the new data
		foreach ($data as $dataRow) {
			$this->insert($dataRow);
		}
	}
	
	/**
	 * Description given in the IChangeable interface.
	 */
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