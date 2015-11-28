<?php

/**
* Model definition for Campaign datasource.
*/
class Datasource_Core_ManagementInformation_MarketingAnswers extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'MarketingAnswers';
    protected $_primary = 'policynumber';
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
	

	/**
	 * Inserts a new marketing answer against a specified quote/policy number.
	 *
	 * @param string $policyNumber
	 * The full quote or policy number.
	 *
	 * @param string $refno
	 * The legacy customer refno.
	 *
	 * @param string $answer
	 * The marketing answer. Answers the question: how did you hear about us.
	 *
	 * @return void
	 */
    public function insertAnswer($policyNumber, $refno, $answer) {
    
        //First insert into the DataStore.
        $data = array(
            'policynumber' => $policyNumber,
            'refno' => $refno,
            'Answer' => $answer
        );

        $this->insert($data);
    }
	
	
	/**
	 * Updates an existing marketing answer.
	 *
	 * @param string $policyNumber
	 * The full quote or policy number.
	 *
	 * @param string $refno
	 * The legacy customer refno.
	 *
	 * @param string $answer
	 * The marketing answer. Answers the question: how did you hear about us.
	 *
	 * @return void
	 */
	public function updateAnswer($policyNumber, $refno, $answer) {
		
		$data = array(
            'policynumber'      =>  $policyNumber,
            'refno'         	=>  $refno,
            'Answer'          	=>  $answer
        );

        $where = $this->quoteInto('policynumber = ?', $policyNumber);
        $this->update($data, $where);
	}
	
	
	/**
	 * Returns true/false according to whether the marketing answer has been stored previously.
	 *
	 * @param string $policyNumber
	 * Identifies the market answer to search for.
	 *
	 * @return boolean
	 * True or false according to whether the marketing answer has been previously stored.
	 */
	public function getConfirmAnswerExists($policyNumber) {
        
        //Attempt to retrieve the customer record.
        $select = $this->select();
        $select->where('policynumber = ?', $policyNumber);
        $rowSet = $this->fetchAll($select);
        
        if(count($rowSet) == 0) {
            
            $returnVal = false;
        }
        else {
            
            $returnVal = true;
        }
        
        return $returnVal;
    }
	
    /**
     * Get a previously stored answer
     *
     * @param string $policyNumber Identifies the market answer to search for.
     *
     * @return string Content of marketing answer previously stored, empty if none.
     */
    public function getAnswer($policyNumber) {
        
        // Attempt to retrieve the customer record
        $select = $this->select();
        $select->where('policynumber = ?', $policyNumber);
        $rowSet = $this->fetchAll($select);
        
        if (count($rowSet) == 0) {
            $returnVal = '';
        } else {
            $rowData = array();
            foreach ($rowSet as $rowArray) {
                foreach ($rowArray as $column => $value) {
                    $rowData[$column] = $value;
                }
            }
            $returnVal = $rowData['Answer'];
        }
        
        return $returnVal;
    }
    
	public function save($policyNumber, $refno, $answer){
		if($this->getConfirmAnswerExists($policyNumber)){
			$this->updateAnswer($policyNumber, $refno, $answer);
		}else{
			$this->insertAnswer($policyNumber, $refno, $answer);
		}
	}
}

?>