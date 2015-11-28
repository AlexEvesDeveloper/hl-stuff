<?php

/**
 * Business rules class which provides underwriting endorsement services.
 * 
 * @todo
 * getEndorsementsRequired() not yet complete.
 */
class Manager_Insurance_LandlordsPlus_Endorsement extends Manager_Insurance_Endorsement {
	
	/**
     * Returns the endorsements required by a quote or policy.
     *
     * This method will identify the endorsements that should be applied to
     * the quote or policy identified by $policyNumber. If any endorsements are
     * identified, they will be detailed in one or more Model_Insurance_Endorsement 
     * objects, which will then be returned in an array. If the quote / policy does
     * not merit any endorsements, then null will be returned.
     *
     * @param string $policyNumber
     * The quote or policy number.
     *
     * @return mixed
     * Returns an array of Model_Insurance_Endorsement objects,
     * or null if no endorsements are applicable.
     * 
     * @todo
     * Not yet complete.
     */
    public function getEndorsementsRequired($quoteID) {
        
    	//Create a quote object from the $policyNumber
    	$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($quoteID);
		$legacyQuoteId = $quoteManager->getModel()->legacyID;
    	
		
    	//Extract the postcode from the policyNumber
    	$properties = $quoteManager->getProperties();
    	$postCode = $properties[0]['postcode'];
    	

    	//Extract the contents cover, if applicable.
    	if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER)) {
    		$contentsMeta = $quoteManager->getProductMeta(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER);
    		$contentsCover = $contentsMeta['cover_amount'];
    	} else {
    		$contentsCover = 0;
    	}
    	
    	$params = Zend_Registry::get('params');
    	$returnArray = array();
    	
    	
    	//First check for flood endorsements.
		$termsDatasource = new Datasource_Insurance_LandlordsPlus_Terms();
    	$floodRiskScore = $termsDatasource->getFloodRiskScore($postCode);
    	if($floodRiskScore > 0) {
    		
    		//Mandatory/optional endorsement
    		$endorsement = new Model_Insurance_Endorsement();
            $endorsement->setPolicyNumber($legacyQuoteId);
                
            $endorsementType = new Model_Insurance_EndorsementType();
            $endorsementType->setID($params->uw->ed->landlordsp->floodExclusion->id);
            $endorsementType->setName($params->uw->ed->landlordsp->floodExclusion->name);
            $endorsement->setEndorsementType($endorsementType);
                
			$endorsement->setEffectiveDate(new Zend_Date($quoteManager->getStartDate(), Zend_Date::ISO_8601));
            $returnArray[] = $endorsement;
    	}
    	
    	
    	//Next check for subsidence endorsements.
    	$subsidenceRiskScore = $termsDatasource->getSubsidenceRiskScore($postCode);
    	if($subsidenceRiskScore > 0) {
    						
			//Mandatory endorsement
    		$endorsement = new Model_Insurance_Endorsement();
            $endorsement->setPolicyNumber($legacyQuoteId);
                
            $endorsementType = new Model_Insurance_EndorsementType();
            $endorsementType->setID($params->uw->ed->landlordsp->subsidence->id);
            $endorsementType->setName($params->uw->ed->landlordsp->subsidence->name);
            $endorsement->setEndorsementType($endorsementType);
                
			$endorsement->setEffectiveDate(new Zend_Date($quoteManager->getStartDate(), Zend_Date::ISO_8601));
            $returnArray[] = $endorsement;
    	}
    	
    	
		//Next check for minimum standards of security.
		if($contentsCover >= $params->uw->et->landlordsp->mandatory->contents) {
			
			//Mandatory endorsement
			$endorsement = new Model_Insurance_Endorsement();
            $endorsement->setPolicyNumber($legacyQuoteId);
                
            $endorsementType = new Model_Insurance_EndorsementType();
            $endorsementType->setID($params->uw->ed->landlordsp->minStandardProtection->id);
            $endorsementType->setName($params->uw->ed->landlordsp->minStandardProtection->name);
            $endorsement->setEndorsementType($endorsementType);
                
			$endorsement->setEffectiveDate(new Zend_Date($quoteManager->getStartDate(), Zend_Date::ISO_8601));
            $returnArray[] = $endorsement;
		}
		
		
		//Provide a return value consistent with this methods contract.
        if(empty($returnArray)) {
            
            $returnVal = null;
        }
        else {
            
            $returnVal = $returnArray;
        }
        return $returnVal;
    }
    
    
    /**
	* Use this method to determine if the flood endorsement can be optionally swapped for a loading. 
	*
	* @param string $postCode
	* The postcode to test.
	*
	* @return boolean
	* Returns true/false according to whether or not the flood endorsement is optional on the
	* postcode passed in.
	*/
    public function getIsFloodEndorsementOptional($postCode) {

    	$params = Zend_Registry::get('params');
		
    	$uwTermsDatasource = new Datasource_Insurance_LandlordsPlus_Terms();
		$floodScore = $uwTermsDatasource->getFloodRiskScore($postCode);
		if ($floodScore == $params->uw->et->landlordsp->optional->floodExclusion->score) {
			
			$returnVal = true;
		}
		else {
			
			$returnVal = false;
		}
		
		return $returnVal;
    }
}

?>
