<?php

/**
 * Class used to identify missing reference subject information.
 */
class Manager_Referencing_DataEntry_MissingInformation_ReferenceSubject {

    /**
     * Identifies the missing information in the reference subject data entry flow.
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
        $referenceSubject = $enquiry->referenceSubject;
        
        
        $missingInfo = array();
        
        
        if(empty($referenceSubject)) {
            
            $missingInfo[] = 'Applicant details missing';
        }
        else {
            
            if(empty($referenceSubject->name->title)) {
                
                $missingInfo[] = 'Applicant details: title';
            }
            
            if(empty($referenceSubject->name->firstName)) {
                
                $missingInfo[] = 'Applicant details: first name';
            }
            
            if(empty($referenceSubject->name->lastName)) {
                
                $missingInfo[] = 'Applicant details: last name';
            }
            
            if(empty($referenceSubject->dob)) {
                
                $missingInfo[] = 'Applicant details: date of birth';
            }
            
            if(empty($referenceSubject->contactDetails->telephone1)) {
                
                if(empty($referenceSubject->contactDetails->telephone2)) {
                
                    $missingInfo[] = 'Applicant details: contact number';
                }
            }
            
            if(empty($referenceSubject->hasAdverseCredit)) {
                
                if($referenceSubject->hasAdverseCredit !== false) {
                
                    $missingInfo[] = 'Applicant details: adverse credit confirmation';
                }
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