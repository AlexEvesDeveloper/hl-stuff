<?php

/**
* Model definition for the ccTransaction Table
*/
class Datasource_Core_CreditCard_Transaction extends Zend_Db_Table_Multidb {
        
    protected $_name = 'ccTransaction';
    protected $_primary = 'ID';
    protected $_multidb = 'db_legacy_homelet';
  
  
    public function saveTransaction($data) {
        
        $policyNumberModel = new Datasource_Core_NumberTracker();
        $PaymentNumber = $policyNumberModel->getNextPaymentRefNumber();
        $dataToInsert = array(
            'policynumber' => $data['policynumber'],
            'trans_id' => $PaymentNumber,
            'prev_trans_id' => 0,
            'amount' => $data['amount'],
            'auth_code' => $data['auth_code'],
            'card_no' => $data['card_no'],
            'card_type' => $data['card_type'],
            'expiry' => substr($data['expiry'],0,2) . "/" . substr($data['expiry'],2,2),
            'customer' => $data['customer'],
            'message' => (isset($data['message'])) ? $data['message'] : '',
            'resp_code' => (isset($data['resp_code'])) ? $data['resp_code'] : '',
            'code' => $data['code'],
            'test_status' => (isset($data['test_status'])) ? $data['test_status'] : 'live',
            'deferred' => (isset($data['deferred'])) ? $data['deferred'] : '',
            'hash' => $data['hash'],
            'generated_from' => 1,
            'entry_date' => date("Y-m-d")
        );
        $this->insert($dataToInsert);
    }
    
    
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