<?php
class LandlordsReferencing_Form_PropertyLease extends Zend_Form_Multilevel
{
    public function init()
    {
		$this->addSubForm(new LandlordsReferencing_Form_Subforms_PropertyAddress(), 'subform_propertyaddress');
		$this->addSubForm(new LandlordsReferencing_Form_Subforms_PropertyMisc(), 'subform_propertymisc');
    }

    /**
     * Saves the form data to the datastore.
     * 
     * @return void
     */
    public function saveData()
    {
    	$session = new Zend_Session_Namespace('referencing_global');
    	$data = $this->getValues();
    	
    	// Create a new Enquiry, or load the existing one.
        $referenceManager = new Manager_Referencing_Reference();
        if (empty($session->referenceId)) {
        	$reference = $referenceManager->createReference();
        	$session->referenceId = $reference->internalId;
        }
        else {
        	$reference = $referenceManager->getReference($session->referenceId);
        }
        
        // Set the customer map
        if (empty($reference->customer)) {
        	$reference->customer = new Model_Referencing_CustomerMap();
        }
        $reference->customer->customerType = Model_Referencing_CustomerTypes::LANDLORD;
        $reference->customer->customerId = $session->customerId;
		
        // Format the property details
        $postcodeManager = new Manager_Core_Postcode();
        $propertyAddress = $postcodeManager->getPropertyByID($data['subform_propertyaddress']['property_address'], false);
        $addressLine1 = 
        	(($propertyAddress['organisation'] != '') ? "{$propertyAddress['organisation']}, " : '')
                . (($propertyAddress['houseNumber'] != '') ? "{$propertyAddress['houseNumber']} " : '')
                . (($propertyAddress['buildingName'] != '') ? "{$propertyAddress['buildingName']}, " : '')
                . $propertyAddress['address2'];
        
        $addressLine2 = $propertyAddress['address4'];
        $town = $propertyAddress['address5'];
        $postCode = $data['subform_propertyaddress']['ins_property_postcode'];
        
        // Store the property details into the PropertyLease.
        if (empty($reference->propertyLease)) {
        	$propertyLeaseManager = new Manager_Referencing_PropertyLease();
        	$reference->propertyLease = $propertyLeaseManager->insertPlaceholder($session->referenceId);
        }
			
        if (empty($reference->propertyLease->address)) {
        	$addressManager = new Manager_Core_Address();
        	$reference->propertyLease->address = $addressManager->createAddress();
        }
        
        $reference->propertyLease->address->addressLine1 = $addressLine1;
        $reference->propertyLease->address->addressLine2 = $addressLine2;
        $reference->propertyLease->address->town = $town;
        $reference->propertyLease->address->postCode = $postCode;
        
        // Store the rental price index details, if provided.
        $this->_savePropertyAspects();
        
        // Set the remainder of the property lease details.
        $reference->propertyLease->rentPerMonth = new Zend_Currency(
        	array(
        		'value' => $data['subform_propertymisc']['total_rent'],
        		'precision' => 0
        	)
        );
        
        $reference->propertyLease->tenancyStartDate = new Zend_Date(
        	$data['subform_propertymisc']['tenancy_start_date'], Zend_Date::DATES);
        	
        $reference->propertyLease->tenancyTerm = $data['subform_propertymisc']['tenancy_term'];
        $reference->propertyLease->noOfTenants = $data['subform_propertymisc']['no_of_tenants'];
        
        $referenceManager->updateReference($reference);
    }
    
    public function _savePropertyAspects()
    {
    	$session = new Zend_Session_Namespace('referencing_global');
		$data = $this->getValues();
    	
    	//$propertyLetType = $data['subform_propertymisc']['property_let_type'];
        $noOfPropertyBedrooms = $data['subform_propertymisc']['property_bedrooms'];
        $propertyType = $data['subform_propertymisc']['property_type'];
        $propertyBirth = $data['subform_propertymisc']['property_birth'];

        /*if (($propertyLetType == '') && ($noOfPropertyBedrooms == '')
        	&& ($propertyType == '') && ($propertyBirth == '')) {
        	//No property aspects have been provided.
        	return;
        }*/

        $propertyAspectManager = new Manager_Referencing_PropertyAspect();
        
        // If property aspects have been provided, store in the datasource.
		/*if ($propertyLetType != '') {
			
			switch($propertyLetType) {
				
				//Validate the property let type provided.
				case Model_Referencing_PropertyAspects_PropertyLetTypes::LET_ONLY:
				case Model_Referencing_PropertyAspects_PropertyLetTypes::MANAGED:
				case Model_Referencing_PropertyAspects_PropertyLetTypes::RENT_COLLECT:
					
					$aspectItem = new Model_Referencing_PropertyAspects_PropertyAspectItem();
					$aspectItem->referenceId = $session->referenceId;
					$aspectItem->propertyAspectId = Model_Referencing_PropertyAspects_PropertyAspectTypes::PROPERTY_LET_TYPES;
					$aspectItem->value = $propertyLetType;
		
					$propertyAspectManager->save($aspectItem);
					break;
			}
		}*/
		
		if ($noOfPropertyBedrooms != '') {
            if ((int) $noOfPropertyBedrooms >= 0 && (int) $noOfPropertyBedrooms <= 7) {
                $aspectItem = new Model_Referencing_PropertyAspects_PropertyAspectItem();
                $aspectItem->referenceId = $session->referenceId;
                $aspectItem->propertyAspectId = Model_Referencing_PropertyAspects_PropertyAspectTypes::NUMBER_OF_BEDROOMS;
                $aspectItem->value = $noOfPropertyBedrooms;

                $propertyAspectManager->save($aspectItem);
			}
		}

		if ($propertyType) {
			switch($propertyType) {
				//Validate the property types provided.
				case Model_Referencing_PropertyAspects_PropertyTypes::DETACHED:
				case Model_Referencing_PropertyAspects_PropertyTypes::SEMI_DETACHED:
				case Model_Referencing_PropertyAspects_PropertyTypes::FLAT:
				case Model_Referencing_PropertyAspects_PropertyTypes::TERRACED:
				case Model_Referencing_PropertyAspects_PropertyTypes::BUNGALOW:
					$aspectItem = new Model_Referencing_PropertyAspects_PropertyAspectItem();
					$aspectItem->referenceId = $session->referenceId;
					$aspectItem->propertyAspectId = Model_Referencing_PropertyAspects_PropertyAspectTypes::PROPERTY_TYPES;
					$aspectItem->value = $propertyType;
		
					$propertyAspectManager->save($aspectItem);
					break;
			}
		}
		
		if ($propertyBirth != '') {
			switch($propertyBirth) {
				//Validate the property age submitted.
				case Model_Referencing_PropertyAspects_PropertyAges::PRE_1850:
				case Model_Referencing_PropertyAspects_PropertyAges::BETWEEN_1850_TO_1899:
				case Model_Referencing_PropertyAspects_PropertyAges::BETWEEN_1900_TO_1919:
				case Model_Referencing_PropertyAspects_PropertyAges::BETWEEN_1920_TO_1945:
				case Model_Referencing_PropertyAspects_PropertyAges::BETWEEN_1946_TO_1979:
				case Model_Referencing_PropertyAspects_PropertyAges::BETWEEN_1980_TO_1990:
				case Model_Referencing_PropertyAspects_PropertyAges::BETWEEN_1991_TO_2000:
				case Model_Referencing_PropertyAspects_PropertyAges::BETWEEN_2001_TO_2010:
				case Model_Referencing_PropertyAspects_PropertyAges::POST_2010:
					
					$aspectItem = new Model_Referencing_PropertyAspects_PropertyAspectItem();
					$aspectItem->referenceId = $session->referenceId;
					$aspectItem->propertyAspectId = Model_Referencing_PropertyAspects_PropertyAspectTypes::PROPERTY_AGES;
					$aspectItem->value = $propertyBirth;
		
					$propertyAspectManager->save($aspectItem);
					break;
			}
		}
    }
}
