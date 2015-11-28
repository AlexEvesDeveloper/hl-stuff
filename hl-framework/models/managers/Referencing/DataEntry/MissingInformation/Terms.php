<?php

/*
 * Class used to identify missing terms agreement information.
 */
class Manager_Referencing_DataEntry_MissingInformation_Terms {

    /**
     * Identifies the missing information in the terms agreement data entry flow.
     * 
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @return mixed
     * Returns an array of missing information strings, if missing information is
     * found. Else returns null.
     */
    public function getMissingInformation($enquiryId) {
        
        $enquiryDataSource = new Datasource_Referencing_Reference();
        $enquiry = $enquiryDataSource->getEnquiry($enquiryId);       
        $missingInfo = array();

        if($enquiry->termsAgreedBy == Model_Referencing_EnquiryAgreement::NOT_AGREED) {
            
            $missingInfo[] = 'Terms of referencing not agreed';
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