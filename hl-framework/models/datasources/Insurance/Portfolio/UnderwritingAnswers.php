<?php
/**
* TODO: Document this
* @param
* @return
* @author John Burrin
* @since1.3
*/
class Datasource_Insurance_Portfolio_UnderwritingAnswers extends Zend_Db_Table_Multidb {
    protected $_name = 'underwritingAnswers';
    protected $_primary = 'answerID';
    protected $_multidb = 'db_portfolio';

    /**
    * Insert a new row into the portfolio table
    *
    * @param Model_Insurance_Portfolio_Portfolio $data The data to be inserted
    *
    * @return bool
    */
    public function save($data){
    	#Zend_Debug::dump($data); die();
        $insertArray = array();

        $insertArray['refNo'] = $data->refNo;
        $insertArray['questionID'] = $data->questionID;
        $insertArray['answerGiven'] = $data->answerGiven;
        $insertArray['dateAnswered'] = $data->dateAnswered;
        
        if($this->_exists($data->refNo,$data->questionID)){     
            return $this->_update($insertArray);
        }else{
        
            return $this->_insert($insertArray);
        }
    }
    
        /**
    * TODO: Document this
    * @param
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
    * TODO: Document this
    * @param 
    * @return
    * @author John Burrin
    */
    private function _update($insertArray){
        $where = $this->quoteInto('refNo = ? AND questionID = ?', $insertArray['refNo'], $insertArray['questionID']);
        if($this->update($insertArray,$where)){
            return true;
        }else{
            return false;
        }
    }
    /**
    * TODO: Document this
    * @param 
    * @return int id of record on success or false on failure
    * @author John Burrin
    */
    private function _exists($refNo, $questionID){
        $where = $this->quoteInto('refNo = ? and questionID = ?', $refNo, $questionID);
        $select = $this->select()
            ->from($this->_name,array('refNo'))
            ->where($where);
        $row = $this->fetchRow($select);
        
        if(!empty($row)){
        	# Zend_Debug::dump($row); die("true");
            return true;
        }
        return false;
    }
    
    public function fetchByRefNo($refNo){
        // Some days I just love the ease of Zend
        $where = $this->getAdapter()->quoteInto('refNo = ?', $refNo);
        $select = $this->select()
            ->from($this->_name)
            ->where($where);
        $row = $this->fetchAll($select);
        return $row;
    }
    
}
?>