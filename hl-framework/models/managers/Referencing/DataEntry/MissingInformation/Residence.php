<?php

/**
 * Class used to identify missing residence information.
 */
class Manager_Referencing_DataEntry_MissingInformation_Residence {

	/**
     * Identifies the missing information in the residence data entry flow.
     *
     * @param integer $chronology
     * The chronology of the residence. Must correspond to one of the consts exposed by
     * the Model_Referencing_ResidenceChronology class.
     * 
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * Returns an array of missing information strings, if missing information is
     * found. Else returns null.
     */
    public function getMissingInformation($chronology, $enquiryId) {
        
		$referenceManager = new Manager_Referencing_Reference();
		$enquiry = $referenceManager->getReference($enquiryId);
        $residenceArray = $enquiry->referenceSubject->residences;
		
		
        $missingInfo = array();
                
        
        //Prepare the label used in the missing information message.
        switch($chronology) {
            
            case Model_Referencing_ResidenceChronology::CURRENT: $label = 'Current'; break;
            case Model_Referencing_ResidenceChronology::FIRST_PREVIOUS: $label = 'Previous'; break;
            case Model_Referencing_ResidenceChronology::SECOND_PREVIOUS: $label = 'Second previous'; break;
        }
        
        
        if(empty($residenceArray)) {
            
            $missingInfo[] = "$label residence details missing";
        }
        else {
            
            foreach($residenceArray as $residence) {
                
                //Look for the appropriate residence.
                if($residence->chronology != $chronology) {
                    
                    continue;
                }
                
                //Current residence found. Check the data provided.
                if(empty($residence->duration)) {
                    
                    $missingInfo[] = "$label residence: duration";
                }
                
                if(empty($residence->address->isOverseasAddress)) {
                    
                    if($residence->address->isOverseasAddress !== false) {
                        
                        $missingInfo[] = "$label residence: is overseas confirmation";
                    }
                }
                
                //Check the address
                $address = $residence->address;
                if(empty($address->flatNumber)) {
                    
                    if(empty($address->houseName)) {
                        
                        if(empty($address->houseNumber)) {
                        
                            if(empty($address->addressLine1)) {
                                
                                $missingInfo[] = "$label residence: 1st line";
                            }
                        }
                    }
                }
                
                if(empty($address->town)) {
                
                    $missingInfo[] = "$label residence: town";
                }
                
                if(empty($address->postCode)) {
                    
                    $missingInfo[] = "$label residence: postcode";
                }
                
                
                //Processing complete.
                break;
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