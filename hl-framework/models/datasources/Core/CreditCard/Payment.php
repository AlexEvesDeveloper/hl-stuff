<?php

/**
* Model definition for the cc Table
*/
class Datasource_Core_CreditCard_Payment extends Zend_Db_Table_Multidb {
        
    protected $_name = 'cc';
    protected $_primary = 'paymentrefno';    
    protected $_multidb = 'db_legacy_homelet';
    
    
    /**
     * Save details, saves a direct debit transaction to cc table
     */
    public function saveDetails($data){
        $params = Zend_Registry::get('params');
       
        $dataToInsert = array(
            'refno' => $data['refno'],
            'policynumber' => $data['policynumber'],
            'paymentfrequency' => $data['paymentfrequency'],
            'cardname' => $data['customer'],
            'cardnumber' => $data['card_no'],
            'cardtype' => $data['card_type'],
            'expirydate' => substr($data['expiry'],0,2) . "/" . substr($data['expiry'],2,2),
            'startdate' => (isset($data['start_date'])) ? substr($data['start_date'],0,2) . "/" . substr($data['start_date'],2,2) : '' ,
            'issueno' => (isset($data['issue'])) ? $data['issue'] : '',
            'paymentdate' => date("Y-m-d"),
            'paymentrefno' => $data['trans_id'],
            'merchantid' => (isset($data['merchant'])) ? $data['merchant'] : $params->secpay->get('merchant')
        );
        $this->insert($dataToInsert);
        $transaction = new Datasource_Core_CreditCard_Transaction();
        $transaction->saveTransaction($data);
    }

    
    
	/**
     * remove payment, remove a payment record by paymentrefno
     * @param string $paymentRefno The payment to be removed from the dd table
     *
     */    
    public function removePayment($paymentrefno){
        $where = $this->quoteInto('paymentrefno = ?', $paymentrefno);
        $this->delete($where);
    }
    
    
    /**
     * fetchByPaymentRefono, retrieves a single payment as an array 
     * @param string $paymentRefnoId of the payment to be removed
     * @return array The row to be returned
     */
    public function getByRefNo($paymentrefno){
		$ccObject = new Model_Core_Creditcard(); 
		$fields = array(
			'refNo' => 'refno',
			'policyNumber' => 'policynumber',
			'paymentFrequency' => 'paymentfrequency',
			'cardName' => 'cardname',
			'cardNumber' => 'cardnumber',
			'cardType' => 'cardtype',
			'expiryDate' => 'expirydate',
			'startDate' => 'startdate',
			'issueNo' => 'issueno',
			'paymentDate' => 'paymentdate',
			'paymentRefNo' => 'paymentrefno',
			'merchantId' => 'merchantid',
			'expWarnLetterSent' => 'expwarnlettersent'
			);
		
		$select = $this->select()
            ->from($this->_name, $fields)
             ->where('refno = ?', $paymentrefno)
              ->order('paymentdate DESC')
              ->order('paymentrefno DESC')
              ->limit(1);
        $row = array();
        $row = $this->fetchRow($select);
        if(!empty($row)){			
            $ccObject->refNo =$row->refNo;
			$ccObject->policyNumber =$row->policyNumber;
            $ccObject->paymentFrequency =$row->paymentFrequency;
            $ccObject->cardName =$row->cardName;
            $ccObject->cardNumber =$row->cardNumber;
            $ccObject->cardType =$row->cardType;
            $ccObject->expiryDate =$row->expiryDate;
            $ccObject->startDate =$row->startDate;
            $ccObject->issueNo =$row->issueNo;
            $ccObject->paymentDate =$row->paymentDate;
			$ccObject->paymentRefNo =$row->paymentRefNo;
			$ccObject->merchantId =$row->merchantId;
			$ccObject->expWarnLetterSent =$row->expWarnLetterSent;
            return $ccObject;  
        }else{ 
            // Could not find a requested Payment
            Application_Core_Logger::log("No Credit Card Payment for this reference number $paymentrefno. Not found in table {$this->_name}", 'warning');
			return false;
        }    
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