<?php
/**
* Model datasource definition for the portfolio table;

* This table holds invidual property records for a portfolio policy
*
*/
class Datasource_Insurance_Portfolio_Property extends Zend_Db_Table_Multidb {
    protected $_name = 'portfolio_properties';
    protected $_primary = 'id';
    protected $_multidb = 'db_portfolio';
    /**
    * Fetch a row from the table by id
    * @param int $id Id of record
    *
    * @return Model_Insurance_Portfolio_Property
    */
    public function getRowById ($id) {
        $dataObject = new Model_Insurance_Portfolio_Property();
        $fields = array(
            'id' => 'id',
            'refno' => 'refno',
            #'houseNumber' => 'houseNumber',
            #'building' => 'building',
            'address1' => 'address1',
            'address2' => 'address2',
            'address3' => 'address3',
            #'address4' => 'address4',
           # 'address5' => 'address5',
            'postcode' => 'postcode',
            'tenantOccupation' => 'tenantOccupation',
            'buildingsSumInsured' => 'buildingsSumInsured',
            'buildingsAccidentalDamage' => new Zend_Db_Expr('IF(buildingsAccidentalDamage = 1 ,"Yes","No")'),
            'buildingsNilExcess' => new Zend_Db_Expr('IF(buildingsNilExcess = 1 ,"Yes","No")'),
            'contentsSumInsured' => 'contentsSumInsured',
            'contentsAccidentalDamage' => new Zend_Db_Expr('IF(contentsAccidentalDamage = 1 ,"Yes","No")'),
            'contentsNilExcess' => new Zend_Db_Expr('IF(contentsNilExcess = 1 ,"Yes","No")'),
            'limitedContents' => new Zend_Db_Expr('IF(limitedContents = 1 ,"Yes","No")'),
			'riskAreaB' => 'buildingsRiskArea',
			'riskArea' => 'contentsRiskArea'
            );
            $select = $this->select()
            ->from($this->_name, $fields)
            ->where('id = ?', $id);
        $row = $this->fetchRow($select);
        if(!empty($row)){
            $dataObject->id = $row->id;
            $dataObject->refno = $row->refno;
            #$dataObject->houseNumber = $row->houseNumber;
           # $dataObject->building = $row->building;
            $dataObject->address1 = $row->address1;
            $dataObject->address2 = $row->address2;
            $dataObject->address3 = $row->address3;
           # $dataObject->address4 = $row->address4;
           # $dataObject->address5 = $row->address5;
            $dataObject->postcode = $row->postcode;
            $dataObject->tenantOccupation = $row->tenantOccupation;
            $dataObject->buildingsSumInsured = $row->buildingsSumInsured;
            $dataObject->buildingsAccidentalDamage = $row->buildingsAccidentalDamage;
            $dataObject->buildingsNilExcess = $row->buildingsNilExcess;
            $dataObject->contentsSumInsured = $row->contentsSumInsured;
            $dataObject->contentsAccidentalDamage = $row->contentsAccidentalDamage;
            $dataObject->contentsNilExcess = $row->contentsNilExcess;
            $dataObject->limitedContents = $row->limitedContents;
			$dataObject->buildingsRiskArea = $row->riskAreaB;
			$dataObject->contentsRiskArea = $row->riskArea;

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
        // if we have an ID we must be doing an update else do an insert       
        if(isset($propertyObject->id) && $propertyObject->id !="" ){
            return $this->_doUpdate($propertyObject);
        }else{
            return $this->_doInsert($propertyObject);
        }

    }

    private function _doUpdate($propertyObject){
       # $updateArray['houseNumber'] = $propertyObject->houseNumber;
       # $updateArray['building'] = $propertyObject->building;
        $updateArray['address1'] = $propertyObject->address1;
        $updateArray['address2'] = $propertyObject->address2;
        $updateArray['address3'] = $propertyObject->address3;
        #$updateArray['address4'] = $propertyObject->address4;
       # $updateArray['address5'] = $propertyObject->address5;
        $updateArray['postcode'] = $propertyObject->postcode;
        $updateArray['refno'] = $propertyObject->refno;
        $updateArray['tenantOccupation'] = $propertyObject->tenantOccupation;
        $updateArray['buildingsSumInsured'] = $propertyObject->buildingsSumInsured;
        $updateArray['buildingsAccidentalDamage'] = $propertyObject->buildingsAccidentalDamage;
        $updateArray['buildingsNilExcess'] = $propertyObject->buildingsNilExcess;
        $updateArray['contentsSumInsured'] = $propertyObject->contentsSumInsured;
        $updateArray['contentsAccidentalDamage'] =  $propertyObject->contentsAccidentalDamage;
        $updateArray['contentsNilExcess'] = $propertyObject->contentsNilExcess;
        $updateArray['limitedContents'] = $propertyObject->limitedContents;
		$updateArray['buildingsRiskArea'] = $propertyObject->buildingsRiskArea;
		$updateArray['contentsRiskArea'] = $propertyObject->contentsRiskArea;


        $where = $this->quoteInto('refno = ? and id = ?', $propertyObject->refno, $propertyObject->id);
        if($this->update($updateArray,$where)){
            return true;
        }else{
            return false;
        }
    }

    private function _doInsert($propertyObject){
        $insertArray = array();
        #$insertArray['houseNumber'] = $propertyObject->houseNumber;
        #$insertArray['building'] = $propertyObject->building;
        $insertArray['address1'] = $propertyObject->address1;
        $insertArray['address2'] = $propertyObject->address2;
        $insertArray['address3'] = $propertyObject->address3;
        #$insertArray['address4'] = $propertyObject->address4;
        #$insertArray['address5'] = $propertyObject->address5;
        $insertArray['postcode'] = $propertyObject->postcode;
        $insertArray['refno'] = $propertyObject->refno;
        $insertArray['tenantOccupation'] = $propertyObject->tenantOccupation;
        $insertArray['buildingsSumInsured'] = $propertyObject->buildingsSumInsured;
        $insertArray['buildingsAccidentalDamage'] = $propertyObject->buildingsAccidentalDamage;
        $insertArray['buildingsNilExcess'] = $propertyObject->buildingsNilExcess;
        $insertArray['contentsSumInsured'] = $propertyObject->contentsSumInsured;
        $insertArray['contentsAccidentalDamage'] =  $propertyObject->contentsAccidentalDamage;
        $insertArray['contentsNilExcess'] = $propertyObject->contentsNilExcess;
        $insertArray['limitedContents'] = $propertyObject->limitedContents;
		$insertArray['buildingsRiskArea'] = $propertyObject->buildingsRiskArea;
		$insertArray['contentsRiskArea'] = $propertyObject->contentsRiskArea;

       if($lastId = $this->insert($insertArray)){
            return $lastId;
        }else{
            // Error
            Application_Core_Logger::log("Could not insert into table {$this->_name}", 'Error');
            return false;
        }
    }
    /**
    * Delete a quote by a qiven record id
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
    * Fetches ALL the properties related by customer refno
    * @param string $policynumber Policy number of the portfolio the properties are on
    * @return array An array of object of type Model_Insurance_Portfolio_Property
    */
    public function fetchPropertiesByrefNo($refNo){
        $fields = array(
            'id' => 'id',
            'refno' => 'refno',
            #'houseNumber' => 'houseNumber',
            #'building' => 'building',
            'address1' => 'address1',
            'address2' => 'address2',
            'address3' => 'address3',
            #'address4' => 'address4',
            #'address5' => 'address5',
            'postcode' => 'postcode',
            'tenantOccupation' => 'tenantOccupation',
            'buildingsSumInsured' => 'buildingsSumInsured',
            'buildingsAccidentalDamage' => new Zend_Db_Expr('IF(buildingsAccidentalDamage = 1 ,"Yes","No")'),
            'buildingsNilExcess' => new Zend_Db_Expr('IF(buildingsNilExcess = 1 ,"Yes","No")'),
            'contentsSumInsured' => 'contentsSumInsured',
            'contentsAccidentalDamage' => new Zend_Db_Expr('IF(contentsAccidentalDamage = 1 ,"Yes","No")'),
            'contentsNilExcess' => new Zend_Db_Expr('IF(contentsNilExcess = 1 ,"Yes","No")'),
            'limitedContents' => new Zend_Db_Expr('IF(limitedContents = 1 ,"Yes","No")'),
			'riskAreaB' => 'buildingsRiskArea',
			'riskArea' => 'contentsRiskArea'
            );
        $select = $this->select()
            ->from($this->_name,$fields )
            ->where('refno = ?', $refNo);
        $rows = $this->fetchAll($select);
        if(!empty($rows)){
            return $rows;
        }else{
            return false;
        }
    }
}
?>
