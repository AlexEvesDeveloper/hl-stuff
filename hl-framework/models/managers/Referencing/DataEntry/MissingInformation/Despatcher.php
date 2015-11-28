<?php

/*
 * Convenience class used to identify missing information in the data entry process.
 *
 * This class prevents calling code from having to locate and instantiate specific
 * MissingInformation manager classes.
 */
class Manager_Referencing_DataEntry_MissingInformation_Despatcher {
    
    
    /**
     * Identifies missing information appropriate to the dataentry flow item passed in.
     *
     * @param string $enquiryId
     * The unique external Enquiry identifier.
     *
     * @param Model_Referencing_DataEntry_FlowItems $flowItem
     * The flow item to check for missing information.
     *
     * @return mixed
     * Returns an array of missing information strings, if missing information is
     * found. Else returns null.
     */
    public function getMissingInformation($enquiryId, $flowItem) {
        
        switch($flowItem) {
            
            case Model_Referencing_DataEntry_FlowItems::PROPERTY_LEASE:
                $propertyLease = new Manager_Referencing_DataEntry_MissingInformation_PropertyLease();
                $missingInformations = $propertyLease->getMissingInformation($enquiryId);
                break;
                
            
            case Model_Referencing_DataEntry_FlowItems::PRODUCT:
                $productSelection = new Manager_Referencing_DataEntry_MissingInformation_ProductSelection();
                $missingInformations = $productSelection->getMissingInformation($enquiryId);
                break;
                
            
            case Model_Referencing_DataEntry_FlowItems::PROSPECTIVE_LANDLORD:
                $prospectiveLandlord = new Manager_Referencing_DataEntry_MissingInformation_ProspectiveLandlord();
                $missingInformations = $prospectiveLandlord->getMissingInformation($enquiryId);
                break;
                
            
            case Model_Referencing_DataEntry_FlowItems::REFERENCE_SUBJECT:
                $referenceSubject = new Manager_Referencing_DataEntry_MissingInformation_ReferenceSubject();
                $missingInformations = $referenceSubject->getMissingInformation($enquiryId);
                break;
            
            case Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE:
                $residence = new Manager_Referencing_DataEntry_MissingInformation_Residence();
                $missingInformations = $residence->getMissingInformation(
                    Model_Referencing_ResidenceChronology::CURRENT, $enquiryId);
                break;
                
            
            case Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE:
                $residence = new Manager_Referencing_DataEntry_MissingInformation_Residence();
                $missingInformations = $residence->getMissingInformation(
                    Model_Referencing_ResidenceChronology::FIRST_PREVIOUS, $enquiryId);
                break;
            
                
            case Model_Referencing_DataEntry_FlowItems::THIRD_RESIDENCE:
                $residence = new Manager_Referencing_DataEntry_MissingInformation_Residence();
                $missingInformations = $residence->getMissingInformation(
                    Model_Referencing_ResidenceChronology::SECOND_PREVIOUS, $enquiryId);
                break;
                
            
            case Model_Referencing_DataEntry_FlowItems::CURRENT_LANDLORD:
            	$currentLandlord = new Manager_Referencing_DataEntry_MissingInformation_CurrentLandlord();
            	$missingInformations = $currentLandlord->getMissingInformation($enquiryId);
            	break;
            
            	
            case Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION:
            	$occupationDatasource = new Datasource_Referencing_Occupations();
            	$occupation = $occupationDatasource->getCurrent($enquiryId);
            	$missingInformationManager = new Manager_Referencing_DataEntry_MissingInformation_Occupation();
            	$missingInformations = $missingInformationManager->getMissingInformation($occupation);
            	break;
            	
            
            case Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION:
            	$occupationDatasource = new Datasource_Referencing_Occupations();
            	$occupation = $occupationDatasource->getSecond($enquiryId);
            	$missingInformationManager = new Manager_Referencing_DataEntry_MissingInformation_Occupation();
            	$missingInformations = $missingInformationManager->getMissingInformation($occupation);
            	break;
            	
            	
            case Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION:
            	$occupationDatasource = new Datasource_Referencing_Occupations();
            	$occupation = $occupationDatasource->getFuture($enquiryId);
            	$missingInformationManager = new Manager_Referencing_DataEntry_MissingInformation_Occupation();
            	$missingInformations = $missingInformationManager->getMissingInformation($occupation);
            	break;
            	
                
            case Model_Referencing_DataEntry_FlowItems::ADDITIONAL_INFORMATION:
                //Additional information is entirely optional, so return null.    
                $missingInformations = null;
                break;
            
                
            case Model_Referencing_DataEntry_FlowItems::REFERENCE_SUMMARY:
                //No information is required in the reference summary data entry flow.    
                $missingInformations = null;
                break;
            
                
            case Model_Referencing_DataEntry_FlowItems::TERMS:
                $terms = new Manager_Referencing_DataEntry_MissingInformation_Terms();
                $missingInformations = $terms->getMissingInformation($enquiryId);
                break;
            
                
            default:
                throw new Zend_Exception('Unknown referencing flow item.');
        }
        

        if(empty($missingInformations)) {
            
            $returnVal = null;
        }
        else {
            
            $returnVal = $missingInformations;
        }

        return $returnVal;
    }
}

?>