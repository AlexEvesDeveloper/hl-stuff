<?php

/**
 * Model definition for the interest table. 
 */
class Datasource_Insurance_Portfolio_BankInterest extends Zend_Db_Table_Multidb {
    protected $_name = 'bank_interest';
    protected $_primary = 'interestID';
    protected $_multidb = 'db_portfolio';
    
	
	
	/**
	* fetch all bank interests by quopte refNo	
	* @param string $refNo
	* @return number of rows or false on failure
	* @author John Burrin
	* @since
	*
	* SELECT properties.*, interest.*
		FROM    homelet.portfolio_properties properties
       INNER JOIN
          homelet.bank_interest interest
       ON (properties.id = interest.propertyId)
       WHERE (portfolio_properties.refno = '61881141.27378');
 
	*/
	public function fetchAllInterestsByrefNo($refNo){
        $select = $this->select()
            ->from(array('i' => $this->_name))
			->setIntegrityCheck(false)
			->join(array('p' => 'portfolio_properties'),'p.id = i.propertyId')
            ->where('i.refno = ?', $refNo);
			
        $rows = $this->fetchAll($select);
        if(!empty($rows)){
            return $rows;
        }else{
            return false;
        }
    }
	
	/**
    * Insert a new row into the bank Interest table table
    *
    * @param Model_Insurance_Portfolio_Portfolio propertyObject The Property to save
    *
    * @return int The last insert ID
    */
    public function save($dataObject){
        // if we have an ID we must be doing an update else do an insert
        if(isset($dataObject->interestID)){
            return $this->_doUpdate($dataObject);
                    
        }else{
            return $this->_doInsert($dataObject);
        }
	}
    
	private function _doUpdate($dataObject){
		$updateArray = array();
        $updateArray['refno'] = $dataObject->refno;
        $updateArray['policynumber'] = $dataObject->policynumber;
        $updateArray['bankname'] = $dataObject->bankname;
        $updateArray['bankaddress1'] = $dataObject->bankaddress1;
        $updateArray['bankaddress2'] = $dataObject->bankaddress2;
        $updateArray['bankaddress3'] = $dataObject->bankaddress3;
        $updateArray['bankaddress4'] = $dataObject->bankaddress4;
        $updateArray['bankpostcode'] = $dataObject->bankpostcode;
        $updateArray['accountnumber'] = $dataObject->accountnumber;
        $updateArray['propertyId'] = $dataObject->bank_property;


        $where = $this->quoteInto('refno = ? and interestID = ?', $dataObject->refno, $dataObject->interestID);
        if($lastId = $this->update($updateArray,$where)){
            return $lastId;
        }else{
            // Error
            Application_Core_Logger::log("Could not update table {$this->_name}", 'Error');
            return false;
        } 
    }

    private function _doInsert($dataObject){
        $insertArray = array();
        $insertArray['refno'] = $dataObject->refno;
        $insertArray['policynumber'] = $dataObject->policynumber;
        $insertArray['bankname'] = $dataObject->bankname;
        $insertArray['bankaddress1'] = $dataObject->bankaddress1;
        $insertArray['bankaddress2'] = $dataObject->bankaddress2;
        $insertArray['bankaddress3'] = $dataObject->bankaddress3;
        $insertArray['bankaddress4'] = $dataObject->bankaddress4;
        $insertArray['bankpostcode'] = $dataObject->bankpostcode;
        $insertArray['accountnumber'] = $dataObject->accountnumber;
        $insertArray['propertyId'] = $dataObject->propertyId;
      
       if($lastId = $this->insert($insertArray)){
            return $lastId;
        }else{
            // Error
            Application_Core_Logger::log("Could not insert into table {$this->_name}", 'Error');
            return false;
        } 
    }
	
    /**
    * Delete a record by a qiven record id
    * @param int $id Index id of the record to be deleted
    */
    public function deleteById($id){
        $where = $this->quoteInto('interestID = ?', $id);
        $this->delete($where);
    }
    
     /**
    * Delete a record by a qiven refno
    * @param string $refNo
    * @return
    * @author John Burrin
    */
    public function deleteByRefNo($refNo){
        $where = $this->getAdapter()->quoteInto('refno = ?', $refNo);
        $this->delete($where);
    }
	
	  /**
    * Delete a record by a qiven record id and refno
    * @param int $id Index id of the record to be deleted
    */
    public function deleteWithRefno($refNo,$id){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$refNo = $pageSession->CustomerRefNo;
        $where = $this->quoteInto('refno = ? and interestID = ?', $refNo, $id);
        $this->delete($where);
    }
}

?>