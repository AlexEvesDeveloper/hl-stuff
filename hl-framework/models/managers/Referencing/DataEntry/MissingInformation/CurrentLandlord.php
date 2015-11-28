<?php

/*
 * Class used to identify missing current landlord information.
 */
class Manager_Referencing_DataEntry_MissingInformation_CurrentLandlord {

	/**
     * Identifies the missing information in the current landlord data entry flow.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * Returns an array of missing information strings, if missing information is
     * found. Else returns null.
     */
    public function getMissingInformation($enquiryId) {
        
        //Identify if current landlord referee details are applicable.
		$referenceManager = new Manager_Referencing_Reference();
		$enquiry = $referenceManager->getReference($enquiryId);

        $currentResidence = null;
        foreach($enquiry->referenceSubject->residences as $residence) {
        	
        	if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
        		
        		$currentResidence = $residence;
        		break;
        	}
        }
        
        if($currentResidence->status != Model_Referencing_ResidenceStatus::TENANT) {
        	
        	//The landlord referee details are not applicable, therefore return.
        	return null;
        }
        
        
        //The current landlord referee details are applicable, so retrieve and process.
        $currentLandlord = $currentResidence->refereeDetails;
        $missingInfo = array();
        
        
        if(empty($currentLandlord)) {
            
            //Current landlord details are optional except on full referencing
            //products.
            $product = $enquiry->productSelection->product;
            if($product->referencingType == Model_Referencing_ProductReferencingTypes::FULL_REFERENCE) {
                
                $missingInfo[] = 'No current landlord details';
            }
        }
        else {
        	
        	//Check the type
        	if(empty($currentLandlord->type)) {
        		
        		$missingInfo[] = 'Current landlord: type';
        	}
            
            
        	//Check the name.
            if(empty($currentLandlord->name->firstName)) {
                
                if(empty($currentLandlord->name->lastName)) {
                    
                    $missingInfo[] = 'Current landlord: name';
                }
            }
            
            
            //Contact details are optional.
            
            
            //Check the address
            $address = $currentLandlord->address;
            if(empty($address->flatNumber)) {
                
                if(empty($address->houseName)) {
                    
                    if(empty($address->houseNumber)) {
                    
                        if(empty($address->addressLine1)) {
                            
                            $missingInfo[] = 'Current landlord address: 1st line';
                        }
                    }
                }
            }
            
            if(empty($address->town)) {
            
                $missingInfo[] = 'Current landlord address: town';
            }
            
            if(empty($address->postCode)) {
                
                $missingInfo[] = 'Current landlord address: postcode';
            }            
        }
        
        
        //Finalize
        if(empty($missingInfo)) {
            
            $returnVal = null;
        }
        else {
            
            $returnVal = $missingInfo;
        }
        
        return $returnVal;
    }
}

?>