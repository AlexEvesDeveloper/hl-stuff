<?php

/**
 * Class used to identify missing occupation information.
 */
class Manager_Referencing_DataEntry_MissingInformation_Occupation {

	/**
     * Identifies the missing information in the occupation data entry flow.
     *
     * @param Model_Referencing_Occupation $occupation
     * The occupation to process.
     */
	public function getMissingInformation($occupation) {
		
		$missingInfo = array();
        
        
        //Identify the label used in the missing information message.
        if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
        	
        	if($occupation->classification == Model_Referencing_OccupationImportance::SECONDARY) {
        		
        		$label = 'Second';
        	}
        }
        
        if(empty($label)) {
        	
	        switch($occupation->chronology) {
	            
	            case Model_Referencing_OccupationChronology::CURRENT: $label = 'Current'; break;
	            case Model_Referencing_OccupationChronology::PREVIOUS: $label = 'Previous'; break;
	            case Model_Referencing_OccupationChronology::FUTURE: $label = 'Future'; break;
	        }
        }
        
        
        //Process and return
        return $this->_process($occupation, $label);
	}
	
	
	/**
	 * Despatches to a dedicated method according to the occupation type.
	 * 
	 * @param Model_Referencing_Occupation $thisOccupation
	 * Object containing the occupation details.
	 * 
	 * @param string $label
	 * Label used in the missing information strings to qualify the missing
	 * information. For example, $label may be set to 'Current' so that
	 * missing informations may look something like:
	 * 
	 * 'Current occupation address: 1st line'
	 * 
	 * @return mixed
     * Returns an array of missing information strings, if missing information is
     * found. Else returns null.
	 * 
     * @todo
	 * Not all occupation types are tested.
	 */
	protected function _process($thisOccupation, $label) {

		$missingInfo = array();

		
		if(empty($thisOccupation)) {
            
            $missingInfo[] = "$label occupation details missing";
        }
        else {
        	
            $missings = null;
        	switch($thisOccupation->type) {
        		
        		case Model_Referencing_OccupationTypes::EMPLOYMENT:
        		case Model_Referencing_OccupationTypes::CONTRACT:
        			$missings = $this->_getMissingEmployment($thisOccupation, $label);
        			break;
        		
        		case Model_Referencing_OccupationTypes::SELFEMPLOYMENT:
        			$missings = $this->_getMissingSelfEmployment($thisOccupation, $label);
        			break;
        			
        		case Model_Referencing_OccupationTypes::RETIREMENT:
        			$missings = $this->_getMissingRetired($thisOccupation, $label);
        			break;
        			
        		case Model_Referencing_OccupationTypes::INDEPENDENT:
        			$missings = $this->_getMissingIndependent($thisOccupation, $label);
        			break;
        			
        		case Model_Referencing_OccupationTypes::STUDENT:
        		case Model_Referencing_OccupationTypes::UNEMPLOYED:
        			$missings = null;
        			break;
        	}
        	
        	
        	if(!empty($missings)) {
        		
        		$missingInfo = $missings;
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
	
	
	/**
	 * Identifies missing information for Model_Referencing_OccupationalTypes::INDEPENDENT occupation types.
	 * 
	 * @param Model_Referencing_Occupation $thisOccupation
	 * Object containing the occupation details.
	 * 
	 * @param string $label
	 * Label used in the missing information strings to qualify the missing information.
	 * 
	 * @return mixed
     * Returns an array of missing information strings, if missing information is
     * found. Else returns null.
	 */
	protected function _getMissingIndependent($thisOccupation, $label) {

		$missingInfo = array();
		
		
		//Income must be specified.
		$missingIncome = false;
		if(empty($thisOccupation->income)) {
			
			$missingIncome = true;
		}
		else if(!$thisOccupation->income->isMore(0)) {
			
			$missingIncome = true;
		}
		
		
		if($missingIncome) {
			
			$missingInfo[] = "$label occupation: income";
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
	
	
	/**
	 * Identifies missing information for Model_Referencing_OccupationalTypes::RETIREMENT occupation types.
	 * 
	 * @param Model_Referencing_Occupation $thisOccupation
	 * Object containing the occupation details.
	 * 
	 * @param string $label
	 * Label used in the missing information strings to qualify the missing information.
	 * 
	 * @return mixed
     * Returns an array of missing information strings, if missing information is
     * found. Else returns null.
	 */
	protected function _getMissingRetired($thisOccupation, $label) {
		
		$missingInfo = array();
		
	    //Address check.
	    $address = $thisOccupation->refereeDetails->address;
	    if(empty($address->flatNumber)) {
	        
	        if(empty($address->houseName)) {
	            
	            if(empty($address->houseNumber)) {
	            
	                if(empty($address->addressLine1)) {
	                    
	                    $missingInfo[] = "$label occupation address: 1st line";
	                }
	            }
	        }
	    }
	    
	    if(empty($address->town)) {
	    
	        $missingInfo[] = "$label occupation address: town";
	    }
	    
	    if(empty($address->postCode)) {
	        
	        $missingInfo[] = "$label occupation address: postcode";
	    }    
	    
	    
	    //Contact number check
	    $contactDetails = $thisOccupation->refereeDetails->contactDetails;
	    if(empty($contactDetails->telephone1)) {
	        
	        $missingInfo[] = "$label occupation: telephone number";
	    }
	    
	    
	    //Organisation name
	    if(empty($thisOccupation->refereeDetails->organisationName)) {
	        
	        $missingInfo[] = "$label occupation: organisation name";
	    }
	    
	    
	    //Contact name.
	    if(empty($thisOccupation->refereeDetails->name->firstName)) {
	        
	        if(empty($this->occupation->refereeDetails->name->lastName)) {
	            
	            $missingInfo[] = "$label occupation: contact name";
	        }
	    }
	    
	    
	    //Contact position.
	    if(empty($thisOccupation->refereeDetails->position)) {
	        
	        $missingInfo[] = "$label occupation: contact position";
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
	
	
	/**
	 * Identifies missing information for Model_Referencing_OccupationalTypes::SELFEMPLOYMENT occupation types.
	 * 
	 * @param Model_Referencing_Occupation $thisOccupation
	 * Object containing the occupation details.
	 * 
	 * @param string $label
	 * Label used in the missing information strings to qualify the missing
	 * information.
	 * 
	 * @return mixed
     * Returns an array of missing information strings, if missing information is
     * found. Else returns null.
	 * 
	 * @todo
	 * SA302 forms not yet supported.
	 */
	protected function _getMissingSelfEmployment($thisOccupation, $label) {
		
		//if(employment.type == 'SA302 Forms')
			//employment.startdate, employment.salary
			
		//else do the following...
		$missingInfo = array();
		
	    //Address check.
	    $address = $thisOccupation->refereeDetails->address;
	    if(empty($address->flatNumber)) {
	        
	        if(empty($address->houseName)) {
	            
	            if(empty($address->houseNumber)) {
	            
	                if(empty($address->addressLine1)) {
	                    
	                    $missingInfo[] = "$label occupation address: 1st line";
	                }
	            }
	        }
	    }
	    
	    if(empty($address->town)) {
	    
	        $missingInfo[] = "$label occupation address: town";
	    }
	    
	    if(empty($address->postCode)) {
	        
	        $missingInfo[] = "$label occupation address: postcode";
	    }    
	    
	    
	    //Contact number check
	    $contactDetails = $thisOccupation->refereeDetails->contactDetails;
	    if(empty($contactDetails->telephone1)) {
	        
	        $missingInfo[] = "$label occupation: telephone number";
	    }
	    
	    
	    //Organisation name
	    if(empty($thisOccupation->refereeDetails->organisationName)) {
	        
	        $missingInfo[] = "$label occupation: organisation name";
	    }
	    
	    
	    //Contact name.
	    if(empty($thisOccupation->refereeDetails->name->firstName)) {
	        
	        if(empty($this->occupation->refereeDetails->name->lastName)) {
	            
	            $missingInfo[] = "$label occupation: contact name";
	        }
	    }
	    
	    
	    //Contact position.
	    if(empty($thisOccupation->refereeDetails->position)) {
	        
	        $missingInfo[] = "$label occupation: contact position";
	    }
	    
	    
	    //Start date at the occupation. Not used on the Web currently.
		/*
	    if(empty($thisOccupation->startDate)) {
	        
	        $missingInfo[] = "$label occupation: start date";
	    }
		*/
	    

	    //Finalize
		if(empty($missingInfo)) {
			
			$returnVal = null;
		}
		else {
			
			$returnVal = $missingInfo;
		}
	    return $returnVal;
	}
	
	
	/**
	 * Identifies missing information for Model_Referencing_OccupationalTypes::EMPLOYMENT occupation types.
	 * 
	 * @param Model_Referencing_Occupation $thisOccupation
	 * Object containing the occupation details.
	 * 
	 * @param string $label
	 * Label used in the missing information strings to qualify the missing
	 * information.
	 * 
	 * @return mixed
     * Returns an array of missing information strings, if missing information is
     * found. Else returns null.
	 */
	protected function _getMissingEmployment($thisOccupation, $label) {

		$missingInfo = array();
		
	    //Address check.
	    $address = $thisOccupation->refereeDetails->address;
	    if(empty($address->flatNumber)) {
	        
	        if(empty($address->houseName)) {
	            
	            if(empty($address->houseNumber)) {
	            
	                if(empty($address->addressLine1)) {
	                    
	                    $missingInfo[] = "$label occupation address: 1st line";
	                }
	            }
	        }
	    }
	    
	    if(empty($address->town)) {
	    
	        $missingInfo[] = "$label occupation address: town";
	    }
	    
	    if(empty($address->postCode)) {
	        
	        $missingInfo[] = "$label occupation address: postcode";
	    }    
	    
	    
	    //Contact number check
	    $contactDetails = $thisOccupation->refereeDetails->contactDetails;
	    if(empty($contactDetails->telephone1)) {
	        
	        $missingInfo[] = "$label occupation: telephone number";
	    }
	    
	    
	    //Organisation name
	    if(empty($thisOccupation->refereeDetails->organisationName)) {
	        
	        $missingInfo[] = "$label occupation: organisation name";
	    }
	    
	    
	    //Contact name.
	    if(empty($thisOccupation->refereeDetails->name->firstName)) {
	        
	        if(empty($this->occupation->refereeDetails->name->lastName)) {
	            
	            $missingInfo[] = "$label occupation: contact name";
	        }
	    }
	    
	    
	    //Contact position.
	    if(empty($thisOccupation->refereeDetails->position)) {
	        
	        $missingInfo[] = "$label occupation: contact position";
	    }
	    
	    
	    //Position held at the organisation
	    if(empty($thisOccupation->position)) {
	        
	        $missingInfo[] = "$label occupation: position held";
	    }
	    
	    
	    //Start date at the occupation.
	    if(empty($thisOccupation->startDate)) {
	        
	        $missingInfo[] = "$label occupation: start date";
	    }

		//If position is not permanent we will need the enddate.
	    if(!$thisOccupation->isPermanent) {

			//We need the enddate if the position is not permanent.
			/*
			if(empty($thisOccupation->endDate)) {

				$missingInfo[] = "$label occupation: end date";
			}
			*/
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