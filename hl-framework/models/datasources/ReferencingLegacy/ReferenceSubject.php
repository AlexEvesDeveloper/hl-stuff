<?php

/**
* Model definition for the Reference Subject datasource.
*/
class Datasource_ReferencingLegacy_ReferenceSubject extends Zend_Db_Table_Multidb {

    /**#@+
     * Mandatory attributes. The Tenant table holds both prospective tenants
     * and guarantors.
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'Tenant';
    protected $_FIRST = 'ID';
    /**#@-*/
    
	/**#@+
	 * List of permitted field names for use with the updateField() method.
	 */
	const FIELD_EMAIL = 'email';
	/**#@-*/


	public function insertReferenceSubject($referenceSubject) {

		//Get the bank account details.
		if(!empty($referenceSubject->bankAccount)) {

			$accountNumber = $referenceSubject->bankAccount->accountNumber;
			$sortCode = $referenceSubject->bankAccount->sortCode;

			if($referenceSubject->bankAccount->isValidated) {

				$isValidated = 'Yes';
			}
			else {

				$isValidated = 'No';
			}
		}
		else {

			$accountNumber = '';
			$sortCode = '';
			$isValidated = 'N/A';
		}


		//Get the current residential status.
		if (!empty($referenceSubject->residences)) {

    		foreach($referenceSubject->residences as $residence) {

    			if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {

    				switch($residence->status) {

    					case Model_Referencing_ResidenceStatus::OWNER:
    						$currentResidentialStatus = 'Owner';
    						break;

    					case Model_Referencing_ResidenceStatus::TENANT:
    						$currentResidentialStatus = 'Tenant';
    						break;

    					case Model_Referencing_ResidenceStatus::LIVING_WITH_RELATIVES:
    						$currentResidentialStatus = 'Living with Relative';
    						break;
    				}
    				break;
    			}
    		}
		}


		//Get the occupational income.
		$totalIncome = new Zend_Currency(array('value' => 0, 'precision' => 0));
		foreach($referenceSubject->occupations as $occupation) {

			$totalIncome->add($occupation->income);
		}


		//Get the current occupational status.
		$currentOccupationalStatus = '';
		foreach($referenceSubject->occupations as $occupation) {

			if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {

				if($occupation->importance == Model_Referencing_OccupationImportance::FIRST) {

					switch($occupation->type) {

						case Model_Referencing_OccupationTypes::EMPLOYMENT:
							$currentOccupationalStatus = 'Employed';
							break;

						case Model_Referencing_OccupationTypes::CONTRACT:
							$currentOccupationalStatus = 'On Contract';
							break;

						case Model_Referencing_OccupationTypes::SELFEMPLOYMENT:
							$currentOccupationalStatus = 'Self Employed';
							break;

						case Model_Referencing_OccupationTypes::INDEPENDENT:
							$currentOccupationalStatus = 'of Independent Means';
							break;

						case Model_Referencing_OccupationTypes::RETIREMENT:
							$currentOccupationalStatus = 'Retired';
							break;

						case Model_Referencing_OccupationTypes::STUDENT:

							$currentOccupationalStatus = 'Student';
							break;

						case Model_Referencing_OccupationTypes::UNEMPLOYMENT:
							$currentOccupationalStatus = 'Unemployed';
							break;
					}

					//Whilst we are here, determine if the occupation is permanent.
					if($occupation->isPermanent) {

						$occupationChange = 'No';
					}
					else {

						$occupationChange = 'Yes';
					}
					break;
				}
			}
		}

		if(empty($referenceSubject->shareOfRent)) {

			$shareOfRent = 0;
		}
		else {

			$shareOfRent = $referenceSubject->shareOfRent->getValue();
		}

		if(empty($referenceSubject->dob)) {

		    $dob = '0000-00-00';
		}
		else {

		    $dob = $referenceSubject->dob->toString(Zend_Date::ISO_8601);
		}

		//Finally, push to munt
        $data = array(
			'title' => empty($referenceSubject->name->title) ? '' : $referenceSubject->name->title,
			'firstname' => empty($referenceSubject->name->firstName) ? '' : $referenceSubject->name->firstName,
			'middlename' => empty($referenceSubject->name->middleName) ? '' : $referenceSubject->name->middleName,
			'lastname' => empty($referenceSubject->name->lastName) ? '' : $referenceSubject->name->lastName,
			'maidenname' => empty($referenceSubject->name->maidenName) ? '' : $referenceSubject->name->maidenName,
			'dob' => $dob,
			'tel' => empty($referenceSubject->contactDetails->telephone1) ? '' : $referenceSubject->contactDetails->telephone1,
			'mobile' => empty($referenceSubject->contactDetails->telephone2) ? '' : $referenceSubject->contactDetails->telephone2,
			'email' => empty($referenceSubject->contactDetails->email1) ? '' : $referenceSubject->contactDetails->email1,
			'rstatus' => $currentResidentialStatus,
			'estatus' => $currentOccupationalStatus,
			'echange' => $occupationChange,
			'RentShare' => $shareOfRent,
			'income' => $totalIncome->getValue(),
			'hasCCJs' => ($referenceSubject->hasAdverseCredit == true) ? 'Yes' : 'No',
			'isForeignNational' => ($referenceSubject->isForeignNational == true) ? 'yes' : 'no',
			'accountNo' => $accountNumber,
			'sortcodeNo' => $sortCode,
			'bankValidationCheckStatus' => $isValidated
		);

        return $this->insert($data);
    }

	
	/**
	 * Emergency method for updating a specific field in the Tenant table.
	 *
	 * This is a 'throw-away' method, and should be deleted as soon as the munt
	 * is deleted.
	 *
	 * @param integer $tenantId
	 * The Tenant identifier.
	 *
	 * @param string $fieldName
	 * The name of the database field whose value should be updated. MUST correspond to
	 * one of the consts exposed by this class, otherwise an exception will be thrown.
	 *
	 * @param mixed $value
	 * The value with which to upate the database field.
	 *
	 * @return void
	 *
	 * @throws Zend_Exception
	 * Occurs if the $fieldName does not correspond to one of the consts exposed by this
	 * class.
	 */
	public function updateField($tenantId, $fieldName, $value) {
		
		switch($fieldName) {
			
			case self::FIELD_EMAIL:
				break;
			default:
				throw new Zend_Exception(get_class() . '::' . __FUNCTION__ . ': Invalid field name provided.');
		}
		
		$data = array(
			$fieldName => $value
		);
		
		$where = $this->quoteInto('ID = ?', $tenantId);
		$this->update($data, $where);
	}


    /**
     * The legacy datasource links the residential status directly against the reference
     * subject, rather than on the individual residences which the subject has occupied.
     * Therefore this method provides a way to correctly populate the residential status,
     * and is likely to be useful by the Datasources_Referencing_Residences class until the
     * old datasources are improved.
     *
     * @param string $enquiryId
     * The unique, external enquiry identifier.
     *
     * @return string
     * Returns a string corresponding to one of the consts exposed by the
     * Model_Referencing_ResidentialStatus class, or empty string if no data is found.
     */
    public function getLegacyResidentialStatus($enquiryId) {

        $enquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
        $referenceSubjectId = $enquiryDatasource->getTenantId($enquiryId);

        $select = $this->select();
        $select->where('ID = ?', $referenceSubjectId);
        $referenceSubjectRow = $this->fetchRow($select);

        return $referenceSubjectRow->rstatus;
    }


    /**
	 * The legacy datasource stores the total annual income in the Tenant table.
     *
     * @param string $enquiryId
     * The unique, external enquiry identifier.
     *
     * @return mixed
     * Returns a Zend_Currency if the income is known, else returns null.
     */
    public function getLegacyTotalAnnualIncome($enquiryId) {

    	$enquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
        $referenceSubjectId = $enquiryDatasource->getTenantId($enquiryId);

        $select = $this->select();
        $select->where('ID = ?', $referenceSubjectId);
        $referenceSubjectRow = $this->fetchRow($select);

        if($referenceSubjectRow->income >= 0) {

        	$returnVal = new Zend_Currency(array('value' => $referenceSubjectRow->income, 'precision' => 0));
        }
        else {

        	$returnVal = null;
        }
        return $returnVal;
    }


    /**
     * Retrieves the specified reference subject.
     *
     * @param string $referenceSubjectId
     * The unique reference subject identifier.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * The reference subject details encapsulated in a Model_Referencing_ReferenceSubject
     * object, or null if the reference subject cannot be found.
     */
    public function getReferenceSubject($referenceSubjectId, $enquiryId) {

        if(empty($referenceSubjectId)) {
			
			return null;
		}
		
		$select = $this->select();
        $select->where('ID = ?', $referenceSubjectId);
        $referenceSubjectRow = $this->fetchRow($select);

        if(empty($referenceSubjectRow)) {

            $returnVal = null;
        }
        else {

            $referenceSubject = new Model_Referencing_ReferenceSubject();
            $referenceSubject->referenceId = $referenceSubjectRow->ID;


			//Load up the bank account details.
			$bankAccount = new Model_Referencing_BankAccount();
			$bankAccount->referenceId = $referenceSubjectRow->ID;
			$bankAccount->accountNumber = $referenceSubjectRow->accountNo;
			$bankAccount->sortCode = $referenceSubjectRow->sortcodeNo;

			switch($referenceSubjectRow->bankValidationCheckStatus) {

				case 'N/A':
				case 'No':
				case '':
					$bankAccount->isValidated = false;
					break;
				default:
					$bankAccount->isValidated = true;
			}
			$referenceSubject->bankAccount = $bankAccount;


			//Load the name details.
            $name = new Model_Core_Name();
            $name->title = $referenceSubjectRow->title;
            $name->firstName = $referenceSubjectRow->firstname;
            $name->middleName = $referenceSubjectRow->middlename;
            $name->lastName = $referenceSubjectRow->lastname;
            $name->maidenName = $referenceSubjectRow->maidenname;
            $referenceSubject->name = $name;

            $contactDetails = new Model_Core_ContactDetails();
            $contactDetails->telephone1 = $referenceSubjectRow->tel;
            $contactDetails->telephone2 = $referenceSubjectRow->mobile;
            $contactDetails->email1 = $referenceSubjectRow->email;
            $referenceSubject->contactDetails = $contactDetails;

            if($referenceSubjectRow->dob != '0000-00-00') {

				$referenceSubject->dob = new Zend_Date($referenceSubjectRow->dob, Zend_Date::ISO_8601);
            }

            $enquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
            if($enquiryDatasource->getLegacyReferenceSubjectType($enquiryId) ==
               Model_Referencing_ReferenceSubjectTypes::TENANT) {

                $referenceSubject->type = Model_Referencing_ReferenceSubjectTypes::TENANT;
            }
            else {

                $referenceSubject->type = Model_Referencing_ReferenceSubjectTypes::GUARANTOR;
            }

            $residenceDatasource = new Datasource_ReferencingLegacy_Residences();
            $referenceSubject->residences = $residenceDatasource->getByEnquiry($enquiryId);

            $occupationDatasource = new Datasource_ReferencingLegacy_Occupations();
            $referenceSubject->occupations = $occupationDatasource->getAllByEnquiry($enquiryId);

            if(!empty($referenceSubjectRow->hasCCJs)) {

                if($referenceSubjectRow->hasCCJs == 'Yes') {

                    $referenceSubject->hasAdverseCredit = true;
                }
                else {

                    $referenceSubject->hasAdverseCredit = false;
                }
            }

            if($referenceSubjectRow->RentShare >= 0) {

	            $referenceSubject->shareOfRent = new Zend_Currency(
		            array(
		                'value' => $referenceSubjectRow->RentShare,
		                'precision' => 0
		            ));
			}

            $returnVal = $referenceSubject;
        }

        return $returnVal;
    }
}

?>