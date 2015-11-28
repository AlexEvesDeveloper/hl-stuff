<?php

/**
 * Business rules class which provides underwriting referrals services for TCI+ products.
 */
class Manager_Insurance_TenantsContentsPlus_Referral extends Manager_Insurance_Referral{

	/**
	 * Implements abstract method from superclass - refer to superclass for description.
	 */
    public function getReferralReasons($policyNumber) {

		$referralReasons = array();
		$params = Zend_Registry::get('params');

        $quote = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $policyNumber);
        if($quote->getPolicyName() == 'tenantsp') {

            //Test 1: If the cover is greater than the threshold, then refer.
			$contentsAmount = $quote->getPolicyOptionAmountCovered('contentstp');
			$contentsThreshold = new Zend_Currency(
                array(
                    'value' => $params->uw->rt->tenantsp->contents,
                    'precision' => 0
                ));

			if($contentsAmount >= $contentsThreshold) {
                $referralReasons[] = $params->uw->rr->tenantsp->cover;
            }


            //Test 2: If claim values are greater than 1000 then refer.
            if(empty($this->_previousClaimsModel)) {
                $this->_previousClaimsModel = new Datasource_Insurance_PreviousClaims();
            }

            $previousClaimsArray = $this->_previousClaimsModel->getPreviousClaims($quote->getRefno());
            if(!empty($previousClaimsArray)) {

                //Tenant has one or more claims. Add the totals together to see if they exceed
                //the threshold.
                $claimsTotal = new Zend_Currency(
					array(
						'value' => 0,
						'precision' => 2));

                foreach($previousClaimsArray as $previousClaim) {

                    $claimsTotal->add($previousClaim->getClaimValue());
                }


				//Test against the previous claims threshold
				$claimsThreshold = new Zend_Currency(
					array(
						'value' => $params->uw->rt->tenantsp->claimsThreshold,
						'precision' => 2
					));

                if($claimsTotal->isMore($claimsThreshold)) {

                    $referralReasons[] = $params->uw->rr->tenantsp->previousClaims;
                }
            }


			//Test 3: Answering the underwriting questions.
			$answersManager = new Manager_Insurance_Answers();
			$answersArray = $answersManager->getUnderwritingAnswers($policyNumber);
			if(empty($answersArray)) {

				//You can't process for referral if no underwriting answers have first been provided.
				throw new Zend_Exception(get_class() . __FUNCTION__ . ": no underwriting answers provided.");
			}

			foreach($answersArray as $currentAnswer) {

				//Identify if the current answer is one that should be checked.
				if(in_array($currentAnswer->getQuestionNumber(), $params->uw->rt->tenantsp->checkAnswer->toArray())) {

					$answer = $currentAnswer->getAnswer();
					if($answer == Model_Insurance_Answer::YES) {

						$referralReasons[] = $params->uw->rr->tenantsp->answer;
					}
				}
			}


			//Test 4: pedal cycles over 1500
			$pedalCyclesModel = new Datasource_Insurance_Policy_Cycles($quote->getRefno(), $policyNumber);
			$pedalCyclesArray = $pedalCyclesModel->listBikes();
			if(!empty($pedalCyclesArray)) {

				$pedalCycleThreshold = new Zend_Currency(
					array(
						'value' => $params->uw->rt->tenantsp->pedalCycle,
						'precision' => 2
					));

				foreach($pedalCyclesArray as $currentPedalCycle) {

					//Compare the pedal cycle values in Zend_Currency format for simplity.
					$currentCycleValue = new Zend_Currency(
						array(
							'value' => $currentPedalCycle['value'],
							'precision' => 2
						));

					if($currentCycleValue->isMore($pedalCycleThreshold)) {

						$referralReasons[] = $params->uw->rr->tenantsp->pedalCycle;
					}
				}
			}


			//Test 5: specified possessions greater than threshold
			$specPossessionsModel = new Datasource_Insurance_Policy_SpecPossessions($policyNumber);
			$specPossessionsArray = $specPossessionsModel->listPossessions();
			if(!empty($specPossessionsArray)) {

				//Wrap the threshold parameter in a Zend_Currency object for easier comparisons.
				$specPossessionThreshold = new Zend_Currency(
					array(
						'value' => $params->uw->rt->tenantsp->specPossession,
						'precision' => 2
					));

				//Cycle through each specpossession...
				foreach($specPossessionsArray as $currentSpecPossession) {

					//Wrap the current specpossession value in a Zend_Currency for easier comparision.
					$currentSpecPossessionValue = new Zend_Currency(
						array(
							'value' => $currentSpecPossession['value'],
							'precision' => 2
						));


					//Determine if the threshold is exceeded:
					if($currentSpecPossessionValue->isMore($specPossessionThreshold)) {

						$referralReasons[] = $params->uw->rr->tenantsp->specPossession;
					}
				}
			}
        }
        else {

            throw new Zend_Exception("Invalid product.");
        }

        return $referralReasons;
    }
    
    
    /**
     * Implements abstract method from superclass - refer to superclass for description.
     */
    public function setToRefer($policyNumber) {

        $quote = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $policyNumber);
        return $quote->setPayStatus('Referred');
    }
}

?>
