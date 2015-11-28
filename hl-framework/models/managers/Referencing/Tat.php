<?php

/**
 * Tenancy application tracker class providing TAT services.
 */
class Manager_Referencing_Tat {

	public $_reference;
	
	/*
      * Build constuct
      * 
      *  @param mixed $enquiryId
      * 
      * @return void
     */
	public function __construct($enquiryId=null) {
		
		if(is_null($enquiryId)) {
			
	         throw new Zend_Exception("Enquiry identifier is null");
		}
		else{
			
			$muntManager = new Manager_ReferencingLegacy_Munt();
        	$this->_reference = $muntManager->getReference($enquiryId);
		}
	}
	
	/**
	 * Specifies whether a TAT is applicable to the reference.
     *
     * TATs are not always applicable for various reasons, and this method holds the business
     * rules to identify this. Should be called before any other TAT-related action, so that
     * additional actions can be averted if a TAT is not applicable.
     *
     * TATs are not applicable to some products, cannot be used if the reference subject (tenant) does
     * not have an email address, cannot be used if older than 30 days, cannot be used if the
     * agent has opted out of the TAT facility, and cannot be used if the reference subject
     * is a guarantor.
     * 
	 * @param void
     *
     * @return boolean
     * True if a TAT is applicable, false otherwise.
     *
     * @todo
     * Currently uses the legacy missing information manager. Should use the new one when the new
     * missing information manager is built.
	 */
    public function isTatApplicable() {

        //First test: is the reference cancelled?
        if($this->_reference->status->state == Model_Referencing_ReferenceStates::CANCELLED) {

            return false;
        }

		//Next test: does the product merit a TAT?
        $product = $this->_reference->productSelection->product;
        
        if(empty($product->variables)) {
		
            return false;
        }
        else if(isset($product->variables[Model_Referencing_ProductVariables::CREDIT_REFERENCE])) {
          
            return false;
        }

        //Next test: only tenants can login.
        if($this->_reference->referenceSubject->type != Model_Referencing_ReferenceSubjectTypes::TENANT) {

            return false;
        }
		
        //Next test: is the reference over >= 30 days?
        if($this->isTatExpired()) {

            return false;
        }


        //Next test: has the agent opted out of the TAT?
        $tatOptedStatusDatasource = new Datasource_Referencing_TatOptedStatus();
		$tatOptedStatus = $tatOptedStatusDatasource->getOptedStatus($this->_reference->customer->customerId);
        if($tatOptedStatus == Model_Referencing_TatOptedStates::OPTED_OUT) {

            return false;
        }

        return true;
    }


    /**
     * Identifies if the TAT has expired.
     *
     * @param void
     * The unique Enquiry identifier (internal or external).
     *
     * @return boolean
     * Returns true if the TAT has expired, false otherwise.
     */
    public function isTatExpired() {

      
        //Obtain the start time from the progress datasource.
    	$params = Zend_Registry::get('params');

    	$progressItem = Manager_Referencing_Progress::findSpecificProgressItem($this->_reference->progress, Model_Referencing_ProgressItemVariables::STARTED);
        
    	//Determine if the TAT has exceeded its activation period.
        $tatDeactivationTime = $progressItem->itemCompletionTimestamp->add($params->tat->activationPeriod, Zend_Date::DAY);
        $currentTime = Zend_Date::now();

        if($currentTime->isLater($tatDeactivationTime)) {

            $returnVal = true;
        }
        else {

        	$returnVal = false;
        }

        return $returnVal;
        
    }


    /**
     * Identifies if the user is allowed to use the TAT.
     *
     * @param mixed $agentSchemeNumber
     * The unique letting agent identifier. May be integer or string.
     *
     *
     * @param Zend_Date $dob
     * The reference subject's date of birth.
     *
     * @return boolean
     * True if the user is allowed to login, false otherwise.
     */
    public function isLoginValid($agentSchemeNumber, $dob) {

        //If the agentschemeno and reference subject DOB all match on the $enquiryId passed in,
        //then the login is valid.
        
        //Agent match check
			$isAgentMatch = false;
			if($this->_reference->customer->customerType == Model_Referencing_CustomerTypes::AGENT){
				if($this->_reference->customer->customerId == $agentSchemeNumber) {
	
					$isAgentMatch = true;
				}
			}
		
            //Dob match check
            $isDobMatch = false;
            if(!empty($this->_reference->referenceSubject)) {
        
            	$referenceSubjectDob = $this->_reference->referenceSubject->dob;
            	if(!empty($this->_reference->referenceSubject->dob)) {
        
					if($referenceSubjectDob->equals($dob, Zend_Date::DATES)) {
						
						$isDobMatch = true;
					}
            	}
            }
        
        //And finalize
        if($isAgentMatch && $isDobMatch) {

            $returnVal = true;
        }
        else {

            $returnVal = false;
        }
        return $returnVal;
    }


    /**
     * Returns a TAT object encapsulating details of the Enquiry.
     *
     * @param void
     * The unique Enquiry identifier (internal or external). May be integer or string.
     *
     * @return mixed
     * Returns a Model_Referencing_Tat object holds details of the reference,
     * or null if not found.
     */
    public function getTat() {

        $tat = new Model_Referencing_Tat();

        //Set the isInvitationSent attribute.
        $tatDatasource = new Datasource_Referencing_TatInvitation();
        $tat->isInvitationSent = $tatDatasource->getIsTatInvitationSent($this->_reference->externalId);
		
        //Set the referenceSubject attribute.
       
        $referenceSubject = $this->_reference->referenceSubject;
        $tat->referenceSubject = $referenceSubject;
		
        //Set the propertyLease attribute.
        $tat->propertyLease = $this->_reference->propertyLease;
		
		
		//Arrive at the Enquiry state by a cascade: Start by assuming the Enquiry state is complete.
		$tat->enquiryStatus = Model_Referencing_TatStates::REFERENCE_COMPLETE;
		
		//Next identify if the state should be changed to 'In Progress' based on the
		//current Enquiry conclusion
		switch($this->_reference->status->state) {
			
			case Model_Referencing_ReferenceStates::INPROGRESS:
			case Model_Referencing_ReferenceStates::INCOMPLETE:
				$tat->enquiryStatus = Model_Referencing_TatStates::REFERENCE_INPROGRESS;
				break;
		}
		
		
		
		$progressItem=Manager_Referencing_Progress::findSpecificProgressItem($this->_reference->progress, Model_Referencing_ProgressItemVariables::FINISHED);
		
		//Finally, override the conclusion if the progress result is not complete.
		if($progressItem->itemState != Model_Referencing_ProgressItemStates::COMPLETE) {

        	//The progress object also has to indicate complete - this will allow us to detect
        	//references that have been reopened.
            $tat->enquiryStatus = Model_Referencing_TatStates::REFERENCE_INPROGRESS;
        }

    
		//Set the occupation attributes.
        foreach($referenceSubject->occupations as $occupation) {
			
            //Set the occupation reference status.
            if(empty($occupation->referencingDetails)) {

            	$isReferencedString = Model_Referencing_TatStates::REFERENCE_ITEM_NOTAPPLICABLE;
            }
            else {
            		            
	            if($occupation->isComplete) {
					$isReferencedString = Model_Referencing_TatStates::REFERENCE_ITEM_COMPLETE;
				}
	            else{
              		$isReferencedString = Model_Referencing_TatStates::REFERENCE_ITEM_INPROGRESS;
				}
				
			}
				
            //Now identify which occupation we are dealing with.
            if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {

                $tat->futureOccupationReferenceStatus = $isReferencedString;
            }
            else if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {

                if($occupation->importance == Model_Referencing_OccupationImportance::FIRST) {

                    $tat->currentOccupationReferenceStatus = $isReferencedString;
                }
                else {

                    $tat->secondOccupationReferenceStatus = $isReferencedString;
                }
            }
        }
		    
        //Some of the occupation attributes may not be applicable. Set them to N/A as appropriate.
		if(empty($tat->currentOccupationReferenceStatus)) {

            $tat->currentOccupationReferenceStatus = Model_Referencing_TatStates::REFERENCE_ITEM_NOTAPPLICABLE;
        }
        if(empty($tat->secondOccupationReferenceStatus)) {

            $tat->secondOccupationReferenceStatus = Model_Referencing_TatStates::REFERENCE_ITEM_NOTAPPLICABLE;
        }
        if(empty($tat->futureOccupationReferenceStatus)) {

            $tat->futureOccupationReferenceStatus = Model_Referencing_TatStates::REFERENCE_ITEM_NOTAPPLICABLE;
        }


        //Set the landlord attribute.
		$residenceManager = new Manager_Referencing_Residence();
		$currentResidence = $residenceManager->findSpecificResidence(
			$this->_reference->referenceSubject->residences,
			Model_Referencing_ResidenceChronology::CURRENT);
		
        if($currentResidence->status != Model_Referencing_ResidenceStatus::TENANT) {

            $tat->currentResidentialReferenceStatus = Model_Referencing_TatStates::REFERENCE_ITEM_NOTAPPLICABLE;
        }
        else {

            if(empty($currentResidence->referencingDetails)) {

                $tat->currentResidentialReferenceStatus = Model_Referencing_TatStates::REFERENCE_ITEM_INPROGRESS;
            }
			else if($currentResidence->referencingDetails->submissionType == null) {
				
				$tat->currentResidentialReferenceStatus = Model_Referencing_TatStates::REFERENCE_ITEM_INPROGRESS;
			}
            else {

                $tat->currentResidentialReferenceStatus = Model_Referencing_TatStates::REFERENCE_ITEM_COMPLETE;
            }
        }


        //Set the missing information attribute.
        /*
        $productName = $enquiry->productSelection->product->name;
        $flowManager = Manager_Referencing_DataEntry_Flow_FlowFactory::createFlowManager($productName);
		$missingInformationManager = new Manager_Referencing_DataEntry_MissingInformation_Despatcher();
		$missingInfoList = array();
		do {

       		$missingInfoSubList = $missingInformationManager->getMissingInformation($enquiry->externalId, $flowManager->currentFlowItem);
       		if(!empty($missingInfoSubList)) {

       			foreach($missingInfoSubList as $subListItem) {

       				$missingInfoList[] = $subListItem;
       			}
       		}
       	}
       	while($flowManager->moveToNext($enquiryId));
       	$tat->missingInformation = $missingInfoList;
       	*/
        
        //Use the legacy missing information manager until the new one is fully operational.
    	$missingInformationManager = new Manager_Referencing_DataEntry_MissingInformation_Legacy();
    	$tat->missingInformation  = $missingInformationManager->getMissingInformation($this->_reference);


        //Set the tat notifications.
        $tatNotifications = new Datasource_Referencing_TatNotification();
        $tat->tatNotifications = $tatNotifications->getByEnquiry($this->_reference->externalId);
     	
        return $tat;
    }
}

?>