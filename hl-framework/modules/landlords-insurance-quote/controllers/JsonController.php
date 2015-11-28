<?php

// Can't be unit tested/code coverage'd as it's all designed to work over ajax
// @codeCoverageIgnoreStart

class LandlordsInsuranceQuote_JsonController extends Zend_Controller_Action
{
	public function init() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		header('Content-type: application/json');
	}

	public function addBankInterestAction() {

		$session = new Zend_Session_Namespace('landlords_insurance_quote');
        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);

		$customerReferenceNumber = $quoteManager->getLegacyCustomerReference();
		$policyNumber = $quoteManager->getLegacyID();

		$request = $this->getRequest();
		$postData = $request->getPost();

		$return = array();
		$return['success'] = false;

		$ajaxForm = new LandlordsInsuranceQuote_Form_Subforms_BankInterestDialog();
		if($ajaxForm->isValid($postData)) {

			//Create a new bank interest object populated with the details provided
			//by the user.
			$bankInterest = new Model_Insurance_LegacyBankInterest();
			$bankInterest->setBankName($postData['bank_name']);
			$bankInterest->setAccountNumber($postData['account_number']);

			$bankAddress = new Model_Core_Address();
			$bankAddress->addressLine1 = $postData['address_line1'];
			$bankAddress->addressLine2 = $postData['address_line2'];
			$bankAddress->town = $postData['town'];
			$bankAddress->postCode = $postData['postcode'];
			$bankInterest->setBankAddress($bankAddress);

			$bankInterest->setRefno($customerReferenceNumber);
			$bankInterest->setPolicyNumber($policyNumber);


			//Attempt to insert the bank interest object into the dbase.
			$bankInterestManager = new Manager_Insurance_LegacyBankInterest();
			$bankInterestManager->insertInterest($bankInterest);


			//Retrieve all the bank interests for display on the dialog.
			$bankInterestArray = $bankInterestManager->getAllInterests($policyNumber, $customerReferenceNumber);
			$model = array();
			foreach($bankInterestArray as $bankInterest) {

				$model[] = array('bankInterest' => $bankInterest);
			}


			//Update the dialog.
			$return['html'] = $this->view->partialLoop('partials/bank-interest-list.phtml', $model);
			$return['success'] = true;
		}
		else {

			foreach ($ajaxForm->getMessages() as $error) {

				$return['errors'] = $error;
			}
		}

		echo Zend_Json::encode($return);
	}

	public function removeBankInterestAction() {

		$request = $this->getRequest();
		$postData = $request->getPost();
		$bankInterestId = $postData['id'];


		//Remove the previous claim identified by the id
		$return['success'] = false;
		if(!empty($bankInterestId)) {

			$session = new Zend_Session_Namespace('landlords_insurance_quote');
	        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);

	        //Retrieve the quote identifiers.
			$customerReferenceNumber = $quoteManager->getLegacyCustomerReference();
			$policyNumber = $quoteManager->getLegacyID();

			//Ensure the bank interest identifier passed in is associated with the
			//policynumber and customer reference number.
			$bankInterestManager = new Manager_Insurance_LegacyBankInterest();
			$bankInterest = $bankInterestManager->getInterest($bankInterestId);

			if(!empty($bankInterest)) {

				if(($bankInterest->getRefno() == $customerReferenceNumber)
					&& ($bankInterest->getPolicyNumber() == $policyNumber)) {

					//Delete the quote.
					$bankInterestManager->removeInterest($bankInterestId);

					//Retrieve all the remaining bank interests for display on the dialog.
					$bankInterestArray = $bankInterestManager->getAllInterests($policyNumber, $customerReferenceNumber);
					$model = array();
					if(!empty($bankInterestArray)) {

						foreach($bankInterestArray as $bankInterest) {

							$model[] = array('bankInterest' => $bankInterest);
						}
					}


					//Update the dialog.
					$return['html'] = $this->view->partialLoop('partials/bank-interest-list.phtml', $model);
					$return['success'] = true;
				}
			}
			else {

				//This method has been called when there are no bank interests to delete. Can happen on ajax
				//calls, so accommodate this.
				$return['success'] = true;
			}
		}

		if(!$return['success']) {

			$return['errors'] = 'Failed to remove the bank interest';
		}

		echo Zend_Json::encode($return);
	}

	public function addPreviousClaimAction() {

		$session = new Zend_Session_Namespace('landlords_insurance_quote');
        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
		$customerReferenceNumber = $quoteManager->getLegacyCustomerReference();

		$request = $this->getRequest();
		$postData = $request->getPost();

		$return = array();
		$return['success'] = false;

		$ajaxForm = new LandlordsInsuranceQuote_Form_Subforms_ClaimsDialog();
		if($ajaxForm->isValid($postData)) {

			$previousClaim = new Model_Insurance_PreviousClaim();
			$previousClaim->setRefno($customerReferenceNumber);

			//Record the previous claim type.
			$claimsManager = new Manager_Insurance_PreviousClaims();
			$claimTypesArray = $claimsManager->getPreviousClaimTypes(Model_Insurance_ProductNames::LANDLORDSPLUS);
			foreach($claimTypesArray as $claimType) {

				if($claimType->getClaimTypeID() == $postData['claim_type']) {

					$previousClaim->setClaimType($claimType);
					break;
				}
			}

			//Record the remaining details.
			$previousClaim->setClaimMonth($postData['claim_month']);
			$previousClaim->setClaimYear($postData['claim_year']);

            // Filter to mirror the subform element definitions.
            $currencyFilterElements = array(
                'claim_value'
            );
            foreach($currencyFilterElements as $filterElement) {
                if (isset($postData[$filterElement])) {
                    $postData[$filterElement] = preg_replace(
                        array('/[^\d\.]/'),
                        array(''),
                        $postData[$filterElement]
                    );
                }
            }

			$claimValue = new Zend_Currency(
				array(
					'value' => $postData['claim_value'],
					'precision' => 0
				));
			$previousClaim->setClaimValue($claimValue);

			//Store the previous claim if not already done so.
			if(!$claimsManager->getIsPreviousClaimAlreadyStored($previousClaim)) {

				$claimsManager->insertPreviousClaim($previousClaim);
			}

			$claimsArray = $claimsManager->getPreviousClaims($customerReferenceNumber);

			$model = array();
			foreach($claimsArray as $claim) {

				$model[] = array('claim' => $claim);
			}

			$return['html'] = $this->view->partialLoop('partials/claims-list.phtml', $model);
			$return['success'] = true;
		}
		else {

			foreach ($ajaxForm->getMessages() as $error) {

				$return['errors'] = $error;
			}
		}

		echo Zend_Json::encode($return);
	}


	public function removePreviousClaimAction() {

		$request = $this->getRequest();
		$postData = $request->getPost();
		$refNo = $postData['refNo'];
		$month = $postData['month'];
		$year = $postData['year'];
		$value = $postData['value'];
		$typeId = $postData['typeId'];


		//Remove the previous claim identified by the id
		if(!empty($refNo)) {

			$claimsManager = new Manager_Insurance_PreviousClaims();

			//Build a PreviousClaim object to pass to the PreviousClaims manager
			//to delete.
			$previousClaim = new Model_Insurance_PreviousClaim();
			$previousClaim->setRefno($refNo);
			$previousClaim->setClaimMonth($month);
			$previousClaim->setClaimYear($year);
			$value = new Zend_Currency(
				array(
					'value' => $value,
					'precision' => 2
				)
			);
			$previousClaim->setClaimValue($value);

			$previousClaimTypes = $claimsManager->getPreviousClaimTypes();
			foreach($previousClaimTypes as $claimType) {

				if($typeId == $claimType->getClaimTypeID()) {

					$previousClaim->setClaimType($claimType);
					break;
				}
			}

			//Finally, remove the previous claim.
			$claimsManager->removePreviousClaim($previousClaim);
		}

		$claimsArray = $claimsManager->getPreviousClaims($refNo);

		$model = array();
		if(!empty($claimsArray)) {

			foreach($claimsArray as $claim) {

				$model[] = array('claim' => $claim);
			}
		}

		$return = array();
		$return['html'] = $this->view->partialLoop('partials/claims-list.phtml', $model);
		$return['success'] = true;
		echo Zend_Json::encode($return);
	}

	/**
     * Save quote function
     */
    public function saveAction() {
		if ($this->getRequest()->isPost()) {

			$password1 = $this->getRequest()->getPost('password1');
			$password2 = $this->getRequest()->getPost('password2');

			if ($password1 != $password2) {
				$return['status']='error';
				$return['errorMessage']='Passwords do not match. Please re-enter';
			} else {
				$customerManager = new Manager_Core_Customer();
				$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
				$legacyCustomerReference = $pageSession->customerRefNo;

				// This will create a customer record as we don't currently have one (only a legacy one)
				$customerID = $customerManager->linkLegacyToNew($legacyCustomerReference, null, Model_Core_Customer::CUSTOMER);

				// Now we need to retreive the newly created customer and update the password
				$customer = $customerManager->getCustomer(Model_Core_Customer::IDENTIFIER, $customerID);
				$customer->setPassword($password1);
				$customerManager->updateCustomer($customer);

				// Email the new customer to give them their details
				$metaData = array(
					'name' => $customer->getFirstName(),
					'quoteNumber'	=>	$pageSession->PolicyNumber
				);

				// Log the customer in
				$auth = Zend_Auth::getInstance();
				$auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

				$adapter = $customerManager->getAuthAdapter(array(
					'password'	=>	$this->getRequest()->getPost('password1'),
					'email' 	=>	$customer->getEmailAddress()
				));

				$result = $auth->authenticate($adapter);
				if (!$result->isValid()) {
					// This really shouldn't ever happen as we've just created the customer!!
				} else {
                    $storage = $auth->getStorage();
                    $storage->write($adapter->getResultRowObject(array(
                        'title',
                        'first_name',
                        'last_name',
						'email_address',
						'id')));
                }

				$emailer = new Application_Core_Mail();
				$emailer->setTo($customer->getEmailAddress(), $customer->getFirstName() . ' ' . $customer->getLastName())
				        ->setSubject('Homelet - Saved Landlords Contents Insurance Quote')
			            ->applyTemplateWithoutFooter('landlordsinsurancequote_saved', $metaData);
				$emailer->send();

				$return['status']='saved';
			}
			echo Zend_Json::encode($return);
		}
	}

    /**
     * Validate a form page's values via AJAX
     *
     * @return void
     */
    public function validatePageAction() {

		$return = array();

        $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');

        $postData = $this->getRequest()->getParams();
		$page = $postData['page'];
		if ($page == '') return;
		
        switch($page) {

            case '1':
                $ajaxForm = new LandlordsInsuranceQuote_Form_Step1();


				if (isset($postData['through_letting_agent']) && $postData['through_letting_agent']=='yes') {
					$subForm->getElement('letting_agent_name')->setRequired(true);
					$subForm->getElement('letting_agent_town')->setRequired(true);
					$subForm->getElement('letting_agent_asn')->setRequired(true);
				}

                break;
            case '2':
                $ajaxForm = new LandlordsInsuranceQuote_Form_Step2();

				$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
				if(isset($pageSession->quoteID))
				{
					$quoteID = $pageSession->quoteID;
					$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($quoteID);
					$premiums = $quoteManager->calculatePremiums();

                    // Filter to mirror the subform element definitions.
                    $currencyFilterElements = array(
                        'building_value',
                        'contents_amount'
                    );
                    foreach($currencyFilterElements as $filterElement) {
                        if (isset($postData[$filterElement])) {
                            $postData[$filterElement] = preg_replace(
                                array('/\..+$/', '/\D/'),
                                array('', ''),
                                $postData[$filterElement]
                            );
                        }
                    }

					// Update the buildings insurance selection for the quote
					if (isset($postData['need_building_insurance']) && $postData['need_building_insurance']=='yes'
						&& $postData['building_built'] != ''
						&& $postData['building_bedrooms'] != ''
						&& $postData['building_type'] != '') {
						// Add buildings insurance
						$quoteManager->addBuildingsCover(
							$postData['building_built'],
							$postData['building_bedrooms'],
							$postData['building_type'],
							$postData['building_insurance_excess'],
							isset($postData['building_accidental_damage'])?$postData['building_accidental_damage']:0,
							$postData['building_value']!=''?$postData['building_value']:0);

						// If we've calculated premiums but not got a dsi value - send a request back to front end for a
						// manual value
                        if ($postData['override_dsi'] == '1' || $quoteManager->calculateDSI() == 0) {
							// We've not got a DSI Value - so we need to make the building value a required field
							$ajaxForm->getSubForm('subform_buildinginsurance')->getElement('building_value')->setRequired(true);
							$return['rebuildValueRequired'] = true;
						} else {
							$return['rebuildValueRequired'] = false;

                            // Remove, re-add and recalculate buildings cover from scratch with given building value forcibly set to zero
                            if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER)) {
                                $quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER);
                                $quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::EMERGENCY_ASSISTANCE);
                            }

                            $quoteManager->addBuildingsCover(
                                $postData['building_built'],
                                $postData['building_bedrooms'],
                                $postData['building_type'],
                                $postData['building_insurance_excess'],
                                isset($postData['building_accidental_damage']) ? $postData['building_accidental_damage'] : 0,
                                0);
						}
					} else {
						if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER)) {
							$quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER);
							$quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::EMERGENCY_ASSISTANCE);
						}
					}


					// Update the contents insurance selection for the quote
					if (isset($postData['need_contents_insurance']) && $postData['need_contents_insurance']=='yes') {
						// Add contents insurance if they've chosen whether it's furnished or not
						if (isset($postData['property_furnished'])) {
							if ($postData['property_furnished']=='yes') {
								if (isset($postData['contents_accidental_damage']) &&
								    isset($postData['contents_amount']) &&
								    isset($postData['contents_excess']))
								{
									// Furnished property
									$quoteManager->addContentsCover(true,
										$postData['contents_accidental_damage'],
										$postData['contents_amount'],
										$postData['contents_excess']);
								}
							} elseif ($postData['property_furnished']=='no') {
								// Un-furnished property
								$quoteManager->addContentsCover(false);
							}
						}
					} else {
						$quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER);
						$quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::UNFURNISHED_CONTENTS_COVER);
					}

					if ($premiums != '') {
						// If this is a flood exclusion area - send that back so the page can display the messages
						if ($premiums['riskAreas']['floodArea']=='1000') {
							$return['floodExcluded'] = true;
						} else {
							$return['floodExcluded'] = false;
						}

						// If this is a subsidence exclusion area - send that back so the page can display the messages
						if ($premiums['riskAreas']['subsidenceArea']=='1000') {
							$return['subsidenceExcluded'] = true;
						} else {
							$return['subsidenceExcluded'] = false;
						}
					}
				}
                break;
            case '3':
                $ajaxForm = new LandlordsInsuranceQuote_Form_Step3();

				$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
				if(isset($pageSession->quoteID))
				{
					$quoteID = $pageSession->quoteID;
					$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($quoteID);

                    // Filter to mirror the subform element definitions.
                    $currencyFilterElements = array(
                        'rent_amount'
                    );
                    foreach($currencyFilterElements as $filterElement) {
                        if (isset($postData[$filterElement])) {
                            $postData[$filterElement] = preg_replace(
                                array('/\..+$/', '/\D/'),
                                array('', ''),
                                $postData[$filterElement]
                            );
                        }
                    }

					// Update the Emergency Assistance selection for the quote
					if (isset($postData['need_emergency_assistance'])) {
						if ($postData['need_emergency_assistance']=='yes') {
							$quoteManager->addEmergencyAssistance();
						} else {
							$quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::EMERGENCY_ASSISTANCE);
						}
					}

					// Update the Boiler & Heating selection for the quote
					if (isset($postData['need_boiler_heating'])) {
						if ($postData['need_boiler_heating']=='yes') {
							$quoteManager->addBoilerAndHeatingCover();
						} else {
							$quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::BOILER_HEATING);
						}
					}

					// Update the Prestiage Rent Guarantee for the quote
					if (isset($postData['need_prestige_rent_guarantee'])) {
						if ($postData['need_prestige_rent_guarantee']=='yes') {
							$quoteManager->addRentGuarantee($postData['rent_amount']);
						} else {
							$quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::RENT_GUARANTEE);
							// Also remove the free Legal Expenses or it causes a few "hiccups"
							$quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::LEGAL_EXPENSES);
						}
					} 
					// Update the Legal Expenses selection for the quote
					if (isset($postData['need_legal_expenses'])) {
						if ($postData['need_legal_expenses'] == 'yes') {
							$quoteManager->addLegalExpensesCover();
						} else {
							// Don't remove the legal expenses if they've just been added for free by the rent guarantee product ;)
							if (!$quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::RENT_GUARANTEE)) {
								$quoteManager->removeProduct(Manager_Insurance_LandlordsPlus_Quote::LEGAL_EXPENSES);
							}
						}
					}
				}
                break;
            case '4':
                $ajaxForm = new LandlordsInsuranceQuote_Form_Step4();

                break;
            case '5':
                $ajaxForm = new LandlordsInsuranceQuote_Form_Step5();

                break;

            default:
                return;
        }
        $valid = $ajaxForm->isValid($postData);

		// TODO: Needs to run the LI+ quote manager and calculate premiums so that they can be returned later on

		if (!$valid) {
			$errorMessages = $ajaxForm->getMessagesFlattened();
			// We need to strip out some complex messages that the end user won't care about
			if (isset($errorMessages['email_address'])) {
				if (isset($errorMessages['email_address']['hostnameUnknownTld'])) {
					unset($errorMessages['email_address']['hostnameUnknownTld']);
				};
				if (isset($errorMessages['email_address']['hostnameLocalNameNotAllowed'])) {
					unset($errorMessages['email_address']['hostnameLocalNameNotAllowed']);
				};
			}

			$return['errorJs'] = $errorMessages;
			$return['errorCount'] = count($errorMessages);
			$return['errorHtml'] = $this->view->partial('partials/error-list.phtml', array('errors' => $errorMessages));
		} else {
			$return['errorJs'] = '';
			$return['errorCount'] = '';
			$return['errorHtml'] = '';
		}

		// Calculate premiums
		$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
		if(isset($pageSession->quoteID))
		{
			$quoteID = $pageSession->quoteID;
			$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($quoteID);
			$return['premiums'] = $quoteManager->calculateQuickPremiums();
			// Do a little bit of nicer formatting for the front end (add commas to thousands etc..)
			$return['fees'] = $quoteManager->getFees();
		}
		
		echo Zend_Json::encode($return);
    }

	public function sendquoteAction() {
		$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
		$quoteID = $pageSession->quoteID;
		$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($quoteID);
		$postdata = $this->getRequest()->getPost();
		if (isset($postdata)) {
			// Find out how the customer wants their quote
			$sendBy = $postdata ['how_send'];
			$request = $this->getRequest();
			$quoteManager->sendQuote($quoteManager->getPolicyNumber(),$sendBy);
		}
	}
}

// @codeCoverageIgnoreEnd
?>
