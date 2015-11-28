<?php
/**
* Model definition for the schedule table
* 
*/
class Datasource_Insurance_Schedules extends Zend_Db_Table_Multidb {
    protected $_name = 'schedule';
    protected $_primary = 'policynumber';
    protected $_multidb = 'db_legacy_homelet';
    
    /**
    * insertNew a new schdule record
    * @param Model_Insurance_Schedule Data to be Saved
    * 
    */
    public function insertNew($data){
        $dataArray = array();
        $dataArray['paymentrefno'] = $data->paymentRefNo;
        $dataArray['january'] = $data->months['january'];
        $dataArray['february'] = $data->months['february'];
        $dataArray['march'] = $data->months['march'];
        $dataArray['april'] = $data->months['april'];
        $dataArray['may'] = $data->months['may'];
        $dataArray['june'] = $data->months['june'];
        $dataArray['july'] = $data->months['july'];
        $dataArray['august'] = $data->months['august'];
        $dataArray['september'] = $data->months['september'];
        $dataArray['october'] = $data->months['october'];
        $dataArray['november'] = $data->months['november'];
        $dataArray['december'] = $data->months['december'];
        $dataArray['policynumber'] = $data->policyNumber;
        $dataArray['sixmonthfee'] = $data->sixMonthFee;
        $dataArray['ddfee'] = $data->ddFee;
        $dataArray['banked'] = $data->banked;
        // Save the record
        if (!$this->retrieveByPolicyNumber($data->policyNumber)) {
            $this->insert($dataArray);
            return true;
        } else {
            // Failed insertion
            Application_Core_Logger::log("Can't insert schedule in table {$this->_name}", 'error');
            return false;
        }   
      
    }
    
    /**
    * Update an existing Schedule
    * @param $policyNumber A policy number
    * 
    */
    public function store($policyNumber,$data){
        $dataArray = array();
        $dataArray['paymentrefno'] = $data->paymentRefNo;
        $dataArray['january'] = $data->january;
        $dataArray['february'] = $data->february;
        $dataArray['march'] = $data->march;
        $dataArray['april'] = $data->april;
        $dataArray['may'] = $data->may;
        $dataArray['june'] = $data->june;
        $dataArray['july'] = $data->july;
        $dataArray['august'] = $data->august;
        $dataArray['september'] = $data->september;
        $dataArray['october'] = $data->october;
        $dataArray['november'] = $data->november;
        $dataArray['december'] = $data->december;
        #$dataArray['policynumber'] = $data->policyNumber;
        $dataArray['sixmonthfee'] = $data->sixMonthFee;
        $dataArray['ddfee'] = $data->ddFee;
        $dataArray['banked'] = $data->banked;
        // Save the record
        $where = $this->quoteInto('policynumber = ?', $policyNumber);
        if (!$this->update($dataArray,$where)) {
            // Failed update
            Application_Core_Logger::log("Can't update schedule in table {$this->_name}", 'error');
        }
    }
    
    /**
    * Retrieve by policynumber
    * @param $policyNumber Policy number of record to retrieve
    * @return Model_Insurance_Schedule
    * 
    */
    public function retrieveByPolicyNumber($policyNumber){
        $scheduleData = new Model_Insurance_Schedule();
         $select = $this->select()
            ->from($this->_name)
            ->where('policynumber = ?', $policyNumber);

        $row = array();      
        $row = $this->fetchRow($select); 
        if ($row){
            $scheduleData->paymentRefNo = $row['paymentrefno'];
            $scheduleData->months['january'] = $row['january'];
            $scheduleData->months['february'] = $row['february'];
            $scheduleData->months['march'] = $row['march'];
            $scheduleData->months['april'] = $row['april'];
            $scheduleData->months['may'] = $row['may'];
            $scheduleData->months['june'] = $row['june'];
            $scheduleData->months['july'] = $row['july'];
            $scheduleData->months['august'] = $row['august'];
            $scheduleData->months['september'] = $row['september'];
            $scheduleData->months['october'] = $row['october'];
            $scheduleData->months['november'] = $row['november'];
            $scheduleData->months['december'] = $row['december'];
            $scheduleData->policyNumber = $row['policynumber'];
            $scheduleData->sixMonthFee = $row['sixmonthfee'];
            $scheduleData->ddFee = $row['ddfee'];
            $scheduleData->banked= $row['banked'];
            return $scheduleData;  
        } else {
            return false;
        }        
    }
   /**
	 * Change a quote number to Policynumber
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