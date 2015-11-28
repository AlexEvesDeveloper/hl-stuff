<?php

/**
 * Business rules class which provides underwriting endorsement services.
 */
class Manager_Insurance_TenantsContentsPlus_Endorsement extends Manager_Insurance_Endorsement {
	
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
     */
    public function getEndorsementsRequired($policyNumber) {
        
        $params = Zend_Registry::get('params');
        $returnArray = array();
		
		$quote = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $policyNumber);
        if($quote->getPolicyName() == 'tenantsp') {

            //As a TCI+ policy, this will automatically require a student sharer endorsement
            $endorsement = new Model_Insurance_Endorsement();
            $endorsement->setPolicyNumber($policyNumber);
            
            $endorsementType = new Model_Insurance_EndorsementType();
            $endorsementType->setID($params->uw->ed->tenantsp->studentSharer->id);
            $endorsementType->setName($params->uw->ed->tenantsp->studentSharer->name);
            $endorsement->setEndorsementType($endorsementType);
            
			$endorsement->setEffectiveDate(new Zend_Date($quote->getStartDate(), Zend_Date::ISO_8601));
            $returnArray[] = $endorsement;
            
            
            //Check if minimum standards of protection endorsement is required.
            $mspEndorsementRequired = false;
			$contentsAmountCovered = $quote->getPolicyOptionAmountCovered('contentstp');
			$mspEndorsementThreshold = new Zend_Currency(
				array(
					'value' => $params->uw->et->tenantsp->mandatory->contents,
					'precision' => 0
				));
			
            if($contentsAmountCovered->isMore($mspEndorsementThreshold)) {
              
                $mspEndorsementRequired = true;
            }
            else {
  
                //MSP endorsements are also required if the applicant lives in a 'security' postcode.
                if($quote->getIsHighRisk() == $params->uw->et->tenantsp->mandatory->highRiskFlag) {
                    
					$mspEndorsementRequired = true;
                }
            }
         
            if($mspEndorsementRequired) {
				
                $endorsement = new Model_Insurance_Endorsement();
                $endorsement->setPolicyNumber($policyNumber);
                
                $endorsementType = new Model_Insurance_EndorsementType();
	            $endorsementType->setID($params->uw->ed->tenantsp->minStandardProtection->id);
	            $endorsementType->setName($params->uw->ed->tenantsp->minStandardProtection->name);
	            $endorsement->setEndorsementType($endorsementType);
                
				$endorsement->setEffectiveDate(new Zend_Date($quote->getStartDate(), Zend_Date::ISO_8601));
                $returnArray[] = $endorsement;
            }
        }
        else {
        	
        	//This method has been used on a quote/policy that is not TenantsContentsPlus. Throw
        	//an exception.
        	throw new Zend_Exception(get_class() . '::' . __FUNCTION__ . ': unknown product.');
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
}

?>