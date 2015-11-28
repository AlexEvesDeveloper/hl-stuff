<?php

/*
 * Class used to identify missing property lease information.
 */
class Manager_Referencing_DataEntry_MissingInformation_PropertyLease {

	/**
     * Identifies the missing information in the property lease data entry flow.
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
        $propertyLease = $enquiry->propertyLease;
        
        $missingInfo = array();
        
        
        //Check the address details.
        if(empty($propertyLease->address->flatNumber)) {
            
            if(empty($propertyLease->address->houseName)) {
                
                if(empty($propertyLease->address->houseNumber)) {
                
                    if(empty($propertyLease->address->addressLine1)) {
                        
                        $missingInfo[] = 'Property address: 1st line';
                    }
                }
            }
        }
        
        if(empty($propertyLease->address->town)) {
            
            $missingInfo[] = 'Property address: town';
        }
        
        if(empty($propertyLease->address->postCode)) {
            
            $missingInfo[] = 'Property address: postcode';
        }
        
        
        //Check the remaining details.
        if(empty($propertyLease->rentPerMonth)) {
            
            $missingInfo[] = 'Rental amount';
        }
        
        if(empty($propertyLease->tenancyTerm)) {
            
            $missingInfo[] = 'Tenancy term';
        }
        
        if(empty($propertyLease->noOfTenants)) {
            
            $missingInfo[] = 'Number of tenants';
        }
        
        if(empty($propertyLease->tenancyStartDate)) {
            
            $missingInfo[] = 'Tenancy start date';
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