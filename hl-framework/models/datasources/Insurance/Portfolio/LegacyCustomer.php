<?php
class Datasource_Insurance_Portfolio_LegacyCustomer extends Zend_Db_Table_Multidb {
    protected $_name = 'portfolio_customer';
    protected $_primary = 'id';
    protected $_multidb = 'db_portfolio';
    
    /**
    * TODO: Document this
    * @param
    * @return
    * @author John Burrin
    * @since
    */
    public function save($data){
        $dataArray = array();
        #$dataArray['id'] = $data->id;
        $dataArray['type_id'] = $data->type_id;
        $dataArray['title'] = $data->title;
        $dataArray['first_name'] = $data->first_name;
        $dataArray['last_name'] = $data->last_name;
        $dataArray['address1'] = $data->address1;
        $dataArray['address2'] = $data->address2;
        $dataArray['address3'] = $data->address3;
        $dataArray['postcode'] = $data->postcode;
        $dataArray['telephone1'] = $data->telephone1;
        $dataArray['telephone2'] = $data->telephone2;
        $dataArray['email_address'] = $data->email_address;
        $dataArray['date_of_birth_at'] = $data->date_of_birth_at;
        $dataArray['password'] = $data->password;
        $dataArray['country'] = $data->country;
        $dataArray['foreign_address'] = $data->foreign_address;
        $dataArray['occupation'] = $data->occupation;
        $dataArray['refNo'] = $data->refNo;
        if($this->_exists($data->refNo)){      
            return $this->_update($dataArray);
        }else{
        
            return $this->_insert($dataArray);
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
        $where = $this->quoteInto('refNo = ?', $insertArray['refNo']);
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
            ->from($this->_name,array('id'))
            ->where('refNo = ?', $refNo);
        $row = $this->fetchRow($select);
          
        if(!empty($row)){
            return true;
        }
        return false;
        
    }

    /**
    * TODO: Document this
    * @param 
    * @return int id of record on success or false on failure
    * @author John Burrin
    */
    public function fetchByRefNo($refNo){
        $select = $this->select()
            ->from($this->_name)
            ->where('refNo = ?', $refNo);
        $row = $this->fetchRow($select);
          
        if(!empty($row)){
            return $row;
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
}
?>