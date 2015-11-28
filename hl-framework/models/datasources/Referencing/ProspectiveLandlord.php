<?php

/**
* Model definition for the ProspectiveLandlord datasource.
*/
class Datasource_Referencing_ProspectiveLandlord extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_referencing';
    protected $_name = 'prospective_landlord';
    protected $_primary = 'id';
    
    protected $_addressDatasource;
    protected $_nameDatasource;
    protected $_contactDatasource;
    
    protected function _loadSources() {
    
    	if(empty($this->_addressDatasource)) {
    		
    		$this->_addressDatasource = new Datasource_Core_Addresses();
    	}
        
    	if(empty($this->_nameDatasource)) {
        
    		$this->_nameDatasource = new Datasource_Core_Names();
    	}
        
    	if(empty($this->_contactDatasource)) {
    		
        	$this->_contactDatasource = new Datasource_Core_ContactDetails();
    	}
    }
    
    /**
     * Creates a new, empty ProspectiveLandlord and corresponding record in the datasource.
     *
     * @param integer $referenceId
     * Links the new ProspectiveLandlord to the Reference.
     *
     * @return Model_Referencing_ProspectiveLandlord
     * Returns the newly created, empty ProspectiveLandlord.
     */
    public function insertPlaceholder($referenceId) {
    
        $prospectiveLandlordId = $this->insert(array());
        if (empty($prospectiveLandlordId)) {
            
            // Failed insertion
            throw new Zend_Exception("Can't create record in table {$this->_name}");
        }
        else {
         
            $prospectiveLandlord = new Model_Referencing_ProspectiveLandlord();
            $prospectiveLandlord->id = $prospectiveLandlordId;
            $returnVal = $prospectiveLandlord;
        }
        
        return $returnVal;
    }
    
    /**
     * Updates an existing PropertyLandlord.
     *
     * @param Model_Referencing_ropertyLandlord
     * The ropertyLandlord details to update in the datasource.
     *
     * @return void
     */
    public function updateProspectiveLandlord($prospectiveLandlord) {        
        
        $this->_loadSources();
        
        //Updated linked types.
        $this->_addressDatasource->updateAddress($prospectiveLandlord->address);
        $this->_nameDatasource->updateName($prospectiveLandlord->name);
        $this->_contactDatasource->updateContactDetails($prospectiveLandlord->contactDetails);
        
        
        //Update this datasource.
        $data = array(
            'name_id' => empty($prospectiveLandlord->name) ? null : $prospectiveLandlord->name->id,
            'address_id' => empty($prospectiveLandlord->address) ? null : $prospectiveLandlord->address->id,
            'contact_id' => empty($prospectiveLandlord->contactDetails) ? null : $prospectiveLandlord->contactDetails->id
        );

        $where = $this->quoteInto('id = ?', $prospectiveLandlord->id);
        $this->update($data, $where);
    }
    
    /**
     * Retrieves the specified ProspectiveLandlord.
     *
     * @param integer $prospectiveLandlordId
     * The unique ProspectiveLandlord identifier.
     *
     * @return mixed
     * The ProspectiveLandlord details, encapsulated in a Model_Referencing_ProspectiveLandlord
     * object, or null if the ProspectiveLandlord cannot be found.
     */
    public function getByProspectiveLandlordId($prospectiveLandlordId) {
        
        $this->_loadSources();
        
    	$select = $this->select();
        $select->where('id = ?', $prospectiveLandlordId);
        $prospectiveLandlordRow = $this->fetchRow($select);
        
        if(!empty($prospectiveLandlordRow)) {

            $prospectiveLandlord = new Model_Referencing_ProspectiveLandlord();
            $prospectiveLandlord->id = $prospectiveLandlordRow->id;
            
            if(!empty($prospectiveLandlordRow->name_id)) {
                
                $prospectiveLandlord->name = $this->_nameDatasource->getById($prospectiveLandlordRow->name_id);
            }
            
            if(!empty($prospectiveLandlordRow->address_id)) {
                
                $prospectiveLandlord->address = $this->_addressDatasource->getById($prospectiveLandlordRow->address_id);
            }
            
            if(!empty($prospectiveLandlordRow->contact_id)) {
                
                $prospectiveLandlord->contactDetails = $this->_contactDatasource->getById($prospectiveLandlordRow->contact_id);
            }
            
            $returnVal = $prospectiveLandlord;
        }
        else {
            
            $returnVal = null;
        }

        return $returnVal;
    }
}

?>