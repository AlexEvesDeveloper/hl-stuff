<?php
/**
 * Manager for Landlords Plus Quotes
 *
 */
class Manager_Insurance_LandlordsPlus_Quote extends Manager_Insurance_Quote {
	// Define some constants for the possible products you can have in a Landlords Plus Quote/Policy
	const BUILDING_COVER = 5;
	const CONTENTS_COVER = 6;
	const UNFURNISHED_CONTENTS_COVER = 7;
	const EMERGENCY_ASSISTANCE = 8;
	const RENT_GUARANTEE = 9;
	const LEGAL_EXPENSES = 10;
	const BOILER_HEATING = 11; // It's a little controversial as to whether this really is a product but
							   // this is the quickest way I could get it working.. :(

    /**
     * Performs a copy of the quote stored within the MYSQL4 db to MYSQL5.
     */
    protected function legacyQuoteMigration($policyNumber, $customerId)
    {
        // Assume a record has not been configured within the MySQL 5 database.
        // Need to copy data over from the MySQL 4 server to patch the web
        // quote and buy process
        $legacyQuotesDs = new Datasource_Insurance_LegacyQuotes();
        $legacyQuote = $legacyQuotesDs->getByPolicyNumber($policyNumber);

        // copy data into a Model_Insurance_Quote object
        $this->_quoteModel = new Model_Insurance_Quote();
        $this->_quoteModel->legacyID = $policyNumber;
        $this->_quoteModel->legacyCustomerID = $legacyQuote->refNo;
        $this->_quoteModel->customerID = $customerId;
        $this->_quoteModel->agentSchemeNumber = $legacyQuote->agentSchemeNumber;
        $this->_quoteModel->issuedDate = $legacyQuote->issueDate;
        $this->_quoteModel->startDate = $legacyQuote->startDate;
        $this->_quoteModel->endDate = $legacyQuote->endDate;
        $this->_quoteModel->payFrequency = $legacyQuote->payBy;
        $this->_quoteModel->policyNumber = $legacyQuote->policyNumber;
        $this->_quoteModel->premium = $legacyQuote->premium;
        $this->_quoteModel->ipt = $legacyQuote->ipt;
        $this->_quoteModel->policyLength = $legacyQuote->policyLength;

        // store the object to db
        $this->_quoteDataSource->save($this->_quoteModel);

        // properties
        $tenantTypeID = null;
        $agentManaged = null;
        $ownershipLengthID = null;
        $noClaimsYearsID = null;
        $excludeFloodCover = null;

//        // TODO: Quote products
//        foreach (explode('|', $legacyQuote->policyOptions) as $option) {
//            if ($option == 'buildingsp') {
//                // Buildings
//            }
//            else if ($option == 'contentslp') {
//                // Contents
//            }
//            else if ($option == 'limitedcontentsp') {
//                // Limited contents
//            }
//            else if ($option == 'legalexpensesp') {
//                // Legal expenses
//            }
//            else if ($option == 'rentguaranteep') {
//                // Rent guarantee
//            }
//            else if ($option == 'emergencyassistancestandalone' || $option == 'emergencyassistancebahstandalone') {
//                // Emergency assistance
//            }
//
//            contentslAccidentalDamagep
//            buildingsAccidentalDamagep
//        }


        $legacyPropertiesDs = new Manager_Insurance_LegacyQuote();
        $legacyProperties = $legacyPropertiesDs->getProperties($policyNumber);

        foreach ($legacyProperties as $propertyId => $property) {
            // Foreach property, check its content and copy into the web quote process
            if ($propertyId == 3) {
                // No claims period
                switch ($property['propertyValue']) {
                    case 0: $noClaimsYearsID = 1; break;        // less than 1 year
                    case 1: $noClaimsYearsID = 2; break;        // 1 year
                    case 2: $noClaimsYearsID = 3; break;        // 2 years
                    case 3: $noClaimsYearsID = 4; break;        // 3 years
                    case 4: $noClaimsYearsID = 5; break;        // more than 3 years
                }
            }
            else if ($propertyId == 4) {
                // Managed property
                switch ($property['propertyValue']) {
                    case 0: $agentManaged = 0; break;           // No
                    case 1: $agentManaged = 1; break;           // Yes
                }
            }
            else if ($propertyId == 5) {
//                // TODO: Excess
//                switch ($property['propertyValue']) {
//                    case 0: break;                              // 0
//                    case 1: break;                              // 100
//                    case 2: break;                              // 250
//                    case 3: break;                              // 500
//                    case 4: break;                              // 1000
//                }
            }
            else if ($propertyId == 6) {
                // Tenant type
                switch ($property['propertyValue']) {
                    case 0: $tenantTypeID = 1; break;           // Employed
                    case 1: $tenantTypeID = 2; break;           // Self Employed
                    case 2: $tenantTypeID = 3; break;           // Student
                    case 3: $tenantTypeID = 4; break;           // Retired
                    case 4: $tenantTypeID = 5; break;           // Unemployed
                    case 5: $tenantTypeID = 7; break;           // Housing authority
                    case 6: $tenantTypeID = 8; break;           // Unknown
                    case 7: $tenantTypeID = 6; break;           // Claiming benefits
                }
            }
            else if ($propertyId == 9) {
                // Buildings flood risk
                switch ($property['propertyValue']) {
                    case 0: $excludeFloodCover = 1; break;      // No
                    case 1: $excludeFloodCover = 0; break;      // Yes
                }
            }
            else if ($propertyId == 11) {
//                // TODO: Buildings cover over 500,000
//                switch ($property['propertyValue']) {
//                    case 0: break;                              // No
//                    case 1: break;                              // Yes
//                }
            }
            else if ($propertyId == 12) {
                // Ownership length
                switch ($property['propertyValue']) {
                    case 0: $ownershipLengthID = 1; break;      // 0 years
                    case 1: $ownershipLengthID = 2; break;      // 1 year
                    case 2: $ownershipLengthID = 3; break;      // 2 years
                    case 3: $ownershipLengthID = 4; break;      // 3 years
                    case 4: $ownershipLengthID = 5; break;      // over 3 years
                }
            }
        }

        // Populate properties
        $propertiesDs = new Datasource_Insurance_Quote_Properties();
        $propertiesDs->add($this->_quoteModel->ID, $legacyQuote->propertyPostcode,
            '', '', $legacyQuote->propertyAddress1,
            $tenantTypeID, $agentManaged, $ownershipLengthID, $noClaimsYearsID, $excludeFloodCover,
            $legacyQuote->propertyAddress2, $legacyQuote->propertyAddress3, '');

        // TODO: repeat for products, product_metas and properties
    }

	/**
	 * Calculates the DSI (Derived Sum Insured) figure for this quotes property
	 *
	 * @return double The DSI amount
	 */
	protected function _calculateDSI() {
		// Firstly we need to get the DSI area using the postcode
		$DSIAreasDatasource = new Datasource_Insurance_DSI_Areas();
		$properties = $this->getProperties();
		$postcode = $properties[0]['postcode'];
		$areaID = $DSIAreasDatasource->getAreaIDByPostcode($postcode);

		// Now we need to convert the meta data stored against the buildings cover into ID's we can use in the lookup matrix
		$buildingsMeta = $this->getProductMeta(self::BUILDING_COVER);
		
		if (count($buildingsMeta)>0) {
			$DSIDatasource = new Datasource_Insurance_Dsi();
			$DSIValues = $DSIDatasource->getValue(
				$areaID,
				$buildingsMeta['build_year'],
				$buildingsMeta['building_type'],
				$buildingsMeta['bedroom_quantity']);
			return $DSIValues;
		}
		return null;
	}

	/**
	 * Calculate loadings for this quote and return them
	 *
	 * @return array Array of loadings
	 */
	protected function _calculateLoadings()	{
		// Calculate loadings for buildings cover
		$buildingCoverMeta = $this->getProductMeta(self::BUILDING_COVER);
		$contentsCoverMeta = $this->getProductMeta(self::CONTENTS_COVER);

		$propertiesDatasource = new Datasource_Insurance_Quote_Properties();
		$properties = $propertiesDatasource->getByQuoteID($this->_quoteModel->ID);

		$tenantLoading = 1;
		switch ($properties[0]['tenant_type_id']) {
			case 1: // Employed
				$tenantLoading = 0.95;
				break;
			case 2: // Self-employed
				$tenantLoading = 0.95;
				break;
			case 3: // Student
				$tenantLoading = 1.10;
				break;
			case 4: // Retired
				$tenantLoading = 0.90;
				break;
			case 5: // Unemployed
				$tenantLoading = 1.10;
				break;
			case 6: // Claiming benefits
				$tenantLoading = 1.10;
				break;
			case 7: // Housing authority
				// Todo: this should defer!!
				break;
			case 8:
				$tenantLoading = 1.10;
				break;
		}

		$buildingExcessLoading = 1;
		if (isset($buildingCoverMeta['excess'])) {
			switch ($buildingCoverMeta['excess']) {
				case 0:
					$buildingExcessLoading = 1.11;
					break;
				case 100:
					$buildingExcessLoading = 1.065;
					break;
				case 250:
					$buildingExcessLoading = 1;
					break;
				case 500:
					$buildingExcessLoading = 0.95;
					break;
				case 1000:
					$buildingExcessLoading = 0.925;
					break;
			}
		}

		$contentsExcessLoading = 1;
		if (isset($contentsCoverMeta['excess'])) {
			switch ($contentsCoverMeta['excess']) {
				case 0:
					$contentsExcessLoading = 1.11;
					break;
				case 100:
					$contentsExcessLoading = 1.065;
					break;
				case 250:
					$contentsExcessLoading = 1;
					break;
				case 500:
					$contentsExcessLoading = 0.95;
					break;
				case 1000:
					$contentsExcessLoading = 0.925;
					break;
			}
		}

		$agentManagedLoading = 1;
/* No 5% discount for managed property, as per OBC 1132 and Redmine #8952, #8532.
    But now back in again as per Redmine #10582! */
		switch ($properties[0]['letting_agent_managed']) {
			case 0: // Not managed
				$agentManagedLoading = 1;
				break;
			case 1: // Managed
				$agentManagedLoading = 0.95;
				break;
		}

		$NCBLoading = 1;
		switch ($properties[0]['no_claims_years_id']) {
			case 1: // 0 years
				$NCBLoading = 1;
				break;
			case 2: // 1 year
				$NCBLoading = 0.975;
				break;
			case 3: // 2 years
				$NCBLoading = 0.95;
				break;
			case 4: // 3 years
				$NCBLoading = 0.925;
				break;
			case 5: // More than 3 years
				$NCBLoading = 0.925;
				break;
		}

		return array(
			'loadings'			=>	array(
				'tenant'			=> 	$tenantLoading,
				'buildingExcess'	=>  $buildingExcessLoading,
				'contentsExcess'	=>	$contentsExcessLoading,
				'agentManaged'		=>	$agentManagedLoading,
				'noClaims'			=>	$NCBLoading),
			'contentsMultiplier'	=>	$tenantLoading * $contentsExcessLoading * $agentManagedLoading * $NCBLoading,
			'buildingMultiplier'	=>	$tenantLoading * $buildingExcessLoading * $agentManagedLoading * $NCBLoading
		);

	}

	/**
	 *
	 */
	public function getEndorsements() {
		$endorsementManager = new Manager_Insurance_LandlordsPlus_Endorsement();
		$endorsements = $endorsementManager->getEndorsementsRequired($this->_quoteModel->ID);

		$endorsementsArray['floodArea'] = false;
		$endorsementsArray['floodOptional'] = false;

		if (count($endorsements)>0) {
			foreach ($endorsements as $endorsement) {
				if ($endorsement->getEndorsementType()->getName() == Model_Insurance_EndorsementTypes::FLOOD_EXCLUSION)
				{
					$endorsementsArray['floodArea'] = true;

					// Flood exclusion endorsement - need to find out if it's optional or not
					$properties = $this->getProperties();
					$floodOptional = $endorsementManager->getIsFloodEndorsementOptional($properties[0]['postcode']);
					$endorsementsArray['floodOptional'] = $floodOptional;
				}
			}
		}

		$endorsementsArray['endorsements'] = $endorsements;

		return $endorsementsArray;
	}

	/**
	 * Get an associative array of admin/service fees for this quote
	 */
	public function getFees() { 
		$feesDatasource = new Datasource_Insurance_Fees();
		$agentsDatasource = new Datasource_Core_Agents();
		$agent = $agentsDatasource->getAgent($this->getAgentSchemeNumber());
		
		$fees = $feesDatasource->getByAgentRateSetID($agent->agentsDealGroupID);
		return $fees;
	}
	
    public function setContactPreference($customerpref)
    {
        $policypreferences = new Datasource_Core_CustomerContactPreferences();
        $policypreferences->clearPreferences($this->_quoteModel->policyNumber);
        $policypreferences->insertPreferences($this->_quoteModel->policyNumber, $customerpref);
    }

	/**
	 * Calculate just the DSI value for the property
	 */
	public function calculateDSI() {
		$properties = $this->getProperties();
		$postcode = $properties[0]['postcode'];
		$dsiValues = $this->_calculateDSI($postcode);
		$dsi = $dsiValues['rebuildValue'];

		return $dsi;
	}

	/**
	 * Calculate premiums for this quote and return them
	 *
	 * @return array Array of premiums
	 */
	public function calculatePremiums($verbose = false) {
		/***************************************************************
		 * WARNING
		 * -------
		 * If you change any pricing in this function - also change
		 * the calculateQuickPremiums function below
		 *
		 * This needs a massive cleanup at some point but
		 * this function is now so messy it was taking 2 seconds to run
		 * on every ajax call - so I had to quickly produce a cut down
		 * version
		 ***************************************************************/
		 
		if ($verbose) echo "Attempting to calculate premiums for Quote ID #" . $this->_quoteModel->ID . " ...<br /><br />";
		$dsi = 0;
		$annualContentsCover = 0;
		$annualNetContentsCover = 0;
		$annualBuildingCover = 0;
		$annualNetBuildingCover = 0;
		$contentsRates = array();
		$buildingRates = array();
		$annualEmergencyAssistance = 0;
		$annualNetEmergencyAssistance = 0;
		$annualRentGuarantee = 0; //36
		$netAnnualRentGuarantee = 0;
		$annualLegalExpensesCover = 0; //35
		$netAnnualLegalExpensesCover = 0;
		$annualBuilding=0; //33
		$netBuilding=0;
		$annualContent=0; //34
		$netContent=0;
		$annualLContent=0; //39
		$netLContent=0;
		$annualBuildingAD =0; //37
		$netBuildingAD =0;
		$annualContentsAD =0; //38
		$netContentsAD =0;
		$annualBFlood=0; //31
		$netBFlood=0;
		$annualCFlood=0; //32
		$netCFlood=0;
	    $annualEA=0; //4
	    $netEA=0;
	    $annualEAB=0; //28
	    $netEAB=0;
	    $annualEABS=0; //29
	    $netEABS=0;
	    $annualEAS=0; //30
	    $netEAS=0;
	    $grossRate=0;
	    $netRate=0;
		
		$dsiValues = array();
		
		// Check to see if this quote has any products - if not we don't need to do anything :)
		if ($this->productCount() > 0) {
			if ($verbose) echo "Quote currently has " . $this->productCount() . " products<br />";
			// First we need the postcode for our property
			$propertiesDatasource = new Datasource_Insurance_Quote_Properties();
			$properties = $propertiesDatasource->getByQuoteID($this->_quoteModel->ID);
			$postcode = $properties[0]['postcode'];
			
			if ($verbose) {
				echo "<br />Properties:<br />";
				Zend_Debug::dump($properties);
			}
			
			// Check to see if we have an agents Rate ID
			$agentsRateID = 0;
			
			$agentsDatasource = new Datasource_Core_Agents();
			$agent = $agentsDatasource->getAgent($this->getAgentSchemeNumber());

			// If this agent has an active valid rate ID we can use it
			if (isset($agent->agentsRateID)) {
				if ($agent->agentsRateID==5 || $agent->agentsRateID==11) {
					$agentsRateID = $agent->agentsRateID;
				}
			}

			if ($verbose) {
				echo "<br />Agent:<br />";
				Zend_Debug::dump($agent);
			}

			// Get the risk areas for LI+
			// Then we need to get the LI+ risk areas for this postcode
			$riskAreasDatasource = new Datasource_Insurance_LandlordsPlus_RiskAreas();
			$riskAreas = $riskAreasDatasource->getByPostcode($postcode);

			if ($verbose) {
				echo "<br />Risk Areas:<br />";
				Zend_Debug::dump($riskAreas);
			}

			// Calculate the discounts/loadings to be applied based on the quote meta data
			$loadings = $this->_calculateLoadings();

			if ($verbose) {
				echo "<br />Loadings:<br />";
				Zend_Debug::dump($loadings);
			}

			// ** BUILDINGS COVER PREMIUMS ** //
			if ($this->hasProduct(self::BUILDING_COVER)) {
				$productMeta = $this->getProductMeta(self::BUILDING_COVER);

				if ($verbose) {
					echo "<br />Quote has Building Cover .. Calculating Premiums<br />";
					echo "Building Cover Meta Data:<br />";
					Zend_Debug::dump($productMeta);

				}

				$dsiValues = $this->_calculateDSI($postcode);
				
				$dsi = $dsiValues['rebuildValue'];

				// If we don't have a DSI - use the manually entered rebuild value
				if (isset($productMeta['rebuild_value']) && $productMeta['rebuild_value'] > 0) $dsi = $productMeta['rebuild_value'];

				if ($dsi>0) {
					if ($verbose) echo "Using DSI Value of " . $dsi. "<br />";

					// and we need to get the rates for Buildings insurance
					$ratesDatasource = new Datasource_Insurance_LandlordsPlus_BuildingCover_Rates();
					$buildingRates = $ratesDatasource->getByProperty($riskAreas['buildingsAreaID'], $productMeta['build_year'],$this->getStartDate());

					// now we can calculate the premiums based on the DSI Value and the rates
					$grossRate = $buildingRates['gross'];
					$netRate = $buildingRates['net'];

					if ($verbose) {
						echo "Building Cover Rates: <br />";
						Zend_Debug::dump($buildingRates);
					}

					if (isset($productMeta['accidental_damage']) && $productMeta['accidental_damage']=='yes') {
						if ($verbose) echo "Adding Accidental Damage Cover to Rate<br />";
						$annualBuildingAD = $buildingRates['grossAccidentalDamage']*$loadings['buildingMultiplier']* ($dsi / 1000);
						$netBuildingAD = $buildingRates['netAccidentalDamage']*$loadings['buildingMultiplier']* ($dsi / 1000);
						$grossRate = $grossRate + $buildingRates['grossAccidentalDamage'];
						$netRate = $netRate + $buildingRates['netAccidentalDamage'];
					}

					if ($verbose) echo "<br />Applying Loadings to Rate = " . $grossRate . " x " . $loadings['buildingMultiplier'] . "<br />";

					$grossRate = $grossRate * $loadings['buildingMultiplier'];
					$netRate = $netRate * $loadings['buildingMultiplier'];

					// Check to see if the property needs flood loadings applying
					$endorsements = $this->getEndorsements();

					if ($verbose) {
						echo "<br />Endorsments:<br />";
						Zend_Debug::dump($endorsements);
					}

					// See if the property is in a flood area with optional endorsement
					// If the endorsement is mandatory we don't charge extra - we just exclude the cover
					if ($endorsements['floodArea'] == true && $endorsements['floodOptional'] == true) {
						// Check to see if the customer has opted out of the flood cover
						$properties = $this->getProperties();
						
						if ($properties[0]['exclude_flood_cover']==1) {
							if ($verbose) echo "Excluding Flood Loadings at Customer's request<br />";
						} else {
						if ($verbose) echo "Adding Flood Loading to Rate<br />";

						$annualBFlood = $buildingRates['grossFlood']* ($dsi / 1000);
						$netBFlood = $buildingRates['netFlood']* ($dsi / 1000);
						$grossRate = $grossRate + $buildingRates['grossFlood'];
						$netRate = $netRate + $buildingRates['netFlood'];
					}
					} elseif($endorsements['floodArea'] == true && $endorsements['floodOptional'] == false) {
						if ($verbose) echo "Excluding Flood Loadings due to high risk<br />";
					}

					if ($verbose) echo "Final Gross Rate = &pound;" . $grossRate . "<br />";

					$buildingRates['grossAfterLoadings'] = $grossRate;
					$buildingRates['netAfterLoadings'] = $netRate;

					if ($verbose) echo "<br />Multiplying Gross Rate by DSI/1000<br />";

					$annualBuildingCover = $grossRate * ($dsi / 1000);
					$annualNetBuildingCover = $netRate * ($dsi / 1000);
					$annualBuilding = $annualBuildingCover-$annualBFlood-$annualBuildingAD;
					$netBuilding = $annualNetBuildingCover-$netBFlood-$netBuildingAD;
					if ($verbose) echo "Final Building Cover Gross Premium = &pound;" . $annualBuildingCover . "<br />";
				} else {
					// We don't have a DSI and no rebuild value has been entered... oops!!
					// Todo: panic!!
					return '';
				}
			}


			// ** UNFURNISHED CONTENTS COVER ** //
			if ($this->hasProduct(self::UNFURNISHED_CONTENTS_COVER)) {
				if ($agentsRateID<>0) {
					$annualContentsCover = 60;
					$annualLContent =60;
				} else {
					$annualContentsCover = 72;
					$annualLContent = 72;
				}
				$annualNetContentsCover = 30;
				$netLContent =30;
			}


			// ** FURNISHED CONTENTS COVER ** //
			if ($this->hasProduct(self::CONTENTS_COVER)) {
				$productMeta = $this->getProductMeta(self::CONTENTS_COVER);

				$ratesDatasource = new Datasource_Insurance_LandlordsPlus_ContentsCover_Rates();
				$contentsRates = $ratesDatasource->getByProperty($riskAreas['contentsAreaID'],$this->getStartDate());

				if (isset($productMeta['accidental_damage']) && $productMeta['accidental_damage']=='yes') {
					$annualContentsAD = $contentsRates['grossAccidentalDamage']* $loadings['contentsMultiplier'] * ($productMeta['cover_amount'] / 1000);
					$netContentsAD = $contentsRates['netAccidentalDamage']* $loadings['contentsMultiplier'] * ($productMeta['cover_amount'] / 1000);
					$annualContentsCover = (($contentsRates['gross'] + $contentsRates['grossAccidentalDamage']) * $loadings['contentsMultiplier']) * ($productMeta['cover_amount'] / 1000);
					$annualNetContentsCover = (($contentsRates['net'] + $contentsRates['netAccidentalDamage']) * $loadings['contentsMultiplier']) * ($productMeta['cover_amount'] / 1000);
				} else {
					$annualContentsCover = ($contentsRates['gross'] * $loadings['contentsMultiplier']) * ($productMeta['cover_amount'] / 1000);
					$annualNetContentsCover = ($contentsRates['net'] * $loadings['contentsMultiplier']) * ($productMeta['cover_amount'] / 1000);
				}
				$annualContent=$annualContentsCover-$annualContentsAD;
				$netContent=$annualNetContentsCover-$netContentsAD;

				// Check to see if the property needs flood loadings applying
				$endorsements = $this->getEndorsements();

				// See if the property is in a flood area with optional endorsement
				// If the endorsement is mandatory we don't charge extra - we just exclude the cover
				if ($endorsements['floodArea'] == true && $endorsements['floodOptional'] == true) {
					if ($verbose) echo "Adding Flood Loading to Rate<br />";

					// Todo: Check to see if the customer has opted out of the flood cover
					$annualCFlood = $contentsRates['grossFlood']* ($productMeta['cover_amount'] / 1000);
					$netCFlood = $contentsRates['netFlood']* ($productMeta['cover_amount'] / 1000);
					$grossRate = $grossRate + $contentsRates['grossFlood'];
					$netRate = $netRate + $contentsRates['netFlood'];
					$annualContentsCover +=$annualCFlood;
					$annualNetContentsCover +=$netCFlood;
				}

				if ($verbose) echo "Final Gross Rate = &pound;" . $grossRate . "<br />";
			}


			// ** EMERGENCY ASSISTANCE & BOILER AND HEATING COVER** //

			if ($this->hasProduct(self::EMERGENCY_ASSISTANCE) && (!$this->hasProduct(self::BUILDING_COVER) && !$this->hasProduct(self::CONTENTS_COVER))) {
				if ($verbose) {
					echo "<br />Quote has Stand-alone Emergency Assistance .. Calculating Premiums<br />";
				}
				// Stand-alone emergency assistance
				if ($agentsRateID<>0) {
					$annualEmergencyAssistance = 90;
				} else {
					$annualEmergencyAssistance = 120;
				}
				
				$annualEAS=$annualEmergencyAssistance;
				$netEAS = 36.50;
				$annualNetEmergencyAssistance = 36.50;
				
				$annualEABS = 0;
				$netEABS = 6.50;
			    $annualEmergencyAssistance += 0;
				$annualNetEmergencyAssistance += 6.50;
												
			} elseif ($this->hasProduct(self::BUILDING_COVER) || $this->hasProduct(self::CONTENTS_COVER)) {
				if ($verbose) {
					echo "<br />Quote has Free Included Emergency Assistance .. Calculating Premiums<br />";
				}
				// Free emergency assistance included with buildings cover
				if ($this->hasProduct(self::BOILER_HEATING)) {
					$annualEAB = 60;
					$netEAB = 6.50;
					$annualEmergencyAssistance = 60;
					$annualNetEmergencyAssistance = 6.50;
				}
				if($this->hasProduct(self::BUILDING_COVER)){
					$annualBuildingCover += 6.50; 
					$annualBuilding += 6.50;
				}else{
					$annualContentsCover += 6.50;
					$annualContent += 6.50;
				}
					$annualEA = 0;
					$netEA = 6.50;
					$annualEmergencyAssistance += 0;
					$annualNetEmergencyAssistance += 6.50;
									
			} 

			// ** RENT GUARANTEE ** //
			if ($this->hasProduct(self::RENT_GUARANTEE)) {
				if ($verbose) {
					echo "<br />Quote has Rent-Guarantee .. Calculating Premiums<br />";
				}
				$productMeta = $this->getProductMeta(self::RENT_GUARANTEE);

				$annualRentGuarantee = $productMeta['monthly_rent'] * 0.36;
				$netAnnualRentGuarantee = $productMeta['monthly_rent'] * 0.12;
			}

			// ** LEGAL EXPENSES ** //
			if ($this->hasProduct(self::LEGAL_EXPENSES)) {
				if ($verbose) {
					echo "<br />Quote has Legal Expenses .. Calculating Premiums<br />";
				}
				if ($this->hasProduct(self::RENT_GUARANTEE)) {
					// Free Legal Expenses cover as part of Rent Guarantee
					$annualLegalExpensesCover = 0;
					$netAnnualLegalExpensesCover = 25;
				} else {
					// Stand alone Legal Expenses cover
					$annualLegalExpensesCover = 114;
					$netAnnualLegalExpensesCover = 25;
				}
			}

			if ($this->hasProduct(self::BUILDING_COVER)) {
				$dsiValues = $this->_calculateDSI($postcode);
				$calculatedDSI = $dsiValues['rebuildValue'];
			} else {
				$calculatedDSI = 0;
			}

			$premiums = array(
				'calculatedDSIValue'					=>	(double)$calculatedDSI,
				'rebuildValue'							=>  $dsi>0?(double)$dsi:0,
				'dsiData'								=>  $dsiValues,
				'riskAreas'								=> 	$riskAreas,
				'buildingsRates'						=>  $buildingRates,
				'contentsRates'							=>  $contentsRates,
				'loadings'								=>  $loadings,
				'grossAnnualBuildingCover'				=>	(double)number_format($annualBuildingCover,2,'.',''),
				'netAnnualBuildingCover'				=> 	(double)number_format($annualNetBuildingCover,2,'.',''),
				'grossAnnualContentsCover'				=>	(double)number_format($annualContentsCover,2,'.',''),
				'netAnnualContentsCover'				=>	(double)number_format($annualNetContentsCover,2,'.',''),
				'grossAnnualEmergencyAssistance'		=>	(double)number_format($annualEmergencyAssistance,2,'.',''),
				'netAnnualEmergencyAssistance'			=>	(double)number_format($annualNetEmergencyAssistance,2,'.',''),
				'grossAnnualRentGuarantee'				=>	(double)number_format($annualRentGuarantee,2,'.',''),
				'netAnnualRentGuarantee'				=>	(double)number_format($netAnnualRentGuarantee,2,'.',''),
				'grossAnnualLegalExpensesCover'			=>  (double)number_format($annualLegalExpensesCover,2,'.',''),
				'netAnnualLegalExpensesCover'			=> 	(double)number_format($netAnnualLegalExpensesCover,2,'.',''),
			    'annualBuilding' 						=>  (double)number_format($annualBuilding,2,'.',''),
				'netBuilding' 							=>  (double)number_format($netBuilding,2,'.',''),
				'annualContent' 						=>  (double)number_format($annualContent,2,'.',''),
				'netContent' 							=>  (double)number_format($netContent,2,'.',''),
				'annualLContent' 						=>  (double)number_format($annualLContent,2,'.',''),
				'netLContent' 							=>  (double)number_format($netLContent,2,'.',''),
				'annualBuildingAD' 						=>  (double)number_format($annualBuildingAD ,2,'.',''),
				'netBuildingAD' 						=>  (double)number_format($netBuildingAD ,2,'.',''),
				'annualContentsAD' 						=>  (double)number_format($annualContentsAD ,2,'.',''),
				'netContentsAD' 						=>  (double)number_format($netContentsAD ,2,'.',''),
				'annualBFlood' 							=>  (double)number_format($annualBFlood,2,'.',''),
				'netBFlood' 							=>  (double)number_format($netBFlood,2,'.',''),
				'annualCFlood' 							=>  (double)number_format($annualCFlood,2,'.',''),
				'netCFlood' 							=>  (double)number_format($netCFlood,2,'.',''),
				'annualEA' 								=>  (double)number_format($annualEA,2,'.',''),
				'netEA' 								=>  (double)number_format($netEA,2,'.',''),
				'annualEAB' 							=>  (double)number_format($annualEAB,2,'.',''),
				'netEAB' 								=>  (double)number_format($netEAB,2,'.',''),
				'annualEABS' 							=>  (double)number_format($annualEABS,2,'.',''),
				'netEABS' 								=>  (double)number_format($netEABS,2,'.',''),
				'annualEAS'								=>  (double)number_format($annualEAS,2,'.',''),
				'netEAS'								=>  (double)number_format($netEAS,2,'.','')
			);

			$premiums ['totalGrossAnnualPremium'] = round(
				$annualBuildingCover +
				$annualContentsCover +
				$annualEmergencyAssistance +
				$annualRentGuarantee +
				$annualLegalExpensesCover, 2);
			$premiums ['totalGrossMonthlyPremium'] = round($premiums['totalGrossAnnualPremium'] / 12, 2);

			// Get IPT percentage
			$propertiesDatasource = new Datasource_Insurance_Quote_Properties();
			$properties = $propertiesDatasource->getByQuoteID($this->_quoteModel->ID);
			$postcode = $properties[0]['postcode'];
			
			$taxDatasource = new Datasource_Core_Tax();
			$tax = $taxDatasource->getTaxbyTypeAndPostcode('ipt', $postcode);
			$ipt = 1 + ($tax['rate'] / 100);

			$premiums ['totalGrossAnnualIPT'] = round(($premiums['totalGrossAnnualPremium'] * $ipt) - $premiums['totalGrossAnnualPremium'], 2);
			$premiums ['totalGrossMonthlyIPT'] = round(($premiums ['totalGrossMonthlyPremium']*$ipt) - $premiums ['totalGrossMonthlyPremium'],2);
			
			return $premiums;
		} else {
			return '';
		}
	}
	
	/**
	 * Calculate quicker for this quote and return them - used by the ajax on the fly
	 *
	 * @return array Array of premiums
	 */
	public function calculateQuickPremiums() {
		$dsi = 0;
		$annualContentsCover = 0;
		$annualBuildingCover = 0;
		$contentsRates = array();
		$buildingRates = array();
		$annualEmergencyAssistance = 0;
		$annualRentGuarantee = 0;
		$annualLegalExpensesCover = 0;
		
		$dsiValues = array();
		
		// Check to see if this quote has any products - if not we don't need to do anything :)
		if ($this->productCount() > 0) {
			// First we need the postcode for our property
			$propertiesDatasource = new Datasource_Insurance_Quote_Properties();
			$properties = $propertiesDatasource->getByQuoteID($this->_quoteModel->ID);
			$postcode = $properties[0]['postcode'];
			
			// Check to see if we have an agents Rate ID
			$agentsRateID = 0;
			
			$agentsDatasource = new Datasource_Core_Agents();
			$agent = $agentsDatasource->getAgent($this->getAgentSchemeNumber());

			// If this agent has an active valid rate ID we can use it
			if (isset($agent->agentsRateID)) {
				if ($agent->agentsRateID==5 || $agent->agentsRateID==11) {
					$agentsRateID = $agent->agentsRateID;
				}
			}

			// Get the risk areas for LI+
			// Then we need to get the LI+ risk areas for this postcode
			$riskAreasDatasource = new Datasource_Insurance_LandlordsPlus_RiskAreas();
			$riskAreas = $riskAreasDatasource->getByPostcode($postcode);

			// Calculate the discounts/loadings to be applied based on the quote meta data
			$loadings = $this->_calculateLoadings();
			
			// ** BUILDINGS COVER PREMIUMS ** //
			if ($this->hasProduct(self::BUILDING_COVER)) {
				$productMeta = $this->getProductMeta(self::BUILDING_COVER);	
				$dsiValues = $this->_calculateDSI($postcode);
				
				$dsi = $dsiValues['rebuildValue'];
				
				// If we don't have a DSI - use the manually entered rebuild value
				if (isset($productMeta['rebuild_value']) && $productMeta['rebuild_value'] > 0) $dsi = $productMeta['rebuild_value'];
				
				if ($dsi>0) {
					// and we need to get the rates for Buildings insurance
					$ratesDatasource = new Datasource_Insurance_LandlordsPlus_BuildingCover_Rates();
					$buildingRates = $ratesDatasource->getByProperty($riskAreas['buildingsAreaID'], $productMeta['build_year'], $this->getStartDate());

					// now we can calculate the premiums based on the DSI Value and the rates
					$grossRate = $buildingRates['gross'];
					$netRate = $buildingRates['net'];

					if (isset($productMeta['accidental_damage']) && $productMeta['accidental_damage']=='yes') {
						$grossRate = $grossRate + $buildingRates['grossAccidentalDamage'];
						$netRate = $netRate + $buildingRates['netAccidentalDamage'];
					}

					$grossRate = $grossRate * $loadings['buildingMultiplier'];
					$netRate = $netRate * $loadings['buildingMultiplier'];

					// Check to see if the property needs flood loadings applying
					$endorsements = $this->getEndorsements();

					// See if the property is in a flood area with optional endorsement
					// If the endorsement is mandatory we don't charge extra - we just exclude the cover
					if ($endorsements['floodArea'] == true && $endorsements['floodOptional'] == true) {
						// Check to see if the customer has opted out of the flood cover
						$properties = $this->getProperties();
						
						if ($properties[0]['exclude_flood_cover']==1) {
							
						} else {
							$annualBFlood = $buildingRates['grossFlood']* ($dsi / 1000);
							$netBFlood = $buildingRates['netFlood']* ($dsi / 1000);
						$grossRate = $grossRate + $buildingRates['grossFlood'];
						$netRate = $netRate + $buildingRates['netFlood'];
					}
					}

					$buildingRates['grossAfterLoadings'] = $grossRate;
					$buildingRates['netAfterLoadings'] = $netRate;

					$annualBuildingCover = $grossRate * ($dsi / 1000);
				} else {
					// We don't have a DSI and no rebuild value has been entered... oops!!
					// Todo: panic!!
					return '';
				}
			}

			// ** UNFURNISHED CONTENTS COVER ** //
			if ($this->hasProduct(self::UNFURNISHED_CONTENTS_COVER)) {
				if ($agentsRateID<>0) {
					$annualContentsCover = 60;
				} else {
					$annualContentsCover = 72;
				}
			}

			// ** FURNISHED CONTENTS COVER ** //
			if ($this->hasProduct(self::CONTENTS_COVER)) {
				$productMeta = $this->getProductMeta(self::CONTENTS_COVER);

				$ratesDatasource = new Datasource_Insurance_LandlordsPlus_ContentsCover_Rates();
				$contentsRates = $ratesDatasource->getByProperty($riskAreas['contentsAreaID'],$this->getStartDate());

				if (isset($productMeta['accidental_damage']) && $productMeta['accidental_damage']=='yes') {
					$annualContentsCover = (($contentsRates['gross'] + $contentsRates['grossAccidentalDamage']) * $loadings['contentsMultiplier']) * ($productMeta['cover_amount'] / 1000);
				} else {
					$annualContentsCover = ($contentsRates['gross'] * $loadings['contentsMultiplier']) * ($productMeta['cover_amount'] / 1000);
				}

				// Check to see if the property needs flood loadings applying
				$endorsements = $this->getEndorsements();

				// See if the property is in a flood area with optional endorsement
				// If the endorsement is mandatory we don't charge extra - we just exclude the cover
				if ($endorsements['floodArea'] == true && $endorsements['floodOptional'] == true) {

					// Todo: Check to see if the customer has opted out of the flood cover
					$annualCFlood = $contentsRates['grossFlood']* ($productMeta['cover_amount'] / 1000);
					$grossRate = $grossRate + $contentsRates['grossFlood'];
					$annualContentsCover +=$annualCFlood;
				}
			}


			// ** EMERGENCY ASSISTANCE & BOILER AND HEATING COVER** //
			if ($this->hasProduct(self::EMERGENCY_ASSISTANCE) && (!$this->hasProduct(self::BUILDING_COVER) && !$this->hasProduct(self::CONTENTS_COVER))) {
				// Stand-alone emergency assistance
				if ($agentsRateID<>0) {
					$annualEmergencyAssistance = 90;
				} else {
					$annualEmergencyAssistance = 120;
				}
				
			} elseif ($this->hasProduct(self::BUILDING_COVER) || $this->hasProduct(self::CONTENTS_COVER)) {
				// Free emergency assistance included with buildings cover
				if ($this->hasProduct(self::BOILER_HEATING)) {
					$annualEmergencyAssistance = 60;
				}
				if($this->hasProduct(self::BUILDING_COVER)){
					$annualBuildingCover += 6.50; 
				}else{
					$annualContentsCover += 6.50;
				}								
			} 

			// ** RENT GUARANTEE ** //
			if ($this->hasProduct(self::RENT_GUARANTEE)) {
				$productMeta = $this->getProductMeta(self::RENT_GUARANTEE);

				$annualRentGuarantee = $productMeta['monthly_rent'] * 0.36;
			}

			// ** LEGAL EXPENSES ** //
			if ($this->hasProduct(self::LEGAL_EXPENSES)) {
				if ($this->hasProduct(self::RENT_GUARANTEE)) {
					// Free Legal Expenses cover as part of Rent Guarantee
					$annualLegalExpensesCover = 0;
				} else {
					// Stand alone Legal Expenses cover
					$annualLegalExpensesCover = 114;
				}
			}

			if ($this->hasProduct(self::BUILDING_COVER)) {
				$dsiValues = $this->_calculateDSI($postcode);
				$calculatedDSI = $dsiValues['rebuildValue'];
			} else {
				$calculatedDSI = 0;
			}

			$premiums = array(
				'calculatedDSIValue'					=>	(double)$calculatedDSI,
				'rebuildValue'							=>  $dsi>0?(double)$dsi:0,
				'grossAnnualBuildingCover'				=>	(double)number_format($annualBuildingCover,2,'.',''),
				'grossAnnualContentsCover'				=>	(double)number_format($annualContentsCover,2,'.',''),
				'grossAnnualEmergencyAssistance'		=>	(double)number_format($annualEmergencyAssistance,2,'.',''),
				'grossAnnualRentGuarantee'				=>	(double)number_format($annualRentGuarantee,2,'.',''),
				'grossAnnualLegalExpensesCover'			=>  (double)number_format($annualLegalExpensesCover,2,'.',''),
			);

			$premiums ['totalGrossAnnualPremium'] = round(
				$annualBuildingCover +
				$annualContentsCover +
				$annualEmergencyAssistance +
				$annualRentGuarantee +
				$annualLegalExpensesCover, 2);
			$premiums ['totalGrossMonthlyPremium'] = round($premiums['totalGrossAnnualPremium'] / 12, 2);

			// Get IPT percentage
			$propertiesDatasource = new Datasource_Insurance_Quote_Properties();
			$properties = $propertiesDatasource->getByQuoteID($this->_quoteModel->ID);
			$postcode = $properties[0]['postcode'];
			
			$taxDatasource = new Datasource_Core_Tax();
			$tax = $taxDatasource->getTaxbyTypeAndPostcode('ipt', $postcode);
			$ipt = 1 + ($tax['rate'] / 100);

			$premiums ['totalGrossAnnualIPT'] = round(($premiums['totalGrossAnnualPremium'] * $ipt) - $premiums['totalGrossAnnualPremium'], 2);
			$premiums ['totalGrossMonthlyIPT'] = round(($premiums ['totalGrossMonthlyPremium']*$ipt) - $premiums ['totalGrossMonthlyPremium'],2);
			
			return $premiums;
		} else {
			return '';
		}
	}


	/**
	 *
	 */
	public function addLegalExpensesCover() {
		if ($this->hasProduct(self::RENT_GUARANTEE)) {
			throw new Exception('Legal Expenses is already included on this quote for free.');
		}
		// Cleanup any existing cover
		if ($this->hasProduct(self::LEGAL_EXPENSES)) {
			$this->removeProduct(self::LEGAL_EXPENSES);
		}

		$quoteProducts = new Datasource_Insurance_Quote_Products();
		$quoteProducts->add($this->_quoteModel->ID, self::LEGAL_EXPENSES);

		$this->save();
	}

	/**
	 *
	 */
	public function addRentGuarantee($monthlyRent) {
		// Cleanup any existing cover
		if ($this->hasProduct(self::RENT_GUARANTEE)) {
			$this->removeProduct(self::RENT_GUARANTEE);
		}
		if ($this->hasProduct(self::LEGAL_EXPENSES)) {
			$this->removeProduct(self::LEGAL_EXPENSES);
		}

		if ($monthlyRent>=350) {
			$quoteProducts = new Datasource_Insurance_Quote_Products();
			$quoteProducts->add($this->_quoteModel->ID, self::RENT_GUARANTEE);
			// Add free Legal Expenses Cover
			$quoteProducts->add($this->_quoteModel->ID, self::LEGAL_EXPENSES);
			
			$quoteProductMetas = new Datasource_Insurance_Quote_Product_Metas();
			$quoteProductMetas->add($this->_quoteModel->ID, self::RENT_GUARANTEE, 'monthly_rent', $monthlyRent);			
		}
		$this->save();
	}

	/**
	 *
	 */
	public function addEmergencyAssistance() {
		if ($this->hasProduct(self::BUILDING_COVER) || $this->hasProduct(self::CONTENTS_COVER)) {
			throw new Exception('Emergency Assistance is already included on this quote for free.');
		}

		$quoteProducts = new Datasource_Insurance_Quote_Products();
		if (!$this->hasProduct(self::EMERGENCY_ASSISTANCE)) {
			$quoteProducts->add($this->_quoteModel->ID, self::EMERGENCY_ASSISTANCE);
		}
		if (!$this->hasProduct(self::BOILER_HEATING)) {
			$quoteProducts->add($this->_quoteModel->ID, self::BOILER_HEATING);
		}

		$this->save();
	}

	/**
	 *
	 */
	 public function addBoilerAndHeatingCover() {
	 	// You can only add B+H cover if you have building cover, or contents over, or stand-alone emergency assistance
	 	if ($this->hasProduct(self::EMERGENCY_ASSISTANCE) &&
	 		(!$this->hasProduct(self::BUILDING_COVER) && !$this->hasProduct(self::CONTENTS_COVER))) {
	 		throw new Exception('Boiler and Heating cover is already included as part of Stand-Alone Emergency Assistance');
	 	}

	 	$quoteProducts = new Datasource_Insurance_Quote_Products();
	 	$quoteProducts->add($this->_quoteModel->ID, self::BOILER_HEATING);

	 	$this->save();
	 }

	/**
	 * Add buildings insurance to the quote
	 *
	 * @param string propertyBuildYear Year the property was built
	 * @param string propertyBedrooms Number of bedrooms in the property
	 * @param string propertyType Property type
	 * @param string excess Chosen excess for the cover
	 * @param string accidentalDamage Accidental damage cover required?
	 * @param int rebuildValue Optional rebuild value for properties where DSI can't be calculated
	 *
	 */
	public function addBuildingsCover($propertyBuildYear, $propertyBedrooms, $propertyType, $excess, $accidentalDamage, $rebuildValue = 0) {
		$quoteProducts = new Datasource_Insurance_Quote_Products();

		// Check to see if the product is already added to this quote - if it is remove it
		if ($this->hasProduct(self::BUILDING_COVER)) {
			$this->removeProduct(self::BUILDING_COVER);
		}

		// Now we need to add this product to the quote
		$quoteProducts->add($this->_quoteModel->ID, self::BUILDING_COVER);

		// Now we need to add all the relevant meta data
		$quoteProductMetas = new Datasource_Insurance_Quote_Product_Metas();
		$quoteProductMetas->add($this->_quoteModel->ID, self::BUILDING_COVER, 'build_year', $propertyBuildYear);
		$quoteProductMetas->add($this->_quoteModel->ID, self::BUILDING_COVER, 'bedroom_quantity', $propertyBedrooms);
		$quoteProductMetas->add($this->_quoteModel->ID, self::BUILDING_COVER, 'building_type', $propertyType);
		$quoteProductMetas->add($this->_quoteModel->ID, self::BUILDING_COVER, 'excess', $excess);
		$quoteProductMetas->add($this->_quoteModel->ID, self::BUILDING_COVER, 'accidental_damage', $accidentalDamage);
		$quoteProductMetas->add($this->_quoteModel->ID, self::BUILDING_COVER, 'rebuild_value', $rebuildValue);
        $quoteProducts->add($this->_quoteModel->ID, self::EMERGENCY_ASSISTANCE);
		$this->save();
	}

	/**
	 * Add contents insurance to the quote
	 *
	 * @param boolean furnished True or False depending on whether the property is furnished
	 * @param string accidentalDamage Add accidental damage cover?
	 * @param int coverAmount The amount to be insured (optional!)
	 * @param int excess The chosen excess (optional!)
	 */
	public function addContentsCover($furnished, $accidentalDamage = null, $coverAmount = null, $excess = null) {
		// Check to see if the product is already added to this quote - if it is remove it
		if ($this->hasProduct(self::UNFURNISHED_CONTENTS_COVER)) {
			$this->removeProduct(self::UNFURNISHED_CONTENTS_COVER);
		}
		if ($this->hasProduct(self::CONTENTS_COVER)) {
			$this->removeProduct(self::CONTENTS_COVER);
		}
		// Remove any emergency assistance that may be on the quote as you get that for free
		if ($this->hasProduct(self::EMERGENCY_ASSISTANCE)  && !$this->hasProduct(self::BUILDING_COVER)) {
			$this->removeProduct(self::EMERGENCY_ASSISTANCE);
		}

		$quoteProducts = new Datasource_Insurance_Quote_Products();

		if ($furnished) {
			// Furnished contents cover
			// Add this product to the quote
			$quoteProducts->add($this->_quoteModel->ID, self::CONTENTS_COVER);

			// Now we need to add all the relevant meta data
			$quoteProductMetas = new Datasource_Insurance_Quote_Product_Metas();
			$quoteProductMetas->add($this->_quoteModel->ID, self::CONTENTS_COVER, 'cover_amount', $coverAmount);
			$quoteProductMetas->add($this->_quoteModel->ID, self::CONTENTS_COVER, 'excess', $excess);
			$quoteProductMetas->add($this->_quoteModel->ID, self::CONTENTS_COVER, 'accidental_damage', $accidentalDamage);
			// Add on free emergency assistance
			if(!$this->hasProduct(self::BUILDING_COVER)){
				$quoteProducts->add($this->_quoteModel->ID, self::EMERGENCY_ASSISTANCE);
			}
		} else {
			// Unfurnished contents cover
			// Add this product to the quote
			$quoteProducts->add($this->_quoteModel->ID, self::UNFURNISHED_CONTENTS_COVER);
		}
		$this->save();
	}

	/**
	 * Save the quote
	 *
	 */
	public function save() {
		parent::save();
		$this->_saveToLegacy();
	}

	/**
     * Save the quote into the legacy database tables
     *
     * @munt 10
     */
    protected function _saveToLegacy() {
    	// Insert/Update the legacy customer table
    	// Check to see if we already have a legacy identifier
    	if (is_null($this->_quoteModel->legacyID) || $this->_quoteModel->legacyID == '') {
    		// This is the first save to legacy so we need to generate a new unique ID
    		// I say unique.. but ermm.. well you'll see *sigh*
    		$numberTracker = new Datasource_Core_NumberTracker();

    		$this->_quoteModel->legacyID = 'QHLI' . (string)$numberTracker->getNextPolicyNumber() . '/01';
    		$this->_quoteDataSource->save($this->_quoteModel); // Do a quick save now we have a legacy quote number
    	}
		$this->_quoteModel->policyNumber = $this->_quoteModel->legacyID;
		$this->_quoteModel->policyLength = 12;

    	// Insert/Update the legacy quote table
    	$legacyQuote = new Model_Insurance_LegacyQuote();
    	$legacyQuote->policyNumber = $this->getLegacyID(); // Quote number
    	$legacyQuote->refNo = $this->getLegacyCustomerReference(); // Customer reference number
    	$legacyQuote->agentSchemeNumber = $this->getAgentSchemeNumber(); // Todo: This should be the agent scheme number for the selected agent!
    	$legacyQuote->policyName = 'landlordsp'; // Landlords+ quote/policy
    	$legacyQuote->issueDate = date('Y-m-d'); // Issued date - today
    	$legacyQuote->startDate = $this->getStartDate();
    	$legacyQuote->endDate = $this->getEndDate();
    	$legacyQuote->policyType = 'L';
    	$legacyQuote->policyLength =12;
		$legacyQuote->payStatus = $this->_quoteModel->status;
    	if($this->getPayFrequency()=="ANNUALLY"){
    		$legacyQuote->payBy="Annually";
    	}
        
        $idd = new Datasource_Insurance_IddSupport();
        if(!$idd->isIddSupport($legacyQuote->policyNumber)){
            $inserArray = array();
            $insertArray['policynumber']=$legacyQuote->policyNumber;
            $insertArray['agentschemeno']=$legacyQuote->agentSchemeNumber;
            $insertArray['csuid']=0;
            $fsaAgentStatusDatasource = new Datasource_Fsa_AgentStatus();
	    $fsaStatus = $fsaAgentStatusDatasource->getAgentFsaStatus($legacyQuote->agentSchemeNumber);
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

    	$properties = $this->getProperties();
    	$legacyQuote->propertyAddress1 = $properties[0]['line_1'];
    	$legacyQuote->propertyAddress2 = $properties[0]['line_2'];
        $legacyQuote->propertyAddress3 = $properties[0]['town'];
    	$legacyQuote->propertyPostcode = $properties[0]['postcode'];

    	// Build the policy options pipe delimitted nonsense!!! FML
    	$premiums = $this->calculatePremiums();
    	if ($premiums != '') {
    		// Depending on if this is an annual or monthly quote we have to save different values in the database :(
    		if($this->getPayFrequency()=="ANNUALLY") {
	    		$legacyQuote->premium = $premiums['totalGrossAnnualPremium'];
	    		$legacyQuote->quote = $premiums['totalGrossAnnualPremium']+$premiums['totalGrossAnnualIPT'];
	    		$legacyQuote->ipt = $premiums['totalGrossAnnualIPT'];
	    	} else {
	    		$legacyQuote->premium = $premiums['totalGrossMonthlyPremium'];
	    		$legacyQuote->quote = $premiums['totalGrossMonthlyPremium']+$premiums['totalGrossMonthlyIPT'];
	    		$legacyQuote->ipt = $premiums['totalGrossMonthlyIPT'];
	    	}
	    	$this->_quoteModel->premium = $premiums ['totalGrossMonthlyPremium'];
    	    $this->_quoteModel->ipt  	= $premiums ['totalGrossAnnualIPT'];
    	    $legacyQuote->riskArea = $premiums['riskAreas']['buildingsAreaID'];
    	    
    	    $legacyQuote->termid = $this->setPolicyTerm();
        }

    	$mult=1;
    	if ($this->getPayFrequency() == 'MONTHLY') $mult=12;

    	$policyOptions = '';
    	$amountsCovered = '';
    	$optionPremiums = '';

    	$pItemHist = new Datasource_Insurance_Policy_PolicyTermItemHist();
    	$cover = new Model_Insurance_Cover();

    	// Get IPT percentage
		$propertiesDatasource = new Datasource_Insurance_Quote_Properties();
		$properties = $propertiesDatasource->getByQuoteID($this->_quoteModel->ID);
		$postcode = $properties[0]['postcode'];
		
		$taxDatasource = new Datasource_Core_Tax();
		$tax = $taxDatasource->getTaxbyTypeAndPostcode('ipt', $postcode);
		$ipt = 1 + ($tax['rate'] / 100);

    	if ($premiums != '') {
	    	if ($this->hasProduct(self::BUILDING_COVER) && $this->isValidValue($premiums['netBuilding'])) {
	    		$policyOptions .= 'buildingsp|';
	    		$amountsCovered .= $premiums['rebuildValue']>0?$premiums['rebuildValue']:$premiums['calculatedDSIValue'];
	    		$amountsCovered .= '|';
	    		$optionPremiums .= round($premiums['annualBuilding']/$mult,2). '|';
	    		$cover->policyOptionID = 33;
	    		$cover->sumInsured = $premiums['rebuildValue']>0?$premiums['rebuildValue']:$premiums['calculatedDSIValue'];
	    		$cover->grosspremium =round($premiums['annualBuilding']*$ipt,2);
	    		$cover->premium=$premiums['annualBuilding'];
	    		$cover->netpremium=$premiums['netBuilding'];
	    		$cover->ipt=$cover->grosspremium-$cover->premium;
	    		$pItemHist->setItemHist($legacyQuote,$cover);
	    		if($this->isValidValue($premiums['netBuildingAD'])){
	    			$policyOptions .='buildingsAccidentalDamagep|';
	    			$amountsCovered .= $premiums['rebuildValue']>0?$premiums['rebuildValue']:$premiums['calculatedDSIValue'];
	    			$amountsCovered .= '|';
	    			$optionPremiums .= round($premiums['annualBuildingAD']/$mult,2).'|';
	    			$cover->policyOptionID = 37;
	    			$cover->grosspremium =round($premiums['annualBuildingAD']*$ipt,2);
	    			$cover->premium=$premiums['annualBuildingAD'];
	    			$cover->netpremium=$premiums['netBuildingAD'];
	    			$cover->ipt=$cover->grosspremium-$cover->premium;
	    			$pItemHist->setItemHist($legacyQuote,$cover);
	    		}
	    		if($this->isValidValue($premiums['netBFlood'])){
	    			$policyOptions .='buildingslflood|';
	    			$amountsCovered .= $premiums['rebuildValue']>0?$premiums['rebuildValue']:$premiums['calculatedDSIValue'];
	    			$amountsCovered .= '|';
	    			$optionPremiums .= round($premiums['annualBFlood']/$mult,2).'|';
	    			$cover->policyOptionID = 31;
	    			$cover->grosspremium =round($premiums['annualBFlood']*$ipt,2);
	    			$cover->premium=$premiums['annualBFlood'];
	    			$cover->netpremium=$premiums['netBFlood'];
	    			$cover->ipt=$cover->grosspremium-$cover->premium;
	    			$pItemHist->setItemHist($legacyQuote,$cover);
	    		}
	    	}
	    	if ($this->hasProduct(self::UNFURNISHED_CONTENTS_COVER) && $this->isValidValue($premiums['netLContent'])) {

	    		$policyOptions .= 'limitedcontentsp|';
	    		$amountsCovered .='5000|';
	    		$optionPremiums .= round($premiums['annualLContent']/$mult,2) . '|';
	    	    $cover->policyOptionID = 39;
	    	    $cover->sumInsured=5000;
	    		$cover->grosspremium =round($premiums['annualLContent']*$ipt,2);
	    		$cover->premium=$premiums['annualLContent'];
	    		$cover->netpremium=$premiums['netLContent'];
	    		$cover->ipt=$cover->grosspremium-$cover->premium;
	    		$pItemHist->setItemHist($legacyQuote,$cover);

	    	}
	    	if ($this->hasProduct(self::CONTENTS_COVER) && $this->isValidValue($premiums['netContent'])) {
	    		$policyOptions .= 'contentslp|';
	    		$meta = $this->getProductMeta(self::CONTENTS_COVER);
	    		$amountsCovered .= $meta['cover_amount'] . '|';
	    		$optionPremiums .= round($premiums['annualContent']/$mult,2) . '|';
	    		$cover->sumInsured = $meta['cover_amount'];
	    		$cover->policyOptionID = 34;
	    		$cover->grosspremium =round($premiums['annualContent']*$ipt,2);
	    		$cover->premium=$premiums['annualContent'];
	    		$cover->netpremium=$premiums['netContent'];
	    		$cover->ipt=$cover->grosspremium-$cover->premium;
	    		$pItemHist->setItemHist($legacyQuote,$cover);
	    		if($this->isValidValue($premiums['netContentsAD'])){
	    			$policyOptions .='contentslAccidentalDamagep|';
	    			$amountsCovered .= $meta['cover_amount'] . '|';
	    			$optionPremiums .= round($premiums['annualContentsAD']/$mult,2).'|';
	    			$cover->policyOptionID = 38;
	    			$cover->grosspremium =round($premiums['annualContentsAD']*$ipt,2);
	    			$cover->premium=$premiums['annualContentsAD'];
	    			$cover->netpremium=$premiums['netContentsAD'];
	    			$cover->ipt=$cover->grosspremium-$cover->premium;
	    			$pItemHist->setItemHist($legacyQuote,$cover);
	    		}
	    		if($this->isValidValue($premiums['netBFlood'])){
	    			$policyOptions .='contentslflood|';
	    			$amountsCovered .= $meta['cover_amount'] . '|';
	    			$optionPremiums .= round($premiums['annualCFlood']/$mult,2).'|';
	    			$cover->policyOptionID = 32;
	    			$cover->grosspremium =round($premiums['annualCFlood']*$ipt,2);
	    			$cover->premium=$premiums['annualCFlood'];
	    			$cover->netpremium=$premiums['netCFlood'];
	    			$cover->ipt=$cover->grosspremium-$cover->premium;
	    			$pItemHist->setItemHist($legacyQuote,$cover);

	    		}
	    	}

    	   // ** RENT GUARANTEE ** //
			if ($this->hasProduct(self::RENT_GUARANTEE) && $this->isValidValue($premiums['netAnnualRentGuarantee'])) {
					$policyOptions .='rentguaranteep|';
					$meta = $this->getProductMeta(self::RENT_GUARANTEE);
	    			$amountsCovered .= $meta['monthly_rent'] . '|';
	    			$optionPremiums .= round($premiums['grossAnnualRentGuarantee']/$mult,2).'|';
	    			$cover->policyOptionID = 36;
	    			$cover->sumInsured=$meta['monthly_rent'];
	    			$cover->grosspremium =round($premiums['grossAnnualRentGuarantee']*$ipt,2);
	    			$cover->premium=$premiums['grossAnnualRentGuarantee'];
	    			$cover->netpremium=$premiums['netAnnualRentGuarantee'];
	    			$cover->ipt=$cover->grosspremium-$cover->premium;
	    			$pItemHist->setItemHist($legacyQuote,$cover);
			}

			// ** LEGAL EXPENSES ** //
			if ($this->hasProduct(self::LEGAL_EXPENSES) && $this->isValidValue($premiums['netAnnualLegalExpensesCover'])) {
					$policyOptions .='legalexpensesp|';
					$amountsCovered .='50000|';
	    			$optionPremiums .= round($premiums['grossAnnualLegalExpensesCover']/$mult,2).'|';
	    			$cover->policyOptionID = 35;
	    			$cover->sumInsured=50000;
	    			$cover->grosspremium =round($premiums['grossAnnualLegalExpensesCover']*$ipt,2);
	    			$cover->premium=$premiums['grossAnnualLegalExpensesCover'];
	    			$cover->netpremium=$premiums['netAnnualLegalExpensesCover'];
	    			$cover->ipt=$cover->grosspremium-$cover->premium;
	    			$pItemHist->setItemHist($legacyQuote,$cover);
			}

	    	// ** EMERGENCY ASSISTANCE ** //
			if ($this->hasProduct(self::EMERGENCY_ASSISTANCE)) {

				if($this->isValidValue($premiums['netEA'])) {
					$policyOptions .='emergencyassistance|';
					$amountsCovered .='1500|';
	    			$optionPremiums .= round($premiums['annualEA']/$mult,2).'|';
	    			$cover->policyOptionID = 4;
	    			$cover->sumInsured=1500;
	    			$cover->grosspremium =round($premiums['annualEA']*$ipt,2);
	    			$cover->premium=$premiums['annualEA'];
	    			$cover->netpremium=$premiums['netEA'];
	    			$cover->ipt=$cover->grosspremium-$cover->premium;
	    			$pItemHist->setItemHist($legacyQuote,$cover);
				}

			   if($this->isValidValue($premiums['netEAB'])) {
					$policyOptions  .='emergencyassistancebahbuildings|';
					$amountsCovered .='0|';
	    			$optionPremiums .= round($premiums['annualEAB']/$mult,2).'|';
	    			$cover->policyOptionID = 28;
	    			$cover->sumInsured=0;
	    			$cover->grosspremium =round($premiums['annualEAB']*$ipt,2);
	    			$cover->premium=$premiums['annualEAB'];
	    			$cover->netpremium=$premiums['netEAB'];
	    			$cover->ipt=$cover->grosspremium-$cover->premium;
	    			$pItemHist->setItemHist($legacyQuote,$cover);
				}
				
			    if($this->isValidValue($premiums['netEAS'])){
					$policyOptions .='emergencyassistancestandalone|';
					$amountsCovered .='1500|';
	    			$optionPremiums .= round($premiums['annualEAS']/$mult,2).'|0|';
	    			$cover->policyOptionID = 30;
	    			$cover->sumInsured=1500;
	    			$cover->grosspremium =round($premiums['annualEAS']*$ipt,2);
	    			$cover->premium=$premiums['annualEAS'];
	    			$cover->netpremium=$premiums['netEAS'];
	    			$cover->ipt=$cover->grosspremium-$cover->premium;
	    			$pItemHist->setItemHist($legacyQuote,$cover);
				}

				if($this->isValidValue($premiums['netEABS'])){
					$policyOptions .='emergencyassistancebahstandalone|';
					$amountsCovered .='0|';
	    			$optionPremiums .= round($premiums['annualEABS']/$mult,2).'|';
	    			$cover->policyOptionID = 29;
	    			$cover->sumInsured=0;
	    			$cover->grosspremium =round($premiums['annualEABS']*$ipt,2);
	    			$cover->premium=$premiums['annualEABS'];
	    			$cover->netpremium=$premiums['netEABS'];
	    			$cover->ipt=$cover->grosspremium-$cover->premium;
	    			$pItemHist->setItemHist($legacyQuote,$cover);
				}

				
			}



	    	// We also have to populate the POLICYVARIABLE table for MTA/Renewals *arrghghh*
	    	// NOTE: This code is seriously ugly due to time restraints. Intention is to re-write it
	    	//       when we re-write the admin suites so we don't have to maintain horrible legacy systems!

	    	/* POLICY VARIABLES */
	    	$policyVariables = array();
	    	$policyNumber = $this->getPolicyNumber();

	    	// Policy Risk Area - 1
	    	$policyVariables[] = array (
	    		'policyVariableDefID'	=>	1,
	    		'policyNumber'			=>	$policyNumber,
	    		'timestamp'				=>	date('Y-m-d h:i:s'),
	    		'value'					=> 	$premiums['riskAreas']['buildingsAreaID']);

	    	// No claims discount - 3
	    	// Possible options 0=0, 1=1, 2=2, 3=3, 4=3+
	    	$properties = $this->getProperties();
	    	$ncbID = $properties[0]['no_claims_years_id'];
	    	$policyVariables[] = array (
	    		'policyVariableDefID'	=>	3,
	    		'policyNumber'			=>	$policyNumber,
	    		'timestamp'				=>	date('Y-m-d h:i:s'),
	    		'value'					=> 	$ncbID - 1);

	    	// Managed property - 4
	    	// Possible options 0=No, 1=Yes
	    	$policyVariables[] = array (
	    		'policyVariableDefID'	=>	4,
	    		'policyNumber'			=>	$policyNumber,
	    		'timestamp'				=>	date('Y-m-d h:i:s'),
	    		'value'					=> 	$properties[0]['letting_agent_managed']);

	    	if ($this->hasProduct(self::BUILDING_COVER)) {
	    		// Year of Construction - 2
		    	$policyVariables[] = array (
		    		'policyVariableDefID'	=>	2,
		    		'policyNumber'			=>	$policyNumber,
		    		'timestamp'				=>	date('Y-m-d h:i:s'),
		    		'value'					=> 	$premiums['dsiData']['yearBuiltID']);

		    	// Buildings excess - 5
		    	// Possible options 0=0, 1=100, 2=250, 3=500, 4=1000
		    	$buildingExcessID = 0;
		    	$buildingMeta = $this->getProductMeta(self::BUILDING_COVER);
	    		$buildingExcess = $buildingMeta['excess'];
	    		switch ($buildingExcess) {
	    			case '0':
	    				$buildingExcessID = 0;
	    				break;
	    			case '100':
	    				$buildingExcessID = 1;
	    				break;
	    			case '250':
	    				$buildingExcessID = 2;
	    				break;
	    			case '500':
	    				$buildingExcessID = 3;
	    				break;
	    			case '1000':
	    				$buildingExcessID = 4;
	    				break;
	    		}

		    	$policyVariables[] = array (
		    		'policyVariableDefID'	=>	5,
		    		'policyNumber'			=>	$policyNumber,
		    		'timestamp'				=>	date('Y-m-d h:i:s'),
		    		'value'					=> 	$buildingExcessID);
			}

	    	// Tenant type - 6
	    	// Possible options
	    	// 		0=Employed
	    	//		1=Self Employed
	    	//		2=Student
	    	//		3=Retired
	    	//		4=Unemployed
	    	//		5=Housing Authority
	    	//		6=Unknown
	    	//		7=Claiming Benefit
	    	$tenantTypeID = 7;

	    	// NOTE : This is the worst code I've EVER written Argghhh
	    	switch ($properties[0]['tenant_type_id']) {
	    		case 1:
	    			$tenantTypeID = 0;
	    			break;
	    		case 2:
	    			$tenantTypeID = 1;
	    			break;
	    		case 3:
	    			$tenantTypeID = 2;
	    			break;
	    		case 4:
	    			$tenantTypeID = 3;
	    			break;
	    		case 5:
	    			$tenantTypeID = 4;
	    			break;
	    		case 6:
	    			$tenantTypeID = 7;
	    			break;
	    		case 7:
	    			$tenantTypeID = 5;
	    			break;
	    		case 8:
	    			$tenantTypeID = 6;
	    			break;
	    	}

	    	$policyVariables[] = array (
	    		'policyVariableDefID'	=>	6,
	    		'policyNumber'			=>	$policyNumber,
	    		'timestamp'				=>	date('Y-m-d h:i:s'),
	    		'value'					=> 	$tenantTypeID);

	    	// Contents area - 7
	    	$policyVariables[] = array (
	    		'policyVariableDefID'	=>	7,
	    		'policyNumber'			=>	$policyNumber,
	    		'timestamp'				=>	date('Y-m-d h:i:s'),
	    		'value'					=> 	$premiums['riskAreas']['contentsAreaID']);

	    	// Contents excess - 8
	    	// Possible options 0=0, 1=100, 2=250, 3=500, 4=1000
	    	if ($this->hasProduct(self::CONTENTS_COVER)) {
	    		$contentsMeta = $this->getProductMeta(self::CONTENTS_COVER);
	    		switch ($contentsMeta['excess']) {
	    			case '0':
	    				$buildingExcessID = 0;
	    				break;
	    			case '100':
	    				$buildingExcessID = 1;
	    				break;
	    			case '250':
	    				$buildingExcessID = 2;
	    				break;
	    			case '500':
	    				$buildingExcessID = 3;
	    				break;
	    			case '1000':
	    				$buildingExcessID = 4;
	    				break;
	    		}
		    	$policyVariables[] = array (
		    		'policyVariableDefID'	=>	8,
		    		'policyNumber'			=>	$policyNumber,
		    		'timestamp'				=>	date('Y-m-d h:i:s'),
		    		'value'					=> 	$buildingExcessID);
	    	}

	    	// Building flood risk - 9
	    	// Possible options 0=No, 1=Yes
	    	$policyVariables[] = array (
	    		'policyVariableDefID'	=>	9,
	    		'policyNumber'			=>	$policyNumber,
	    		'timestamp'				=>	date('Y-m-d h:i:s'),
	    		'value'					=> 	$premiums['riskAreas']['floodArea']==0?0:1);

	    	// Contents flood risk - 10
	    	// Possible options 0=No, 1=Yes
	    	$policyVariables[] = array (
	    		'policyVariableDefID'	=>	10,
	    		'policyNumber'			=>	$policyNumber,
	    		'timestamp'				=>	date('Y-m-d h:i:s'),
	    		'value'					=> 	$premiums['riskAreas']['floodArea']==0?0:1);

	    	// Buildings cover over 500,000 - 11
	    	// Possible options 0=No, 1=Yes
	    	$buildingValue = $premiums['rebuildValue']>0?$premiums['rebuildValue']:$premiums['calculatedDSIValue'];
	    	$policyVariables[] = array (
	    		'policyVariableDefID'	=>	11,
	    		'policyNumber'			=>	$policyNumber,
	    		'timestamp'				=>	date('Y-m-d h:i:s'),
	    		'value'					=> 	$buildingValue<=500000?0:1);

	    	// Years owned property - 12
	    	// Possible options
	    	//		0 = 0
	    	//		1 = 1
	    	//		2 = 2
	    	//		3 = 3
	    	// 		4 = 3+
	    	$yearsOwnedID = $properties[0]['ownership_length_id'];
	    	$policyVariables[] = array (
	    		'policyVariableDefID'	=>	12,
	    		'policyNumber'			=>	$policyNumber,
	    		'timestamp'				=>	date('Y-m-d h:i:s'),
	    		'value'					=> 	$yearsOwnedID-1);

	    	/********************/

	    	// Save the policy variables
	    	$legacyPolicyVariables = new Datasource_Insurance_LandlordsPlus_Legacy_PolicyVariables();
	    	$legacyPolicyVariables->save($policyVariables);
	    }

    	$legacyQuote->policyOptions = trim($policyOptions,'|');
    	$legacyQuote->amountsCovered = trim($amountsCovered,'|');
    	$legacyQuote->optionPremiums = trim($optionPremiums,'|');

    	$policyCover = new Datasource_Insurance_Policy_Cover();
    	$policyCover->populateFromLegacy($legacyQuote->policyNumber, $legacyQuote->policyOptions, $legacyQuote->amountsCovered, $legacyQuote->optionPremiums);

    	$legacyQuoteDatasource = new Datasource_Insurance_LegacyQuotes();
    	$legacyQuoteDatasource->save($legacyQuote);


		if ($premiums != '' && $this->hasProduct(self::BUILDING_COVER)) {
			// We also need to populate the legacy SIVALUE table or MTA's & Renewals don't work :(
            $buildingMeta = $this->getProductMeta(self::BUILDING_COVER);

	    	$sivalueData = array();

	    	$sivalueData['policynumber'] = $this->getPolicyNumber();

	    	// If SI is derived siTypeID = 1, if it's manual then siTypeID = 2
	    	if(isset($buildingMeta['rebuild_value']) && $buildingMeta['rebuild_value'] > 0 ) {
	    		$sivalueData['siTypeID'] = 2;
	    		$sivalueData['siValue'] = $premiums['rebuildValue'];
	    	} else {
	    		$sivalueData['siTypeID'] = 1;
	    		$sivalueData['siValue'] = $premiums['calculatedDSIValue'];
	    	}

	    	// policyOptionID is always 33 - Landlords Buildings+
	    	$sivalueData['policyOptionID'] = 33;

	    	// startdate and enddate are entered here too! *sigh*
	    	$sivalueData['startdate'] = $this->getStartDate();
	    	$sivalueData['enddate'] = $this->getEndDate();

	    	// dsiArea
	    	$sivalueData['dsiArea'] = $premiums['dsiData']['areaID'];
	    	// dsiYearBuiltID
	    	$sivalueData['dsiYearBuiltID'] = $premiums['dsiData']['yearBuiltID'];
	    	// dsiPropertyTypeID
	    	$sivalueData['dsiPropertyTypeID'] = $premiums['dsiData']['dsiPropertyTypeID'];
	    	// dsiBedroomNumID
	    	$sivalueData['dsiBedroomNumID'] = $premiums['dsiData']['dsiBedroomQuantityID'];

	    	$siValueDatasource = new Datasource_Insurance_LandlordsPlus_Legacy_Sivalue();
	    	$siValueDatasource->save($sivalueData);
		}

    }


	/**
	 * Converts quote to policy
	 */
	public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if(empty($policyNumber)) {
			$policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
		}

        //Create a policy object from the data passed in.
        $quoteDatasource = new Datasource_Insurance_LegacyQuotes();
        $quote = $quoteDatasource->getByPolicyNumber($quoteNumber);
        $quote->policyNumber = $policyNumber;
        $quote->status = "Policy";

	    if($this->getPayFrequency()=="ANNUALLY"){
    		$quote->payBy="Annually";
    	}
    	if($this->getPayMethod()=="DD"){
    		$quote->payMethod="DirectDebit";
    	}elseif($this->getPayMethod()=="CC"){
    		$quote->payMethod="CreditCard";
    	}
        //Set the issueDate and timecompleted fields (which oddly record the same value but
        //in different formats).
        $issueDate = Zend_Date::now();
        $quote->issueDate = $issueDate->toString(Zend_Date::ISO_8601);
        $quote->timeCompleted = $issueDate->toString(Zend_Date::TIMESTAMP);
        $quote->payStatus="UpToDate";
        // Update the optionpremiums field in the database
       /* if($quote->payBy=="Annually") {
	        $optionPremiums = explode('|',$quote->optionPremiums);
	        for($i=0;$i<count($optionPremiums);$i++){
	         $optionPremiums[$i] = round($optionPremiums[$i]*$quote->policyLength,2);
	          }
	        $optionPremiums = implode('|', $optionPremiums);
	        $quote->optionPremiums = $optionPremiums;
        }*/

        //Write the policy to the datasource
        $policyDatasource = new Datasource_Insurance_LandlordsPlus_Policies();
        $policyDatasource->save($quote);

        //Delete the legacy quote.

        $quoteDatasource->remove(array(Manager_Insurance_TenantsContentsPlus_Quote::POLICY_NUMBER => $quoteNumber));

        // And finally - remove the new quote as well
        $this->delete();

	}

        public function getQuote () {
            return $this->_quoteModel->policyNumber;
}

}
?>
