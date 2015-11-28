<?php

/**
 * Referencing Munt Manager class providing conversion services between the new
 * Referencing Object Model (ROM) and the legacy, munting datasources.
 */
class Manager_ReferencingLegacy_Munt
{
    /**
     * Creates a new reference in the old datasource.
     *
     * Involves creating an Enquiry record and a corresponding progress record.
     *
     * @return Model_Referencing_Reference
     * A minimal Reference object encapsulating internal and external identifiers
     * only, corresponding to the new record inserted in the legacy datasources.
     */
    public function createReference()
    {
        // Create an Enquiry record in the legacy datasource, then pull the identifiers
        // from this into the new datasource.
        $legacyEnquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
        $reference = $legacyEnquiryDatasource->createNewEnquiry();

        //Insert a progress record to pair with the Enquiry.
        $progressDatasource = new Datasource_ReferencingLegacy_Progress();
        $progressDatasource->createNewProgress((string)$reference->externalId);

        return $reference;
    }

    /**
     * Searches the legacy DB for references that match the given criteria.
     *
     * @param $agentschemeno
     * @param $criteria
     * @param $orderBy
     * @param $pageNumber
     * @param $rowLimit
     * @return array Array of arrays of bare results, empty array if no results.
     */
    public function searchLegacyReferences($agentschemeno, $criteria, $orderBy, $pageNumber, $rowLimit, $offset = null)
    {
        $referenceLegacySearchDatasource = new Datasource_ReferencingLegacy_ReferenceSearch();
        return $referenceLegacySearchDatasource->searchReferences($agentschemeno,
            $criteria, $orderBy, $pageNumber, $rowLimit, $offset);
    }

    /**
     * Retrieves the specified reference from the legacy (munting) datasources.
     *
     * @param mixed $referenceId
     * The unique Reference identifier. May be the IRN (internal refno) or the ERN (external
     * refno).
     *
     * @return mixed
     * The Reference details, encapsulated in a Model_Referencing_Reference object,
     * or null if the Reference cannot be found.
     */
    public function getReference($referenceId)
    {
        $legacyEnquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
        return $legacyEnquiryDatasource->getEnquiry($referenceId);
    }


    /**
     * Returns a minimal Reference object.
     *
     * The minimal Reference object holds attributes that are directly its own, and does
     * NOT hold values for linked objects (such as the ReferenceSubject, PropertyLease etc).
     * Therefore, client code would not be able, for example, so access $reference->productSelection.
     * This method is useful for quickly accessing the internal and external reference numbers.
     *
     * @param mixed $referenceId
     * The unique Reference identifier. May be the IRN (internal refno) or the ERN (external
     * refno).
     *
     * @throws Zend_Exception
     * @return mixed
     * The minimal Reference details, encapsulated in a Model_Referencing_Reference object,
     * or null if the Reference cannot be found.
     */
    public function getMinimalReference($referenceId)
    {
        throw new Zend_Exception(get_class() . '::' . __FUNCTION__ . ': not yet implemented.');
    }

    /**
     * Copies the referencing data from the new datasources to the old.
     *
     * Allows newly entered references visibility on the HRT.
     *
     * @param Model_Referencing_Reference $reference
     * The Reference to muntify.
     *
     * @return void
     */
    public function updateReference($reference)
    {
        if (!empty($reference->propertyLease->prospectiveLandlord)) {
            // The Enquiry.newllrefno is the prospective landord details stored in
            // the landlordref table.
            $name = $reference->propertyLease->prospectiveLandlord->name;
            $address = $reference->propertyLease->prospectiveLandlord->address;
            $contactDetails = $reference->propertyLease->prospectiveLandlord->contactDetails;

            $landlordRefDatasource = new Datasource_ReferencingLegacy_LandlordRef();
            $muntingEnquiryFields['newllrefno'] = $landlordRefDatasource->insertLandlordRef($name, $address, $contactDetails);
        }

        // The Enquiry.lrefrefno is the current landlord details.
        $muntingEnquiryFields['lrefrefno']  = '';

        if (!empty($reference->referenceSubject->residences)) {
            foreach ($reference->referenceSubject->residences as $residence) {
                if ($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
                    if ($residence->status == Model_Referencing_ResidenceStatus::TENANT) {
                        //If product is a full reference, then record the landlord reference details
                        if ($reference->productSelection->product->variables[Model_Referencing_ProductVariables::FULL_REFERENCE] == 1) {
                            $name = $residence->refereeDetails->name;
                            $address = $residence->refereeDetails->address;
                            $contactDetails = $residence->refereeDetails->contactDetails;

                            $muntingEnquiryFields['lrefrefno'] = $landlordRefDatasource->insertLandlordRef(
                                $name, $address, $contactDetails);
                        }
                    }
                    break;
                }
            }
        }

        //The Enquiry.proprefno holds the property lease details.
        $propertyLeaseDatasource = new Datasource_ReferencingLegacy_PropertyLease();
        $muntingEnquiryFields['proprefno'] = $propertyLeaseDatasource->insertPropertyLease($reference->propertyLease);


        //The Enquiry.TenantID holds the ReferenceSubject details.
        $referenceSubjectDatasource = new Datasource_ReferencingLegacy_ReferenceSubject();
        $muntingEnquiryFields['TenantID'] = $referenceSubjectDatasource->insertReferenceSubject(
            $reference->referenceSubject);


        //The Enquiry.ta[1-3]refno hold indexes to the tenant_address table, which holds up to
        //three of the reference subject addresses.
        $currentResidence = 0;
        $firstPreviousResidence = 0;
        $secondPreviousResidence = 0;

        foreach($reference->referenceSubject->residences as $residence) {
            switch($residence->chronology) {
                case Model_Referencing_ResidenceChronology::CURRENT:
                    $currentResidence = $residence;
                    break;

                case Model_Referencing_ResidenceChronology::FIRST_PREVIOUS:
                    $firstPreviousResidence = $residence;
                    break;

                case Model_Referencing_ResidenceChronology::SECOND_PREVIOUS:
                    $secondPreviousResidence = $residence;
                    break;
            }
        }

        //Initial settings for the ta[1-3]refnos
        $muntingEnquiryFields['ta1refno'] = 0;
        $muntingEnquiryFields['ta2refno'] = 0;
        $muntingEnquiryFields['ta3refno'] = 0;

        //Cycle through the residences and save each.
        $residenceDatasource = new Datasource_ReferencingLegacy_Residences();
        for($i = 0; $i < count($reference->referenceSubject->residences); $i++) {
            if(count($reference->referenceSubject->residences) == ($i+1)) {
                $isLast = true;
            }
            else {
                $isLast = false;
            }

            switch($i) {
                case 0:
                    $muntingEnquiryFields['ta1refno'] = $residenceDatasource->insertResidence($currentResidence, $isLast);
                    break;

                case 1:
                    $muntingEnquiryFields['ta2refno'] = $residenceDatasource->insertResidence($firstPreviousResidence, $isLast);
                    break;

                case 2:
                    $muntingEnquiryFields['ta3refno'] = $residenceDatasource->insertResidence($secondPreviousResidence, $isLast);
                    break;
            }
        }


        //If the first residence is overseas, then ensure its ID is stored the Enquiry.foreignaddress
        //field.
        $muntingEnquiryFields['foreignaddress'] = 0;
        if($currentResidence->address->isOverseasAddress) {
            $muntingEnquiryFields['foreignaddress'] = $muntingEnquiryFields['ta1refno'];
            $muntingEnquiryFields['ta1refno'] = 0;
        }

        //Update the Enquiry record.
        $this->_pushToEnquiryMunt($reference, $muntingEnquiryFields);

        //Insert the Occupation records
        $this->_pushToEmploymentMunt($reference);

        //Update the progress record
        $progressDatasource = new Datasource_ReferencingLegacy_Progress();
        $progressDatasource->updateProgress($reference);
    }

    protected function _pushToEmploymentMunt($reference)
    {
        $occupationDatasource = new Datasource_ReferencingLegacy_Occupations();

        //If the reference subject is self employed and has a future employer, then the Munt only
        //recognizes the future employer and does not record the current....
        $occupationManager = new Manager_Referencing_Occupation();
        $currentOccupation = $occupationManager->findSpecificOccupation(
            $reference->referenceSubject->occupations,
            Model_Referencing_OccupationChronology::CURRENT,
            Model_Referencing_OccupationImportance::FIRST);

        $futureOccupation = $occupationManager->findSpecificOccupation(
            $reference->referenceSubject->occupations,
            Model_Referencing_OccupationChronology::FUTURE,
            Model_Referencing_OccupationImportance::FIRST);

        if($currentOccupation->type == Model_Referencing_OccupationTypes::SELFEMPLOYMENT) {
            if(!empty($futureOccupation)) {
                if($futureOccupation->type == Model_Referencing_OccupationTypes::EMPLOYMENT) {
                    $occupationDatasource->insertOccupation($futureOccupation, $reference->externalId);
                    return;
                }
            }
        }

        // Loop round the occupations. If the occupation is insertable, then
        // insert it.
        foreach($reference->referenceSubject->occupations as $occupation) {
            switch($occupation->type) {
                case Model_Referencing_OccupationTypes::EMPLOYMENT:
                case Model_Referencing_OccupationTypes::CONTRACT:
                case Model_Referencing_OccupationTypes::SELFEMPLOYMENT:
                case Model_Referencing_OccupationTypes::INDEPENDENT:
                case Model_Referencing_OccupationTypes::RETIREMENT:
                    //Insert the occupation
                    $occupationDatasource->insertOccupation($occupation, $reference->externalId);
                    break;
            }
        }
    }

    /**
     * @todo
     * The data protection needs to be implemented here.
     */
    protected function _pushToEnquiryMunt($reference, $muntingEnquiryFields)
    {
        //Push to Enquiry munt
        if($reference->completionMethod == Model_Referencing_ReferenceCompletionMethods::ONE_STEP) {
            $completionMethod = 'complete';
        }
        else {
            $completionMethod = 'email';
        }

        //We have
        $data = array(
            'PolicyLength' => $reference->productSelection->duration,
            'generatedby' => 'Landlord',
            'compmethod' => $completionMethod,
            'AgentID' => 1403796,
            'csuid' => 0,
            'agentrefno' => 0,
            'landrefno' => $reference->customer->legacyCustomerId,
            'proprefno' => $muntingEnquiryFields['proprefno'],
            'TenantID' => $muntingEnquiryFields['TenantID'],
            'ta1refno' => $muntingEnquiryFields['ta1refno'],
            'ta2refno' => $muntingEnquiryFields['ta2refno'],
            'ta3refno' => $muntingEnquiryFields['ta3refno'],
            'foreignaddress' => $muntingEnquiryFields['foreignaddress'],
            'bankrefno' => 0,
            'lrefrefno' => $muntingEnquiryFields['lrefrefno'],
            'newllrefno' => $muntingEnquiryFields['newllrefno'],
            'emptype' => $this->_getCurrentOccupation($reference),
            'payref' => 0,
            'termsagreedby' => 'Landlord',    //Terms agreed by is always 'Landlord' for private references
            'conclusion' => '',
            'policynumber' => '',
            'is2002' => 'no',
            'origin' => '',
            'centre' => 'Lincoln',
            'assessor' => 0,
            'nochange' => 'unlocked',
            'emailoveride' => 0,
            'ID' => $reference->internalId,
            'resproofonfile' => 'no',
            'autorejected' => 'no',
            'overridden' => 'no',
            'ProductID' => $reference->productSelection->product->key,
            'PreviousID' => 0,
            'agentStatusAtStart' => null,
            'Guarantor' => 0,
            'Renewal' => 0,
            'referrer' => null
        );

        $enquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
        $enquiryDatasource->updateEnquiry($data, (string)$reference->externalId);
    }


    /**
     * Utility method for the EnquiryMunt.
     */
    protected function _getCurrentOccupation($reference)
    {
        $occupationType = null;

        foreach($reference->referenceSubject->occupations as $occupation) {
            if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
                switch($occupation->type) {

                    case Model_Referencing_OccupationTypes::EMPLOYMENT:
                        $occupationType = 'Employed';
                        break;

                    case Model_Referencing_OccupationTypes::CONTRACT:
                        $occupationType = 'On Contract';
                        break;

                    case Model_Referencing_OccupationTypes::SELFEMPLOYMENT:
                        $occupationType = 'Self Employed';
                        break;

                    case Model_Referencing_OccupationTypes::INDEPENDENT:
                        $occupationType = 'of Independent Means';
                        break;

                    case Model_Referencing_OccupationTypes::RETIREMENT:
                        $occupationType = 'Retired';
                        break;

                    case Model_Referencing_OccupationTypes::STUDENT:
                        $occupationType = 'Student';
                        break;

                    case Model_Referencing_OccupationTypes::UNEMPLOYMENT:
                        $occupationType = 'Unemployed';
                        break;
                }
            }
            break;
        }

        return $occupationType;
    }

    /**
     * Get all references for the selected reference numbers
     *
     * @param array $referenceIds Reference numbers
     * @param string $refnoSearch Refno search restriction
     * @param array $orderby Array of order by columns
     * @return array List of references
     */
    public function getAllReferences($referenceIds, $refnoSearch, $orderby)
    {
        $legacyEnquiry = new Datasource_ReferencingLegacy_Enquiry();
        return $legacyEnquiry->getAllReferences($referenceIds, $refnoSearch, $orderby);
    }

    /**
     *  Get the latest report object for the selected reference
     * 
     * @param string $refno Reference number
     * @param string $type Optional type restriction
     */
    public function getLatestReport($refno, $type = null) {
        $legacyReport = new Datasource_ReferencingLegacy_ReportHistory();
        return $legacyReport->getLatestReport($refno, $type);
    }
}
