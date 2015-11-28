<?php
    /**
    * Manager class responsible for producing a portfolio quote
    * This has been based on the existing quote and calulator logic in legacy portfolio
    *
    * This is one mucky manager
    */
    class Manager_Insurance_Portfolio_Quote {
        private $_agentsRateID;
        private $_rates;
        private $_factors;
        
        public function quote($refNo){

        $buildingAreaSum 	= array(0,0,0,0,0,0,0,0);
        $buildingAreaPremium = array(0,0,0,0,0,0,0,0);
        $contentsAreaSum 	= array(0,0,0,0,0,0,0);
        $contentsAreaPremium = array(0,0,0,0,0,0,0);

        $professionalprop = 0;
        $total_sum_buildings = 0;
        $total_sum_contents = 0;
        $limited_contents_cost = 0;

        $professionalRate = 0;

        $buildingsAccidentalDamage = 0;
        $buildingsNoExcess = 0;
        $contentsAccidentalDamage = 0;
        $contentsNoExcess = 0;
        $limited_contents_cost = 0;
        $bReferred = false;
        $has_claims = 0;
        $tenantnum=0;
        // Fetch some rates information
        // Agents Rate ID
        $agent = new Manager_Core_Agent();
        # TODO: need to reove this schemenumber
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$session = new Zend_Session_Namespace('homelet_global');
        $this->_agentsRateID = $agent->getRatesetIDByASN($session->referrer);

        $portfolioRates = new Datasource_Insurance_Portfolio_PortfolioRates();
        $this->_rates = $this->_fetchRates();
        $this->_factors = $this->_fetchFactors();
        // Fetch some fees information
        // Monthly admin fee

        $propertyManager= new Manager_Insurance_Portfolio_Property();
        $properties = array();
        // Fetch all the properties related to this customer refNo
        $properties = $propertyManager->fetchAllProperties($refNo)->toArray();

        $tenantnum = count ($properties);
        if ($tenantnum < 2 ){
        	header("location: /portfolio/insurance-quote/step2");
        	return;
        }
        $dsBuildingsRiskArea = new Datasource_Insurance_RiskAreas_Buildings();
        $dsContentsRiskArea = new Datasource_Insurance_RiskAreas_LandlordsContents();
            // Now iterate thru the properties
            foreach($properties as $property){
                // Calculate Buildings
                $riskAreaB = $dsBuildingsRiskArea->getCurrentRate($property['postcode']);
                $arrayNum = $riskAreaB - 1; // array sets first location to 0
                if($riskAreaB >= 1 && $riskAreaB <=7){
             		if($property['tenantOccupation'] != "DSS"){
                        $buildingAreaSum[$arrayNum] += $property['buildingsSumInsured'];
                    }else{
                        $buildingAreaSum[7] += $property['buildingsSumInsured'];
                    }
                    $total_sum_buildings += $property['buildingsSumInsured'];
                }

               if(($property['buildingsAccidentalDamage'] == "Yes") && ($property['tenantOccupation'] != "DSS"))
                {
                    $buildingsAccidentalDamage += $property['buildingsSumInsured'];
                }
                if(($property['buildingsNilExcess'] == "Yes") && ($property['tenantOccupation'] != "DSS"))
                {
                    $buildingsNoExcess += $property['buildingsSumInsured'];
                }
                // End Calculate Buildings

                // Calculate Contents
           		if($property['limitedContents'] != "Yes"){ // If Contents Insurance for Unfurnished Properties was not selected
                    $contentsRiskArea = $dsContentsRiskArea->getCurrentRate($property['postcode']);
                    $arrayNum = $contentsRiskArea - 1;
                    if($contentsRiskArea >= 1 && $contentsRiskArea <=5){

                        if($property['tenantOccupation'] != "DSS"){
                            $contentsAreaSum[$arrayNum] += $property['contentsSumInsured'];
                        } else {
                            $contentsAreaSum[5] += $property['contentsSumInsured'];
                        }
                            $total_sum_contents += $property['contentsSumInsured'];
                    }

                    if(($property['contentsAccidentalDamage'] == "Yes") && ($property['tenantOccupation'] != "DSS")){
                        $contentsAccidentalDamage += $property['contentsSumInsured'];
                    }
        			if(($property['contentsNilExcess'] == "Yes") && ($property['tenantOccupation'] != "DSS")){
                        $contentsNoExcess += $property['contentsSumInsured'];
                    }
                }else {
                    // calculate the cost of limited contents
                    $limited_contents_cost += $this->_rates['limited_contents_price'];
                }

                // Count Professional Tenants
                switch($property['tenantOccupation'])
                {
                    case "Employed":
                    case "Retired":
                    case "Self Employed":
                        $professionalprop++;
                    break;
                    case "DSS":
                        // If the occupier is classified as DSS and the user has opted for contents insurance
                        // then the referral display should be shown instead of the calculated premium
                        $tenantnum = ($tenantnum == 1) ? 1 : $tenantnum--;

                        if(($limited_contents_cost > 0) || ($total_sum_contents > 0))
                            $bReferred = true;
                    break;
                    default:
                        //$bReferred = true; // non-proffessional tenants no longer need referring
                    break;
                }
            }

        // Calculate Premiums
        /* buildings premiums ---------------------------------- */
        $buildings_premium = 0;
        for($i = 0; $i <= 7; $i++) // loop through number of building risk areas
        {
            $buildingAreaPremium[$i] = (($buildingAreaSum[$i]/100) * $this->_rates['buildingNet'][$i]);
            $buildings_premium += $buildingAreaPremium[$i];
        }
        $buildingsAD_premium = ($buildingsAccidentalDamage == "") ? 0 : ($buildingsAccidentalDamage/100) * $this->_rates['buildingsAD_multiplyer'];
        $buildingsNE_premium = ($buildingsNoExcess == "") ? 0 : ($buildingsNoExcess/100) * $this->_rates['buildingsNE_multiplyer'];

        $buildingsEX_premium = $buildingsAD_premium + $buildingsNE_premium;

        $premium =  $buildings_premium;
        $premium += $buildingsEX_premium;

        /* contents premiums ---------------------------------- */
        $contents_premium = 0;
        for($i = 0; $i <= 4; $i++) // loop through number of content risk areas
        {
            $contentsAreaPremium[$i] = (($contentsAreaSum[$i]/100) * $this->_rates['contentsNet'][$i]);
            $contents_premium += $contentsAreaPremium[$i];
        }

        $contentsAD_premium = ($contentsAccidentalDamage == "") ? 0 : ($contentsAccidentalDamage/100) * $this->_rates['contentsAD_multiplyer'];
        $contentsNE_premium = ($contentsNoExcess == "") ? 0 : ($contentsNoExcess/100) * $this->_rates['contentsNE_multiplyer'];

        $contentsEX_premium = $contentsAD_premium + $contentsNE_premium;

        $premium += $contents_premium;
        $premium += $contentsEX_premium ;

        $total_premium = $premium;

    //------------------------------------------
        // Calculate Tenancy
    //------------------------------------------
        $professionalRate = ($professionalprop / $tenantnum) * 100;
        $excess = 0;
        if(($professionalRate >= 85)){
            $total_premium *= $this->_factors['professionalRate']["85-100"];
        }
        else if(($professionalRate >= 70)){
            $total_premium *= $this->_factors['professionalRate']["70-84"];
        }
        else if(($professionalRate >= 50)){
            $total_premium *= $this->_factors['professionalRate']["50-69"];
        }
        else if(($professionalRate >= 40))
        {
            $excess = 500;
            $total_premium *= $this->_factors['professionalRate']["40-49"];
        }
        else if(($professionalRate >= 30))
        {
            $excess = 500;
            $total_premium *= $this->_factors['professionalRate']["30-39"];
        }
        else if(($professionalRate >= 20))
        {
            $excess = 750;
            $total_premium *= $this->_factors['professionalRate']["20-29"];
        }
        else if(($professionalRate >= 10))
        {
            $excess = 750;
            $total_premium *= $this->_factors['professionalRate']["10-19"];

        }
        else
        {
            $excess = 750;
            $total_premium *= $this->_factors['professionalRate']["0-9"];
        }
        $tenantPremium = $total_premium;

        //------------------------------------------
            // Calculate Claims
        //------------------------------------------
        #$previousClaims = explode("|","|||||||||||||||");
        $claimsManager = new Manager_Insurance_Portfolio_PreviousClaims();
        $claimsAmount = $claimsManager->getClaimsTotal($refNo);
        #$claimsAmount = $previousClaims[3]+$previousClaims[7]+$previousClaims[11]+$previousClaims[15];

        if($claimsAmount > 0 && $total_premium > 0)
        {
            $number_of_years = 3;
            $claimsRatio = ($claimsAmount / ($total_premium * $number_of_years)); // Covers three years
            $bReferred = true;
            $has_claims = true;
        } else{
            $claimsRatio = 0;
		}


            if($claimsRatio == 0){
                $claimsExperience = $total_premium * $this->_factors['claimRate']["0-0"];
            }
            else if($claimsRatio < 25)
                $claimsExperience = $total_premium * $this->_factors['claimRate']["1-24"];
            else if($claimsRatio < 50)
                $claimsExperience = $total_premium * $this->_factors['claimRate']["25-49"];
            else if($claimsRatio < 60)
                $claimsExperience = $total_premium * $this->_factors['claimRate']["50-59"];
            else if($claimsRatio < 70)
                $claimsExperience = $total_premium * $this->_factors['claimRate']["60-69"];
            else if($claimsRatio < 80)
                $claimsExperience = $total_premium * $this->_factors['claimRate']["70-79"];
            else if($claimsRatio < 90)
                $claimsExperience = $total_premium * $this->_factors['claimRate']["80-89"];
            else if($claimsRatio < 100)
                $claimsExperience = $total_premium * $this->_factors['claimRate']["90-99"];
            else if($claimsRatio < 120)
                $claimsExperience = $total_premium * $this->_factors['claimRate']["100-119"];
            else if($claimsRatio < 140)
                $claimsExperience = $total_premium * $this->_factors['claimRate']["120-139"];
            else if($claimsRatio < 160)
                $claimsExperience = $total_premium * $this->_factors['claimRate']["140-159"];
            else {
                $claimsExperience = $total_premium;
                $bReferred = true;
            }

        // discounts on total sums
            if($total_sum_buildings < 1000000) // less than 1 million
                $sumInsuredDiscount = $this->_factors['sumInsuredDiscRate']["0-999999"];
            else if($total_sum_buildings < 2000000) // less than 2 million

                $sumInsuredDiscount = $this->_factors['sumInsuredDiscRate']["1000000-1999999"];
            else if($total_sum_buildings < 3000000) // less than 3 million

                $sumInsuredDiscount = $this->_factors['sumInsuredDiscRate']["2000000-2999999"];
            else if($total_sum_buildings >= 3000000) // greater than 3 million

                $sumInsuredDiscount = $this->_factors['sumInsuredDiscRate']["3000000-0"];

            $netPremium = $sumInsuredDiscount * $claimsExperience;

        // Net Rates
           if($total_sum_buildings)
           {
               $netRateA = ($total_sum_buildings * $this->_factors['netRate_multiplyer'])/100;
               $netRateB = $netPremium;
               $netRateBmultiply = ($netRateB / $total_sum_buildings) * 100;
               $loadingDiscount = (($netRateBmultiply / $this->_factors['netRate_multiplyer']) * 100) - 100 ;
           }


        //	PREMIUM SUMMARY
        //////////////////////////////////////////////////////////////////////

        // Gross Premium Details
            $gross = ($total_sum_buildings * $this->_factors['gross_premium_multiplyer'])/100;
            $grossExcIPTmultiply = (($this->_factors['gross_premium_multiplyer'] * $loadingDiscount)/100) + $this->_factors['gross_premium_multiplyer'];
            $grossExcIPT = ($total_sum_buildings * $grossExcIPTmultiply)/100;
            $grossIncIPT = $grossExcIPT * 105 / 100;
            $grossIPT = $grossIncIPT - $grossExcIPT;

        // Gross Premium with 45 % commission
        // Annual calculation
            $UserQuoteAnnualPremium = ($netPremium * $this->_factors['commission']);
            $UserQuoteAnnualPremium += $limited_contents_cost;
            $UserQuoteAnnualIPT = ($UserQuoteAnnualPremium * $this->_factors['ipt_multiplyer']);

        //Monthly Calculation
            $UserQuoteMonthlyPremium = ($netPremium * $this->_factors['commission']);
            $UserQuoteMonthlyPremium += $limited_contents_cost;
            $UserQuoteMonthlyPremium = ($UserQuoteMonthlyPremium / 12);
            $UserQuoteMonthlyIPT = ($UserQuoteMonthlyPremium * $this->_factors['ipt_multiplyer']);

        // Insurer Disbursement details
            $IPTonGross = $UserQuoteAnnualIPT;
            $UWTotal = $netPremium + $IPTonGross;
            $netnetPremium = $netPremium * $this->_factors['netnetPremium_multiplyer'];
            $UWPremium = $netnetPremium + $IPTonGross;

        // USER QUOTE based on chosen payment method
        $frmPayment = "Monthly";
    	$UserQuotePremium = ($frmPayment == "Annually") ? sprintf("%01.2f", $UserQuoteAnnualPremium) : sprintf("%01.2f",$UserQuoteMonthlyPremium) ;
    	$UserQuoteIPT = ($frmPayment == "Annually") ? sprintf("%01.2f", $UserQuoteAnnualIPT) : sprintf("%01.2f",$UserQuoteMonthlyIPT);
    	$UserQuoteService = ($frmPayment == "Annually") ? 0.00 : $this->_factors['serviceCharge'] ;
    	$UserQuote = $UserQuotePremium + $UserQuoteIPT + $UserQuoteService ;

		$premiums = array();
		$premiums['UserQuoteAnnualPremium'] =  round($UserQuoteAnnualPremium,2);
		$premiums['UserQuoteMonthlyPremium'] = round($UserQuoteMonthlyPremium,2);
		$premiums['UserQuoteAnnualIPT'] = round($UserQuoteAnnualIPT,2);
		$premiums['UserQuoteMonthlyIPT'] = round($UserQuoteMonthlyIPT,2);
		$premiums['UserQuoteAnnualPremiumInc']  = $premiums['UserQuoteAnnualPremium'] + $premiums['UserQuoteAnnualIPT'];
		$premiums['UserQuoteMonthlyPremiumInc']  = $premiums['UserQuoteMonthlyPremium'] + $premiums['UserQuoteMonthlyIPT'] + $this->_factors['serviceCharge'];
		$returnArray =  array(
			'premiums' => $premiums,
			'referred' => $bReferred,
			'has_claims' => $has_claims,
			'buildingAreaSum' => $buildingAreaSum,
			'buildings_premium' => $buildings_premium,
			'buildingAreaPremium' => $buildingAreaPremium,
			'contentsAreaSum' => $contentsAreaSum,
			'contentsAreaPremium' => $contentsAreaPremium,
			'contents_premium' => $contents_premium,
			'tenantnum' => $tenantnum,
			'professionalprop' => $professionalprop,
			'professionalRate' => $professionalRate,
			'total_sum_buildings' => $total_sum_buildings,
			'total_sum_contents' => $total_sum_contents,
			'limited_contents_cost' => $limited_contents_cost,
			'buildingsAccidentalDamage' => $buildingsAccidentalDamage,
			'buildingsNoExcess' => $buildingsNoExcess,
			'contentsAccidentalDamage' => $contentsAccidentalDamage,
			'contentsNoExcess' => $contentsNoExcess,
			'gross' => $gross,
			'grossExcIPTmultiply' => $grossExcIPTmultiply,
			'grossExcIPT' => $grossExcIPT,
			'grossIncIPT' => $grossIncIPT,
			'grossIPT' => $grossIPT,
			'premium' => $premium,
			'excess' => $excess,
			'sumInsuredDiscount' => $sumInsuredDiscount,

		// Gross Premium with 45 % commission
		// Annual calculation
			'UserQuoteAnnualPremium' => $UserQuoteAnnualPremium,
			'UserQuoteAnnualIPT' => $UserQuoteAnnualIPT,

		//Monthly Calculation
			'UserQuoteMonthlyPremium' => $UserQuoteMonthlyPremium,
			'UserQuoteMonthlyIPT' => $UserQuoteMonthlyIPT,

		// Insurer Disbursement details
			'IPTonGross' => $IPTonGross,
			'UWTotal' => $UWTotal,
			'netnetPremium' => $netnetPremium,
			'UWPremium' => $UWPremium,
			'netRateA' => $netRateA,
			'netRateB' => $netRateB,
			'netRateBmultiply' => $netRateBmultiply,
			'loadingDiscount' => $loadingDiscount,
			'claimsExperience' => $claimsExperience,
			'professionalRate' => $professionalRate,
			'tenantPremium' => $tenantPremium,
			'claimsAmount' => $claimsAmount,
			'claimsRatio' => $claimsRatio,


			'buildingsAD_premium' => $buildingsAD_premium,
			'buildingsNE_premium' => $buildingsNE_premium,
			'buildingsEX_premium' => $buildingsEX_premium,

			'contentsAD_premium' => $contentsAD_premium,
			'contentsNE_premium' => $contentsNE_premium,
			'contentsEX_premium' => $contentsEX_premium,

			// USER QUOTE based on chosen payment method
			'UserQuotePremium' => $UserQuotePremium,
			'UserQuoteIPT' => $UserQuoteIPT,
			'UserQuoteService' => $UserQuoteService,
			'UserQuote' => $UserQuote,

			// This is mucky - Need to put it in a table, but it isn't stored in legacy at the moment
			'Additional' => $pageSession->step3,
			'Dpa' => $pageSession->step1,

			// Other stuff
			'agentschemeno' => $session->referrer,
			'csuid' => $session->csu,
			'origin' => $session->origin
			);

           	return $returnArray;
        }

        /**
        * TODO: Document this
        * @param
        * @return
        * @author John Burrin
        * @since 1.3
        */
        private function _fetchRates(){
            $ratesDataSource = new Datasource_Insurance_Portfolio_PortfolioRates();
            return $ratesDataSource->fetchRates(1);
        }

        /**
        * TODO: Document this
        * @param
        * @return
        * @author John Burrin
        * @since 1.3
        */
        private function _fetchFactors(){
            $ratesDataSource = new Datasource_Insurance_Portfolio_PortfolioRates();
            return $ratesDataSource->fetchFactors(date("Y-m-d"));
        }

        /**
        * This will convert all the new data into the old lagacy tables
        * @param
        * @return
        * @author John Burrin
        * @since 1.3
        *
        * Munten ergo sum
        */
        public function convertLegacy($refNo){

            // fetch quote premiums
            $premiums = $this->quote($refNo);
            // Fetch the Landlords details
            $landlord = new Manager_Insurance_Portfolio_LegacyCustomer();
            $landlordDetails = $landlord->fetchByRefNo($refNo);

            // Fetch all the properties
            $propertyManager = new Manager_Insurance_Portfolio_Property();
            $propertyObject = new Model_Insurance_Portfolio_Property();
            $propertyObject = $propertyManager->fetchAllProperties($refNo);

            // populate the legacy portfoliostat table
            $legacyPortfolioStat = new Model_Insurance_Portfolio_LegacyPortfolio();
            $legacyPortfolioStat->agentSchemeNo = 1403796;
           // $legacyPortfolioStat->csuId = "";
            $legacyPortfolioStat->customerRefNo = "";
            $legacyPortfolioStat->date = date("Y-m-d");
            $legacyPortfolioStat->email = $landlordDetails['email_address'];
            $legacyPortfolioStat->heardFrom = "";
            $legacyPortfolioStat->hpc = "";
            $legacyPortfolioStat->name = $landlordDetails['first_name'] . " " . $landlordDetails['last_name'];
            $legacyPortfolioStat->numOfHouse = count($propertyObject);
            $legacyPortfolioStat->policyNumber = "";
            $legacyPortfolioStat->quote = $premiums['premiums']['UserQuoteAnnualPremium'];
            $legacyPortfolioStat->referred = "";
            $legacyPortfolioStat->refNo = $refNo;
            $legacyPortfolioStat->telephone = $landlordDetails['telephone1'];
            // Set up datasources to the risk areas
            $dsBuildingsRiskArea = new Datasource_Insurance_RiskAreas_Buildings();
            $dsContentsRiskArea = new Datasource_Insurance_RiskAreas_LandlordsContents();

            //Save the portfoliostat data
            $legacyPortfolioManager = new Manager_Insurance_Portfolio_LegacyPortfolio();
            $legacyPortfolioManager->save($legacyPortfolioStat);
            
            $idd = new Datasource_Insurance_IddSupport();
            if(!$idd->isIddSupport($legacyPortfolioStat->refNo)){
                $inserArray = array();
                $insertArray['policynumber']=$legacyPortfolioStat->refNo;
                $insertArray['agentschemeno']=$legacyPortfolioStat->agentSchemeNo;
                $insertArray['csuid']=0;
                $fsaAgentStatusDatasource = new Datasource_Fsa_AgentStatus();
	        $fsaStatus = $fsaAgentStatusDatasource->getAgentFsaStatus($legacyPortfolioStat->agentSchemeNo);
                if(isset($fsaStatus['status_abbr'])){
                    $insertArray['FSA_status']=$fsaStatus['status_abbr'];
                }    
                else{
                    $insertArray['FSA_status']="";
                }
                $insertArray['origsaleid']=9;
                $insertArray['callerid']=2;
                $idd->setIddSupport($insertArray);

            }
            // Setup an agent manager
            $agent = new Manager_Core_Agent();

            // get polict options
            $ds_rates = new Datasource_Insurance_Portfolio_PortfolioRates();
            $rates = $ds_rates->fetchRates();
            $optionsString = $this->_getPolicyOptions();

            // Save the property data into the legacy portfolio table
            $legacyPropertyManager = new Manager_Insurance_Portfolio_LegacyProperty();
            foreach ($propertyObject as $property){
                // Create a new Model_Insurance_Portfolio_LegacyProperty Object
                $legacyPropertyObject = new Model_Insurance_Portfolio_LegacyProperty();
                $legacyPropertyObject->amountsCovered = $this->_getAmountsCovered($property);
                $legacyPropertyObject->discount = "";
                $legacyPropertyObject->excessId = "";
                $legacyPropertyObject->ipt = "";
                $legacyPropertyObject->optionDiscounts = "";
                $legacyPropertyObject->optionPremiums = "";
                $legacyPropertyObject->policyNumber = $refNo;
                $legacyPropertyObject->policyOptions = $optionsString;
                $legacyPropertyObject->premium = "";
                $legacyPropertyObject->propAddress1 = $property->address1;
                $legacyPropertyObject->propAddress3 = $property->address2;
                $legacyPropertyObject->propAddress5 = $property->address3;
                $legacyPropertyObject->propPostcode = $property->postcode;
                $legacyPropertyObject->quote = 0.00;
                $legacyPropertyObject->rateSetId = $agent->getRatesetIDByASN($refNo);
                $legacyPropertyObject->riskArea = $dsContentsRiskArea->getCurrentRate($property['postcode']);
                $legacyPropertyObject->riskAreaB = $dsBuildingsRiskArea->getCurrentRate($property['postcode']);
                $legacyPropertyObject->surcharge = "";
                $legacyPropertyManager->save($legacyPropertyObject);
            }
        }

        /**
        * TODO: Document this
        * @param
        * @return
        * @author John Burrin
        * @since
        */
        private function _getAmountsCovered($property){
            $amount = array();
            //buildings|buildingsAccidentalDamage|buildingsNilExcess|contents|contentsAccidentalDamage|contentsNilExcess|limitedcontents
            if($property['buildingsSumInsured'] != 0)   $amount[] = $property['buildingsSumInsured']; else $amount[] = 0;
            if($property['buildingsAccidentalDamage'] == "Yes")   $amount[] = $property['buildingsSumInsured']; else $amount[] = 0;
            if($property['buildingsNilExcess'] == "Yes")   $amount[] = $property['buildingsSumInsured']; else $amount[] = 0;
            if($property['contentsSumInsured'] != 0)   $amount[] = $property['contentsSumInsured']; else $amount[] = 0;
            if($property['contentsAccidentalDamage'] == "Yes")   $amount[] = $property['contentsSumInsured']; else $amount[] = 0;
            if($property['contentsNilExcess'] == "Yes")   $amount[] = $property['contentsSumInsured']; else $amount[] = 0;
            if($property['limitedContents'] == "Yes")   $amount[] = "5000"; else $amount[] = 0;
            $returnString = implode("|",$amount);
            return $returnString;
        }

        /**
        * TODO: Document this
        * @param
        * @return
        * @author John Burrin
        * @since
        */
        private function _getPolicyOptions(){
            // Don't fecking ask!!!!!
            /*
            $policyOptions = new Datasource_Insurance_Portfolio_PortfolioRates();
            $options = $policyOptions->fetchOptionsByProduct('portfolio');
            $optionsArray = array();
            foreach ($options as $option){
                if(!in_array($option['policyOption'],$optionsArray)){
                    $optionsArray[] = $option['policyOption'];
                }
            }
            */
            return "buildings|buildingsAccidentalDamage|buildingsNilExcess|contents|contentsAccidentalDamage|contentsNilExcess|limitedcontents";
        }

        private function emailQuote(){

        }
    }
?>
