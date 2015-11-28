<?php

/*
 * Convenience class used to identify missing information in the data entry process.
 *
 * This class depends on missing information identified in the HRT notes (!), and as such
 * should be deprecated as soon as possible.
 */
class Manager_Referencing_DataEntry_MissingInformation_Legacy {

    /**
     * Returns the missing information on a reference, if any.
     *
     * @param Model_Referencing_Reference $reference
     * The Reference object to check for missing information.
     *
     * @return mixed
     * Returns an array of all missing informations, or null if there are no
     * missing informations.
     */
     public function getMissingInformation($reference) {
    	
    	//First check the $reference object to see if there is an interim report time. If yes,
    	//then we know that all information has been provided, so return null.
    	if(!empty($reference->progress)) {
    		
    		$progressItem=Manager_Referencing_Progress::findSpecificProgressItem($reference->progress, Model_Referencing_ProgressItemVariables::INTERIM_REPORT_BUILT);
    		if($progressItem->itemState == Model_Referencing_ProgressItemStates::COMPLETE) {
    			
    			return null;
    		}
    	}
    	
    	
    	//No interim report time yet, so check if there are missing informations on this reference.
    	$notesDatasource = new Datasource_Referencing_Notes();
    	$notesArray = $notesDatasource->getNotes($reference->externalId);
		$initialMissingInformation = null;
    	if(!empty($notesArray)) {
        	
            foreach($notesArray as $currentNote) {
	      	
	        	if(preg_match("/There were the following problems/", $currentNote['text'])) {
	
	        		$initialMissingInformation = $currentNote['text'];
	        		break;
	        	}
	        }
        }
        
        if(empty($initialMissingInformation)) {
        	
        	//No missing information.
        	return null;
        }
        
        
        
        //If here then at some point there was missing information on this reference. Look to see
        //if some information was subsequently provided.
        $isSubsequentlyUpdated = false;
        foreach($notesArray as $currentNote) {
      	
            if(($currentNote['textId'] == '31') || ($currentNote['textId'] == '59')) {

        		$isSubsequentlyUpdated = true;
        		break;
        	}
        }

        if(!$isSubsequentlyUpdated) {
        	
        	//There have been no subsequent updates. Return the missing information as an array.
        	preg_match_all('/\d+\)(.+)\n/m', $initialMissingInformation, $matches);
			return $matches[1];
        }
      
        
        
        //If here then determine what information was subsequently provided. Explode the
        //missing information blob into an array and check each item to see if it has 
        //been subsequently provided.
		preg_match_all('/\d+\)(.+)\n/m', $initialMissingInformation, $matches);	
        $updatedMissingInformation = array();
        foreach($matches[1] as $currentMissingItem) {
        	
        	if($this->_isStillMissing($reference, $currentMissingItem)) {
        		
        		$updatedMissingInformation[] = $currentMissingItem;
        	}
        }
        
        if(empty($updatedMissingInformation)) {
        	
        	$updatedMissingInformation = null;
        }
        
        return $updatedMissingInformation;
    }
    
    
	/**
     * Advises if specific informations are still missing on a reference.
     * 
     * @param Model_Referencing_Reference $reference
     * Represents the reference to check.
     * 
     * @param string $missingElement
     * Indicates what missing information to search for.
     * 
     * @return boolean
     * Returns true if the $missingElement is still missing, false otherwise.
     * 
     * @todo
     * Bank account details for foreign nationals not yet implemented.
     */
    protected function _isStillMissing($reference, $missingElement) {
    	
    	$isStillMissing = false;
    	
    	try {
    		
	    	if(preg_match("/Property street/", $missingElement) == 1) {
	    		
    		    if(empty($reference->propertyLease->property->address->addressLine1)) {
    				
    				$isStillMissing = true;
    			}
	    	}
	    	else if(preg_match("/Property town/", $missingElement) == 1) {
	    		
	    	    if(empty($reference->propertyLease->property->address->town)) {
    				
    				$isStillMissing = true;
    			}
	    	}
	        else if(preg_match("/Property total rent/", $missingElement) == 1) {
	    		
	        	if(empty($reference->propertyLease->rentPerMonth)) {
    				
    				$isStillMissing = true;
    			}
    			else if($reference->propertyLease->rentPerMonth->getValue() == 0) {
    				
    				$isStillMissing = true;
    			}
	    	}
	        else if(preg_match("/Tenant christian name/", $missingElement) == 1) {
    				
    			if(empty($reference->referenceSubject->name->firstName)) {
    				
    				$isStillMissing = true;
    			}
	    	}
	        else if(preg_match("/Tenant last name/", $missingElement) == 1) {
	    		
	            if(empty($reference->referenceSubject->name->lastName)) {
    				
    				$isStillMissing = true;
    			}
	    	}
	        else if(preg_match("/Invalid date of birth/", $missingElement) == 1) {
	    		
	        	if(empty($reference->referenceSubject->dob)) {
    				
    				$isStillMissing = true;
    			}
	    	}
	        else if(preg_match("/total annual income/", $missingElement) == 1) {
    			
    			//The legacy datasources are crap and store the total annual income in the Tenant table 
    			//rather than distributing this across the occupational records in the badly-named employment 
    			//table. Test this.
				$referenceSubjectDatasource = new Datasource_ReferencingLegacy_ReferenceSubject();
				$totalAnnualIncome = $referenceSubjectDatasource->getLegacyTotalAnnualIncome($reference->externalId);
				if(empty($totalAnnualIncome)) {
					
					$isStillMissing = true;
				}
				else {

					if($totalAnnualIncome->getValue() == 0) {
	
						$isStillMissing = true;
					}
				}
	    	}
	        else if(preg_match("/The tenants rent share is missing/", $missingElement) == 1) {
	    		
	        	if(empty($reference->referenceSubject->shareOfRent)) {
    				
    				$isStillMissing = true;
    			}
	        	else if($reference->referenceSubject->shareOfRent->getValue() == 0) {
    				
    				$isStillMissing = true;
    			}
	    	}
	        else if(preg_match("/Application form - The Bank Account/", $missingElement) == 1) {
	    		
	    		//Full string is: Application form - The Bank Account/Passport No/Nationality Details was missing
	    	}
	        else if(preg_match("/Current address - months at address/", $missingElement) == 1) {
	    		
	    		foreach($reference->referenceSubject->residences as $residence) {
	    			
	    			if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
	    				
	    				if(empty($residence->duration)) {
	    					
	    					$isStillMissing = true;
	    				}
	    			}
	    		}
	    	}
	        else if(preg_match("/Current address - street was blank/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->residences as $residence) {
	    			
	    			if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
	    				
	    				if(empty($residence->address->addressLine1)) {
	    					
	    					$isStillMissing = true;
	    				}
	    			}
	    		}
	    	}
	        else if(preg_match("/Current address - postcode was blank/", $missingElement) == 1) {
	    		
	       		foreach($reference->referenceSubject->residences as $residence) {
	    			
	    			if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
	    				
	    				if(empty($residence->address->postCode)) {
	    					
	    					$isStillMissing = true;
	    				}
	    			}
	    		}
	    	}
	        else if(preg_match("/1st Previous address - street was blank/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->residences as $residence) {
	    			
	    			if($residence->chronology == Model_Referencing_ResidenceChronology::FIRST_PREVIOUS) {
	    				
	    				if(empty($residence->address->addressLine1)) {
	    					
	    					$isStillMissing = true;
	    				}
	    			}
	    		}
	    	}
	        else if(preg_match("/1st Previous address - postcode was blank/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->residences as $residence) {
	    			
	    			if($residence->chronology == Model_Referencing_ResidenceChronology::FIRST_PREVIOUS) {
	    				
	    				if(empty($residence->address->postCode)) {
	    					
	    					$isStillMissing = true;
	    				}
	    			}
	    		}
	    	}
	        else if(preg_match("/1st previous address - months at address/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->residences as $residence) {
	    			
	    			if($residence->chronology == Model_Referencing_ResidenceChronology::FIRST_PREVIOUS) {
	    				
	    				if(empty($residence->duration)) {
	    					
	    					$isStillMissing = true;
	    				}
	    			}
	    		}
	    	}
	        else if(preg_match("/2nd Previous address - street was blank/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->residences as $residence) {
	    			
	    			if($residence->chronology == Model_Referencing_ResidenceChronology::SECOND_PREVIOUS) {
	    				
	    				if(empty($residence->address->addressLine1)) {
	    					
	    					$isStillMissing = true;
	    				}
	    			}
	    		}
	    	}
	        else if(preg_match("/2nd Previous address - postcode was blank/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->residences as $residence) {
	    			
	    			if($residence->chronology == Model_Referencing_ResidenceChronology::SECOND_PREVIOUS) {
	    				
	    				if(empty($residence->address->postCode)) {
	    					
	    					$isStillMissing = true;
	    				}
	    			}
	    		}
	    	}
	        else if(preg_match("/No Property Landlord name given/", $missingElement) == 1) {
	    		
	    		if(empty($reference->propertyLease->prospectiveLandlord->name->firstName)) {

	    			if(empty($reference->propertyLease->prospectiveLandlord->name->lastName)) {
	    				
	    				$isStillMissing = true;
	    			}
	    		}
	    	}
	        else if(preg_match("/No Property Landlord street address given/", $missingElement) == 1) {
	    		
	        	if(empty($reference->propertyLease->prospectiveLandlord->address->addressLine1)) {
	    				
	    			$isStillMissing = true;
	    		}
	    	}
	        else if(preg_match("/No Property Landlord town address given/", $missingElement) == 1) {
	    		
	        	if(empty($reference->propertyLease->prospectiveLandlord->address->town)) {
	    				
	    			$isStillMissing = true;
	    		}
	    	}
	        else if(preg_match("/No Property Landlord telephone number given/", $missingElement) == 1) {
	    		
	        	if(empty($reference->propertyLease->prospectiveLandlord->contactDetails->telephone1)) {
	    				
	        		if(empty($reference->propertyLease->prospectiveLandlord->contactDetails->telephone2)) {
	        		
	        			$isStillMissing = true;
	        		}
	    		}
	    	}
	        else if(preg_match("/No Property Landlord telephone, email/", $missingElement) == 1) {
	    		
	        	if(empty($reference->propertyLease->prospectiveLandlord->contactDetails->telephone1)) {
	    				
	        		if(empty($reference->propertyLease->prospectiveLandlord->contactDetails->telephone2)) {
	        		
	        			if(empty($reference->propertyLease->prospectiveLandlord->contactDetails->email1)) {
	        			
	        				if(empty($reference->propertyLease->prospectiveLandlord->contactDetails->email2)) {
	        				
	        					if(empty($reference->propertyLease->prospectiveLandlord->contactDetails->fax1)) {
	        					
	        						if(empty($reference->propertyLease->prospectiveLandlord->contactDetails->fax2)) {
	        						
	        							$isStillMissing = true;
	        						}
	        					}
	        				}
	        			}
	        		}
	    		}
	    	}
	        else if(preg_match("/No Property Landlord postcode given/", $missingElement) == 1) {
	    		
	        	if(empty($reference->propertyLease->prospectiveLandlord->address->postCode)) {
	    				
	    			$isStillMissing = true;
	    		}
	    	}
	        else if(preg_match("/No Landlord telephone, email or fax/", $missingElement) == 1) {
	    		
	    		//Current landlord
	    		foreach($reference->referenceSubject->residences as $residence) {
	    			
	    			if($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
	    				
		    			if(empty($residence->refereeDetails->contactDetails->telephone1)) {
		    				
			        		if(empty($residence->refereeDetails->contactDetails->telephone2)) {
			        		
			        			if(empty($residence->refereeDetails->contactDetails->email1)) {
			        			
			        				if(empty($residence->refereeDetails->contactDetails->email2)) {
			        				
			        					if(empty($residence->refereeDetails->contactDetails->fax1)) {
			        					
			        						if(empty($residence->refereeDetails->contactDetails->fax2)) {
			        						
			        							$isStillMissing = true;
			        						}
			        					}
			        				}
			        			}
			        		}
			    		}
			    		break;
	    			}
	    		}
	    	}
	        else if(preg_match("/current employer start date invalid/", $missingElement) == 1) {
	    		
	    		foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
	    				
	    				if(empty($occupation->startDate)) {
	    					
	    					$isStillMissing = true;
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/current employer end date invalid/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
	    				
	    				if(empty($occupation->endDate)) {
	    					
	    					$isStillMissing = true;
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/current employer gross salary not provided/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
	    				
	    				if(empty($occupation->income)) {
	    					
	    					$isStillMissing = true;
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/current employer company name not provided/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
	    				
	    				if(empty($occupation->refereeDetails->organisationName)) {
	    					
	    					$isStillMissing = true;
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/current employer company address and postcode not provided/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
	    				
	    				if(empty($occupation->refereeDetails->organisationAddress->addressLine1)) {
	    					
	    					if(empty($occupation->refereeDetails->organisationAddress->postCode)) {
	    					
	    						$isStillMissing = true;
	    					}
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/current employer telephone number not provided/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->chronology == Model_Referencing_OccupationChronology::CURRENT) {
	    				
	    				if(empty($occupation->refereeDetails->contactDetails->telephone1)) {
	    					
	    					if(empty($occupation->refereeDetails->contactDetails->telephone2)) {
	    					
	    						$isStillMissing = true;
	    					}
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/second employer start date invalid/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->classification == Model_Referencing_OccupationImportance::SECONDARY) {
	    				
	    				if(empty($occupation->startDate)) {
	    					
	    					$isStillMissing = true;
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/second employer gross salary not provided/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->classification == Model_Referencing_OccupationImportance::SECONDARY) {
	    				
	    				if(empty($occupation->income)) {
	    					
	    					$isStillMissing = true;
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/second employer company name not provided/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	        		if($occupation->classification == Model_Referencing_OccupationImportance::SECONDARY) {
	    				
	    				if(empty($occupation->refereeDetails->organisationName)) {
	    					
	    					$isStillMissing = true;
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/second employer company address and/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	        		if($occupation->classification == Model_Referencing_OccupationImportance::SECONDARY) {
	    				
	    				if(empty($occupation->refereeDetails->organisationAddress->addressLine1)) {
	    					
	    					if(empty($occupation->refereeDetails->organisationAddress->postCode)) {
	    					
	    						$isStillMissing = true;
	    					}
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/second employer telephone number not/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	        		if($occupation->classification == Model_Referencing_OccupationImportance::SECONDARY) {
	    				
	    				if(empty($occupation->refereeDetails->contactDetails->telephone1)) {
	    					
	    					if(empty($occupation->refereeDetails->contactDetails->telephone2)) {
	    					
	    						$isStillMissing = true;
	    					}
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/Application form - no product selected/", $missingElement) == 1) {
	    		
	    		if(is_null($reference->productSelection->product->name)) {
	    			
	    			$isStillMissing = true;
	    		}
	    	}
	        else if(preg_match("/future employer start date invalid/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
	    				
	    				if(empty($occupation->startDate)) {
	    					
	    					$isStillMissing = true;
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/future employer gross salary not provided/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
	    				
	    				if(empty($occupation->income)) {
	    					
	    					$isStillMissing = true;
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/future employer company name not provided/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
	    				
	    				if(empty($occupation->refereeDetails->organisationName)) {
	    					
	    					$isStillMissing = true;
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/future employer company address/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
	    				
	    				if(empty($occupation->refereeDetails->organisationAddress->addressLine1)) {
	    					
	    					if(empty($occupation->refereeDetails->organisationAddress->postCode)) {
	    					
	    						$isStillMissing = true;
	    					}
	    				}
	    				break;
	    			}
	    		}
	    	}
	        else if(preg_match("/future employer telephone number not provided/", $missingElement) == 1) {
	    		
	        	foreach($reference->referenceSubject->occupations as $occupation) {
	    			
	    			if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
	    				
	    				if(empty($occupation->refereeDetails->contactDetails->telephone1)) {
	    					
	    					if(empty($occupation->refereeDetails->contactDetails->telephone2)) {
	    					
	    						$isStillMissing = true;
	    					}
	    				}
	    				break;
	    			}
	    		}
	    	}
    	}
    	catch(Exception $e) {
    		
    		$isStillMissing = true;
    	}
    	
    	return $isStillMissing;
    }
}

?>