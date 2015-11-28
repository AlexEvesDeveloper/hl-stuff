<?php

/*
 * Class used to identify missing prospective landlord information.
 */
class Manager_Referencing_DataEntry_MissingInformation_ProspectiveLandlord {

	/**
     * Identifies the missing information in the prospective landlord data entry flow.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * Returns an array of missing information strings, if missing information is
     * found. Else returns null.
     */
    public function getMissingInformation($enquiryId) {
        
		$referenceManager = new Manager_Referencing_Reference();
		$enquiry = $referenceManager->getReference($enquiryId);
        $prospectiveLandlord = $enquiry->propertyLease->prospectiveLandlord;
        
        $missingInfo = array();
        
        
        if(empty($prospectiveLandlord)) {
            
            //Prospective landlord details are optional except on rentguarantee
            //products.
            $product = $enquiry->productSelection->product;
            if($product->isRentGuarantee) {
                
                $missingInfo[] = 'No prospective landlord details';
            }
        }
        else {
            
            //Check the name.
            if(empty($prospectiveLandlord->name->firstName)) {
                
                if(empty($prospectiveLandlord->name->lastName)) {
                    
                    $missingInfo[] = 'Prospective landlord: name';
                }
            }
            
            
            //Contact details are optional.
            
            
            //Check the address
            $address = $prospectiveLandlord->address;
            if(empty($address->flatNumber)) {
                
                if(empty($address->houseName)) {
                    
                    if(empty($address->houseNumber)) {
                    
                        if(empty($address->addressLine1)) {
                            
                            $missingInfo[] = 'Prospective landlord address: 1st line';
                        }
                    }
                }
            }
            
            if(empty($address->town)) {
            
                $missingInfo[] = 'Prospective landlord address: town';
            }
            
            if(empty($address->postCode)) {
                
                $missingInfo[] = 'Prospective landlord address: postcode';
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