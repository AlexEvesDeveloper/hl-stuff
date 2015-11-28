<?php
/**
* Additional Information Datasource
* @param
* @return
* @author John Burrin
* @since
*/
class Datasource_Insurance_Portfolio_AdditionalInformation extends Zend_Db_Table_Multidb {
    protected $_name = 'additional_information';
    protected $_primary = 'id';
    protected $_multidb = 'db_portfolio';

    /**
    * Insert a new row into the additional_information table
    *
    * @param Model_Insurance_Portfolio_AdditionalInformation $data The data to be inserted
    *
    * @return bool
    */
    public function save($data){
        $insertArray = array();

        $insertArray['refNo'] = $data->refNo;
        $insertArray['questionId'] = $data->questionId;
        $insertArray['propertyId'] = $data->propertyId;
        $insertArray['information'] = $data->information;

        if($this->_exists($data->refNo,$data->questionId, $data->propertyId)){
            return $this->_update($insertArray);
        }else{

            return $this->_insert($insertArray);
        }
    }

    /**
    * Private function to perform the insert
    * @param  array
    * @return 
    * @author John Burrin
    */
    private function _insert($insertArray){
        if($id = $this->insert($insertArray)){
            return $id;
        }else{
            // Error
            Application_Core_Logger::log("Could not insert into table {$this->_name}", 'error');
            return false;
        }
    }

    /**
    * Private function to perform an update
    * @param array
    * @return bool
    * @author John Burrin
    */
    private function _update($insertArray){
        $where = $this->quoteInto('refNo = ? AND questionId = ? AND propertyId = ?', $insertArray['refNo'], $insertArray['questionId'], $insertArray['propertyId']);
        if($this->update($insertArray,$where)){
            return true;
        }else{
            return false;
        }
    }
    /**
    * private function to check a record exists or not
    * @param string $refNo, integer $questionId, integer $propertyId
    * @return int id of record on success or false on failure
    * @author John Burrin
    */
    private function _exists($refNo, $questionId, $propertyId){
        $where = $this->quoteInto('refNo = ? and questionId = ? and propertyId = ?', $refNo, $questionId, $propertyId);
        $select = $this->select()
            ->from($this->_name,array('refNo'))
            ->where($where);
        $row = $this->fetchRow($select);

        if(!empty($row)){
            return true;
        }
        return false;
    }


    public function fetchAllAdditionalByrefNo($refNo,$qid){
        $select = $this->select()
            ->from(array('a' => $this->_name),array(
                                                    'aid'=>'a.id',
                                                    'questionId' => 'questionId',
                                                    'information' => 'information',
                                                    ))
			->setIntegrityCheck(false)
			->join(array('p' => 'portfolio_properties'),'p.id = a.propertyId')
            ->where("a.refNo = '$refNo' AND a.questionId = '$qid'");
        $rows = $this->fetchAll($select);

        if(!empty($rows)){
            return $rows;
        }else{
            return false;
        }
    }

    public function fetchByRefNo($refNo){
        $where = $this->getAdapter()->quoteInto('refNo = ?', $refNo);
        $select = $this->select()
            ->from($this->_name)
            ->where($where);
        $row = $this->fetchAll($select);
        return $row;
    }

    /**
    * Delete a record by a qiven record id and refno
    * @param int $id Index id of the record to be deleted
    */
    public function deleteWithRefno($refNo,$id){
        $where = $this->quoteInto('refno = ? and id = ?', $refNo, $id);
        $this->delete($where);
    }

    public function hasAdditions($refNo, $quid){
        #$where = $this->getAdapter()->quoteInto('refNo = ? and questionId = ?', $refNo, $quid);
        $where = "refNo='$refNo' and questionId='$quid'";

        $select = $this->select();
        $select->from($this->_name, array('count(*) as amount'))
        ->where($where);

        $row = $this->fetchrow($select);
        $count = $row->amount;

        if($count == 0){
            return false;
        }else{
            return true;
        }
    }
}

?>
