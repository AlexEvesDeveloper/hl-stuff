<?php

/**
* Model definition for the dd Table
* 
*/
class Datasource_Core_DirectDebit_Payment extends Zend_Db_Table_Multidb{
    
    protected $_name = 'dd';
    protected $_primary = 'paymentrefno';    
    protected $_multidb = 'db_legacy_homelet';
    
    
    /**
     * save details saves a single transaction to the dd table
     * @param array Array data to be saved to the table
     * @example simulating this sql  INSERT INTO dd
	 *			(refno,
	 *			policynumber,
	 *			paymentfrequency,
	 *			accountname,
	 *			accountnumber,
	 *			sortcode,
	 *			paymentdate,
	 *			paymentrefno,
	 *			AUDDIS,
	 *			errormark)
	 *		VALUES
	 *			("4936245.13595",
	 *			"PHLI2883291/01",
	 *			"Monthly",
	 *			"Johhnnyy B",
	 *			"0152154125",
	 *			"111111",
	 *			"2011-02-22",
	 *			"DDHL0321541",
	 *			"0N",
	 *			"No")
	 * @ todo I've had to change this because $data is being passed in as a Model_Core_Directdebit object rather than an array!!!
	 *        so you need to update this docblock!
     */
    public function saveDetails($data){
         $dataToInsert = array(
            'refno' => $data->refNo,
            'policynumber' => $data->policyNumber,
            'paymentfrequency' => $data->paymentFrequency,
            'accountname' => $data->accountName,
            'accountnumber' => $data->accountNumber,
            'sortcode' => $data->sortCode,
            'paymentdate' => $data->paymentDate,
			/**
			 * TODO: The paymentrefno contains a flag which is the White label ID an 
			 * HL for HomeLet, do we need to implement Whitelabeling?
			 **/
            'paymentrefno' => $data->paymentRefNo,
            'AUDDIS' => $data->AUDDIS,
            'errormark' => $data->errorMark
        );

        $this->insert($dataToInsert);
    }
    
    
    /**
     * remove payment, remove a payment record by paymentrefno
     * @param string $paymentRefno The payment to be removed from the dd table
     *
     */
    public function removePayment($paymentRefno){
        $where = $this->quoteInto('paymentrefno = ?', $paymentRefno);
        $this->delete($where);
    }
    
    
    /**
     * fetchByPaymentRefno, retrieves a single payment as an array 
     * @param string $paymentRefnoId of the payment to be removed
     * @return array The row to be returned
     */
    public function fetchByPaymentRefno($paymentRefNo){
        $select = $this->select();
        $select->where('paymentrefno = ?', $paymentRefNo);
        $returnArray = array();
        $returnArray = $this->fetchRow($select);
        return $returnArray;
    }

    /**
    * Get a direct debit entry by it's policy reference number
    * @param string $refNo releated policy reference number
    * @return Model_Core_Directdebit A Direct debit payment object
    */
    public function getByRefNo($refNo){
        $ddObject = new Model_Core_Directdebit(); 
        // Fields we want
        // 'alias' => 'field name'
        $fields = array(
            'refNo' => 'refno',
            'policyNumber' => 'policynumber',
            'paymentFrequency' => 'paymentfrequency',
            'accountName' => 'accountname',
            'accountNumber' => 'accountnumber',
            'sortCode' => 'sortcode',
            'paymentDate' => 'paymentdate',
            'paymentRefNo' => 'paymentrefno',
            'Auddis' => 'AUDDIS',
            'errorMark' => 'errormark');
        $select = $this->select()
            ->from($this->_name, $fields)
             ->where('refno = ?', $refNo)
             ->where('AUDDIS = "0N" ')
             ->where('paymentrefno not like "DD%" ')
             ->order('paymentdate DESC')
             ->order('paymentrefno DESC')
             ->limit(1);

        $row = array();
        $row = $this->fetchRow($select);
        if(!empty($row)){			
            $ddObject->refNo =$row->refNo;
			$ddObject->policyNumber =$row->policyNumber;
            $ddObject->paymentFrequency =$row->paymentFrequency;
            $ddObject->accountName =$row->accountName;
            $ddObject->accountNumber =$row->accountNumber;
            $ddObject->sortCode =$row->sortCode;
            $ddObject->paymentDate =$row->paymentDate;
            $ddObject->paymentRefNo =$row->paymentRefNo;
            $ddObject->AUDDIS =$row->Auddis;
            $ddObject->errorMark =$row->errorMark;
            return $ddObject;  
        }else{ 
            // Could not find a requested Payment
            Application_Core_Logger::log("No Direct Debit Payment for this referncenumber $refNo. Not found in table {$this->_name}", 'warning');
			return false;
        }        
    }
    
    /**
    * getPaymentRefNo - Get a payment reference number by policy reference number
    * @param string $refNo Policy reference number
    * @return String Direct Debit Payment Reference number
    * 
    */
    public function getPaymentRefNo($refNo){
        // Fields we want
        // 'alias' => 'field name'
        $fields = array(
            'paymentRefNo' => 'paymentrefno');
        $select = $this->select()
            ->from($this->_name, $fields)
             ->where('policynumber = ?', $refNo);
        $row = array();      
        $row = $this->fetchRow($select);

        if(!empty($row) ){
            $returnData =$row['paymentRefNo'];
            return $returnData;  
        }else{
            // Could not find a requested Payment
            Application_Core_Logger::log("No Direct Debit Payment for this referncenumber $refNo. Not found in table {$this->_name}", 'warning');
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
