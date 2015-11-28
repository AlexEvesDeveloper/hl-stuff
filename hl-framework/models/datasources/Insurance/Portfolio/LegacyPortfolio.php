<?php
/**
* Model definition for the table portfoliostat table
* This table IS the portfolio quote,
 and is link to the portfolio table by refno
* 
* No I don't like its name either
*/
class Datasource_Insurance_Portfolio_LegacyPortfolio extends Zend_Db_Table_Multidb {
    protected $_name = 'portfoliostat';
    protected $_primary = 'ID';
    protected $_multidb = 'db_legacy_homelet';

    /**
    * Fetch a row from the table by id
    * @param int $id Id of record
    *
    * @return Model_Insurance_Portfolio_Portfolio
    */
    public function getRowById ($id) {
       $dataObject = new Model_Insurance_Portfolio_LegacyPortfolio();
            $select = $this->select()
            ->from($this->_name)
            ->where('ID = ?', $id);
        $row = $this->fetchRow($select);
        if(!empty($row)){
            $dataObject->id = $row->ID;
            $dataObject->refNo = $row->refno;
            $dataObject->quote = $row->quote;
            $dataObject->date = $row->date;
            $dataObject->policyNumber = $row->policynumber;
            $dataObject->agentSchemeNo = $row->agentschemeno;
            $dataObject->csuId = $row->csuid;
            $dataObject->name = $row->name;
            $dataObject->email = $row->email;
            $dataObject->telephone = $row->telephone;
            $dataObject->numOfHouse = $row->numOfHouse;
            $dataObject->heardFrom = $row->heardfrom;
            $dataObject->referred = $row->referred;
            $dataObject->hpc = $row->HPC;
            $dataObject->customerRefNo = $row->customerrefno;
            return $dataObject;
        }else{
            return false;
        }
    }

    /**
    * Fetch a row from the table by refNo
    * @param String $refNo refNo of record
    *
    * @return Model_Insurance_Portfolio_Portfolio
    */
    public function getRowByRefNo ($refNo) {
        $dataObject = new Model_Insurance_Portfolio_LegacyPortfolio();
            $select = $this->select()
            ->from($this->_name)
            ->where('refno = ?', $refNo);

        $row = $this->fetchRow($select);
        if(!empty($row)){
            $dataObject->id = $row->ID;
            $dataObject->refNo = $row->refno;
            $dataObject->quote = $row->quote;
            $dataObject->date = $row->date;
            $dataObject->policyNumber = $row->policynumber;
            $dataObject->agentSchemeNo = $row->agentschemeno;
            $dataObject->csuId = $row->csuid;
            $dataObject->name = $row->name;
            $dataObject->email = $row->email;
            $dataObject->telephone = $row->telephone;
            $dataObject->numOfHouse = $row->numOfHouse;
            $dataObject->heardFrom = $row->heardfrom;
            $dataObject->referred = $row->referred;
            $dataObject->hpc = $row->HPC;
            $dataObject->customerRefNo = $row->customerrefno;
            return $dataObject;
        }else{
            return false;
        }
    }

    /**
    * Fetch a row from the table by Customer refNo
    * @param String $refNo refNo of record
    *
    * @return Model_Insurance_Portfolio_Portfolio
    */
    public function getRowByCustomerRefNo ($refNo) {
        $dataObject = new Model_Insurance_Portfolio_LegacyPortfolio();
            $select = $this->select()
            ->from($this->_name)
            ->where('customerrefno = ?', $refNo);

        $row = $this->fetchRow($select);
        if(!empty($row)){
            $dataObject->id = $row->ID;
            $dataObject->refNo = $row->refno;
            $dataObject->quote = $row->quote;
            $dataObject->date = $row->date;
            $dataObject->policyNumber = $row->policynumber;
            $dataObject->agentSchemeNo = $row->agentschemeno;
            $dataObject->csuId = $row->csuid;
            $dataObject->name = $row->name;
            $dataObject->email = $row->email;
            $dataObject->telephone = $row->telephone;
            $dataObject->numOfHouse = $row->numOfHouse;
            $dataObject->heardFrom = $row->heardfrom;
            $dataObject->referred = $row->referred;
            $dataObject->hpc = $row->HPC;
            $dataObject->customerRefNo = $row->customerrefno;
            return $dataObject;
        }else{
            return false;
        }
    }

    
    /**
    * Insert a new row into the portfolio table
    *
    * @param Model_Insurance_Portfolio_Portfolio $data The data to be inserted
    *
    * @return bool
    */
    public function save($data){
        $insertArray = array();
        $insertArray['id'] = $data->id;
        $insertArray['refno'] = $data->refNo;
        $insertArray['quote'] = $data->quote;
        $insertArray['date'] = $data->date;
        $insertArray['policynumber'] = $data->policyNumber;
        $insertArray['agentschemeno'] = $data->agentSchemeNo;
        $insertArray['csuid'] = $data->csuId;
        $insertArray['name'] = $data->name;
        $insertArray['email'] = $data->email;
        $insertArray['telephone'] = $data->telephone;
        $insertArray['numOfHouse'] = $data->numOfHouse;
        $insertArray['heardfrom'] = $data->heardFrom;
        $insertArray['referred'] = $data->referred;
        $insertArray['HPC'] = $data->hpc;
        $insertArray['customerrefno'] = $data->customerRefNo;
        
        if($this->_exists($data->refNo)){     
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
        $where = $this->quoteInto('ID = ?', $insertArray['id']);
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
    private function _exists($refNo){
        $select = $this->select()
            ->from($this->_name,array('ID'))
            ->where('refno= ?', $refNo);
        $row = $this->fetchRow($select);
        
        if(!empty($row)){
            return true;
        }
        return false;
        
    }

    /**
    * TODO: Document this
    * @param
    * @return
    * @author John Burrin
    */
    public function deleteById($id){
        $where = $this->quoteInto('ID = ?', $id);
        $this->delete($where);
    }
}
?>