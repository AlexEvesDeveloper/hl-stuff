<?php

class Manager_Referencing_DataEntry_Flow_OccupationalFlow {

	/**
	 * Identifies if the system can despatch to an occupation.
	 *
	 * @param integer $occupationType
	 * The type of occupation which the system may or may not despatch to.
	 *
	 * @return boolean
	 * Returns true if can despatch to the occupation, false otherwise.
	 */
	/*
	protected function _isDespatchAllowed($occupationType) {
		
		if(($occupationType != Model_Referencing_OccupationalTypes::STUDENT)
			&& ($occupationType != Model_Referencing_OccupationalTypes::UNEMPLOYMENT)) {
			
			$isDespatchAllowed = true;
		}
		else {
			
			$isDespatchAllowed = false;
		}
		
		return $isDespatchAllowed;
	}
	*/
	
	
	/**
	 * Identifies if the system should despatch to a form for the $occupation passed in.
	 *
	 * @param Model_Referencing_Occupation $occupation
	 * The Occupation to test.
	 *
	 * @param Model_Referencing_Reference
	 * The Reference linking all the occupations.
	 *
	 * @return boolean
	 * Returns true if can despatch to the occupation, false otherwise.
	 */
	protected function _isDespatchAllowed($occupation, $reference) {
		
		if(($occupation->type == Model_Referencing_OccupationTypes::STUDENT)
			|| ($occupation->type == Model_Referencing_OccupationTypes::UNEMPLOYMENT)) {
			
			//We cannot display an occupation data capture form for this type of occupation.
			return false;
		}
		
		if($occupation->type == Model_Referencing_OccupationTypes::SELFEMPLOYMENT) {
			
			if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
				
				//If there is a future occupation
				foreach($reference->referenceSubject->occupations as $occupation) {
					
					if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
						
						//We cannot display an occupation data capture form when the reference subject
						//is self-employed and has a future employer. Stupid business rules.
						return false;
					}
				}
			}
		}
		
		//We can despatch to the appropriate form for this $occupation.
		return true;
	}
	
	
	public function moveToPreviousOccupation($currentFlowItem, $referenceId) {
		
		//Determine if the current flow item is something other than an
		//occupation.
		$isOccupation = false;
		$isCurrentOccupation = false;
		$isSecondOccupation = false;
		$isFutureOccupation = false;

		switch($currentFlowItem) {
			
			case Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION:
				$isOccupation = true;
				$isCurrentOccupation = true;
				break;
			
			case Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION:
				$isOccupation = true;
				$isSecondOccupation = true;
				break;
			
			case Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION:
				$isOccupation = true;
				$isFutureOccupation = true;
				break;
		}
		
		$referenceManager = new Manager_Referencing_Reference();
		$reference = $referenceManager->getReference($referenceId);			
		$occupationArray = $reference->referenceSubject->occupations;
		
		if(!$isOccupation) {
			
			//Do we have a future occupation?
			foreach($occupationArray as $occupation) {
				
				if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
					
					//We do indeed. However, we do not want to despatch to the future occupation if that occupation
					//is either student or unemployed.
					if($this->_isDespatchAllowed($occupation, $reference)) {
						
						return Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION;
					}
					break;
				}
			}
			
			
			//Do we have a second occupation?
			foreach($occupationArray as $occupation) {
				
				if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
					
					if($occupation->importance == Model_Referencing_OccupationImportance::SECONDARY) {
						
						//Match found. See if we can despatch to it.
						if($this->_isDespatchAllowed($occupation, $reference)) {
							
							return Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION;
						}
						break;
					}
				}
			}
			
			
			//Do we have a current occupation?
			foreach($occupationArray as $occupation) {
				
				if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
					
					//Match found. See if we can despatch to it.
					if($this->_isDespatchAllowed($occupation, $reference)) {
						
						return Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION;
					}
					break;
				}
			}
			
			
			//No occupations applicable.
			return null;
		}
		
		
		if($isFutureOccupation) {
			
			//Do we have a second occupation?			
			foreach($occupationArray as $occupation) {
				
				if($occupation->importance == Model_Referencing_OccupationImportance::SECONDARY) {
					
					//Match found. See if we can despatch to it.
					if($this->_isDespatchAllowed($occupation, $reference)) {
						
						return Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION;
					}
					break;
				}
			}
			
			
			//Do we have a current occupation?
			foreach($occupationArray as $occupation) {
				
				if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
					
					//Match found. See if we can despatch to it.
					if($this->_isDespatchAllowed($occupation, $reference)) {
						
						return Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION;
					}
					break;
				}
			}
			
			
			//No occupations applicable.
			return null;
		}
		
		
		if($isSecondOccupation) {
			
			//Do we have a current occupation?			
			foreach($occupationArray as $occupation) {
				
				if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
					
					//Match found. See if we can despatch to it.
					if($this->_isDespatchAllowed($occupation, $reference)) {
						
						return Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION;
					}
					break;
				}
			}
			
			
			//No occupations applicable.
			return null;
		}
		
		
		//If here then the current flow item is the current occupation. No other occupations
		//are available to despatch to, so return null.
		return null;		
	}
	
	
	public function moveToNextOccupation($currentFlowItem, $referenceId) {
		
		//Determine if the current flow item is something other than an
		//occupation.
		$isOccupation = false;
		$isCurrentOccupation = false;
		$isSecondOccupation = false;
		$isFutureOccupation = false;
		
		switch($currentFlowItem) {
			
			case Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION:				
				$isOccupation = true;
				$isCurrentOccupation = true;
				break;
			
			case Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION:
				$isOccupation = true;
				$isSecondOccupation = true;
				break;
			
			case Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION:
				$isOccupation = true;
				$isFutureOccupation = true;
				break;
		}
		
		
		$referenceManager = new Manager_Referencing_Reference();
		$reference = $referenceManager->getReference($referenceId);
		
		
		if(!$isOccupation) {
			
			//Do we have a current occupation?
			$occupationArray = $reference->referenceSubject->occupations;
			foreach($occupationArray as $occupation) {
				
				if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
					
					//Match found. See if we can despatch to it.
					if($this->_isDespatchAllowed($occupation, $reference)) {

						return Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION;
					}
					break;
				}
			}
			

			//Do we have a future occupation?
			foreach($occupationArray as $occupation) {
				
				if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
					
					//Match found. See if we can despatch to it.
					if($this->_isDespatchAllowed($occupation, $reference)) {
						
						return Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION;
					}
					break;
				}
			}
			
			
			//No occupations applicable.
			return null;
		}
		
		
		if($isCurrentOccupation) {
			
			//Do we have a second occupation?
			$occupationArray = $reference->referenceSubject->occupations;
			foreach($occupationArray as $occupation) {

				if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
				
					if($occupation->importance == Model_Referencing_OccupationImportance::SECOND) {
					
						//Match found. See if we can despatch to it.
						if($this->_isDespatchAllowed($occupation, $reference)) {
							
							return Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION;
						}
						break;
					}
				}
			}
			

			//Do we have a future occupation?
			foreach($occupationArray as $occupation) {
				
				if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
					
					//Match found. See if we can despatch to it.
					if($this->_isDespatchAllowed($occupation, $reference)) {
						
						return Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION;
					}
					break;
				}
			}
			
			
			//No occupations applicable.
			return null;
		}
		
		
		if($isSecondOccupation) {
			
			//Do we have a future occupation?
			$occupationArray = $reference->referenceSubject->occupations;
			foreach($occupationArray as $occupation) {
				
				if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
					
					//Match found. See if we can despatch to it.
					if($this->_isDespatchAllowed($occupation, $reference)) {
						
						return Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION;
					}
					break;
				}
			}
			
			
			//No occupations applicable.
			return null;
		}
		
		
		//If here then the current flow item is the future occupation. No other occupations
		//are available to despatch to, so return null.
		return null;
	}
}