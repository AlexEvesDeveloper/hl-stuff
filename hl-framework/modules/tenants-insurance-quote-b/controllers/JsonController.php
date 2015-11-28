<?php

// Can't be unit tested/code coverage'd as it's all designed to work over ajax
// @codeCoverageIgnoreStart

class TenantsInsuranceQuoteB_JsonController extends Zend_Controller_Action
{
	public function init() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		header('Content-type: application/json');
	}
	
	public function saveAction() {
		if ($this->getRequest()->isPost()) {
			
			$password1 = $this->getRequest()->getPost('password1');
			$password2 = $this->getRequest()->getPost('password2');
			
			if ($password1 != $password2) {
				$return['status']='error';
				$return['errorMessage']='Passwords do not match. Please re-enter';
			} else {
				$customerManager = new Manager_Core_Customer();
				$pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
				$legacyCustomerReference = $pageSession->CustomerRefNo;
				
				// This will create a customer record as we don't currently have one (only a legacy one)
				$customerID = $customerManager->linkLegacyToNew($legacyCustomerReference, null, Model_Core_Customer::TENANT);
				
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
				        ->setSubject('Homelet - Saved Tenants Contents Insurance Quote')
			            ->applyTemplateWithoutFooter('tenantsinsurancequote_saved', $metaData);
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
		
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
		
        $postData = $this->getRequest()->getParams();
		$page = $postData['page'];
		
        switch($page) {
            case '1':
                $ajaxForm = new TenantsInsuranceQuoteB_Form_Step1();
				
                break;
            case '2':
                $ajaxForm = new TenantsInsuranceQuoteB_Form_Step2();
				
                // Update unspecified possessions SI
                $quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null,null,$pageSession->PolicyNumber);
                $unspecPossessionsSI = preg_replace('/\/\d+/', '', $postData['possessions_cover']);
                $unspecPossessionsSI = (is_numeric($unspecPossessionsSI)) ? $unspecPossessionsSI : 0;
                $quoteManager->setCoverAmount($unspecPossessionsSI, Manager_Insurance_TenantsContentsPlus_Quote::UNSPECIFIEDPOSSESSIONS);
				
                break;
            case '3':
                $ajaxForm = new TenantsInsuranceQuoteB_Form_Step3();
				
                // Update unspecified possessions SI from upsell, if...
                $quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null,null,$pageSession->PolicyNumber);
                $unspecPossessionsSI = $quoteManager->getCoverAmount(Manager_Insurance_TenantsContentsPlus_Quote::UNSPECIFIEDPOSSESSIONS);
                if ($unspecPossessionsSI == 0) {
                    if (isset($postData['upsell']) && $postData['upsell'] == 'yes') {
                        $quoteManager->setCoverAmount(2000, Manager_Insurance_TenantsContentsPlus_Quote::UNSPECIFIEDPOSSESSIONS);
                    }
                } elseif ($unspecPossessionsSI == 2000) {
                    if (isset($postData['upsell']) && $postData['upsell'] == 'no') {
                        $quoteManager->setCoverAmount(0, Manager_Insurance_TenantsContentsPlus_Quote::UNSPECIFIEDPOSSESSIONS);
                    }
                }
				$quoteManager->setPropertyPostcode($postData['ins_postcode']);
				
				/*
					Display Minimum Security Protection wording on step3 page
					based on the property postcode
				*/
				$postcode = explode(' ',$postData['ins_postcode']); 
				$mspManager = new Manager_Insurance_MinimumSecurityProtection();		
				$msp = $mspManager->isHighRiskPostcode($postcode[0]);
				$return['msp'] = $msp;
				
                break;
            case '4':
                $ajaxForm = new TenantsInsuranceQuoteB_Form_Step4();
				
                break;
            case '5':
                $ajaxForm = new TenantsInsuranceQuoteB_Form_Step5();
				
                break;
            case 'dd':
                $ajaxForm = new TenantsInsuranceQuoteB_Form_DirectDebit();
				
                break;
            default:
                return;
        }
        $valid = $ajaxForm->isValid($postData);
		
		// Get the agent scheme number from the agentSchemeNumber if it is set
		$session = new Zend_Session_Namespace('homelet_global');
		$agentSchemeNumber = $session->agentSchemeNumber;
		if (isset($pageSession->CustomerRefNo)) {
			Application_Core_Logger::log('Customer Reference Number : ' . $pageSession->CustomerRefNo);
			$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote($pageSession->CustomerRefNo, $agentSchemeNumber,$pageSession->PolicyNumber);
		} else {
			// We don't have a customer record so we'll do a non-committal quick quote for now
			$quoteManager = new Manager_Insurance_TenantsContentsPlus_QuickQuote($agentSchemeNumber);
		}
		
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
				/*
				isset($errorMessages->hostnameUnknownTld) ||
				isset($errorMessages->hostnameLocalNameNotAllowed)) {
				die('foo');*/
			
			$return['errorJs'] = $errorMessages;
			$return['errorCount'] = count($errorMessages);
			$return['errorHtml'] = $this->view->partial('/partials/error-list.phtml', array('errors' => $errorMessages));
			$return['premiums'] = '';
			$return['fees'] = '';
			$return['postData'] = $postData;
			
			if ($page>1) {
				// On pages after the first quick quote we can show prices even when the form is invalid - I think!
				$premiums = $quoteManager->calculatePremiums();
				$fees = $quoteManager->getFees();
				
				$return['premiums'] = $premiums;
				$return['fees'] = $fees;
			}
		} else {
			$return['errorJs'] = '';
			$return['errorCount'] = '';
			$return['errorHtml'] = '';
			$return['postData'] = $postData;
			
			// Form was valid - so calculate prices and fee's to return
			
			// Calculate premiums
			if ($page == 1) {
				// Special processing for quick- and full-quotes' contents SI
				$input = $this->getRequest()->getPost();
				
				$cover = 0;
				if ($input['contents_cover_a'] != '') {
					if ($input['contents_cover_a'] == '15000+') {
						if ($input['contents_cover_b'] != '' && (int)$input['contents_cover_b'] > 15000) {
							$cover = (int)$input['contents_cover_b'];
						}
					} else {
						$cover = (int)$input['contents_cover_a'];
					}
				}
				if ($cover > 0) {
					$quoteManager->setCoverAmount($cover, Manager_Insurance_TenantsContentsPlus_Quote::CONTENTS);
					$premiums = $quoteManager->calculatePremiums();
				}
			}
			$premiums = $quoteManager->calculatePremiums();
			$fees = $quoteManager->getFees();
			
			$return['premiums'] = $premiums;
			$return['fees'] = $fees;
			
		}
		
		echo Zend_Json::encode($return);
    }
    

    /**
     * Find and return how many sharers are allowed based on contents SI via AJAX
     *
     * @return void
     */
    public function sharersAction() {
		// Get the contents amount from the post data
        $query = $this->getRequest()->getQuery();
		$contentsSI = $query['contents_cover_a'];
		if ($contentsSI == '15000+') { $contentsSI = $query['contents_cover_ab']; }
		
		// Calculate number of sharers allowed
		$sharersManager = new Manager_Insurance_TenantsContentsPlus_Sharers();
		$contentsAmount = new Zend_Currency(array('value' => $contentsSI, 'precision' => 0));
		$sharersAllowed = $sharersManager->getNoOfSharersAllowed($contentsAmount);
		
        // Return the result
        $return['sharersAllowed'] = $sharersAllowed;
		echo Zend_Json::encode($return);
    }
    
    /**
     * Validate and handle adding and removing bikes via AJAX
     *
     * @return void
     */
    public function bicycleAction() {
        $output = array();
		
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        $bike = new Datasource_Insurance_Policy_Cycles($pageSession->CustomerRefNo, $pageSession->PolicyNumber);
		
        $ajaxForm = new TenantsInsuranceQuoteB_Form_Json_Bicycle();
		
        // Ignore 'bicycle' field for AJAX requests
        $ajaxForm->subform_bicycle->getElement('bicycle')->setRequired(false);
		
        $request = $this->getRequest();
		$postdata = $request->getPost();
		
		// Force the elements to be required as this is an add item click - ugly :(
        if (isset($postdata['addBike']) && $postdata['addBike'] == 1) {
            $ajaxForm->subform_bicycle->getElement('bicycle_make')->setRequired(true);
            $ajaxForm->subform_bicycle->getElement('bicycle_model')->setRequired(true);
            $ajaxForm->subform_bicycle->getElement('bicycle_serial')->setRequired(false);
            $ajaxForm->subform_bicycle->getElement('bicycle_value')->setRequired(true);
        }
        
		$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null,null,$pageSession->PolicyNumber);
		
        if ($ajaxForm->isValid($postdata)) {
            // Check if a new bike's details are being added and bicycles is below max
            if (isset($postdata['addBike']) && $postdata['addBike'] == 1) {
                $cleanData = $ajaxForm->getValues();
                $bike->addNew($cleanData['subform_bicycle']);
            }
			
            // Check if an existing bike's details are being removed
            if (isset($postdata['removeBike']) && $postdata['removeBike'] == 1) {
                $bike->remove($postdata['bikeNum']);
            }
			
			$totalValue = $bike->getTotalValue();
			
			// Now we need to update the total amounts covered in the quote manager
			$quoteManager->setCoverAmount($totalValue, Manager_Insurance_TenantsContentsPlus_Quote::PEDALCYCLES);
        }
		
        $errorMessages = $ajaxForm->getMessagesFlattened();
        $output['errorJs'] = $errorMessages;
		$output['errorCount'] = count($errorMessages);
        $output['errorHtml'] = $this->view->partial('partials/error-list.phtml', array('errors' => $errorMessages));
		
        // Tell page if max bikes reached
        $output['disableAdd'] = ($bike->countBikes() == $bike->maxBicycles) ? 1 : 0;
		
        $output['html'] = $this->view->yourBicycle();
		
		$premiums = $quoteManager->calculatePremiums();
        $fees = $quoteManager->getFees();
		
		$output['premiums'] = $premiums;
		$output['fees'] = $fees;
		echo Zend_Json::encode($output);
    }

    /**
     * Validate and handle adding and removing possessions via AJAX
     *
     * @return void
     */
    public function possessionAction() {
        $output = array();
		
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
		
		// TODO: This needs to stop using datasources directly and use the quote manager
		
		// Add the bikes details to the database
        $possession = new Datasource_Insurance_Policy_SpecPossessions($pageSession->PolicyNumber);
        $ajaxForm = new TenantsInsuranceQuoteB_Form_Json_Possessions();
		
        // Ignore 'away_from_home' and 'above_x' fields for AJAX requests
        $ajaxForm->subform_possessions->getElement('away_from_home')->setRequired(false);
	$ajaxForm->subform_possessions->getElement('above_x')->setRequired(false);
		
        $request = $this->getRequest();
        $postdata = $request->getPost();
		
        // Force the elements to be required as this is an add item click - ugly :(
		if (isset($postdata['addPossession']) && $postdata['addPossession'] == 1) {
			$ajaxForm->subform_possessions->getElement('possession_categoryId')->setRequired(true);
			$ajaxForm->subform_possessions->getElement('possession_description')->setRequired(true);
			$ajaxForm->subform_possessions->getElement('possession_value')->setRequired(true);
		}
		
        $ajaxForm->populate($postdata);
		
        $quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $pageSession->PolicyNumber);
		if ($ajaxForm->isValid($postdata)) {
            // Check if a new possession's details are being added and possessions is below max
			if (isset($postdata['addPossession']) && $postdata['addPossession'] == 1) {
				$cleanData = $ajaxForm->getValues(); // According to the Zend manual these *should* be the clean values
                $possession->addNew($cleanData['subform_possessions']);
			}
			
            // Check if an existing possession's details are being removed
            if (isset($postdata['removePossession']) && $postdata['removePossession'] == 1) {
                $possession->remove($postdata['possessionNum']);
            }
			
			$totalValue = $possession->getTotalValue();
			
			// Now we need to update the total amounts covered in the quote manager
			$quoteManager->setCoverAmount($totalValue, Manager_Insurance_TenantsContentsPlus_Quote::SPECIFIEDPOSSESSIONS);
        }
		
        $errorMessages = $ajaxForm->getMessagesFlattened();
        $output['errorJs'] = $errorMessages;
		$output['errorCount'] = count($errorMessages);
        $output['errorHtml'] = $this->view->partial('partials/error-list.phtml', array('errors' => $errorMessages));
		
        // Tell page if max possessions reached
        $output['disableAdd'] = ($possession->countPossessions() == $possession->maxPossessions) ? 1 : 0;
		
        $output['html'] = $this->view->getHelper('yourPossessions')->yourPossessions();
		
		$premiums = $quoteManager->calculatePremiums();
        $fees = $quoteManager->getFees();
		
		$output['premiums'] = $premiums;
		$output['fees'] = $fees;
		
        echo Zend_Json::encode($output);
    }

    /**
     * Validate and handle adding and removing claims via AJAX
     *
     * @return void
     */
    public function claimAction() {
        $output = array();

        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');

        $ajaxForm = new TenantsInsuranceQuoteB_Form_Json_Claims();

        $request = $this->getRequest();
        $postdata = $request->getPost();

        $ajaxForm->populate($postdata);

        if ($ajaxForm->isValid($postdata)) {
            // Invoke previous claims manager
            $claimsManager = new Manager_Insurance_PreviousClaims();

            // Check if a new claim's details are being added
            if (isset($postdata['addClaim']) && $postdata['addClaim'] == 1) {
                $cleanData = $ajaxForm->getValues();

                $claim = new Model_Insurance_PreviousClaim();
                $claim->setRefno($pageSession->CustomerRefNo);
                
                $claimType = new Model_Insurance_PreviousClaimType();
                $claimType->setClaimTypeID($cleanData['subform_importantinformation']['claim_type']);
                $claim->setClaimType($claimType);
                
                $claim->setClaimMonth($cleanData['subform_importantinformation']['claim_month']);
                $claim->setClaimYear($cleanData['subform_importantinformation']['claim_year']);
                $claim->setClaimValue(new Zend_Currency(array('value' => $cleanData['subform_importantinformation']['claim_value'])));

                $claimsManager->insertPreviousClaim($claim);
            }

            // Check if an existing claim's details are being removed
            if (isset($postdata['removeClaim']) && $postdata['removeClaim'] == 1) {
                // First fetch all claims
                $allClaims = $claimsManager->getPreviousClaims($pageSession->CustomerRefNo);
                // Now iterate through and remove the selected index
                $t_allClaims = array();
                foreach($allClaims as $key => $claim) {
                    if ($key != $postdata['claimNum']) {
                        $t_allClaims[] = $claim;
                    }
                }
                // Delete all previous claims and replace with new list
                $claimsManager->removeAllPreviousClaims($pageSession->CustomerRefNo);
                $claimsManager->insertPreviousClaims($t_allClaims);
            }

        } else {

        }

        $errorMessages = $ajaxForm->getMessagesFlattened();
        $output['errorJs'] = $errorMessages;
		$output['errorCount'] = count($errorMessages);
        $output['errorHtml'] = $this->view->partial('partials/error-list.phtml', array('errors' => $errorMessages));
        $output['html'] = $this->view->getHelper('yourClaims')->yourClaims();

        echo Zend_Json::encode($output);
    }
	
	public function sendquoteAction() {
		$pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
		$postdata = $this->getRequest()->getPost();
		if (isset($postdata)) {
		$sendBy = $postdata ['how_send'];
			$request = $this->getRequest();
			$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null,null,$pageSession->PolicyNumber);
			$quoteManager->sendQuote($pageSession->PolicyNumber,$sendBy);
		}
	}
}

// @codeCoverageIgnoreEnd
?>
