<?php

/**
 * Business rules class which changes quote identifiers to policy identifiers
 * in the relevant datasources. For example, a quote QHLI12345678/01 with records
 * in the DocumentHistory and Endorsements datasources, would be changed to
 * PHLI12345678/01 in those same datasources.
 */
class Manager_Insurance_TenantsContentsPlus_QuoteToPolicy {

	/**
	 * Full description given in the IChangeable interface.
	 *
	 * Will convert all quote identifiers to policy identifiers, for a given
	 * quote number, in all appropriate datasources.
	 *
	 * @param $string $quoteNumber
	 * The full QHLI number.
	 *
	 * @param string $policyNumber
	 * Optional. The full PHLI number. If not provided, then it will be assumed that
	 * the 'QHLI' should be changed to 'PHLI'.
	 *
	 * @return void
	 */
	public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		
		//Test to see if a test account was uesed to enter the policy
		$tester = new Manager_Core_Test();
		if($tester->isTestPolicy($quoteNumber)) {
			
			//Populate a TestPolicy object into the datasource.
			$testPolicy = new Model_Core_TestPolicy();
			 		
	    	$quoteDatasource = new Datasource_Insurance_LegacyQuotes();
	    	$quote = $quoteDatasource->getByPolicyNumber($quoteNumber);
            $testPolicy->agentschemeno = $quote->agentSchemeNumber;;
            
            $testPolicy->csuid = 87;
            $testPolicy->isTestPolicy = "Yes";
            $testPolicy->policynumber = $quoteNumber;
            
            $testPolicyDatasource = new Datasource_Insurance_Policy_TestPolicy();
            $testPolicyDatasource->insertTestPolicy($testPolicy);
		}


		$changeableArray = array();
		$changeableArray[] = new Datasource_Insurance_Answers();
		$changeableArray[] = new Datasource_Insurance_Endorsements();
		$changeableArray[] = new Datasource_Insurance_AdditionalInformation();
		$changeableArray[] = new Datasource_Insurance_TenantsContentsPlus_Sharers();
        $changeableArray[] = new Datasource_Insurance_Policy_Term();
		$changeableArray[] = new Datasource_Core_UnderwritingEmailLog();
		$changeableArray[] = new Datasource_Core_DirectDebit_Payment();
		$changeableArray[] = new Datasource_Core_CreditCard_Transaction();
		$changeableArray[] = new Datasource_Core_CreditCard_Payment();
		$changeableArray[] = new Datasource_Insurance_Schedules();
		$changeableArray[] = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $quoteNumber);
		
        $changeableArray[] = new Datasource_Core_CustomerContactPreferences();
		$changeableArray[] = new Datasource_Insurance_DocumentQueue();
        $changeableArray[] = new Datasource_Insurance_DocumentHistory();
		$changeableArray[] = new Datasource_Insurance_DocumentQueue();
		$changeableArray[] = new Datasource_Insurance_PolicyNotes();
		$changeableArray[] = new Datasource_Insurance_Policy_Cover();
		$changeableArray[] = new Datasource_Insurance_Policy_Cycles(null,$quoteNumber);
		$changeableArray[] = new Datasource_Insurance_Policy_SpecPossessions($quoteNumber);
		$changeableArray[] = new Datasource_Insurance_Policy_Term();	
        $changeableArray[] = new Datasource_Insurance_IddSupport();

                $changeableArray[] = new Datasource_Insurance_CompletionNotes();
		foreach($changeableArray as $currentChangeable) {
			
			$currentChangeable->changeQuoteToPolicy($quoteNumber, $policyNumber);
		}
	}
}

?>
