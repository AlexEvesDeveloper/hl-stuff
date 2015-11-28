<?php
/**
* TODO: Document this
* @param
* @return
* @author John Burrin
* @since
*/
class Datasource_Insurance_Portfolio_PreviousClaims extends Zend_Db_Table_Multidb {
    protected $_name = 'previous_claims';
    protected $_primary = 'id';
    protected $_multidb = 'db_portfolio';
    /**
    * Insert a new row into the claims table
    *
    * @param Model_Insurance_Portfolio_PreviousClaims Object The claim to save
    *
    * @return int The last insert ID
    */
    public function save($dataObject){
        // if we have an ID we must be doing an update else do an insert
        if(isset($propertyObject->id)){
            return $this->_doUpdate($dataObject);
        }else{
            return $this->_doInsert($dataObject);
        }

    }
        private function _doUpdate($dataObject){
        $updateArray['refno'] = $dataObject->refno;
        $updateArray['claimtype'] = $dataObject->claimtype;
        $updateArray['claimmonth'] = $dataObject->claimmonth;
        $updateArray['claimyear'] = $dataObject->claimyear;
        $updateArray['claimvalue'] = $dataObject->claimvalue;
        $updateArray['claimTypeID'] = $dataObject->claimTypeID;
        $updateArray['propertyId'] = $dataObject->propertyId;

        $where = $this->quoteInto('refno = ? and id = ?', $dataObject->refno, $dataObject->id);
        if($this->update($updateArray,$where)){
            return true;
        }else{
            return false;
        }
    }

    private function _doInsert($dataObject){
        $insertArray = array();
        $insertArray['refno'] = $dataObject->refno;
        $insertArray['claimtype'] = $dataObject->claimtype;
        $insertArray['claimmonth'] = $dataObject->claimmonth;
        $insertArray['claimyear'] = $dataObject->claimyear;
        $insertArray['claimvalue'] = $dataObject->claimvalue;
        $insertArray['claimTypeID'] = $dataObject->claimTypeID;
        $insertArray['propertyId'] = $dataObject->propertyId;

       if($lastId = $this->insert($insertArray)){
            return $lastId;
        }else{
            // Error
            Application_Core_Logger::log("Could not insert into table {$this->_name}", 'error');
            return false;
        }
    }

    public function fetchAllClaimsByrefNo($refNo){
        $select = $this->select()
            ->from(array('c' => $this->_name),array(
                                                    'cid'=>'c.id',
                                                    'claimtype' => 'claimtype',
                                                    'claimmonth' => 'claimmonth',
                                                    'claimyear' => 'claimyear',
                                                    'claimvalue' => 'claimvalue',
                                                    'claimTypeID' => 'claimTypeID'
                                                    ))
			->setIntegrityCheck(false)
			->join(array('p' => 'portfolio_properties'),'p.id = c.propertyId')
            ->where('c.refno = ?', $refNo);

        $rows = $this->fetchAll($select);

        if(!empty($rows)){
            return $rows;
        }else{
            return false;
        }
    }

    /**
    * Delete a record by a qiven record id and refno
    * @param int $id Index id of the record to be deleted
    */
    public function deleteWithRefno($refNo,$id){
        $where = $this->quoteInto('refno = ? and id = ?', $refNo, $id);
        $this->delete($where);
    }

    /**
    * Delete a record by a qiven record id
    * @param int $id Index id of the record to be deleted
    */
    public function deleteById($id){
        $where = $this->quoteInto('id = ?', $id);
        $this->delete($where);
    }

     /**
    * TODO: Document this
    * @param
    * @return
    * @author John Burrin
    */
    public function deleteByRefNo($refNo){
        $where = $this->getAdapter()->quoteInto('refno = ?', $refNo);
        $this->delete($where);
    }

    /**
    * return the total valu of claims by refNo
    * SELECT SUM(claimvalue) as claimValue from previous_claims WHERE refno = "UWP6545_27722";
    * @param string $refNo theference number linke to quote
    * @return string sum of claims
    * @author John Burrin
    * @since
    *
    */
    public function getClaimsTotal($refNo){
        // Some days I just love the ease of Zend
        $sumPaymentExp = new Zend_Db_Expr('SUM(claimvalue)');
        $where = $this->getAdapter()->quoteInto('refno = ?', $refNo);
        $select = $this->select()
            ->from($this->_name,array('claimValue' => $sumPaymentExp))
            ->where($where);
        $row = $this->fetchRow($select);
        return $row['claimValue'];
    }

    /**
    * TODO: Document this
    * @param
    * @return
    * @author John Burrin
    * @since 1.3
    *
        SELECT previous_claims.refno,
       previous_claims.claimtype,
       previous_claims.claimmonth,
       previous_claims.claimyear,
       previous_claims.claimvalue,
       previous_claims.claimTypeID,
       claimTypes.claimTypeText,
       portfolio_properties.houseNumber,
       portfolio_properties.building,
       portfolio_properties.address1,
       portfolio_properties.address2,
       portfolio_properties.address3,
       portfolio_properties.address4,
       portfolio_properties.address5,
       portfolio_properties.postcode
  FROM    (   homelet.previous_claims previous_claims
           INNER JOIN
              homelet.portfolio_properties portfolio_properties
           ON (previous_claims.propertyId = portfolio_properties.id))
       INNER JOIN
          homelet.claimTypes claimTypes
       ON (claimTypes.claimTypeID = previous_claims.claimTypeID)
 WHERE (previous_claims.refno = 'UWP6550_64982')
    */
    public function fetchWithClaimTypes($refNo){
        $select = $this->select()
            ->from(array('claims' => $this->_name),array(
                                                    'cid'=>'claims.id',
                                                    'claimtype' => 'claims.claimtype',
                                                    'claimmonth' => 'claims.claimmonth',
                                                    'claimyear' => 'claims.claimyear',
                                                    'claimvalue' => 'claims.claimvalue',
                                                    'claimTypeID' => 'claims.claimTypeID'
                                                    ))
			->setIntegrityCheck(false)
			->join(array('claimType' => 'claimTypes'),'claims.claimTypeID = claimType.claimTypeID')
            ->join(array('properties' => 'portfolio_properties'),'properties.id = claims.propertyId')
            ->where('claims.refno = ?', $refNo);

        $rows = $this->fetchAll($select);
        if(!empty($rows)){
            return $rows;
        }else{
            return false;
        }
    }
}

?>