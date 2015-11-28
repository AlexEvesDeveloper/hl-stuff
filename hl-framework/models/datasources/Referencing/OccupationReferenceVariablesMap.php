<?php

/**
* Model definition for the OccupationVariablesMap datasource.
*/
class Datasource_Referencing_OccupationReferenceVariablesMap extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes.
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'occupation_reference_variables_map';
    protected $_primary = array('occupation_id', 'variable_id');
    /**#@-*/
    

    public function upsertVariables($occupationReference) {
    
    	if(empty($occupationReference)) {

    		return;
    	}
    	
    	//Clear the existing occupation reference variables.
        $where = $this->quoteInto('occupation_id = ? ', $occupationReference->occupationId);
        $this->delete($where);
    	
    	//Insert the new occupation reference variables.
        foreach($occupationReference->variables as $key => $value) {
        	
	        $data = array(
	            'occupation_id' => $occupationReference->occupationId,
	            'variable_id' => $key,
	            'value' => $this->_convertValue($key, $value)
	        );
        	$this->insert($data);
        }
    }
    
    
    protected function _convertValue($key, $value) {
    	
    	switch($key) {
    		
    		case Model_Referencing_OccupationReferenceVariables::START_DATE:
    			$convertedValue = $value->toString(Zend_Date::ISO_8601);
                break;
                    
            case Model_Referencing_OccupationReferenceVariables::IS_TITLE_CONFIRMED:
            case Model_Referencing_OccupationReferenceVariables::IS_FULL_TIME:
            case Model_Referencing_OccupationReferenceVariables::IS_EMPLOYED_FOR_NEXT_6MONTHS:
            case Model_Referencing_OccupationReferenceVariables::IS_SERVICE_PROVIDED:
            case Model_Referencing_OccupationReferenceVariables::CAN_MEET_RENT:
            case Model_Referencing_OccupationReferenceVariables::IS_FUTURE_GUARANTEED:
                $convertedValue = ($value) ? 1 : 0;
                break;
                    
            case Model_Referencing_OccupationReferenceVariables::INCOME_AMOUNT:
            case Model_Referencing_OccupationReferenceVariables::AVERAGE_INCOME_AMOUNT:
            case Model_Referencing_OccupationReferenceVariables::GROSS_PENSION:
            	$convertedValue = $value->getValue();
                break;
                    
            case Model_Referencing_OccupationReferenceVariables::CONFIRMED_BY:
            case Model_Referencing_OccupationReferenceVariables::CONTRACT_DURATION:
            case Model_Referencing_OccupationReferenceVariables::DURATION_OF_SERVICE:
            case Model_Referencing_OccupationReferenceVariables::DURATION_OF_OCCUPATION:
            	$convertedValue = $value;
                break;
    	}
    	
    	return $convertedValue;
    }
    
    
    /**
     * Retrieves occupation reference variables linked to an occupation.
     *
     * @param integer $occupationId
     * Identifies the occupation reference variables to retrieve.
     * 
     * @return array
     * An array of occupation reference variables, or null if none found.
     */
    public function getVariables($occupationId) {
        
        $select = $this->select();
        $where = $this->quoteInto('occupation_id = ? ', $occupationId);
        $select->where($where);
        $variableRows = $this->fetchAll($select);
        
        if(count($variableRows) == 0) {
            
            //Return the occupation unchanged.
            return null;
        }
        
        
        //Update the $occupation object with the variables, and return.
        $returnArray = array();
        foreach($variableRows as $row) {
            
            switch($row->variable_id) {
                
                case Model_Referencing_OccupationReferenceVariables::START_DATE:
                	$startDate = new Zend_Date($row->value, Zend_Date::ISO_8601);
                	$returnArray[Model_Referencing_OccupationReferenceVariables::START_DATE] = $startDate;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::IS_TITLE_CONFIRMED:
                	$isTitleConfirmed = ($row->value) ? true : false;
                	$returnArray[Model_Referencing_OccupationReferenceVariables::IS_TITLE_CONFIRMED] = $isTitleConfirmed;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::IS_FULL_TIME:
                	$isFullTime = ($row->value) ? true : false;
                	$returnArray[Model_Referencing_OccupationReferenceVariables::IS_FULL_TIME] = $isFullTime;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::INCOME_AMOUNT:
                	$incomeAmount = new Zend_Currency(array('value' => $row->value, 'precision' => 0));
                	$returnArray[Model_Referencing_OccupationReferenceVariables::INCOME_AMOUNT] = $incomeAmount;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::IS_EMPLOYED_FOR_NEXT_6MONTHS:
                	$isEmployedForNext6Months = ($row->value) ? true : false;
                	$returnArray[Model_Referencing_OccupationReferenceVariables::IS_EMPLOYED_FOR_NEXT_6MONTHS] = $isEmployedForNext6Months;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::CONFIRMED_BY:
                	$returnArray[Model_Referencing_OccupationReferenceVariables::CONFIRMED_BY] = $row->value;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::CONTRACT_DURATION:
                	$returnArray[Model_Referencing_OccupationReferenceVariables::CONTRACT_DURATION] = $row->value;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::IS_SERVICE_PROVIDED:
                	$isServiceProvided = ($row->value) ? true : false;
                	$returnArray[Model_Referencing_OccupationReferenceVariables::IS_SERVICE_PROVIDED] = $isServiceProvided;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::DURATION_OF_SERVICE:
                	$returnArray[Model_Referencing_OccupationReferenceVariables::DURATION_OF_SERVICE] = $row->value;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::CAN_MEET_RENT:
                	$canMeetRent = ($row->value) ? true : false;
                	$returnArray[Model_Referencing_OccupationReferenceVariables::CAN_MEET_RENT] = $canMeetRent;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::IS_FUTURE_GUARANTEED:
                	$isFutureGuaranteed = ($row->value) ? true : false;
                	$returnArray[Model_Referencing_OccupationReferenceVariables::IS_FUTURE_GUARANTEED] = $isFutureGuaranteed;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::AVERAGE_INCOME_AMOUNT:
                	$averageIncomeAmount = new Zend_Currency(array('value' => $row->value, 'precision' => 0));
                	$returnArray[Model_Referencing_OccupationReferenceVariables::AVERAGE_INCOME_AMOUNT] = $averageIncomeAmount;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::GROSS_PENSION:
                	$grossPension = new Zend_Currency(array('value' => $row->value, 'precision' => 0));
                	$returnArray[Model_Referencing_OccupationReferenceVariables::GROSS_PENSION] = $grossPension;
                    break;
                    
                case Model_Referencing_OccupationReferenceVariables::DURATION_OF_OCCUPATION:
                	$returnArray[Model_Referencing_OccupationReferenceVariables::DURATION_OF_OCCUPATION] = $row->value;
                    break;
            }
        }
        
        return $returnArray;
    }
    
    
    /**
     * Deletes an existing occupation variable.
     *
     * @param integer $occupationId
     * Identifies the Occupation who's variable we are to delete.
     *
     * @param integer $variableId
     * Identifies the occupation variable to delete.
     *
     * @return void
     */
    public function deleteVariable($occupationId, $variableId) {
        
        $where = $this->quoteInto('occupation_id = ? AND variable_id = ? ', $occupationId, $variableId);
        $this->delete($where);
    }
    
    
    /**
     * Deletes all occupation variables stored against a particular occupation.
     *
     * @param integer $occupationId
     * Identifies the linked occupation variables to delete.
     *
     * @return void
     */
    public function deleteAllVariables($occupationId) {
        
        $where = $this->quoteInto('occupation_id = ? ', $occupationId);
        $this->delete($where);
    }
}

?>