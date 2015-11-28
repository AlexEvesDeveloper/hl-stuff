<?php
/**
* Model datasource definition for the portfolio table;

* This table holds invidual property records for a portfolio policy
*
*/
class Datasource_Insurance_Portfolio_LegacyProperty extends Zend_Db_Table_Multidb {
	
    protected $_name = 'portfolio';
    protected $_primary = 'ID';
    protected $_multidb = 'db_legacy_homelet';
    /**
    * Fetch a row from the table by id
    * @param int $id Id of record
    *
    * @return Model_Insurance_Portfolio_Property
    */
    public function getRowById ($id) {
        $dataObject = new Model_Insurance_Portfolio_Property();
            $select = $this->select()
            ->from($this->_name)
            ->where('ID = ?', $id);
        $row = $this->fetchRow($select);
        if(!empty($row)){
            $dataObject->id= $row->ID;
            $dataObject->policyNumber = $row->policynumber;
            $dataObject->premium = $row->premium;
            $dataObject->quote = $row->quote;
            $dataObject->ipt = $row->IPT;
            $dataObject->policyOptions = $row->policyoptions;
            $dataObject->amountsCovered = $row->amountscovered;
            $dataObject->optionPremiums = $row->optionpremiums;
            $dataObject->propAddress1 = $row->propaddress1;
            $dataObject->propAddress3 = $row->propaddress3;
            $dataObject->propAddress5 = $row->propaddress5;
            $dataObject->propPostcode = $row->proppostcode;
            $dataObject->riskArea = $row->riskarea;
            $dataObject->discount = $row->discount;
            $dataObject->surcharge = $row->surcharge;
            $dataObject->rateSetId = $row->rateSetID;
            $dataObject->excessId = $row->excessID;
            $dataObject->optionDiscounts = $row->optiondiscounts;
            $dataObject->riskAreaB = $row->riskareab;
            return $dataObject;
        }else{
            return false;
        }
    }
    
    /**
    * Insert a new row into the portfolio table
    *
    * @param Model_Insurance_Portfolio_Portfolio propertyObject The Property to save
    *
    * @return int The last insert ID
    */
    public function save($propertyObject){
        
        $insertArray = array();

        $insertArray['policynumber'] = $propertyObject->policyNumber;
        $insertArray['premium'] = $propertyObject->premium;
        $insertArray['quote'] = $propertyObject->quote;
        $insertArray['IPT'] = $propertyObject->ipt;
        $insertArray['policyoptions'] = $propertyObject->policyOptions;
        $insertArray['amountscovered'] = $propertyObject->amountsCovered;
        $insertArray['optionpremiums'] = $propertyObject->optionPremiums;
        $insertArray['propaddress1'] =  $propertyObject->propAddress1;
        $insertArray['propaddress3'] = $propertyObject->propAddress3;
        $insertArray['propaddress5'] = $propertyObject->propAddress5;
        $insertArray['proppostcode'] = $propertyObject->propPostcode;
        $insertArray['riskarea'] = $propertyObject->riskArea;
        $insertArray['discount'] = $propertyObject->discount;
        $insertArray['surcharge'] = $propertyObject->surcharge;
        $insertArray['rateSetID'] = 0;
        $insertArray['excessID'] = $propertyObject->excessId;
        $insertArray['optiondiscounts'] = $propertyObject->optionDiscounts;
        $insertArray['riskareab'] = $propertyObject->riskAreaB;
      
       if($lastId = $this->insert($insertArray)){
            return $lastId;
        }else{
            // Error
            Application_Core_Logger::log("Could not insert into table {$this->_name}", 'error');
            return false;
        } 
    }
    
    /**
    * Delete a quote by a qiven record id
    * @param int $id Index id of the record to be deleted
    */
    public function deleteById($id){
        $where = $this->quoteInto('ID = ?', $id);
        $this->delete($where);
    }
    
    /**
    * Fetches ALL the properties related by policy number
    * @param string $policynumber Policy number of the portfolio the properties are on
    * @return array An array of object of type Model_Insurance_Portfolio_Property
    */
    public function fetchPropertiesByPolicyNumber($policyNumber){
        $select = $this->select()
            ->from($this->_name)
            ->where('policynumber = ?', $policyNumber);
        $this->setRowClass('Model_Insurance_Portfolio_Property');
        $rows = $this->fetchAll($select);
        if(!empty($rows)){
            return $rows;
        }else{
            return false;
        }
    }
}
?>