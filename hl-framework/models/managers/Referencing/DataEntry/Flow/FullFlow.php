<?php

/**
 * Advises which data entry flow items to display for a full reference.
 */
class Manager_Referencing_DataEntry_Flow_FullFlow extends Manager_Referencing_DataEntry_Flow_FlowManager {
    
    /**
     * Sets the initial data entry flow item.
     */
    public function __construct() {
        
        $this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::PROPERTY_LEASE;
    }
    
    
    /**
     * Full description given in superclass.
     */
    public function moveToPrevious($referenceId = null) {
        
        $isMoved = true;
        
        switch($this->currentFlowItem) {
            
            case Model_Referencing_DataEntry_FlowItems::PROPERTY_LEASE:
                $isMoved = false;
                break;
            
            case Model_Referencing_DataEntry_FlowItems::PRODUCT:
                $this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::PROPERTY_LEASE;
                break;
                
            case Model_Referencing_DataEntry_FlowItems::PROSPECTIVE_LANDLORD:
                $this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::PRODUCT;
                break;
            
            case Model_Referencing_DataEntry_FlowItems::REFERENCE_SUBJECT:
                $this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::PROSPECTIVE_LANDLORD;
                break;
            
            case Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE:
                $this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::REFERENCE_SUBJECT;
                break;
                
            case Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE:
				$this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE;
                break;
				
            case Model_Referencing_DataEntry_FlowItems::THIRD_RESIDENCE:
				$this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE;
                break;
				
			case Model_Referencing_DataEntry_FlowItems::CURRENT_LANDLORD:
				
				 //Determine which residence to process/display next.
                $residenceFlowManager = new Manager_Referencing_DataEntry_Flow_ResidentialFlow();
                $this->currentFlowItem = $residenceFlowManager->moveToPreviousResidence($this->currentFlowItem, $referenceId);
                break;
				
			case Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION:
			case Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION:
			case Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION:
            case Model_Referencing_DataEntry_FlowItems::TERMS:
                //Determine if an occupation is next.
				$occupationFlowManager = new Manager_Referencing_DataEntry_Flow_OccupationalFlow();
                $nextFlowItem = $occupationFlowManager->moveToPreviousOccupation($this->currentFlowItem, $referenceId);
                if(!empty($nextFlowItem)) {
                    
					$this->currentFlowItem = $nextFlowItem;
					break;
                }
				
				
				//Determine if the current landlord is next.
				$referenceManager = new Manager_Referencing_Reference();
				$reference = $referenceManager->getReference($referenceId);
				$referenceSubject = $reference->referenceSubject;
				$residenceArray = $referenceSubject->residences;
				
				$isMovedToCurrentLandlord = false;
				foreach($residenceArray as $residence) {
					
					if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
						
						if($residence->status == Model_Referencing_ResidenceStatus::TENANT) {
						
							$this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::CURRENT_LANDLORD;
							$isMovedToCurrentLandlord = true;
						}
						break;
					}
				}
				
				if($isMovedToCurrentLandlord) {
					
					break;
				}
				
				
				//Determine which residence is next.
                $residenceFlowManager = new Manager_Referencing_DataEntry_Flow_ResidentialFlow();
                $this->currentFlowItem = $residenceFlowManager->moveToPreviousResidence($this->currentFlowItem, $referenceId);
                break;
            
			case Model_Referencing_DataEntry_FlowItems::PRICE_CONFIRMATION:
                $this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::TERMS;
                break;
			
            default:
                throw new Zend_Exception('Unknown flow');
        }
        
        return $isMoved;
    }
    
    
	/**
     * Full description given in superclass.
     */
    public function moveToNext($referenceId = null) {
        
        $isMoved = true;
        switch($this->currentFlowItem) {
            
            case Model_Referencing_DataEntry_FlowItems::PROPERTY_LEASE:
                $this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::PRODUCT;
                break;
            
            case Model_Referencing_DataEntry_FlowItems::PRODUCT:
                $this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::PROSPECTIVE_LANDLORD;
                break;
                
            case Model_Referencing_DataEntry_FlowItems::PROSPECTIVE_LANDLORD:
                $this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::REFERENCE_SUBJECT;
                break;
            
            case Model_Referencing_DataEntry_FlowItems::REFERENCE_SUBJECT:
                $this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE;
                break;
            
            case Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE:
            case Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE:
            case Model_Referencing_DataEntry_FlowItems::THIRD_RESIDENCE:
				
				//Determine which residence is next, if any.
                $residenceFlowManager = new Manager_Referencing_DataEntry_Flow_ResidentialFlow();
                $nextFlowItem = $residenceFlowManager->moveToNextResidence($this->currentFlowItem, $referenceId);
                if(!empty($nextFlowItem)) {
                    
					$this->currentFlowItem = $nextFlowItem;
					break;
				}
				
				//Determine if the current landlord is next.
				$referenceManager = new Manager_Referencing_Reference();
				$reference = $referenceManager->getReference($referenceId);
				$referenceSubject = $reference->referenceSubject;
				$residenceArray = $referenceSubject->residences;
				
				$isMovedToCurrentLandlord = false;
				foreach($residenceArray as $residence) {

					if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {

						if($residence->status == Model_Referencing_ResidenceStatus::TENANT) {

							$this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::CURRENT_LANDLORD;
							$isMovedToCurrentLandlord = true;
						}
						break;
					}
				}
				
				if($isMovedToCurrentLandlord) {
					
					break;
				}
				
				//Determine which occupation is next.
				$occupationFlowManager = new Manager_Referencing_DataEntry_Flow_OccupationalFlow();
                $nextFlowItem = $occupationFlowManager->moveToNextOccupation($this->currentFlowItem, $referenceId);
                if(!empty($nextFlowItem)) {
					
					$this->currentFlowItem = $nextFlowItem;
					break;
                }
				
				//Move to terms.
				$this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::TERMS;
				break;
				
			case Model_Referencing_DataEntry_FlowItems::CURRENT_LANDLORD:
				
				//Determine which occupation is next.
				$occupationFlowManager = new Manager_Referencing_DataEntry_Flow_OccupationalFlow();
                $nextFlowItem = $occupationFlowManager->moveToNextOccupation($this->currentFlowItem, $referenceId);
                if(!empty($nextFlowItem)) {
                    
					$this->currentFlowItem = $nextFlowItem;
					break;
                }
				
				
				//Move to terms.
				$this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::TERMS;
				break;
				
			case Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION:
			case Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION:
			case Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION:

				//Determine which occupation is next.
				$occupationFlowManager = new Manager_Referencing_DataEntry_Flow_OccupationalFlow();
                $nextFlowItem = $occupationFlowManager->moveToNextOccupation($this->currentFlowItem, $referenceId);
                if(!empty($nextFlowItem)) {
                    
					$this->currentFlowItem = $nextFlowItem;
					break;
                }
				
				
				//Move to terms.
				$this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::TERMS;
				break;
            
            case Model_Referencing_DataEntry_FlowItems::TERMS:
                $this->currentFlowItem = Model_Referencing_DataEntry_FlowItems::PRICE_CONFIRMATION;
                break;
			
			case Model_Referencing_DataEntry_FlowItems::PRICE_CONFIRMATION:
                $isMoved = false;
                break;
            
            default:
                throw new Zend_Exception('Unknown flow');
        }
        
        return $isMoved;
    }
}

?>