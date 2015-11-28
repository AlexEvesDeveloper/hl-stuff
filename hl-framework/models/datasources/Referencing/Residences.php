<?php

/**
* Model definition for the residences datasource.
*/
class Datasource_Referencing_Residences extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_referencing';
    protected $_name = 'residence';
    protected $_primary = 'id';
    
    protected $_addressDatasource;
    
    protected function _loadSources() {
    	
    	if(empty($this->_addressDatasource)) {
    		
			$this->_addressDatasource = new Datasource_Core_Addresses();
		}
    }

	/**
     * Creates a new, empty Residence and corresponding record in the datasource.
     *
     * @param integer $referenceId
     * The unique Reference identifier. This is used to link the new Residence
     * record to the Reference record.
     *
     * @param integer $chronology
     * Indicates the residential chronology. Must corresond to one of the consts
     * exposed by the Model_Referencing_ResidentialChronology class.
     *
     * @return Model_Referencing_Residence
     * Returns the newly created, empty Residence.
     */
    public function insertPlaceholder($referenceId, $chronology) {
    
	    $id = $this->insert(array('reference_id' => $referenceId, 'chronology_id' => $chronology));
		
        $residence = new Model_Referencing_Residence();
		$residence->id = $id;
		$residence->referenceId = $referenceId;
		$residence->chronology = $chronology;
		
		$addressManager = new Manager_Core_Address();
		$residence->address = $addressManager->createAddress();
        return $residence;
    }
    
    /**
     * Updates an existing residence in the datasource.
     *
     * @param Model_Referencing_Residence
     * The residence to update in the datasource.
     *
     * @return void
     */
    public function updateResidence($residence) {
        
		if(empty($residence)) {
			
			return;
		}
		
		$this->_loadSources();

		$data = array(
			'reference_id' => $residence->referenceId,
			'chronology_id' => $residence->chronology,
			'address_id' => empty($residence->address->id) ? null : $residence->address->id,
            'duration_in_months' => $residence->durationAtAddress,
			'status_id' => $residence->status
        );
        
        $where = $this->quoteInto('id =? ', $residence->id);
        $this->update($data, $where);
		
		//Update linked type.
        $this->_addressDatasource->updateAddress($residence->address);
    }
    
    /**
     * Retrieves all residence details against a specific Reference.
     *
     * @param integer $referenceId
     * The unique internal Reference identifier.
     *
     * @return mixed
     * An array of Model_Referencing_Residence objects, or null if no
     * residences found.
     */
    public function getByReferenceId($referenceId) {

		$this->_loadSources();
    	
    	$select = $this->select();
		$select->where('reference_id = ?', $referenceId);
		$residenceRows = $this->fetchAll($select);
		
		$returnArray = array();		
		foreach($residenceRows as $residenceRow) {

			$residence = new Model_Referencing_Residence();
			$residence->id = $residenceRow->id;
			$residence->referenceId = $residenceRow->reference_id;
			$residence->chronology = $residenceRow->chronology_id;
			$residence->address = $this->_addressDatasource->getById($residenceRow->address_id);
			$residence->durationAtAddress = $residenceRow->duration_in_months;
			$residence->status = $residenceRow->status_id;			
			$returnArray[] = $residence;
		}
		
		if(empty($returnArray)) {
			
			$returnVal = null;
		}
		else {

			$returnVal = $returnArray;
		}
		return $returnVal;
    }
	
	/**
     * Deletes an existing Residence.
     *
     * @param Model_Referencing_Residence
     * The Residence to delete.
     *
     * @return void
     */
	public function deleteResidence($residence) {
		
		$where = $this->quoteInto('id = ? ', $residence->id);
        $this->delete($where);
	}
}

?>