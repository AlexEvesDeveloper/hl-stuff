<?php

/**
* Model definition for the occupations datasource.
*/
class Datasource_Referencing_Occupations extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_referencing';
    protected $_name = 'occupation';
    protected $_primary = 'id';
    
	/**
     * Creates a new, empty occupation and corresponding record in the datasource.
     *
     * @param integer $referenceId
     * The unique Reference identifier. This is used to link the new occupation
     * record to the Reference record.
     *
     * @param integer $chronology
     * Indicates the occupational chronology. Must corresond to one of the consts
     * exposed by the Model_Referencing_OccupationalChronology class.
     *
     * @param integer $classification
	 * Indicates the occupational classification. Must corresond to one of the consts
     * exposed by the Model_Referencing_OccupationalClassifiers class.
     *
     * @return Model_Referencing_Occupation
     * Returns the newly created, empty Occupation.
     */
    public function createNewOccupation($referenceId, $chronology, $classification) {
	
	    $id = $this->insert(array(
			'reference_id' => $referenceId,
			'chronology_id' => $chronology,
			'importance_id' => $classification));
		
        $occupation = new Model_Referencing_Occupation();
		$occupation->id = $id;
		$occupation->referenceId = $referenceId;
		$occupation->chronology = $chronology;
		$occupation->importance = $classification;
        return $occupation;
    }
    
    /**
     * Updates an existing occupation in the datasource.
     *
     * @param Model_Referencing_Occupation
     * The occupation to update in the datasource.
     *
     * @return void
     */
    public function updateOccupation($occupation) {
        
		if(empty($occupation)) {
			
			return;
		}		
		
		//Translate 'isPermanent'
		if(empty($occupation->isPermanent)) {
			
			$isPermanent = 0;
		}
		else {
			
			if($occupation->isPermanent) {
				
				$isPermanent = 1;
			}
			else {
				
				$isPermanent = 0;
			}
		}
		
		//Update...
		$data = array(
			'reference_id' => $occupation->referenceId,
			'importance_id' => $occupation->importance,
			'chronology_id' => $occupation->chronology,
            'type_id' => empty($occupation->type) ? null : $occupation->type,
            'is_permanent' => $isPermanent,
			'income' => empty($occupation->income) ? null : $occupation->income->getValue(),
			'start_date' => empty($occupation->startDate) ? null : $occupation->startDate->toString(Zend_Date::ISO_8601)
        );
        
        $where = $this->quoteInto('id = ? ', $occupation->id);
        $this->update($data, $where);
		
		
		//Now update the linked data.
		$occupationVariables = new Datasource_Referencing_OccupationVariablesMap();
		if(!empty($occupation->variables)) {
			
			$partId = Model_Referencing_OccupationVariables::ENDDATE;
			if(array_key_exists($partId, $occupation->variables)) {
				
				$endDate = $occupation->variables[$partId];
				$partValue = $endDate->toString(Zend_Date::ISO_8601);
				$occupationVariables->upsertVariable($occupation->id, $partId, $partValue);
			}
			else {
				
				//Delete any existing details.
				$occupationVariables->deleteVariable($occupation->id, $partId);
			}
			
			
			$partId = Model_Referencing_OccupationVariables::POSITION;
			$partValue = $occupation->variables[$partId];
			if(!empty($partValue)) {
				
				$occupationVariables->upsertVariable($occupation->id, $partId, $partValue);
			}
			else {
				
				//Delete any existing details.
				$occupationVariables->deleteVariable($occupation->id, $partId);
			}
			
			
			$partId = Model_Referencing_OccupationVariables::PAYROLL_NUMBER;
			$partValue = $occupation->variables[$partId];
			if(!empty($partValue)) {
				
				$occupationVariables->upsertVariable($occupation->id, $partId, $partValue);
			}
			else {
				
				//Delete any existing details.
				$occupationVariables->deleteVariable($occupation->id, $partId);
			}
		}
		else {
			
			$occupationVariables->deleteAllVariables($occupation->id);
		}
    }
    
    /**
     * Retrieves all occupation details against a specific Reference.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return mixed
     * An array of Model_Referencing_Occupation objects, or null if no
     * occuapations are found.
     */
    public function getByReferenceId($referenceId) {
        
        $select = $this->select();
		$select->where('reference_id = ?', $referenceId);
		$occupationRows = $this->fetchAll($select);
		
		$returnArray = array();
		$occupationVariables = new Datasource_Referencing_OccupationVariablesMap();
		$occupationRefereeDatasource = new Datasource_Referencing_OccupationReferees();
		foreach($occupationRows as $occupationRow) {
			
			$occupation = new Model_Referencing_Occupation();
			$occupation->id = $occupationRow->id;
			$occupation->referenceId = $occupationRow->reference_id;
			$occupation->chronology = $occupationRow->chronology_id;
			$occupation->importance = $occupationRow->importance_id;
			$occupation->type = $occupationRow->type_id;
			
			//Convert binary value into boolean
			if(empty($occupationRow->is_permanent)) {
				
				$occupation->isPermanent = false;
			}
			else {
				
				$occupation->isPermanent = true;
			}
			
			//Put the income into a Zend_Currency
			if(!empty($occupationRow->income)) {
			
				$occupation->income = new Zend_Currency(
					array(
						'value' => $occupationRow->income,
						'precision' => 0
					)
				);
			}
			else {
				
				$occupation->income = new Zend_Currency(
					array(
						'value' => 0,
						'precision' => 0
					)
				);
			}
			
			//Put the start and end dates into Zend_Dates.
			if(!empty($occupationRow->start_date)) {
				
				$occupation->startDate = new Zend_Date($occupationRow->start_date, Zend_Date::ISO_8601);
			}
			
			
			//Retrieve the occupation referee, if one exists.
			$occupation->refereeDetails = $occupationRefereeDatasource->getByOccupationId($occupationRow->id);
			
			
			//Retrieve the remaining details from the occupation variables datasource
			$occupation->variables = $occupationVariables->getVariables($occupation->id);
			$returnArray[] = $occupation;
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
     * Deletes an existing Occupation and all linked records.
     *
     * @param Model_Referencing_Occupation
     * The Occupation to delete.
     *
     * @return void
     */
	public function deleteOccupation($occupation) {
		
		//First delete the main occupation record.
		$where = $this->quoteInto('id = ? ', $occupation->id);
        $this->delete($where);
		
		
		//Next delete the linked occupation_variables records
		$occupationVariables = new Datasource_Referencing_OccupationVariablesMap();
		$occupationVariables->deleteAllVariables($occupation->id);
	}
}

?>