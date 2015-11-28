<?php
/**
* Model definition for the table portfolio_properties table
* This table is the portfolio quote,
 and is link to the customer table by refno
* 
* No I don't like its name either
*/
class Datasource_Insurance_Portfolio_Portfolio extends Zend_Db_Table_Multidb {
    protected $_name = 'portfolio_properties';
    protected $_primary = 'id';
    protected $_multidb = 'db_portfolio';

    /**
    * Fetch a row from the table by id
    * @param int $id Id of record
    *
    * @return Model_Insurance_Portfolio_Portfolio
    */
    public function getRowById ($id) {
       $dataObject = new Model_Insurance_Portfolio_Portfolio();
            $select = $this->select()
            ->from($this->_name)
            ->where('id = ?', $id);
        $row = $this->fetchRow($select);

        if(!empty($row)){
            $dataObject->id = $row->id;
            $dataObject->refNo = $row->refno;
            $dataObject->building = $row->building;
            $dataObject->postcode = $row->postcode;
            $dataObject->tenantOccupation = $row->tenantOccupation;
            $dataObject->buildingsSumInsured = $row->buildingsSumInsured;
            $dataObject->buildingsAccidentalDamage = $row->buildingsAccidentalDamage;
            $dataObject->buildingsNilExcess = $row->buildingsNilExcess;
            $dataObject->contentsSumInsured = $row->contentsSumInsured;
            $dataObject->contentsAccidentalDamage = $row->contentsAccidentalDamage;
            $dataObject->contentsNilExcess = $row->contentsNilExcess;
            $dataObject->limitedContents = $row->limitedContents;

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
        $dataObject = new Model_Insurance_Portfolio_Portfolio();
            $select = $this->select()
            ->from($this->_name)
            ->where('refno = ?', $refNo);

        $row = $this->fetchRow($select);
        if(!empty($row)){
            $dataObject->id = $row->id;
            $dataObject->refNo = $row->refno;
            $dataObject->building = $row->building;
            $dataObject->postcode = $row->postcode;
            $dataObject->tenantOccupation = $row->tenantOccupation;
            $dataObject->buildingsSumInsured = $row->buildingsSumInsured;
            $dataObject->buildingsAccidentalDamage = $row->buildingsAccidentalDamage;
            $dataObject->buildingsNilExcess = $row->buildingsNilExcess;
            $dataObject->contentsSumInsured = $row->contentsSumInsured;
            $dataObject->contentsAccidentalDamage = $row->contentsAccidentalDamage;
            $dataObject->contentsNilExcess = $row->contentsNilExcess;
            $dataObject->limitedContents = $row->limitedContents;
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
        $dataObject = new Model_Insurance_Portfolio_Portfolio();
            $select = $this->select()
            ->from($this->_name)
            ->where('customerrefno = ?', $refNo);

        $row = $this->fetchRow($select);
        if(!empty($row)){
            $dataObject->id = $row->id;
            $dataObject->refNo = $row->refno;
            $dataObject->building = $row->building;
            $dataObject->postcode = $row->postcode;
            $dataObject->tenantOccupation = $row->tenantOccupation;
            $dataObject->buildingsSumInsured = $row->buildingsSumInsured;
            $dataObject->buildingsAccidentalDamage = $row->buildingsAccidentalDamage;
            $dataObject->buildingsNilExcess = $row->buildingsNilExcess;
            $dataObject->contentsSumInsured = $row->contentsSumInsured;
            $dataObject->contentsAccidentalDamage = $row->contentsAccidentalDamage;
            $dataObject->contentsNilExcess = $row->contentsNilExcess;
            $dataObject->limitedContents = $row->limitedContents;
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
        $insertArray['refno'] = $data->refno;
        $insertArray['building'] = $data->building;
        $insertArray['postcode'] = $data->postcode;
        $insertArray['tenantOccupation'] = $data->tenantOccupation;
        $insertArray['buildingsSumInsured'] = $data->buildingsSumInsured;
        $insertArray['buildingsAccidentalDamage'] = $data->buildingsAccidentalDamage;
        $insertArray['buildingsNilExcess'] = $data->buildingsNilExcess;
        $insertArray['contentsSumInsured'] = $data->contentsSumInsured;
        $insertArray['contentsAccidentalDamage'] = $data->contentsAccidentalDamage;
        $insertArray['contentsNilExcess'] = $data->contentsNilExcess;
        $insertArray['limitedContents'] = $data->limitedContents;
        
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
            ->where('ID = ?', $refNo);
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