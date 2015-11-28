<?php

/**
* Model definition for the occupational references datasource.
*/
class Datasource_ReferencingLegacy_OccupationalReferences extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'employment';
    protected $_primary = 'refno';
    /**#@-*/
    
    
    /**
     * Not implemented in this release.
     */
    public function insertOccupationalReference($id) {
    
        throw new Zend_Exception(get_class() . __FUNCTION__ . ': not implemented in this release.');
    }
    
    
    /**
     * Not implemented in this release.
     */
    public function updateOccupationalReference($id) {
        
        throw new Zend_Exception(get_class() . __FUNCTION__ . ': not implemented in this release.');
    }
    
    
    /**
     * Retrieves the occupational reference for a specific occupation.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @param integer $chronology
     * The occupation chronology. Must correspond to one of the consts exposed by
     * the Model_Referencing_OccupationalChronology class.
     * 
     * @param integer $importance
     * The occupation importance relative to the other occupations in the chronology.
     * Must correspond to one of the consts in the Model_Referencing_OccupationImportance class.
     *
     * @return mixed
     * Returns a Model_Referencing_OccupationalReference object, or null if no
     * occupational reference is found.
     */
    public function getByEnquiry($enquiryId, $chronology, $importance = Model_Referencing_OccupationImportance::FIRST) {	
		
		//Now identify the occupations recorded in the employment table.
		if($importance == Model_Referencing_OccupationImportance::FIRST) {

			switch($chronology) {
				
				case Model_Referencing_OccupationChronology::CURRENT:
					$jobStatus = 'Current';
					break;
				case Model_Referencing_OccupationChronology::FUTURE:
					$jobStatus = 'Future';
					break;
			}
			
			$select = $this->select();
			$select->where('enquiryRefno = ?', $enquiryId);
			$occupationalReferenceRow = $this->fetchRow($select);
			$returnVal = $this->_createDomainObject($occupationalReferenceRow);
		}
		else {
			
			//If here then the occupation is a SECOND occupation.
			if($chronology == Model_Referencing_OccupationChronology::CURRENT) {
				
				//Jump through hoops to support the god-awful legacy datasource.
				$jobStatus = 'Second';
			}
			else {
				
				//Future, Previous
				$jobStatus = 'Future';
			}
			
			$select = $this->select();
			$select->where('enquiryRefno = ?', $enquiryId);
			$occupationalReferenceRow = $this->fetchRow($select);
			$returnVal = $this->_createDomainObject($occupationalReferenceRow);
		}
		return $returnVal;
    }
	
	
	/**
     * Retrieves the occupational reference for a specific occupation.
     *
     * @param int $referenceId
     * The unique occupational reference identifier.
     *
     * @return mixed
     * Returns a Model_Referencing_OccupationalReference object, or null if no
     * occupational reference is found.
     */
	public function getOccupationalReference($referenceId) {
		
		$select = $this->select();
		$select->where('refno = ?', $referenceId);
		$occupationalReferenceRow = $this->fetchRow($select);
		return $this->_createDomainObject($occupationalReferenceRow);
	}
	
	
	/**
     * Retrieves the occupational reference for a specific occupation.
     *
     * @param Zend_Db_Table_Row $occupationalReferenceRow
     * The result of a search in the datasource.
     *
     * @return mixed
     * Returns a Model_Referencing_OccupationalReference object, or null if no
     * occupational reference is found.
     */
	protected function _createDomainObject(Zend_Db_Table_Row $occupationalReferenceRow) {
		
		if(!empty($occupationalReferenceRow)) {
		
			$occupationalReference = new Model_Referencing_OccupationReference();
			
			switch($occupationalReferenceRow->type) {
				
				case 'Employer':
					$occupationalReference->provisionType = Model_Referencing_OccupationReferenceProvisions::EMPLOYMENT_REFERENCE;
					break;
				case 'Accountant':
					$occupationalReference->provisionType = Model_Referencing_OccupationReferenceProvisions::ACCOUNTANT_REFERENCE;
					break;
				case 'Pension Administrator':
					$occupationalReference->provisionType = Model_Referencing_OccupationReferenceProvisions::PENSION_ADMINISTRATOR_REFERENCE;
					break;
				case 'Independent Means':
					$occupationalReference->provisionType = Model_Referencing_OccupationReferenceProvisions::ACCOUNTANT_REFERENCE;
					break;
				case 'SA302 Forms':
					$occupationalReference->provisionType = Model_Referencing_OccupationReferenceProvisions::SA302_FORMS;
					break;
				case 'Pension Statement':
					$occupationalReference->provisionType = Model_Referencing_OccupationReferenceProvisions::PENSION_STATEMENTS;
					break;
			}
			
			switch($occupationalReferenceRow->emphowconf) {
			
				case 'Phone':
					$occupationalReference->submissionType = Model_Referencing_ReferenceSubmissionTypes::PHONE;
					break;
				case 'Fax':
					$occupationalReference->submissionType = Model_Referencing_ReferenceSubmissionTypes::FAX;
					break;
				case 'Email':
					$occupationalReference->submissionType = Model_Referencing_ReferenceSubmissionTypes::EMAIL;
					break;
				case 'Letter':
					$occupationalReference->submissionType = Model_Referencing_ReferenceSubmissionTypes::LETTER;
					break;
			}
			
			//Add the occupation reference variables - the things that change from reference to reference.
			$occupationalReference->variables = $this->_loadOccupationReferenceVariables($occupationalReferenceRow);
			
			if($occupationalReferenceRow->acceptemploy == 'Accept') {
			
				$occupationalReference->isAcceptable = true;
			}
			else {
				
				$occupationalReference->isAcceptable = false;
			}
			
			$returnVal = $occupationalReference;
		}
		else {
			
			$returnVal = null;
		}
        
        return $returnVal;
	}
	
	protected function _loadOccupationReferenceVariables(Zend_Db_Table_Row $occupationalReferenceRow) {
		
		$variables = array();
		
		if(!empty($occupationalReferenceRow->employstartdate) && ($occupationalReferenceRow->employstartdate != '0000-00-00')) {
			
			$variables[Model_Referencing_OccupationReferenceVariables::START_DATE] = new Zend_Date(
				$occupationalReferenceRow->employstartdate, Zend_Date::ISO_8601);
		}
		
		if($occupationalReferenceRow->employtitle == 'Yes') {
			
			$variables[Model_Referencing_OccupationReferenceVariables::IS_TITLE_CONFIRMED] = true;
		}
		else {
			
			$variables[Model_Referencing_OccupationReferenceVariables::IS_TITLE_CONFIRMED] = false;
		}
		
		if($occupationalReferenceRow->employtime == 'Full') {
			
			$variables[Model_Referencing_OccupationReferenceVariables::IS_FULL_TIME] = true;
		}
		else {
			
			$variables[Model_Referencing_OccupationReferenceVariables::IS_FULL_TIME] = false;
		}
		
		if(!empty($occupationalReferenceRow->reported_salary)) {
			
			//For non-accountancy
			$variables[Model_Referencing_OccupationReferenceVariables::INCOME_AMOUNT] = new Zend_Currency(
				array(
					'precision' => 0,
					'value' => $occupationalReferenceRow->reported_salary
				)
			);
			
			//For accountancy
			$variables[Model_Referencing_OccupationReferenceVariables::AVERAGE_INCOME_AMOUNT] =
				$variables[Model_Referencing_OccupationReferenceVariables::INCOME_AMOUNT];
			
			//For pensions
			$variables[Model_Referencing_OccupationReferenceVariables::GROSS_PENSION] =
				$variables[Model_Referencing_OccupationReferenceVariables::INCOME_AMOUNT];
		}
		
		/*
		//This cannot be implemented with the munting datasources.
		if(is there an overtime amount) {
			
			$variables[Model_Referencing_OccupationReferenceVariables::OVERTIME_AMOUNT] = new Zend_Currency(
				array(
					'precision' => 0,
					'value' => ?
				)
			)
		}
		*/
		
		if($occupationalReferenceRow->employnextsix == 'Yes') {
			
			$variables[Model_Referencing_OccupationReferenceVariables::IS_EMPLOYED_FOR_NEXT_6MONTHS] = true;
		}
		else {
			
			$variables[Model_Referencing_OccupationReferenceVariables::IS_EMPLOYED_FOR_NEXT_6MONTHS] = false;
		}
			
		$variables[Model_Referencing_OccupationReferenceVariables::CONFIRMED_BY] = $occupationalReferenceRow->ewhoby;
		$variables[Model_Referencing_OccupationReferenceVariables::CONTRACT_DURATION] = $occupationalReferenceRow->contractmonths;
		
		if($occupationalReferenceRow->seacctservice == 'Yes') {
			
			$variables[Model_Referencing_OccupationReferenceVariables::IS_SERVICE_PROVIDED] = true;
		}
		else {
			
			$variables[Model_Referencing_OccupationReferenceVariables::IS_SERVICE_PROVIDED] = false;
		}
			
		$variables[Model_Referencing_OccupationReferenceVariables::DURATION_OF_SERVICE] =
			($occupationalReferenceRow->sehowlongyrs * 12) + $occupationalReferenceRow->sehowlongmths;
		
		if($occupationalReferenceRow->semeetrent == 'Yes') {
			
			$variables[Model_Referencing_OccupationReferenceVariables::CAN_MEET_RENT] = true;
		}
		else {
			
			$variables[Model_Referencing_OccupationReferenceVariables::CAN_MEET_RENT] = false;
		}
		
		return $variables;
	}
}

?>
