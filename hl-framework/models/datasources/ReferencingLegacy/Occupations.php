<?php

/**
* Model definition for the occupations datasource.
*/
class Datasource_ReferencingLegacy_Occupations extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'employment';
    protected $_FIRST = 'refno';
    /**#@-*/
    
	
	public function removeAllOccupations($referenceId) {
		
		$where = $this->quoteInto('enquiryRefno = ? ', (string)$referenceId);
        $this->delete($where);
	}
    
    /**
     * Not implemented in this release.
     */
    public function insertOccupation($occupation, $referenceId) {
		
		//Type conversion
		switch($occupation->type) {
			
			case Model_Referencing_OccupationTypes::EMPLOYMENT:
			case Model_Referencing_OccupationTypes::CONTRACT:
				$type = 'Employer';
				break;
			
			case Model_Referencing_OccupationTypes::STUDENT:
				$type = 'University(Student)';
				break;
			
			case Model_Referencing_OccupationTypes::INDEPENDENT:
			case Model_Referencing_OccupationTypes::SELFEMPLOYMENT:
				$type = 'Accountant';
				break;
			
			case Model_Referencing_OccupationTypes::RETIREMENT:
				$type = 'Pension Administrator';
				break;
			
			default:
				$type = '';
				break;
		}
		
		
		//Jobstatus conversion.
		if($occupation->importance == Model_Referencing_OccupationImportance::SECOND) {
			
			$jobStatus = 'Second';
		}
		else {
			
			switch($occupation->chronology) {
				
				case Model_Referencing_OccupationChronology::CURRENT:
					$jobStatus = 'Current';
					break;
				case Model_Referencing_OccupationChronology::FUTURE:
					$jobStatus = 'Future';
					break;
			}
		}
		
		$positionHeld = '';
		if(!empty($occupation->variables)) {
			
			$positionHeld = $occupation->variables[Model_Referencing_OccupationVariables::POSITION];
		}
		
		$payrollNumber = '';
		if(!empty($occupation->variables)) {
			
			$payrollNumber = $occupation->variables[Model_Referencing_OccupationVariables::PAYROLL_NUMBER];
		}
	
		if(!empty($occupation->refereeDetails) && !empty($occupation->refereeDetails->name)) {
			
			$contactName = $occupation->refereeDetails->name->firstName . ' ' . $occupation->refereeDetails->name->lastName;
		}
		else {
			
			$contactName = '';	
		}
		
        $data = array(
			'companyname' => empty($occupation->refereeDetails->organisationName) ? '' : $occupation->refereeDetails->organisationName,
			'contactname' => $contactName,
			'contactposition' => empty($occupation->refereeDetails->position) ? '' : $occupation->refereeDetails->position,
			'address1' => empty($occupation->refereeDetails->address->addressLine1) ? '' : $occupation->refereeDetails->address->addressLine1,
			'address2' => empty($occupation->refereeDetails->address->addressLine2) ? '' : $occupation->refereeDetails->address->addressLine2,
			'town' => empty($occupation->refereeDetails->address->town) ? '' : $occupation->refereeDetails->address->town,
			'postcode' => empty($occupation->refereeDetails->address->postCode) ? '' : $occupation->refereeDetails->address->postCode,
			'tel' => empty($occupation->refereeDetails->contactDetails->telephone1) ? '' : $occupation->refereeDetails->contactDetails->telephone1,
			'fax' => empty($occupation->refereeDetails->contactDetails->fax1) ? '' : $occupation->refereeDetails->contactDetails->fax1,
			'email' => empty($occupation->refereeDetails->contactDetails->email1) ? '' : $occupation->refereeDetails->contactDetails->email1,
			'salary' => empty($occupation->income) ? '0' : $occupation->income->getValue(),
			'positionheld' => empty($positionHeld) ? '' : $positionHeld,
			'payrollno' => empty($payrollNumber) ? '' : $payrollNumber,
			'startdate' => empty($occupation->startDate) ? '0000-00-00' : $occupation->startDate->toString(Zend_Date::ISO_8601),
			'permanent' => ($occupation->isPermanent == true) ? 'Yes' : 'No',
			'type' => $type,
			'enquiryRefno' => $referenceId,
			'Jobstatus' => $jobStatus
		);
		
        return $this->insert($data);
    }
    
    
    /**
     * Retrieves all occupation details against a specific Enquiry.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * An array of Model_Referencing_Occupation objects, or null if no
     * occuapations are found.
     *
     * @todo
     * Occupation referee details are not yet captured and stored.
     */
    public function getAllByEnquiry($enquiryId) {
        
        $returnArray = array();
		
		
		//First check if the reference subject is a student or unemployed - neither of these
		//occupations are stored in the employment table.
		$enquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
		$legacyEmploymentType = $enquiryDatasource->getLegacyEmploymentType($enquiryId);
		if($enquiryDatasource->getIdentifierType($enquiryId) == Model_Referencing_ReferenceKeyTypes::EXTERNAL) {
			
			$referenceId = $enquiryDatasource->getInternalIdentifier($enquiryId);
		}
		else {
			
			$referenceId = $enquiryId;
		}

		if($legacyEmploymentType == 'Unemployed') {
			
			$occupation = new Model_Referencing_Occupation();
			$occupation->referenceId = $referenceId;
			$occupation->importance = Model_Referencing_OccupationImportance::FIRST;
			$occupation->chronology = Model_Referencing_OccupationChronology::CURRENT;
			$occupation->type = Model_Referencing_OccupationTypes::UNEMPLOYMENT;
			$occupation->isPermanent = true;
			$occupation->income = new Zend_Currency(array('precision' => 0, 'value' => 0));
			
			//Referees and referencing are not applicable for this occupation status.
			$occupation->refereeDetails = null;
			$occupation->referencingDetails = null;
			$returnArray[] = $occupation;
		}
		else if($legacyEmploymentType == 'Student') {
			
			$occupation = new Model_Referencing_Occupation();
			$occupation->referenceId = $referenceId;
			$occupation->importance = Model_Referencing_OccupationImportance::FIRST;
			$occupation->chronology = Model_Referencing_OccupationChronology::CURRENT;
			$occupation->type = Model_Referencing_OccupationTypes::STUDENT;
			$occupation->isPermanent = true;
			$occupation->income = new Zend_Currency(array('precision' => 0, 'value' => 0));
			
			//Referees and referencing are not applicable for this occupation status.
			$occupation->refereeDetails = null;
			$occupation->referencingDetails = null;
			$returnArray[] = $occupation;
		}
		

		//Now identify the occupations recorded in the employment table.
		$select = $this->select();
        $select->where('enquiryRefno = ? ', (string)$enquiryId);
        $occupationsArray = $this->fetchAll($select);
        
        if(!empty($occupationsArray)) {
          
			foreach($occupationsArray as $occupationRow) {
				
				$occupation = new Model_Referencing_Occupation();
				$occupation->id = $occupationRow->refno;
				$occupation->referenceId = $referenceId;
				$occupation->importance = $this->_getOccupationimportance($occupationRow->Jobstatus);
				$occupation->chronology = $this->_getOccupationalChronology($occupationRow->Jobstatus);
				
				//The $occupationRow->type refers to the occupational confirmation type, i.e. the
				//person or means by which the reference will be provided. Therefore it needs to be
				//translated into a type.
				$occupation->type = $this->_getOccupationType($occupationRow->type);
				
				if($occupationRow->permanent == 'Yes') {
					
					$occupation->isPermanent = true;
				}
				else {
					
					$occupation->isPermanent = false;
				}
				
				if(!empty($occupationRow->salary)) {
					
					$occupation->income = new Zend_Currency(
						array(
							'precision' => 0,
							'value' => $occupationRow->salary
						)
					);
				}
				
				if($occupationRow->startdate != '0000-00-00') {
					
					$occupation->startDate = new Zend_Date($occupationRow->startdate, Zend_Date::ISO_8601);
				}
				
				if($occupationRow->complete==1){
					$occupation->isComplete=true;
				}
				else{
					$occupation->isComplete=false;
				}
				//Assign the occupation variables, if applicable.
				$variables = array();
				if($occupationRow->enddate != '0000-00-00') {
					
					$endDate = new Zend_Date($occupationRow->enddate, Zend_Date::ISO_8601);
					$variables[Model_Referencing_OccupationVariables::ENDDATE] = $endDate;
				}	

				$position = $occupationRow->positionheld;
				if(!empty($position)) {
					
					$variables[Model_Referencing_OccupationVariables::POSITION] = $position;
				}
				
				$payrollNo = $occupationRow->payrollno;
				if(!empty($payrollNo)) {
					
					$variables[Model_Referencing_OccupationVariables::PAYROLL_NUMBER] = $payrollNo;
					$variables[Model_Referencing_OccupationVariables::PENSION_NUMBER] = $payrollNo;
				}
				
				if(!empty($variables)) {
				
					$occupation->variables = $variables;
				}

				
				//Add the referee details and reference details.
				$occupationalReferees = new Datasource_ReferencingLegacy_OccupationalReferees();
				$occupation->refereeDetails = $occupationalReferees->getOccupationalReferee($occupationRow->refno);

				$occupationalReferences = new Datasource_ReferencingLegacy_OccupationalReferences();
				$occupation->referencingDetails = $occupationalReferences->getByEnquiry(
					$enquiryId, $occupation->chronology, $occupation->importance);
				
				$returnArray[] = $occupation;
			}
        }
        
        return $returnArray;
    }
	
	
	/**
     * Returns the current occupation.
     * 
     * @param $enquiryId
     * 
     * @return mixed
     * Returns a Model_Referencing_Occupation if the occupation is found,
     * else returns null.
     */
    public function getCurrent($enquiryId) {
    	
    	$occupationArray = $this->getAllByEnquiry($enquiryId);
    	
    	$returnVal = null;
    	foreach($occupationArray as $occupation) {
    		
    		if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
    			
    			if($occupation->importance == Model_Referencing_OccupationImportance::FIRST) {
    				
    				$returnVal = $occupation;
    				break;
    			}
    		}
    	}
    	
    	return $returnVal;
    }
    
    
    /**
     * Returns the second occupation.
     * 
     * @param $enquiryId
     * 
     * @return mixed
     * Returns a Model_Referencing_Occupation if the occupation is found,
     * else returns null.
     */
    public function getSecond($enquiryId) {
    	
    	$occupationArray = $this->getAllByEnquiry($enquiryId);
    	
    	$returnVal = null;
    	foreach($occupationArray as $occupation) {
    		
    		if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
    			
    			if($occupation->importance == Model_Referencing_OccupationImportance::SECOND) {
    				
    				$returnVal = $occupation;
    				break;
    			}
    		}
    	}
    	
    	return $returnVal;
    }
	
    
    /**
     * Returns the future occupation.
     * 
     * @param $enquiryId
     * 
     * @return mixed
     * Returns a Model_Referencing_Occupation if the occupation is found,
     * else returns null.
     */
    public function getFuture($enquiryId) {
    	
    	$occupationArray = $this->getAllByEnquiry($enquiryId);
    	
    	$returnVal = null;
    	foreach($occupationArray as $occupation) {
    		
    		if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
    			    				
    			$returnVal = $occupation;
    			break;
    		}
    	}
    	
    	return $returnVal;
    }
	
	/**
	 * Identifies an occupation as FIRST or SECOND.
	 *
	 * @param string $occupationStatus
	 * The occupation status as indicated by the legacy employment table.
	 *
	 * @return integer
	 * An integer corresponding to one of the consts exposed by the
	 * Model_Referencing_OccupationalClassifiers class.
	 *
	 * @throw Zend_Exception
	 * Throws a Zend_Exception if the importance is unidentified.
	 */
	protected function _getOccupationimportance($occupationStatus) {
			
		switch($occupationStatus) {
			
			case 'Current':
				$returnVal = Model_Referencing_OccupationImportance::FIRST;
				break;
			
			case 'Second':
				$returnVal = Model_Referencing_OccupationImportance::SECOND;
				break;
				
			case 'Previous':
				$returnVal = Model_Referencing_OccupationImportance::FIRST;
				break;
				
			case 'Future':
				$returnVal = Model_Referencing_OccupationImportance::FIRST;
				break;
			
			default:
				$returnVal = Model_Referencing_OccupationImportance::FIRST;
				break;
		}
		
		return $returnVal;
	}
	
	
	/**
	 * Identifies an occupation as curent, previous or future.
	 *
	 * @param string $occupationStatus
	 * The occupation status as indicated by the legacy employment table.
	 *
	 * @return string
	 * A string corresponding to one of the consts exposed by the
	 * Model_Referencing_OccupationalChronology class.
	 *
	 * @throw Zend_Exception
	 * Throws a Zend_Exception if the chronology is unidentified.
	 */
	protected function _getOccupationalChronology($occupationStatus) {
		
		switch($occupationStatus) {
			
			case 'Current':
			case 'Second':
				$returnVal = Model_Referencing_OccupationChronology::CURRENT;
				break;
			
			case 'Previous':
				$returnVal = Model_Referencing_OccupationChronology::PREVIOUS;
				break;
			
			case 'Future':
				$returnVal = Model_Referencing_OccupationChronology::FUTURE;
				break;
			
			default:
				$returnVal = Model_Referencing_OccupationChronology::CURRENT;
				break;
		}
		
		return $returnVal;
	}
	
	
	/**
	 * Identifies the occupation type as employment, retirement, independent, selfemployment.
	 *
	 * @param string $occupationType
	 * The occupation type as indicated by the legacy employment table.
	 *
	 * @return integer
	 * An integer corresponding to one of the consts exposed by the
	 * Model_Referencing_OccupationalTypes class.
	 *
	 * @throw Zend_Exception
	 * Throws a Zend_Exception if the type is unidentified.
	 */
	protected function _getOccupationType($occupationType) {

		//Determine the occupation type form the legacy employment table.
		switch($occupationType) {
			
			case 'Employer':
				$returnVal = Model_Referencing_OccupationTypes::EMPLOYMENT;
				break;
			
			case 'Pension Administrator':
			case 'Pension Statement':
				$returnVal = Model_Referencing_OccupationTypes::RETIREMENT;
				break;
			
			case 'Independent Means':
				$returnVal = Model_Referencing_OccupationTypes::INDEPENDENT;
				break;
			
			case 'Accountant':
			case 'SA302 Forms':
				$returnVal = Model_Referencing_OccupationTypes::SELFEMPLOYMENT;
				break;
			
			default:
				//Assume employment, however, this should be improved by modifying the
				//referencing datasources.
				$returnVal = Model_Referencing_OccupationTypes::EMPLOYMENT;
		}
		
		return $returnVal;
	}
}

?>
