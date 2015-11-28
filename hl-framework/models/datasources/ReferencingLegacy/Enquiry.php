<?php

/**
* Model definition for the Enquiry datasource. The Enquiry links together all aspects of the
* referencing process. The Enquiry identifier can be used to identify all related data,
* not just that in the Enquiry datasource.
*/
class Datasource_ReferencingLegacy_Enquiry extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'Enquiry';
    protected $_primary = 'RefNo';
    /**#@-*/

 	/**
	 * Generates a unique external Enquiry identifer.
	 *
	 * @return string
	 * Returns a unique external Enquiry identifier.
	 */
	protected function _buildUniqueIdentifier()
    {

        $select = $this->select();
        $utils = new Application_Core_Utilities();

        while(true) {
			$legacyIdentifier = $utils->_generateRefno();
            $select->where('RefNo = ? ', (string)$legacyIdentifier);
            $row = $this->fetchRow($select);

            if(empty($row)) {
				//A unique identifier has been found.
                break;
            }
        }

        return $legacyIdentifier;
	}

    /**
     * Inserts a new, empty Enquiry into the datasource and returns a corresponding object.
     *
     * This method will allocate unique internal and external Enquiry identifiers
     * to the new Reference object.
     *
     * @throws Zend_Exception
     * @return Model_Referencing_Reference
     * Holds the details of the newly inserted Reference.
     */
    public function createNewEnquiry()
    {
		$externalId = null;

		//Attempt to insert a new Enquiry record with a unique RefNo.
		$attempts = 0;
		while(true) {
			if($attempts >= 3) {
				//Repeatedly failed insertion. Critical error - do not allow the user to proceed.
				throw new Zend_Exception(get_class() . '::' . __FUNCTION__ . ': Cant created record in Enquiry.');
			}

			$externalId = $this->_buildUniqueIdentifier();
			$data = array('RefNo' => $externalId);
			if($this->insert($data)) {
				break;
			}

			$attempts++;
		}


		// Create a new Enquiry object to hold the newly created internal
		// and external Enquiry identifiers, then return this.
		$select = $this->select();
		$select->where('RefNo = ? ', (string)$externalId);
		$row = $this->fetchRow($select);

		$enquiry = new Model_Referencing_Reference();
		$enquiry->internalId = $row->ID;
		$enquiry->externalId = $externalId;

        return $enquiry;
    }

    /**
     * Internal utility method for retrieving the Enquiry row.
     *
     * Supports both the IRN and the ERN, for convenience.
     *
     * @param string $enquiryId
     * The Reference identifier (internal or external).
     *
     * @return Zend_Db_Table_Row
     * The data source.
     */
    protected function _getEnquiryRow($enquiryId)
    {
        $select = $this->select();
		$enquiryIdString = (string)$enquiryId;

        if(ctype_digit($enquiryIdString)) {
            //All the characters in $enquiryId are numeric
            $select->where('ID = ? ', $enquiryId);
        }
        else {
            $select->where('RefNo = ? ', $enquiryIdString);
        }

        return $this->fetchRow($select);
    }


    /**
     * Retrieves the specified Reference.
     *
     * @param mixed $referenceId
     * The unique Reference identifier. May be the IRN (internal refno) or the ERN (external
     * refno).
     *
     * @return mixed
     * The Reference details, encapsulated in a Model_Referencing_Reference object,
     * or null if the Reference cannot be found.
     */
    public function getEnquiry($referenceId)
    {
        $enquiryRow = $this->_getEnquiryRow($referenceId);

        if (empty($enquiryRow)) {
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ':Unable to find Enquiry.');
            $returnVal = null;
        }
        else {
            //Populate the details into a Customer object.
            $reference = new Model_Referencing_Reference();
            $reference->internalId = $enquiryRow->ID;
            $reference->externalId = $enquiryRow->RefNo;
            $reference->declarationVersion = $enquiryRow->declaration_version;

            $productDatasource = new Datasource_ReferencingLegacy_Product();
            $product = $productDatasource->getProductByID($enquiryRow->ProductID);

            if (!empty($product)) {
                $reference->productSelection = new Model_Referencing_ProductSelection();
				$reference->productSelection->referenceId = $enquiryRow->ID;
                $reference->productSelection->product = $product;

				switch($enquiryRow->PolicyLength) {
					case 0: $reference->productSelection->duration = 0; break;
					case 6: $reference->productSelection->duration = 6; break;
					case 12: $reference->productSelection->duration = 12; break;
					default: $reference->productSelection->duration = 0;
				}
            }

            $progressDatasource = new Datasource_ReferencingLegacy_Progress();
            $reference->progress = $progressDatasource->getByEnquiry($enquiryRow->RefNo);

            $propertyLeaseDatasource = new Datasource_ReferencingLegacy_PropertyLease();
            $reference->propertyLease = $propertyLeaseDatasource->getPropertyLease($enquiryRow->proprefno, $enquiryRow->RefNo);

            $referenceSubjectDatasource = new Datasource_ReferencingLegacy_ReferenceSubject();
            $reference->referenceSubject = $referenceSubjectDatasource->getReferenceSubject($enquiryRow->TenantID, $enquiryRow->RefNo);


			//Build the reference status.
			$referenceStatus = new Model_Referencing_ReferenceStatus();
			$referenceStatus->referenceId = $enquiryRow->ID;

            if ($enquiryRow->nochange == 'cancelled') {
				$referenceStatus->state = Model_Referencing_ReferenceStates::CANCELLED;
            }
            else {
				$progressManager = new Manager_Referencing_Progress();
				$progressItem = $progressManager->findSpecificProgressItem(
					$reference->progress,
					Model_Referencing_ProgressItemVariables::FINAL_REPORT_BUILT);

				if (($progressItem == null) ||
                    ($progressItem->itemState == Model_Referencing_ProgressItemStates::INCOMPLETE)) {
					$referenceStatus->state = Model_Referencing_ReferenceStates::INPROGRESS;
				}
				else if(strcasecmp("Incomplete - Awaiting further information", $enquiryRow->conclusion) == 0) {
					$referenceStatus->state = Model_Referencing_ReferenceStates::INCOMPLETE;
					$referenceStatus->reasonForState = Model_Referencing_ReferenceStateReasons::AWAITING_FURTHER_INFORMATION;
				}
				else if(strcasecmp("Incomplete - Awaiting Completion of Tenant", $enquiryRow->conclusion) == 0) {
					$referenceStatus->state = Model_Referencing_ReferenceStates::INCOMPLETE;
					$referenceStatus->reasonForState = Model_Referencing_ReferenceStateReasons::AWAITING_TENANT_COMPLETION;
				}
				else {
					$referenceStatus->state = Model_Referencing_ReferenceStates::COMPLETE;
				}
            }

			$reference->status = $referenceStatus;

			//Build the reference decision
			if (($enquiryRow->conclusion == '') || (preg_match("/incomplete/i", $enquiryRow->conclusion)) ) {
				$reference->decision = null;
			}
			else {
				$decision = new Model_Referencing_Decision();
				$decision->referenceId = $enquiryRow->ID;

				if(preg_match("/not acceptable/i", $enquiryRow->conclusion)) {
					$decision->decision = Model_Referencing_Decisions::NOT_ACCEPTABLE;
					$decision->decisionReasons = '';	//TO DO HERE
				}
				else {
					$decision->decision = Model_Referencing_Decisions::ACCEPTABLE;

					//Add caveats, if applicable
					$caveats = array();

					if (preg_match("/guarantor/i", $enquiryRow->conclusion)) {
						$decisionCaveat = new Model_Referencing_DecisionCaveat();
						$decisionCaveat->caveat = Model_Referencing_DecisionCaveats::WITH_GUARANTOR;

						//Identify the caveat reasons.
						$decisionCaveat->caveatReason = null;

						$caveats[] = $decisionCaveat;
					}

					if (preg_match("/condition/i", $enquiryRow->conclusion)) {
						$decisionCaveat = new Model_Referencing_DecisionCaveat();
						$decisionCaveat->caveat = Model_Referencing_DecisionCaveats::WITH_CONDITION;

						//Identify the caveat reasons.
						$decisionCaveat->caveatReason = null;

						$caveats[] = $decisionCaveat;
					}

					if (!empty($caveats)) {
						//Attach the array of caveat(s) to the Decision object.
						$decision->caveats = $caveats;
					}
				}

				//Attach the Decision object to the Reference object.
				$reference->decision = $decision;
			}

			/*
            $reference->termsAgreedBy = $enquiryRow->termsagreedby;
			*/
			if ($enquiryRow->termsagreedby == 'Tenant') {
				$reference->completionMethod = Model_Referencing_ReferenceCompletionMethods::TWO_STEP;
			}
			else {
				$reference->completionMethod = Model_Referencing_ReferenceCompletionMethods::ONE_STEP;
			}

			//Set the customer details. If the agent is HomeLet Direct, then the customer is a PLL.
            $agentDatasource = new Datasource_Core_Agents();
            $lettingAgent = $agentDatasource->getAgent($enquiryRow->AgentID);

			$params = Zend_Registry::get('params');
			if ($lettingAgent->agentSchemeNumber != $params->homelet->defaultAgent) {
				$reference->customer = new Model_Referencing_CustomerMap();
				$reference->customer->customerType = Model_Referencing_CustomerTypes::AGENT;
				$reference->customer->customerId = $lettingAgent->agentSchemeNumber;
                $reference->customer->legacyCustomerId = null;
			}
			else {
				$reference->customer = new Model_Referencing_CustomerMap();
				$reference->customer->customerType = Model_Referencing_CustomerTypes::LANDLORD;

				if ($enquiryRow->landrefno != 0) {
					$reference->customer->legacyCustomerId = $enquiryRow->landrefno;
				}
			}

            $returnVal = $reference;
        }
        return $returnVal;
    }


    /**
     * Returns the legacy employment type.
     *
     * The legacy employment type is provided to support the legacy
     * table structures.
     *
     * @param string $enquiryId
     * The unique Reference identifier. May be the IRN (internal refno) or the ERN (external
     * refno).
     *
     * @return string
     * The legacy employment type.
     *
     * @todo
     * Evolve the datasources so that this method can be deprecated.
     */
    public function getLegacyEmploymentType($enquiryId)
    {
        $enquiryRow = $this->_getEnquiryRow($enquiryId);
        return $enquiryRow->emptype;
    }


    /**
     * Returns the reference subject type as stored in the legacy datasource.
     *
     * @param string $enquiryId
     * The unique Enquiry identifier. May be the IRN (internal refno) or the ERN (external
     * refno).
     *
     * @return string
     * Returns a string corresponding to one of the consts exposed by the
     * Model_Referencing_ReferenceSubjectTypes class.
     *
     * @todo
     * Evolve the datasources so that this method can be deprecated.
     */
    public function getLegacyReferenceSubjectType($enquiryId)
    {
        $enquiryRow = $this->_getEnquiryRow($enquiryId);

        if($enquiryRow->Guarantor == 1) {
            $returnVal = Model_Referencing_ReferenceSubjectTypes::GUARANTOR;
        }
        else {
            $returnVal = Model_Referencing_ReferenceSubjectTypes::TENANT;
        }

        return $returnVal;
    }


    /**
     * Returns the reference subject's current landlord identifier.
     *
     * @param string $enquiryId
     * The unique Enquiry identifier. May be the IRN (internal refno) or the ERN (external
     * refno).
     *
     * @return string
     * The reference subject's current landlord identifier.
     */
    public function getCurrentLandlordId($enquiryId)
    {
        $enquiryRow = $this->_getEnquiryRow($enquiryId);
        return $enquiryRow->lrefrefno;
    }

    /**
     * Returns the prospective landlord identifier.
     *
     * @param string $enquiryId
     * The unique Enquiry identifier. May be the IRN (internal refno) or the ERN (external
     * refno).
     *
     * @return string
     * The prospective landlord identifier.
     */
    public function getProspectiveLandlordId($enquiryId)
    {
        $enquiryRow = $this->_getEnquiryRow($enquiryId);
        return $enquiryRow->newllrefno;
    }

    /**
     * Returns the product identifier.
     *
     * @param string $enquiryId
     * The unique Enquiry identifier. May be the IRN (internal refno) or the ERN (external
     * refno).
     *
     * @return integer
     * The product identifier.
     */
    public function getProductId($enquiryId)
    {
    	$enquiryRow = $this->_getEnquiryRow($enquiryId);
        return $enquiryRow->ProductID;
    }

    /**
     * Returns the identifier to one of the reference subject's addresses in the last three years.
     *
     * The identifier can be used by calling code to pass to the residence
     * datasource, so that all relevant residential information can be retrieved,
     * including details of the referee and reference given.
     *
     * @param mixed $enquiryId
     * The unique Enquiry identifier. Can be the IRN or the ERN.
     *
     * @param string $residentialChronology
     * Must correspond to one of the consts exposed by the
     * Model_Referencing_ResidentialChronology class.
     *
     * @throws Zend_Exception
     * @return int
     * The residence identifier.
     */
    public function getResidenceId($enquiryId, $residentialChronology)
    {
        $enquiryRow = $this->_getEnquiryRow($enquiryId);

        switch($residentialChronology) {
            case Model_Referencing_ResidenceChronology::CURRENT:
                $returnVal = $enquiryRow->ta1refno;
                break;

            case Model_Referencing_ResidenceChronology::FIRST_PREVIOUS:
                $returnVal = $enquiryRow->ta2refno;
                break;

            case Model_Referencing_ResidenceChronology::SECOND_PREVIOUS:
                $returnVal = $enquiryRow->ta3refno;
                break;

            default:
                throw new Zend_Exception('Invalid residence chronology.');
        }

        return $returnVal;
    }

    /**
     * Returns the reference subject identifier.
     *
     * @param string $enquiryId
     * The unique Enquiry identifier. May be the IRN (internal refno) or the ERN (external
     * refno).
     *
     * @return int
     * The reference subject identifier.
     */
    public function getTenantId($enquiryId)
    {
        $enquiryRow = $this->_getEnquiryRow($enquiryId);
        return $enquiryRow->TenantID;
    }


    /**
     * Utility method to get the IRN associated with the ERN passed in.
     *
     * The IRN is the internal Enquiry identifier, containing only digits. The ERN
     * is the external Enquiry identifier, containing a period and optionally a
     * forward slash.
     *
     * @param string $ern
     * The external Enquiry identifier.
     *
     * @return mixed
     * Returns the internal Enquiry identifier (IRN) as an integer, if found.
     * Otherwise returns null.
     */
    public function getInternalIdentifier($ern)
    {
        $select = $this->select(array('ID'));
        $select->where('RefNo = ? ', (string)$ern);
        $enquiryRow = $this->fetchRow($select);

        if(empty($enquiryRow)) {
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ':Invalid external identifier.');
            $returnVal = null;
        }
        else {
            $returnVal = $enquiryRow->ID;
        }

        return $returnVal;
    }


    /**
     * Utility method to get the ERN associated with the IRN passed in.
     *
     * The ERN is the external Enquiry identifier and is represented as a string,
     * as it may contain a period and forward slash. The IRN is the internal
     * Enquiry identifier, and contains only digits.
     *
     * @param string $irn
     * The internal Enquiry identifier.
     *
     * @return mixed
     * Returns the external Enquiry identifier (ERN) as a string, if found.
     * Otherwise returns null.
     */
    public function getExternalIdentifier($irn)
    {

        $select = $this->select(array('RefNo'));
        $select->where('ID = ?', $irn);
        $enquiryRow = $this->fetchRow($select);

        if (empty($enquiryRow)) {
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ':Invalid internal identifier.');
            $returnVal = null;
        }
        else {
            $returnVal = $enquiryRow->RefNo;
        }

        return $returnVal;
    }

    /**
     * Utility method to get the latest ERN associated with the policy number in.
     *
     * The ERN is the external Enquiry identifier and is represented as a string,
     * as it may contain a period and forward slash. The policy number is a string
     * used in the insurance database but referred from referencing when used
     * for rent guarantee.
     *
     * @param string $policynumber The policy number
     *
     * @param int $subject
     * @return mixed
     * Returns the external Enquiry identifier (ERN) as a string, if found.
     * Otherwise returns null.
     */
    public function getExternalIdentifierByPolicyNumber($policynumber,
                                                        $subject = Model_Referencing_ReferenceSubjectTypes::TENANT)
    {
        $select = $this->select(array('RefNo'));
        $select->where('policynumber = ?', $policynumber);

        if ($subject == Model_Referencing_ReferenceSubjectTypes::TENANT) {
        	$select->where('Guarantor = 0');
        }
        elseif ($subject == Model_Referencing_ReferenceSubjectTypes::GUARANTOR) {
        	$select->where('Guarantor = 1');
        }

        $select->order('RefNo desc');
        $select->limit(1);
        
        $enquiryRow = $this->fetchRow($select);
        
        if (!empty($enquiryRow)) {
            return $enquiryRow->RefNo;
        }
        
        $returnVal = null;
    }


    /**
     * Indicates whether the identifier passed in is an IRN, an ERN or invalid.
     *
     * @param string $enquiryId
     * Internal Enquiry identifier, or external.
     *
     * @return integer
     * Returns a const exposed by the Model_Referencing_ReferenceKeyIdentifiers class
     * indicating whether the $enquiryId is an IRN, ERN or invalid.
     */
    public function getIdentifierType($enquiryId)
    {
        $enquiryIdString = (string)$enquiryId;

    	if (ctype_digit($enquiryIdString)) {
            $returnVal = Model_Referencing_ReferenceKeyTypes::INTERNAL;
        }
        else {
            $returnVal = Model_Referencing_ReferenceKeyTypes::EXTERNAL;
        }

        return $returnVal;
    }


	/**
	 * Updates an existing Enquiry in the legacy datasource.
	 *
	 * @param array $data
	 * The array of data with which to update the Enquiry.
	 *
	 * @param string $externalId
	 * The unique external Enquiry identifier.
	 *
	 * @return void.
	 */
	public function updateEnquiry($data, $externalId)
    {
		$where = $this->quoteInto('RefNo = ? ', (string)$externalId);
        $this->update($data, $where);
	}
    
    public function getRenewalPremium($months, $irn)
    {
        $enquiryRow = $this->_getEnquiryRow($irn);
        $prodID=$enquiryRow->ProductID;

        if ($prodID == 17) {
            $prodID = 10;
        }

        if ($prodID == 18) {
            $prodID = 9;
        }

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('e' => $this->_name), array());
        $select->joinLeft(array('p' => 'Product'), 'p.ID = '.$prodID, array());
        $select->joinLeft(array('r' => 'Price'), 'r.ProductID = p.ID', array('Price'));
        $select->joinLeft(array('t' => 'Tenant'), 't.ID = e.TenantID', array());
        $select->joinLeft(array('b' => 'PriceBand'), 'b.Name = r.Band', array());
        $select->joinLeft(array('a' => 'Agent'), 'a.ID = e.AgentID', array());
        
        $select->where('r.Duration = ?', $months);
        $select->where('a.TypeID = r.AgentTypeID');
        $select->where('t.RentShare >= b.Low');
        $select->where('t.RentShare <= b.High');
        $select->where('e.ID = ?', $irn);
        $select->where('r.renewal = 1');
        $select->where('r.Guarantor = 0');
        
        $premium = $this->fetchRow($select);
        
        if (!isset($premium) || count($premium) < 1) {
            // no price found, assume a price deal
            $select = $this->select();
            $select->setIntegrityCheck(false);
            
            $select->from(array('e' => $this->_name), array());
            $select->joinLeft(array('p' => 'Product'), 'p.ID = '.$prodID, array());
            $select->joinLeft(array('r' => 'Price'), 'r.ProductID = p.ID', array('Price'));
            $select->joinLeft(array('t' => 'Tenant'), 't.ID = e.TenantID', array());
            $select->joinLeft(array('b' => 'PriceBand'), 'b.Name = r.Band', array());
            $select->joinLeft(array('a' => 'homeletuk_com.newagents'), 'a.agentschemeno = e.AgentID', array());
            
            $select->where('r.Duration = ?', $months);
            
            // ieeeeee!
            $select->where(new Zend_Db_Expr('(a.premier = "no" AND r.AgentTypeID = 1) OR (a.premier = "yes" AND r.AgentTypeID = 2)'));
            
            $select->where('t.RentShare >= b.Low');
            $select->where('t.RentShare <= b.High');
            $select->where('e.ID = ?', $irn);
            $select->where('r.renewal = 1');
            $select->where('r.Guarantor = 0');
            $premium = $this->fetchRow($select);
        }
        
        if (isset($premium) || count($premium) > 0)
        {
            return $premium->Price;
        }
        
        return null;
    }
	
	/**
     * Returns the AgentID for the reference from enquiry table.
     *
     * @param string $enquiryId
     * The unique Enquiry identifier.
     * May be the IRN (internal refno) or the ERN (external refno).
     *
     * @return string
     * The AgentID ~ AgentSchemeNo
     */
    public function getReferenceAgentID($enquiryId)
    {
        $enquiryRow = $this->_getEnquiryRow($enquiryId);
        return $enquiryRow->AgentID;
    }

    public function getAllReferences($referenceIds, $refSearch, $orderBy = array())
    {
        $results = array();

        // Transform order by parameter
        if (count($orderBy) > 0)
        {
            $transformedOrderBy = array();
            foreach ($orderBy as $orderByField => $orderByDir) {
                // Validate direction
                if (!in_array(strtoupper($orderByDir), array('ASC', 'DESC'))) {
                    // Invalid direction, ignore
                    continue;
                }

                $transformedOrderBy[] = "$orderByField $orderByDir";
            }
        }
        else
        {
            // Default sort
            $transformedOrderBy = array('start_date DESC');
        }

        $select = $this->select()
                       ->setIntegrityCheck(false)
                       ->from(array('e' => $this->_name), array())
                       ->columns(array('e.*', 'e.refno as externalrefno', new Zend_Db_Expr('IF (e.conclusion != "", "Complete", "Pending") as status')))
                       ->join(array('p' => 'progress'), 'p.refno = e.refno')
                       ->join(array('t' => 'Tenant'), 't.ID = e.TenantID')
                       ->join(array('r' => 'property'), 'r.refno = e.proprefno')
                       ->where('e.refno IN (?)', $referenceIds)
                       ->where('p.paidfor = "Yes"')
                       ->order($transformedOrderBy);

        // Restrict policy number search field
        if ($refSearch != '') {
            $select->having('e.refno LIKE ?', '%' . $refSearch . '%');
        }

        $rowSet = $this->fetchAll($select);
        if (count($rowSet) > 0) {
            foreach ($rowSet as $row) {
                $reference = new Model_Referencing_Reference();
                $reference->internalId = $row['ID'];
                $reference->externalId = $row['externalrefno'];

                $reference->propertyLease = new Model_Referencing_PropertyLease();
                $reference->propertyLease->tenancyStartDate = new Zend_Date($row['start_date'], Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);

                $reference->propertyLease->address = new Model_Core_Address();
                $reference->propertyLease->address->addressLine1 = $row['address1'];
                $reference->propertyLease->address->addressLine2 = $row['address2'];
                $reference->propertyLease->address->town = $row['town'];
                $reference->propertyLease->address->postCode = $row['postcode'];

                $reference->referenceSubject = new Model_Referencing_ReferenceSubject();
                $reference->referenceSubject->name = new Model_Core_Name();
                $reference->referenceSubject->name->title = $row['title'];
                $reference->referenceSubject->name->firstName = $row['firstname'];
                $reference->referenceSubject->name->middleName = $row['middlename'];
                $reference->referenceSubject->name->lastName = $row['lastname'];

                $reference->status = $row['status'];

                $results[] = $reference;
            }
        }

        return $results;
    }
}
