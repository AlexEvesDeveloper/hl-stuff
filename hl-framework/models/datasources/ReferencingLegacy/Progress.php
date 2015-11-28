<?php

/**
* Model definition for the referencing progress datasource.
*/
class Datasource_ReferencingLegacy_Progress extends Zend_Db_Table_Multidb {

    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'progress';
    protected $_FIRST = 'refno';
    /**#@-*/


	/**
     * Inserts a new, empty progress record into the datasource.
     *
     * Should be called whenever a new Enquiry record is created, so that
     * it can be correctly paired with that.
     *
     * @param string $refNo
     * The unique external Enquiry identifier.
     *
	 * @return void
     */
	public function createNewProgress($refNo) {

		$data = array('refno' => (string)$refNo);
		$this->insert($data);
	}


	/**
	 * Inserts a record into the legacy progress table.
	 *
	 * @param Model_Referencing_Reference
	 * Encapsulates the Reference data.
	 *
	 * @return void
	 */
	public function insertProgress($reference) {

		$this->_upsertProgress($reference, 'insert');
	}


	/**
	 * Updates an existing progress record in the legacy progress table.
	 *
	 * @param Model_Referencing_Reference
	 * Encapsulates the Reference data.
	 *
	 * @return void
	 */
	public function updateProgress($reference) {

		$this->_upsertProgress($reference, 'update');
	}


	/**
	 * Internal method, upserts progress records.
	 *
	 * @param Model_Referencing_Reference $reference
	 */
	protected function _upsertProgress($reference, $upsertType) {

		//Capture the bank details progress.
		if(!empty($reference->referenceSubject->bankAccount)) {

			$accountNumber = $reference->referenceSubject->bankAccount->accountNumber;
			$sortCode = $reference->referenceSubject->bankAccount->sortCode;
			if(!empty($accountNumber) && !empty($sortCode)) {

				$bankDetails = 'Complete';
			}
			else {

				$bankDetails = 'Incomplete';
			}
		}
		else {

			$bankDetails = 'N/A';
		}


		//Set the start date
		$startDate = Zend_Date::now();
		$startDate = $startDate->toString(Zend_Date::ISO_8601);


		//If the product is a credit reference, provide minimal data.
		if(isset($reference->productSelection->product->variables[Model_Referencing_ProductVariables::CREDIT_REFERENCE])) {

			$fullReference = 'N/A';
			$landLetterSent = 'N/A';
			$employLetterSent = 'N/A';
			$employDetails = 'N/A';
			$landlordRef = 'N/A';
			$employerRef = 'N/A';
		}
		else {

			$fullReference = 'No';

			//Identify the value for 'landlettersent'. This will be 'N/A' if the reference subject
			//does not have a current landlord, and 'No' if they do.
			$residences = $reference->referenceSubject->residences;

			$residenceManager = new Manager_Referencing_Residence();
			$currentResidence = $residenceManager->findSpecificResidence($residences, Model_Referencing_ResidenceChronology::CURRENT);

			if(empty($currentResidence->refereeDetails)) {

				$landLetterSent = 'N/A';
			}
			else {

				$landLetterSent = 'No';
			}


			//Identify the value for 'employlettersent'. This will be 'N/A' if the applicant is unemployed
			//with no future occupation. Else will be 'No'.
			$occupations = $reference->referenceSubject->occupations;

			$occupationManager = new Manager_Referencing_Occupation();
			$currentOccupation = $occupationManager->findSpecificOccupation($occupations,
				Model_Referencing_OccupationChronology::CURRENT, Model_Referencing_OccupationImportance::FIRST);

			$employLetterSent = 'No';
			if($currentOccupation->type == Model_Referencing_OccupationTypes::UNEMPLOYMENT) {

				if(count($occupations) == 1) {

					$employLetterSent = 'N/A';
				}
			}
			else if($currentOccupation->type == Model_Referencing_OccupationTypes::STUDENT) {

				$employLetterSent = 'N/A';
			}


			//The 'employdetails' field is always set to 'Complete' for full references.
			$employDetails = 'Complete';


			//Determine the value for 'landlordref'. This should be set to 'N/A' if the reference
			//subject is a homeowner or living with relatives. Else it should be set to
			//'Incomplete'.
			$landlordRef = 'N/A';
			if($currentResidence->status == Model_Referencing_ResidenceStatus::TENANT) {

				$landlordRef = 'Incomplete';
			}


			//Deterine the value for 'employerref'. This should be 'N/A' if the $employLetterSent
			//is 'N/A', else should be 'Incomplete'.
			$employerRef = 'Incomplete';
			if($employLetterSent == 'N/A') {

				$employerRef = 'N/A';
			}
		}


		//Upsert the record.
		$data = array(
			'propertydetails' => 'Complete',
			'propertylandlorddetails' => 'Complete',
			'tenantdetails' => 'Complete',
			'tenantaddressdetails' => 'Complete',
			'bankdetails' => $bankDetails,
			'plandlorddetails' => 'Complete',
			'llapproved' => 'Yes',
			'termsagreed' => 'Yes',
			'creditreference' => 'No',
			'start_time' => $startDate,

        	'fullreference' => $fullReference,
        	'landlettersent' => $landLetterSent,
        	'employlettersent' => $employLetterSent,
        	'employdetails' => $employDetails,
        	'landlordref' => $landlordRef,
        	'employerref' => $employerRef
		);

		if($upsertType == 'insert') {

			$data['refno'] = $reference->externalId;
			$this->insert($data);
		}
		else {

			$where = $this->quoteInto('refno = ? ', (string)$reference->externalId);
        	$this->update($data, $where);
		}
    }


    /**
     * Returns the progress details corresponding to the id passed in.
     *
     * @param string $enquiryId
     * The external Enquiry identifier.
     *
     * @return mixed
     * Returns the matching details in a Model_Referencing_Progress object, or
     * null if no match found.
     */
    public function getByEnquiry($enquiryId) {

        $select = $this->select();
        $select->where('refno = ? ', (string)$enquiryId);
        $progressRow = $this->fetchRow($select);

        if(empty($progressRow)) {

            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ':Unable to find progress record.');
            $returnVal = null;
        }
        else {

            $progress = new Model_Referencing_Progress();
			$progress->items = array();

			//Only record the APPLICABLE ProgressItems.
			if($progressRow->start_time != '0000-00-00 00:00:00') {

				$progressItem = new Model_Referencing_ProgressItem();
				$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::STARTED;
				$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
				$progressItem->itemCompletionTimestamp = new Zend_Date($progressRow->start_time, Zend_Date::ISO_8601);
				$progress->items[] = $progressItem;
			}

			switch($progressRow->propertydetails) {

				case 'Complete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::PROPERTY_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'Incomplete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::PROPERTY_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->propertylandlorddetails) {

				case 'Complete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::PROPERTY_LANDLORD_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'Incomplete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::PROPERTY_LANDLORD_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->tenantdetails) {

				case 'Complete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::REFERENCE_SUBJECT_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'Incomplete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::REFERENCE_SUBJECT_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->tenantaddressdetails) {

				case 'Complete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::RESIDENTIAL_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'Incomplete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::RESIDENTIAL_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->plandlorddetails) {

				case 'Complete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::RESIDENTIAL_REFEREE_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'Incomplete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::RESIDENTIAL_REFEREE_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->employdetails) {

				case 'Complete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::OCCUPATION_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'Incomplete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::OCCUPATION_DETAILS_SUBMITTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->termsagreed) {

				case 'Yes':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::TERMS_AGREED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'No':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::TERMS_AGREED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->paidfor) {

				case 'Yes':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::PAYMENT_ARRANGED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'No':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::PAYMENT_ARRANGED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->txtobureau) {

				case 'Yes':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::CREDIT_DATA_REQUESTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progressItem->itemCompletionTimestamp = new Zend_Date($progressRow->tx_time, Zend_Date::ISO_8601);
					$progress->items[] = $progressItem;
					break;

				case 'No':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::CREDIT_DATA_REQUESTED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->landlettersent) {

				case 'Yes':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::RESIDENTIAL_REFEREE_LETTER_SENT;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'No':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::RESIDENTIAL_REFEREE_LETTER_SENT;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->employlettersent) {

				case 'Yes':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::CURRENT_OCCUPATION_REFEREE_LETTER_SENT;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'No':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::CURRENT_OCCUPATION_REFEREE_LETTER_SENT;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->additionalemploylettersent) {

				case 'Yes':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::SECOND_OCCUPATION_REFEREE_LETTER_SENT;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'No':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::SECOND_OCCUPATION_REFEREE_LETTER_SENT;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->futureemploylettersent) {

				case 'Yes':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::FUTURE_OCCUPATION_REFEREE_LETTER_SENT;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'No':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::FUTURE_OCCUPATION_REFEREE_LETTER_SENT;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->tenantlettersent) {

				case 'Yes':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::REFERENCE_SUBJECT_LETTER_SENT;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'No':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::REFERENCE_SUBJECT_LETTER_SENT;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->landlordref) {

				case 'Complete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::RESIDENTIAL_REFERENCE_RECEIVED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'Incomplete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::RESIDENTIAL_REFERENCE_RECEIVED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			switch($progressRow->employerref) {

				case 'Complete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::OCCUPATION_REFERENCES_RECEIVED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
					$progress->items[] = $progressItem;
					break;

				case 'Incomplete':
					$progressItem = new Model_Referencing_ProgressItem();
					$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::OCCUPATION_REFERENCES_RECEIVED;
					$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
					$progress->items[] = $progressItem;
					break;
			}

			if($progressRow->intrep_time == '0000-00-00 00:00:00') {

				$progressItem = new Model_Referencing_ProgressItem();
				$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::INTERIM_REPORT_BUILT;
				$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
				$progress->items[] = $progressItem;
			}
			else {

				$progressItem = new Model_Referencing_ProgressItem();
				$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::INTERIM_REPORT_BUILT;
				$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
				$progressItem->itemCompletionTimestamp = new Zend_Date($progressRow->intrep_time, Zend_Date::ISO_8601);
				$progress->items[] = $progressItem;
			}

			if($progressRow->finrep_time == '0000-00-00 00:00:00') {

				$progressItem = new Model_Referencing_ProgressItem();
				$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::FINAL_REPORT_BUILT;
				$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
				$progress->items[] = $progressItem;
			}
			else {

				$progressItem = new Model_Referencing_ProgressItem();
				$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::FINAL_REPORT_BUILT;
				$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
				$progressItem->itemCompletionTimestamp = new Zend_Date($progressRow->finrep_time, Zend_Date::ISO_8601);
				$progress->items[] = $progressItem;
			}

        	if($progressRow->firstfin_time == '0000-00-00 00:00:00') {

				$progressItem = new Model_Referencing_ProgressItem();
				$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::FINISHED;
				$progressItem->itemState = Model_Referencing_ProgressItemStates::INCOMPLETE;
				$progress->items[] = $progressItem;
			}
			else {

				$progressItem = new Model_Referencing_ProgressItem();
				$progressItem->itemVariable = Model_Referencing_ProgressItemVariables::FINISHED;
				$progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
				$progressItem->itemCompletionTimestamp = new Zend_Date($progressRow->firstfin_time, Zend_Date::ISO_8601);
				$progress->items[] = $progressItem;
			}

			$returnVal = $progress;
        }

        return $returnVal;
    }
}

?>