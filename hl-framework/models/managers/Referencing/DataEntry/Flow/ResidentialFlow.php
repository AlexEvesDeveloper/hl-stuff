<?php

class Manager_Referencing_DataEntry_Flow_ResidentialFlow {
    
    /**
     * @todo
     * This method is not implemented properly.
     */
    public function moveToPreviousResidence($currentFlowItem, $referenceId) {
        
        $residentialDatasource = new Datasource_Referencing_Residences();
        $residenceArray = $residentialDatasource->getByReferenceId($referenceId);
        
        switch(count($residenceArray)) {
            
            case 1: $returnVal = Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE; break;
            case 2: $returnVal = Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE; break;
            case 3: $returnVal = Model_Referencing_DataEntry_FlowItems::THIRD_RESIDENCE; break;
            default: throw new Zend_Exception('Move to previous: residence despatch error');
        }
        
        return $returnVal;
    }
    
    
    /**
     * @todo
     * Clean up addresses made redunant when the user traverses back and forwards.
     */
    public function moveToNextResidence($currentFlowItem, $referenceId) {
        
        if($currentFlowItem == Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE) {
            
            $residentialDatasource = new Datasource_Referencing_Residences();
            $residenceArray = $residentialDatasource->getByReferenceId($referenceId);
            
            
            $firstResidence = null;
            foreach($residenceArray as $residence) {
                
                if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
                    
                    $firstResidence = $residence;
                    break;
                }
            }
            
            
            $despatchToNext = true;
            if($firstResidence->durationAtAddress >= 36) {
    
                //Cannot despatch to another residence as we have enough. This is expected
                //behaviour, so return null to indicate this, rather than an exception.
                $despatchToNext = false;
            }
            else if(isset($firstResidence->address)) {
            
                if((isset($firstResidence->address->isOverseasAddress)) && ($firstResidence->address->isOverseasAddress)) {
                    
                    //Cannot despatch to another residence if this residence is overseas.
                    $despatchToNext = false;
                }
            }
            
            if($despatchToNext) {
                
                $returnVal = Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE;
            }
            else {
            
                //Cleanup address 2 and 3?
                $returnVal = null;
            }
        }
        else if($currentFlowItem == Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE) {
            
            //Determine if there are three years of address history
            $residentialDatasource = new Datasource_Referencing_Residences();
            $residenceArray = $residentialDatasource->getByReferenceId($referenceId);
            
            
            $firstResidence = null;
            $secondResidence = null;
            foreach($residenceArray as $residence) {
                
                if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
                    
                    $firstResidence = $residence;
                    continue;
                }
                else if($residence->chronology == Model_Referencing_ResidenceChronology::FIRST_PREVIOUS) {
                    
                    $secondResidence = $residence;
                    continue;
                }
            }
            
            $despatchToNext = true;
            $totalAddressHistoryLength = $firstResidence->durationAtAddress + $secondResidence->durationAtAddress;
            if($totalAddressHistoryLength >= 36) {
    
                //Cannot despatch to another residence as we have enough. This is expected
                //behaviour, so return null to indicate this, rather than an exception.
                $despatchToNext = false;
            }
            else if(isset($secondResidence->address)) {
            
                if((isset($secondResidence->address->isOverseasAddress)) && ($secondResidence->address->isOverseasAddress)) {
                    
                    //Cannot despatch to another residence if the second residence is overseas.
                    $despatchToNext = false;
                }
            }
            
            
            if($despatchToNext) {
                
                $returnVal = Model_Referencing_DataEntry_FlowItems::THIRD_RESIDENCE;
            }
            else {
                
                //Cleanup address 3?
                $returnVal = null;
            }
        }
        else if($currentFlowItem == Model_Referencing_DataEntry_FlowItems::THIRD_RESIDENCE) {
        
            //Cannot despatch to another residence as we have enough. This is expected
            //behaviour, so return null to indicate this, rather than an exception.
            $returnVal = null;
        }
        else {
            
            throw new Zend_Exception('Move to next: residence despatch error');
        }
        
        return $returnVal;
    }
}

?>