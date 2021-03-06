<?php

class TenantsInsuranceQuoteB_IndexController extends Zend_Controller_Action {

    private $_stepMax = 5; // Number of form steps, excluding special pages like payment screens
	private $_customerReferenceNumber;
	private $_policyNumber;
	private $_agentSchemeNumber;

	private $_webLeadSummaryId;

	public function init() {
		
		$session = new Zend_Session_Namespace('homelet_global');

		$baseURL = 'http';
		if (isset($_SERVER["HTTPS"])) {
			if ($_SERVER["HTTPS"] == "on") {$baseURL .= "s";}
		}
		$baseURL .= "://";
		$baseURL .= $_SERVER["SERVER_NAME"];

		// Bit of a dirty hack to use a layout from the CMS module
		$layout = Zend_Layout::getMvcInstance();                                
                $layout->setLayoutPath(APPLICATION_PATH . '/modules/cms/layouts/scripts');
                $layout->setLayout('default');
        
		$this->view->headScript()->appendFile('/assets/tenants-insurance-quote-b/js/tenants_form.js');
		$this->view->pageTitle = 'Tenants Insurance Quote';
		$this->url = trim($this->getRequest()->getRequestUri(),'/');

		$menuData = array(
			'selected'  => 'tenants',
			'url'       => $this->url
		);

		// Check to see if we have a referrer code - if we do store it in a session variable
		if ($this->getRequest()->getParam('referrer')!='') {
			$session->referrer = $this->getRequest()->getParam('referrer');
		} elseif(!isset($session->referrer)) {
			// no passed parameter so default
			$session->referrer ="direct";
		}

		// Check to see if we have a referrer code - if we do store it in a session variable
		if ($this->getRequest()->getParam('asn')!='') {
			$session->agentSchemeNumber = $this->getRequest()->getParam('asn');
		} elseif(!isset($session->agentSchemeNumber)) {
			// no passed parameter so default
			$params = Zend_Registry::get('params');
			$session->agentSchemeNumber = $params->homelet->defaultAgent;
		}

		// Check to see if we have a csuid - if we do store it in a session variable - Sorry Phil
		if ($this->getRequest()->getParam('csu')!='') {
			$session->csu = $this->getRequest()->getParam('csu');
		} elseif(!isset($session->csu)) {
			// no passed parameter so default it to our web user 87
			$session->csu ="87";
		}

		// This could be quite yucky - I'm just trying to work out how to get the menu structure to work
		$mainMenu = $this->view->partial('partials/homelet-mainmenu.phtml', 'cms', $menuData);
		$subMenu = $this->view->partial('partials/homelet-submenu.phtml', 'cms', $menuData);
		$layout = Zend_Layout::getMvcInstance();
		$layout->getView()->mainMenu = $mainMenu;
		$layout->getView()->subMenu = $subMenu;
		if (isset($menuData['selected'])) { $layout->getView()->styling = $menuData['selected']; }

		// Load the site link urls from the parameters and push them into the layout
		$params = Zend_Registry::get('params');
		$layout->getView()->urls = $params->url->toArray();

		// Load session data into private variables
		$pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
		if(isset($pageSession->CustomerRefNo)) $this->_customerReferenceNumber = $pageSession->CustomerRefNo;
		if(isset($pageSession->PolicyNumber)) $this->_policyNumber = $pageSession->PolicyNumber;
		if(isset($pageSession->webLeadSummaryId)) $this->_webLeadSummaryId = $pageSession->webLeadSummaryId;

		$session = new Zend_Session_Namespace('homelet_global');
	}
	
	/**
	 * Called from an 'unsubscribe' link in the tenant mailer.
	 *
	 * Unsubscribes a potential customer from marketing by phone/post and sms/email.
	 */
	public function unsubscribeAction() {
		
		$request = $this->getRequest();
		$refNo = $request->getParam('refno');
		$token = $request->getParam('token');

		
		//Retrieve the secure passcode so that we can ensure the token passed in
		//is from a legitmate source.
		$params = Zend_Registry::get('params');
		$passcode = $params->security->genericRefSecurityString;
		$generatingString = "refno=$refNo&passcode=$passcode";
		
		//Check to ensure this is a legitimate request:
		if(sha1($generatingString) === $token) {
			
			$subscription = new Datasource_ReferencingLegacy_DataProtections();
			$subscription->unsubscribe($refNo);
			$this->view->message = 'Unsubscribe Successful';
            $this->view->messageExtra = 'You\'ve been successfully ' .
                'unsubscribed from our email communications.<br />If you did ' .
                'this in error, you can re-subscribe by emailing us at ' .
                '<a href="mailto:hello@homelet.co.uk">hello@homelet.co.uk</a>';
		}
		else {

			$this->view->message = 'Invalid reference number provided';
            $this->view->messageExtra = 'You have not been successfully ' .
                'unsubscribed from our email communications.<br />If you ' .
                'continue to get this error, you can unsubscribe by emailing ' .
                'us at ' .
                '<a href="mailto:hello@homelet.co.uk">hello@homelet.co.uk</a>';
		}
	}

	/**
	 * Resume an existing quote for authenticated customers. Customers
     * who are not authenticated are redirected to the login page.
	 *
	 * @return void
	 */
	public function retrieveAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        if ($auth->hasIdentity()) {
            // Check to see if we have a reference number to load up
            if ($this->getRequest()->getParam('quote') != '') {
                $quoteNumber = $this->getRequest()->getParam('quote');

                // Customer is logged in and is trying to retrieve a specific quote
                // We need to check to make sure they own it
                $customerID = $auth->getStorage()->read()->id;

                // Get customers legacy IDs and confirm the refno of the quote
                // can be accessed by the customer.
                $legacyCustomerMap = new Datasource_Core_CustomerMaps();
                $legacyIDs = $legacyCustomerMap->getLegacyIDs($customerID);

                $quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $quoteNumber);
                $quote = $quoteManager->getQuoteObject();

                if (in_array($quote->refNo, $legacyIDs)) {
                    // This customer does own this reference - so set the page session stuff up and redirect
                    if ($quote->policyType == 'T') {
                        // Make sure this is a tenants quote
                        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
                        $pageSession->CustomerRefNo = $quote->refNo;
                        $pageSession->PolicyNumber = $quote->policyNumber;

                        //Retrieve the WebLead summary ID so that the WebLead can continue to be updated and important
                        //details captured, such as the campaign code.
                        $webLeadManager = new Manager_Core_WebLead();
                        $pageSession->webLeadSummaryId = $webLeadManager->getSummaryId($pageSession->PolicyNumber);

                        $this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/step1');
                    }
                    else {
                        // This isn't a tenants quote! OOPS
                        $this->render('retrieve-failed');
                        return;
                    }
                }
            }
        }

        $this->_helper->redirector->gotoUrl('/login?referrerUrl=/my-homelet/quotes');
	}

	/**
	 * Quick function that clears all cookies and re-directs to step 1. Used to force a new quote
	 *
	 * @params void
	 * @return void
	 */
	public function newAction() {
		Zend_Session::namespaceUnset('tenants_insurance_quote');
		$this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/step1');
	}

    private function saveStep1(Zend_Form $pageForm)
    {
        // Get sub forms
        $subFormPersonalDetails = $pageForm->getSubForm('subform_personaldetails');
        $subFormDataProtection = $pageForm->getSubForm('subform_dataprotection');
        $subFormContentsInsurance = $pageForm->getSubForm('subform_contentsinsurance');
        $subFormSharers = $pageForm->getSubForm('subform_sharers');

        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        $customerManager = new Manager_Core_Customer();

        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        // TODO: This should check reference number exists, not just property!
        if(!isset($this->_customerReferenceNumber)) {
            // Do we already have a legacy customer with this email address?
            $customer = $customerManager->getCustomerByEmailAddress($subFormPersonalDetails->getElement('email_address')->getValue());

            if (!$customer) {
                // We don't have a session so we need to create a legacy customer/quote entry to save against
                // All new customers are now of type Model_Core_Customer::CUSTOMER
                $customer = $customerManager->createNewCustomer($subFormPersonalDetails->getElement('email_address')->getValue(), Model_Core_Customer::CUSTOMER);
                $legacyCustomer = $customer;

                if (!isset($pageSession->IsNewCustomer)) {
                    $pageSession->IsNewCustomer = true;
                }
            }
            else {
                if (!isset($pageSession->IsNewCustomer)) {
                    $pageSession->IsNewCustomer = false;
                }

                $legacyCustomer = $customerManager->createNewCustomer($subFormPersonalDetails->getElement('email_address')->getValue(), Model_Core_Customer::CUSTOMER, true);
            }

            $customerManager->linkLegacyToNew($legacyCustomer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER), $customer->getIdentifier(Model_Core_Customer::IDENTIFIER));


            // Now get the reference number from the newly created customer
            $customerRefNo = $customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER);

            $this->_customerReferenceNumber = $customerRefNo;
            $pageSession->CustomerRefNo = $customerRefNo;
            $pageSession->CustomerID = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);
        }
        else {
            // We are in session so just instantiate the customer manager with the existing reference number
            $customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $this->_customerReferenceNumber);
            $customerRefNo = $pageSession->CustomerRefNo;
            $pageSession->CustomerID = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);

            if (!isset($pageSession->IsNewCustomer)) {
                $pageSession->IsNewCustomer = false;
            }
        }

        $pageSession->CustomerEmail = $subFormPersonalDetails->getElement('email_address')->getValue();
        $pageSession->CustomerDob = $subFormPersonalDetails->getElement('date_of_birth_at')->getValue();


        if (!$auth->hasIdentity()) {
            // Update the customer record with the form data, but only if they are a new customer
            // Existing customers must phone us.
            // Note that no message stating this is given to the end user
            if ($subFormPersonalDetails->getElement('title')->getValue() != "Other") {
                $customer->setTitle($subFormPersonalDetails->getElement('title')->getValue());
            }
            else {
                $customer->setTitle($subFormPersonalDetails->getElement('other_title')->getValue());
            }

            $customer->setFirstName($subFormPersonalDetails->getElement('first_name')->getValue());
            $customer->setLastName($subFormPersonalDetails->getElement('last_name')->getValue());
            $customer->setTelephone(Model_Core_Customer::TELEPHONE1, $subFormPersonalDetails->getElement('phone_number')->getValue());
            $customer->setTelephone(Model_Core_Customer::TELEPHONE2, $subFormPersonalDetails->getElement('mobile_number')->getValue());
            $customer->setEmailAddress($subFormPersonalDetails->getElement('email_address')->getValue());
            $customer->setDateOfBirthAt(
                Application_Core_Utilities::ukDateToMysql(
                    $subFormPersonalDetails->getElement('date_of_birth_at')->getValue()
                )
            );
            $customer->setIsForeignAddress(false);
        }

        // TODO: Make this all better...
        $customerManager->updateCustomer($customer);

        // Customer is logged in and starting a new quote - so we need to pre-populate the customers details from stored details
        $customerID = $auth->getStorage()->read()->id;
        $customerManager->linkLegacyToNew($customerRefNo, $customerID);

        // See if we have an agent scheme number, if so use it
        $session = new Zend_Session_Namespace('homelet_global');
        $agentSchemeNumber = $session->agentSchemeNumber;

        // Instantiate the quote manager - if this is a new customer it will automatically create a new quote
        // otherwise it will use the existing quote
        $quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote($customerRefNo, $agentSchemeNumber,$pageSession->PolicyNumber);

        // Set the contents SI - this does an automatic save
        if (is_numeric($subFormContentsInsurance->getElement('contents_cover_a')->getValue())) {
            $contentsSI = $subFormContentsInsurance->getElement('contents_cover_a')->getValue();
        }
        else {
            $contentsSI = $subFormContentsInsurance->getElement('contents_cover_b')->getValue();
        }

        $quoteManager->setCoverAmount($contentsSI, Manager_Insurance_TenantsContentsPlus_Quote::CONTENTS);

        // Grab the policy number for the quote and save it to the session
        $policyNumber = $quoteManager->getPolicyNumber();
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        $pageSession->PolicyNumber = $policyNumber;
        $this->_policyNumber = $policyNumber;

        // Set the contact preferences
        $contactPreferences = new Model_Core_CustomerContactPreferences();
        $contactPreferences->addPreference(Model_Core_CustomerContactPreferences::EMAIL_METHOD);
        $quoteManager->setContactPreference($contactPreferences);
        $quoteManager->setIssueDate(Zend_Date::now()->toString('YYYY-MM-dd'));

        //Save the sharer details.
        $contentsSICurrency = new Zend_Currency
        (
            array
            (
                'value' => $contentsSI,
                'precision' => 2
            ));

        $sharersManager = new Manager_Insurance_TenantsContentsPlus_Sharers();
        $noOfSharersAllowed = $sharersManager->getNoOfSharersAllowed($contentsSICurrency);
        switch($noOfSharersAllowed) {

            case 1:
                $sharers = new Model_Insurance_TenantsContentsPlus_Sharers($this->_policyNumber);
                $sharers->setSharerOccupation(
                    Model_Insurance_TenantsContentsPlus_Sharers::SHARER_01,
                    $subFormSharers->getElement('policy_sharer1_occupation')->getValue());
                $sharersManager->insertSharers($sharers);
                break;

            case 2:
                $sharers = new Model_Insurance_TenantsContentsPlus_Sharers($this->_policyNumber);
                $sharers->setSharerOccupation(
                    Model_Insurance_TenantsContentsPlus_Sharers::SHARER_01,
                    $subFormSharers->getElement('policy_sharer1_occupation')->getValue());
                $sharers->setSharerOccupation(
                    Model_Insurance_TenantsContentsPlus_Sharers::SHARER_02,
                    $subFormSharers->getElement('policy_sharer2_occupation')->getValue());
                $sharersManager->insertSharers($sharers);
                break;
        }

        //Record this WebLead, if not already done so. First create or
        //retrieve the WebLead summary.
        $webLeadManager = new Manager_Core_WebLead();
        if(empty($this->_webLeadSummaryId)) {

            $isNewWebLead = true;
            $webLeadSummary = $webLeadManager->createNewSummary();
        }
        else {

            $isNewWebLead = false;
            $webLeadSummary = $webLeadManager->getSummary($this->_webLeadSummaryId);
        }

        //Create or retrieve the step 1 blob.
        if(!$webLeadManager->getBlobExists($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP1)) {

            $webLeadBlob = $webLeadManager->createNewBlob(
                $webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP1);
        }
        else {

            $webLeadBlob = $webLeadManager->getBlob(
                $webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP1);
        }

        //Update the WebLead summary and store.
        $webLeadSummary->title = $subFormPersonalDetails->getElement('title')->getValue();
        $webLeadSummary->firstName = $subFormPersonalDetails->getElement('first_name')->getValue();
        $webLeadSummary->lastName = $subFormPersonalDetails->getElement('last_name')->getValue();

        if ($subFormPersonalDetails->getElement('phone_number')->getValue() != '') {
            $contactNumber = $subFormPersonalDetails->getElement('phone_number')->getValue();
        }
        else {
            $contactNumber = $subFormPersonalDetails->getElement('mobile_number')->getValue();
        }

        $webLeadSummary->contactNumber = $contactNumber;
        $webLeadSummary->emailAddress = $subFormPersonalDetails->getElement('email_address')->getValue();

        if($isNewWebLead) {

            $webLeadSummary->startTime = $this->_offsetDate();
        }

        $webLeadSummary->lastUpdatedTime = $this->_offsetDate();
        $webLeadSummary->product = Model_Core_WebLeadProduct::TCIPLUS;
        $webLeadSummary->quoteNumber = $pageSession->PolicyNumber;
        $webLeadManager->updateSummary($webLeadSummary);

        //Update the blob, serialize and store.
        $webLeadBlob->blob = Zend_Json::encode($_POST);
        $webLeadBlob->blobChecksum = crc32($webLeadBlob->blob);
        $webLeadManager->updateBlob($webLeadBlob);

        //Record the WebLead identifier in the page session.
        $pageSession->webLeadSummaryId = $webLeadSummary->webLeadSummaryId;


        //Capture and store the insurance and WebLead data protections.
        $this->_saveDPA(
            $customerRefNo,
            Model_Core_DataProtection_ItemEntityTypes::INSURANCE,
            $subFormDataProtection->getElement('dpa_phone_post')->getValue(),
            $subFormDataProtection->getElement('dpa_sms_email')->getValue(),
            $subFormDataProtection->getElement('dpa_resale')->getValue());

        $this->_saveDPA(
            $webLeadSummary->webLeadSummaryId,
            Model_Core_DataProtection_ItemEntityTypes::WEBLEAD,
            $subFormDataProtection->getElement('dpa_phone_post')->getValue(),
            $subFormDataProtection->getElement('dpa_sms_email')->getValue(),
            $subFormDataProtection->getElement('dpa_resale')->getValue());
    }

	/**
	 * Quick function that clears all cookies and re-directs to step 1. Used to force a new quote
	 *
	 * @params void
	 * @return void
	 */
	public function step1Action() {
		$pageForm = new TenantsInsuranceQuoteB_Form_Step1();

        // Check to see if we are already logged in
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

		// Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript
        (
            'var ajaxValidate = true; var ajaxValidatePage = 1;'
        );

		if ($this->getRequest()->isPost()) {
			// We need to validate and save the data
			$valid = $this->_formStepCommonValidate($pageForm, 1);
			if ($valid) {
				// Check to see if we have a session
				$customerManager = new Manager_Core_Customer();

                $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
                $subFormPersonalDetails = $pageForm->getSubForm('subform_personaldetails');
                $customer = $customerManager->getCustomerByEmailAddress($subFormPersonalDetails->getElement('email_address')->getValue());

                if ($pageSession->step1Email != $subFormPersonalDetails->getElement('email_address')->getValue()) {
                    // Email address has been changed by a new customer going backwards. Reset the IsNewCustomer trigger
                    $pageSession->IsNewCustomer = null;
                }

                if ((!$auth->hasIdentity() && $customer && $customer->getAccountLoadComplete() == true &&
                    $pageSession->IsNewCustomer !== true)) {
                    // Force redirection on to login page for existing customer

                    // Serialise form object to session to reload on successful login page
                    $pageSession->IsNewCustomer = false;
                    $pageSession->step1FormValues = $pageForm->getValues(true);
                    $pageSession->step1Email = $subFormPersonalDetails->getElement('email_address')->getValue();

                    $this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/login');
                }
                else {
                    // Save the data and continue to next step
                    $pageSession->IsNewCustomer = true;
                    $pageSession->step1Email = $subFormPersonalDetails->getElement('email_address')->getValue();

                    $this->saveStep1($pageForm);

                    // Everything has been saved ok so navigate to next step
                    $this->_formStepCommonNavigate(1);
                    return;
                }
			}
            elseif (isset($_POST['back'])) {
				$this->_formStepCommonNavigate(1);
				return;
			}
		}

		// Load the element data from the database if we can
		if ($this->_formStepCommonPopulate($pageForm, 1))
		{
			// Render the page unless we have been redirected
            $subFormPersonalDetails = $pageForm->getSubForm('subform_personaldetails');
            if ($auth->hasIdentity()) {
                // If logged in, prevent the email field from being modified
                $subFormPersonalDetails->setReadOnly();
            }

			$this->view->form = $pageForm;
			$this->render('step');
		}
	}

    /**
     * Login form for existing customers
     *
     * @return void
     */
    public function loginAction()
    {
        $form = new TenantsInsuranceQuoteB_Form_Login();
        $request = $this->getRequest();

        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        $pageForm = new TenantsInsuranceQuoteB_Form_Step1();
        $pageForm->populate($pageSession->step1FormValues);

        $subFormPersonalDetails = $pageForm->getSubForm('subform_personaldetails');
        $form->getElement('email')->setValue($subFormPersonalDetails->getElement('email_address')->getValue());

        if ($request->isPost()) {
            if ($form->isValid($_POST)) {
                // Save step1 form data and move to step 2

                // Null off all reference numbers that may have been set via a new customer who has gone
                // back and used an existing customer email address.
                $this->_customerReferenceNumber = null;
                $pageSession->CustomerRefNo = null;
                $this->_policyNumber = null;
                $pageSession->PolicyNumber = null;
                $this->_webLeadSummaryId = null;
                $pageSession->webLeadSummaryId = null;

                $this->saveStep1($pageForm);
                $this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/step2');
            }
        }

        $this->view->form = $form;
    }

	/**
	 * Initialise the step 2 form
	 *
	 * @return void
	 */
	public function step2Action() {
        $pageForm = new TenantsInsuranceQuoteB_Form_Step2();
        // Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 2;'
        );

		if ($this->getRequest()->isPost()) {
			$valid = $this->_formStepCommonValidate($pageForm, 2);

			if ($valid) {
				$data = $pageForm->getValues();

				//Update the WebLead summary and create a STEP2 blob.
				$webLeadManager = new Manager_Core_WebLead();
				$webLeadSummary = $webLeadManager->getSummary($this->_webLeadSummaryId);
				$webLeadSummary->lastUpdatedTime = $this->_offsetDate();
				$webLeadManager->updateSummary($webLeadSummary);

				//Determine if a new STEP2 blob needs to be created, or an existing one retrieved.
				if($webLeadManager->getBlobExists($this->_webLeadSummaryId, Model_Core_WebLeadStep::STEP2)) {
					$webLeadBlob = $webLeadManager->getBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP2);
				}
				else {
					$webLeadBlob = $webLeadManager->createNewBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP2);
				}

				//Update the blob and store
				$webLeadBlob->blob = Zend_Json::encode($_POST);
				$webLeadBlob->blobChecksum = crc32($webLeadBlob->blob);
				$webLeadManager->updateBlob($webLeadBlob);

				// Instantiate the quote manager
				$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber);

				// Update the unspecified possessions SI
				$unspecPossessionsSI = $data['subform_possessions']['possessions_cover'];
				$unspecPossessionsSI = (is_numeric($unspecPossessionsSI)) ? $unspecPossessionsSI : 0;
				$quoteManager->setCoverAmount($unspecPossessionsSI, Manager_Insurance_TenantsContentsPlus_Quote::UNSPECIFIEDPOSSESSIONS);

				// Everything has been saved ok so navigate to next step
				$this->_formStepCommonNavigate(2);
				return;
			} elseif (isset($_POST['back'])) {
				$this->_formStepCommonNavigate(2);
				return;
			}
		}

		// Load the element data from the database if we can
		if ($this->_formStepCommonPopulate($pageForm, 2))
		{
			// Render the page unless we have been redirected
			$this->view->form = $pageForm;
			$this->render('step');
		}
	}

	/**
	 * Initialise the step 3 form [Important Information Form]
	 *
	 * @return void
	 */
	public function step3Action() {

        $pageForm = new TenantsInsuranceQuoteB_Form_Step3();

        // Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 3;'
        );

        // Get customer details
        $customerManager = new Manager_Core_Customer();
        $customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $this->_customerReferenceNumber);

        // Hydrate registration form
        if (isset($pageForm->subform_register) || isset($pageForm->subform_login)) {
            // Grab a new customer to populate the form
            $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
            $newCust = $customerManager->getCustomer(Model_Core_Customer::IDENTIFIER, $pageSession->CustomerID);

            if (isset($pageForm->subform_register)) {
                if ($newCust) {
                    $pageForm->subform_register->email->setValue($newCust->getEmailAddress());
                    $pageForm->subform_register->security_question->setValue($newCust->getSecurityQuestion());
                    $pageForm->subform_register->security_answer->setValue($newCust->getSecurityAnswer());
                    $emailAddress = $newCust->getEmailAddress();
                }
                else {
                    $pageForm->subform_register->email->setValue($customer->getEmailAddress());
                    $emailAddress = $customer->getEmailAddress();
                }

                if (!$emailAddress) {
                    $emailAddress = $newCust->getEmailAddress();
                }
            }
            else {
                if ($newCust) {
                    $pageForm->subform_login->email->setValue($newCust->getEmailAddress());
                }
            }
        }

        if ($this->getRequest()->isPost()) {

			$valid = $this->_formStepCommonValidate($pageForm, 3);

            if (isset($pageForm->subform_register)) {
                $pageForm->subform_register->getElement('email')->setValue($emailAddress);
            }

			if ($valid) {
                $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
                $pageSession->IsNewCustomer = false;
				$data = $pageForm->getValues();

				//Update the WebLead summary and create a STEP3 blob.
				$webLeadManager = new Manager_Core_WebLead();
				$webLeadSummary = $webLeadManager->getSummary($this->_webLeadSummaryId);
				$webLeadSummary->lastUpdatedTime = $this->_offsetDate();
				$webLeadSummary->promotionCode = $data["subform_howhear"]['campaign_code'];
				$webLeadManager->updateSummary($webLeadSummary);

				//Determine if a new STEP3 blob needs to be created, or an existing one retrieved.
				if($webLeadManager->getBlobExists($this->_webLeadSummaryId, Model_Core_WebLeadStep::STEP3)) {

					$webLeadBlob = $webLeadManager->getBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP3);
				}
				else {

					$webLeadBlob = $webLeadManager->createNewBlob( $webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP3);
				}

				//Update the blob and store
				$webLeadBlob->blob = Zend_Json::encode($_POST);
				$webLeadBlob->blobChecksum = crc32($webLeadBlob->blob);
				$webLeadManager->updateBlob($webLeadBlob);

				// Instantiate the quote manager
				$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber); 
                            
				// Save new ASN if there is one

				// Create a postcode model
				$postcode = new Manager_Core_Postcode();
				// Get the address as array for Insured and correspondance address
				$insuredAddress = $postcode->getPropertyByID($data['subform_insuredaddress']['ins_address'], false);
				$correspondenceAddress = $postcode->getPropertyByID($data['subform_correspondencedetails']['cor_address'], false);

				// Update the property address in the quote
				$quoteManager->setPropertyAddress(
					(($insuredAddress['organisation'] != '') ? "{$insuredAddress['organisation']}, " : '')
					. (($insuredAddress['buildingName'] != '') ? "{$insuredAddress['buildingName']}, " : '')
				    . (($insuredAddress['houseNumber'] != '') ? "{$insuredAddress['houseNumber']} " : '')
                    .                   $insuredAddress['address2'],
					$insuredAddress['address4'],
					$insuredAddress['address5'],
					$insuredAddress['postcode']);

				// Update start and end dates
				$startDate = $data['subform_policydetails']['policy_start'];
				$startDate = substr($startDate, 6, 4) . '-' . substr($startDate, 3, 2) . '-' . substr($startDate, 0, 2);
				$endDate = date('Y-m-d', strtotime(date('Y-m-d', strtotime($startDate)) . ' +1 year -1 day'));
				$quoteManager->setStartAndEndDates($startDate, $endDate);

				//Update the customer in the DataStore and the LegacyDataStore. Use the CustomerManager
				//to do this.
				//$customerManager = new Manager_Core_Customer();

				//First get the existing customer details.
				//$customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $this->_customerReferenceNumber);

				//Now modify the details.
				$customer->setAddressLine(
                Model_Core_Customer::ADDRESSLINE1,
                (($correspondenceAddress['organisation'] != '') ? "{$correspondenceAddress['organisation']}, " : '')
                    . (($correspondenceAddress['houseNumber'] != '') ? "{$correspondenceAddress['houseNumber']} " : '')
                    . (($correspondenceAddress['buildingName'] != '') ? "{$correspondenceAddress['buildingName']}, " : '')
                    . $correspondenceAddress['address2']);
				$customer->setAddressLine(Model_Core_Customer::ADDRESSLINE2, $correspondenceAddress['address4']);
				$customer->setAddressLine(Model_Core_Customer::ADDRESSLINE3, $correspondenceAddress['address5']);
				$customer->setPostCode($correspondenceAddress['postcode']);
                $customer->setDateOfBirthAt(
                    Application_Core_Utilities::ukDateToMysql(
                        $pageSession->CustomerDob
                    )
                );

				//Finally, save the details back to both DataStores.
	                        $customerManager->updateCustomer($customer);
				$premiums = $quoteManager->calculatePremiums();

				// Save MI information - how did you hear about us
				$marketQuestion = new Manager_Core_ManagementInformation();
				$marketQuestion->saveMarketingAnswers($this->_policyNumber, $this->_customerReferenceNumber, $data["subform_howhear"]["how_hear"]);

                // Perform login/register procedure
                $auth = Zend_Auth::getInstance();
                $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

                if (isset($data['subform_register'])) {
                    // Process registration
                    $params = Zend_Registry::get('params');

                    $newCustomer = $customerManager->getCustomerByEmailAddress($data['subform_register']['email']);

                    if (!$newCustomer) {
                        $newCustomer = $customerManager->createCustomerFromLegacy($data['subform_register']['email'], $this->_customerReferenceNumber);
                    }

                    // Update customer with password and security data
                    $newCustomer->setTitle($customer->getTitle());
                    $newCustomer->setFirstName($customer->getFirstName());
                    $newCustomer->setLastName($customer->getLastName());

		            $newCustomer->setAddressLine(
                        Model_Core_Customer::ADDRESSLINE1,
                        (($correspondenceAddress['organisation'] != '') ? "{$correspondenceAddress['organisation']}, " : '')
                        . (($correspondenceAddress['houseNumber'] != '') ? "{$correspondenceAddress['houseNumber']} " : '')
                        . (($correspondenceAddress['buildingName'] != '') ? "{$correspondenceAddress['buildingName']}, " : '')
                        . $correspondenceAddress['address2']);
                    $newCustomer->setAddressLine(Model_Core_Customer::ADDRESSLINE2, $correspondenceAddress['address4']);
                    $newCustomer->setAddressLine(Model_Core_Customer::ADDRESSLINE3, $correspondenceAddress['address5']);
                    $newCustomer->setPostCode($correspondenceAddress['postcode']);
                    $newCustomer->setDateOfBirthAt(
                        Application_Core_Utilities::ukDateToMysql(
                            $pageSession->CustomerDob
                        )
                    );

//                    assuming that the email is already set and so won't require setting again.
//                    $newCustomer->setEmailAddress($data['subform_register']['email']);

                    $newCustomer->setSecurityQuestion($data['subform_register']['security_question']);
                    $newCustomer->setSecurityAnswer($data['subform_register']['security_answer']);
                    $newCustomer->setPassword($data['subform_register']['password']);
                    $newCustomer->setAccountLoadComplete(true);

                    $newCustomer->typeID = Model_Core_Customer::CUSTOMER;

                    $customerManager->updateCustomer($newCustomer);

                    // Create sign-up completion email
                    $mail = new Application_Core_Mail();
                    $mail->setTo($data['subform_register']['email'], null);
                    $mail->setFrom('hello@homelet.co.uk', 'HomeLet');
                    $mail->setSubject('My HomeLet account validation');

                    //  Generate activation link
                    $mac = new Application_Core_Security($params->myhomelet->activation_mac_secret, false);
                    $digest = $mac->generate(array('email' => $data['subform_register']['email']));

                    $activationLink = 'email=' . $data['subform_register']['email'] . '&'
                        . 'mac=' . $digest;

                    // Apply template
                    $mail->applyTemplate('core/account-validation',
                        array('activationLink' => $activationLink,
                            'homeletWebsite' => $params->homelet->domain,
                            'firstname'      => $newCustomer->getFirstName(),
                            'templateId'     => 'HL2442 12-12',
                            'heading'        => 'Validating your My HomeLet account'),
                        false,
                        '/email-branding/homelet/portal-footer.phtml',
                        '/email-branding/homelet/portal-header.phtml');

                    $mail->applyTextTemplate('core/account-validationtxt',
                        array('activationLink' => $activationLink,
                            'homeletWebsite' => $params->homelet->domain,
                            'firstname'      => $newCustomer->getFirstName(),
                            'templateId'     => 'HL2442 12-12',
                            'heading'        => 'Validating your My HomeLet account'),
                        false,
                        '/email-branding/homelet/portal-footer-txt.phtml',
                        '/email-branding/homelet/portal-header-txt.phtml');

                    // Send email
                    $mail->send();

                    // Everything has been saved ok so navigate to next step
                    $this->_formStepCommonNavigate(3);
                }
                elseif ($auth->hasIdentity()) {
                    $this->_formStepCommonNavigate(3);
                }

				//return;
			} elseif (isset($_POST['back'])) {
				$this->_formStepCommonNavigate(3);
				return;
			}
                                   
		}

		// Load the element data from the database if we can
		if ($this->_formStepCommonPopulate($pageForm, 3))
		{
			// Render the page unless we have been redirected
			$this->view->form = $pageForm;
			$this->render('step');
		}
	}

	/**
	 * Validates the underwriting state, then proceeds to the Step 4 form.
	 *
	 * Long desc.
	 *
	 * Populate this step with the underwriting
	 */
	public function step4Action() {	
        $pageForm = new TenantsInsuranceQuoteB_Form_Step4();
		// Tell page to use AJAX validation as we go
		$this->view->headScript()->appendScript(
			'var ajaxValidate = true; var ajaxValidatePage = 4;'
		);
		
		/*
			Moved this here as the validation and advance to next page was being processed before back
			therefore a back on a valid page went forward
		 */
		if (isset($_POST['back'])) {
				$this->_formStepCommonNavigate(4);
				return;
		}

		if ($this->getRequest()->isPost() ) {
			$valid = $this->_formStepCommonValidate($pageForm, 4);

			if ($valid) {
			    //Update the WebLead summary and create a STEP4 blob.
				$webLeadManager = new Manager_Core_WebLead();
				$webLeadSummary = $webLeadManager->getSummary($this->_webLeadSummaryId);
				$webLeadSummary->lastUpdatedTime = $this->_offsetDate();
				$webLeadManager->updateSummary($webLeadSummary);

				//Determine if a new STEP4 blob needs to be created, or an existing one retrieved.
				if($webLeadManager->getBlobExists($this->_webLeadSummaryId, Model_Core_WebLeadStep::STEP4)) {
					$webLeadBlob = $webLeadManager->getBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP4);
				}
				else {
					$webLeadBlob = $webLeadManager->createNewBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP4);
				}

				//Update the blob and store
				$webLeadBlob->blob = Zend_Json::encode($_POST);
				$webLeadBlob->blobChecksum = crc32($webLeadBlob->blob);
				$webLeadManager->updateBlob($webLeadBlob);

				//Specialized validation of the underwriting inputs.
				$proceed = $this->_processUnderwriting($pageForm);
				if(!$proceed) {
				    //If the quote has been referred, do nothing further.
					// Redirect the browser to the quote referred page
					$this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/referred');
				    return;
				}

				// Everything has been saved ok so navigate to next step
				$this->_formStepCommonNavigate(4);

				return;
			}
			}

		// Load the element data from the database if we can
		if ($this->_formStepCommonPopulate($pageForm, 4))
		{
			// Render the page unless we have been redirected
			$this->view->form = $pageForm;
			$this->render('step');
		}
	}

	/**
	 * Initialise the step 5 form
	 *
	 * @return void
	 */
	public function step5Action() {
        $pageForm = new TenantsInsuranceQuoteB_Form_Step5();
        // Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 5;'
        );

		if ($this->getRequest()->isPost()) {
			$valid = $this->_formStepCommonValidate($pageForm, 5);

			if ($valid) {
				// $feeData = new Model_Insurance_Fee();
				// $fees = new Manager_Insurance_Fee();
				// $feeData = $fees->fetchFeesByRateSetId(45);

				$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber);

				$data = $pageForm->getValues();

				$payBy = $data['subform_paymentselection']['payment_method'] == 'dd'?"DirectDebit":"CreditCard";
				$quoteManager->setPayMethod($payBy);
				$quoteManager->setPayBy($data['subform_paymentselection']['payment_frequency']);

				$premiums = $quoteManager->calculatePremiums();
				$quoteManager->setPolicyTerm();

				$session = new Zend_Session_Namespace('homelet_global');
				/*
					Save the csu and the quote number in the MI tables
				*/
				$generatedByData = new Model_Core_ManagementInformation_GeneratedBy();
				$generatedByData->csuId = $session->csu;
				$generatedByData->policyNumber = $this->_policyNumber;
				$generatedBy = new Manager_Core_ManagementInformation();
				$generatedBy->saveGeneratedBy($generatedByData);

				$this->_formStepCommonNavigate(5);
				return;
			} elseif (isset($_POST['back'])) {
				$this->_formStepCommonNavigate(5);
				return;
			}
		}

		// Load the element data from the database if we can
		if ($this->_formStepCommonPopulate($pageForm, 5))
		{
			// Render the page unless we have been redirected
			$this->view->form = $pageForm;
			$this->render('step');
		}
	}


    /**
     * Controller for the credit card payment page
     *
     * @return void
     *
     * WARNING: This action doesn't use the same validation and common navigation as the rest
     * because it posts to secpay. This may need refactoring at some point but I've ran out
     * of time at the moment :(
     */
    public function ccAction() {
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        $allCompleted = true;

        for($i = 1; $i < $this->_stepMax; $i++) {
            if (!isset($pageSession->completed[$i]) || !$pageSession->completed[$i]) {
                $allCompleted = false;
            }
        }

        if (!$allCompleted) {
            $this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/step' . $this->_stepMax);
        }

        $this->view->errorCount = 0;
		$this->view->errorsHtml = '';
        $this->view->stepNum = 'cc';
        $this->view->stepMax = $this->_stepMax;

		$params = Zend_Registry::get('params');
		$PolicyNumber = $this->_policyNumber;
		$customerRefNo = $this->_customerReferenceNumber;


		// Populate the quick quote box
		$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber);
		$premiums = $quoteManager->calculatePremiums();
		$fees = $quoteManager->getFees();

		$this->view->premiums = $premiums;
		$this->view->fees = $fees;

        // Tell view if the user has chosen to pay monthly or annually
        $this->view->payMonthly = (strtolower($quoteManager->getPayBy()) == 'monthly');

		/*
		 * The Policy Number is used for the trans_id so the quotenumber needs converting
		 * Which for us means just changing the Q to a P
		 *
		 */
		$transId = str_replace("Q","P",$PolicyNumber);
		$amount = (strtolower($quoteManager->getPayBy()) == 'monthly') ? $premiums->total  + $fees->tenantspMonthlyFee : $premiums->annualTotal;

		$this->view->monthlyPremium= $amount;
		$this->view->annualPremium = $amount;

		$remotePassword = $params->secpay->get('remotepassword');
		$digestkey = md5($transId.$amount.$remotePassword);

		$pageForm = new TenantsInsuranceQuoteB_Form_Subforms_CreditCard();
		$this->view->formUrl = $params->secpay->get('host');
		$formData= array(
			'merchant' => $params->secpay->get('merchant'),
			'trans_id' => $transId,
			'policynumber' => $transId,
			'webleadsummaryid' => $this->_webLeadSummaryId,
			'refno' => $customerRefNo,
			'paymentfrequency' => $quoteManager->getPayBy(),
			'amount' => number_format($amount, 2),
			'callback' => $params->secpay->tenants->b->get('success_callback'),
			'template' => $params->secpay->get('template'),
			'repeat' => $params->secpay->get('repeat'),
			'test_status' => $params->secpay->get('test_status'),
			'test_mpi_status'=> $params->secpay->get('test_mpi_status'),
			'usage_type'=> $params->secpay->get('usage_type'),
			'cb_flds'=> "refno:card_type:policynumber:paymentfrequency:webleadsummaryid",
			'cb_card_type' => "",
			'digest' => "",
			'confirmationcode' => "",
			'dups' => "",
		);

		$pageForm->populate($formData);
        $this->view->form = $pageForm;
        $this->view->formAction = $params->secpay->get('host');
        // Tell page NOT to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = false;'
        );
    }

    /**
     * Controller for the credit card failed payment page
     *
     * @return void
     *
  
     */
    public function ccFailedAction() {
  		
    }
    
	/**
	 * Action for the Confirmation of a Successful Credit card Payment
	 *
	 * @return void
	 * @todo the Company name need to be fetched from branding
	 */
	public function ccconfirmationAction(){
		$params = Zend_Registry::get('params');
		$pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
		$request = $this->getRequest();
		
		$this->_customerReferenceNumber = $request->getParam('refno');
		$this->_policyNumber = $request->getParam('policynumber');
		
		$hash = $request->getParam('hash');
		if ($hash == ""){
			$this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/cc-failed');
			exit();
		}
		
		$policyNumber = $this->_policyNumber;
		$quoteNumber = str_replace("P", "Q", $policyNumber);
		
		// Check to see if this quote has already been converted to a policy - if it has we don't want to do any of the below
		$policyManager = new Manager_Insurance_LegacyPolicy();
		$policy = $policyManager->getByPolicyNumber($policyNumber);
		if (is_null($policy)) {	
			// Test the has to prove that this is a valid request
			$remotePassword = $params->secpay->get('remotepassword');
			$request = $this->getRequest();
			$url = $request->getRequestUri();
			$pos = strpos($url, "hash=");
			$url = substr($url, 0, $pos);
			$url .= $remotePassword;
	
			// if the hash and the md5'ed url match then this is a valid request callback
			if( (md5($url) == $hash) ) {
				$params = $this->getRequest()->getParams();
	
				if ( isset($params['code']) &&  $params['code'] == "A") {
					// Populate all the missing cookie information from the parameters from secpay
					$this->_customerReferenceNumber = $request->getParam('refno');
					$this->_policyNumber = $request->getParam('policynumber');
					$this->_webLeadSummaryId = $request->getParam('webleadsummaryid');
					$this->_policyNumber = str_replace("P", "Q", $this->_policyNumber);
					$quoteNumber = $this->_policyNumber;

					$quote = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber);

					// Update Pay Status
					$quoteObject = new Model_Insurance_LegacyQuote();
					$quoteObject = $quote->getQuoteObject();
					$agentSchemeNumber = Manager_Core_Agent::filterAsn($quote->getAgentSchemeNumber());
	
					$quoteObject->startTime = date("h:j:s");
					$quoteObject->payStatus = "UpToDate";
					$quote->update($quoteObject);
	
					$creditCard = new Datasource_Core_CreditCard_Payment();
					$creditCard->saveDetails($request->getParams());
					$schedule = new Manager_Insurance_Schedule();
	
					$schedule->save($this->_customerReferenceNumber, $quote);
	
					$quoteToPolicyManager = new Manager_Insurance_TenantsContentsPlus_QuoteToPolicy();
					$quoteToPolicyManager->changeQuoteToPolicy($quoteNumber);
	
					// Unset the quote stuff as it no longer exists!!!
					unset($quote);
					unset($quoteObject);
	
					$policyNumber = str_replace("Q", "P", $quoteNumber);
					$transId = $policyNumber;
					$months=($request->getParam('paymentfrequency')=='Monthly') ? 1 : 12;
					$disbursement = new Manager_Core_Disbursement();
				    $disbursement->processDisbursement($policyNumber,$request->getParam('amount'),$months,"CC");
                                    $disbursement->createTermImage($policyNumber);	
					//Update the WebLeads
					$webLeadManager = new Manager_Core_WebLead();
				    $webLeadSummary = $webLeadManager->getSummary($this->_webLeadSummaryId);
				    $webLeadSummary->quoteNumber = $policyNumber;
				    $webLeadSummary->status = Model_Core_WebLeadStatus::COMPLETE;
					$webLeadSummary->completedTime = $this->_offsetDate();
				    $webLeadManager->updateSummary($webLeadSummary);
	
					// Transaction is complete unset the Session to prevent navigation to other steps again
					Zend_Session::namespaceUnset('tenants_insurance_quote');

                    // Document management stuff, only runs on first conversion of quote -> policy
                    $documentManager = new Manager_Insurance_Document();
                    $documentManager->createAndPostDocument($policyNumber, Model_Insurance_TenantsContentsPlus_DocumentTypes::NEW_POLICY_DOCS);
                    $documentArray = $documentManager->getDocuments($policyNumber, Model_Insurance_TenantsContentsPlus_DocumentTypes::NEW_POLICY_DOCS);
                    $firstDocument = $documentArray[0];

                    // Put document number in the view, on any later page loads to the same URL this part will not be available for better security
                    if (!is_null($firstDocument)) $this->view->documentNumber = $firstDocument->request_hash;
			    }else{
			    	
			    	// The auth code, failed so redirect to back to payment
					$this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/cc-failed');
					exit();
			    }
			} else {
				$extendedMessage  = 'request url = ' . $request->getRequestUri() . "\n\r";
				$extendedMessage .= 'trimmed url = ' . $url . "\n\r";
				$extendedMessage .= 'our hash = ' . $hash . "\n\r";
				$extendedMessage .= 'secpay hash = ' . md5($url) . "\n\r";
				$log = Zend_Registry::get('logger');
				$log->setEventItem('extendedMessage', $extendedMessage);
	            $log->setEventItem('ipAddress', $this->getRequest()->getServer('REMOTE_ADDR'));
	            $log->setEventItem('requestURL', $request->getRequestUri());
	            $log->setEventItem('trace', '');
	            $log->setEventItem('file', '');
	            $log->setEventItem('line', '');
	            $log->crit('Secpay fraud detected (tenants insurance quote)');
				
				// Hashs are bad to feck to fraud warning page
				$this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/fraud-attempt');
				exit();
			}
		} else {
			$agentSchemeNumber = Manager_Core_Agent::filterAsn($policy->agentSchemeNumber);
		}

		// Put Policy number in the view
		$this->view->policyNumber = $policyNumber;

		// Put domain name of the legacy server in the view
        $params = Zend_Registry::get('params');
		$this->view->domain = $params->homelet->legacyDomain;

		$whiteLabelManager  = new Manager_Core_WhiteLabel();
		$whiteLabelData = new Model_Core_WhiteLabel();
		$whiteLabelData = $whiteLabelManager->fetchByAgentSchemeNumber($agentSchemeNumber);
		$this->view->companyName = $whiteLabelData->companyName;
		$this->view->openingTimes = $params->cms->openingtimes->insurance->tenants;

        // Extra values passed into view for Google Analytics commerce tracking:
        if (is_null($policy)) {
            $policy = $policyManager->getByPolicyNumber($policyNumber);
        }
        $this->view->policy = $policy; // Policy object

        // todo: Fix disgusting way of passing additional data through two chained views.
        $extraVars = new stdClass();
        $monthMultiplier = (strtolower($policy->paySchedule) == 'monthly') ? 12 : 1;
        $extraVars->quoteAnnual = $policy->quote * $monthMultiplier;
        $extraVars->iptAnnual = $policy->ipt * $monthMultiplier;
        $this->view->extraVars = $extraVars;
	}
	
	public function fraudAttemptAction(){
		
	}
	
	/**
     * Controller for the direct debit payment page
     *
     * @return void
     * * array('sort' => '938611', 'acct' => '07806039', 'assert' => 'true'),
     */
    public function ddAction() {
        $pageForm = new TenantsInsuranceQuoteB_Form_DirectDebit();
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
		// Tell page NOT to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = \'dd\';'
        );

		if ($this->getRequest()->isPost()) {
			$valid = $this->_formStepCommonValidate($pageForm, 'dd');
            if ($valid && isset($_POST['dd_confirm'])) {
				// Form is valid and the user has confirmed the bank branch details
                $pageSession->completed['dd'] = true; // Mark page as valid, so user can progress
				// Save the direct debit details and redirect to confirmation page
				$formData = $pageForm->getValues();
				$ddData = new Model_Core_Directdebit();

				$ddData->refNo = $this->_customerReferenceNumber;
				$ddData->policyNumber = $this->_policyNumber;
				$ddData->accountName = $formData["subform_directdebit"]["dd_accountname"];
				$ddData->accountNumber = $formData["subform_directdebit"]["bank_account_number"];
				$ddData->sortCode =preg_replace('/-/','',$formData["subform_directdebit"]["bank_sortcode_number"]);

				$quote = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber);
				$startDate = $quote->getStartDate();
				$firstPayMonth = date("Y-m-d",strtotime("$startDate + 1 month"));

				$ddData->paymentDate = $firstPayMonth;
				$ddData->paymentFrequency = ucfirst(strtolower($quote->getPayBy()));
				// Create the Manage Object
				$ddPayment = new Manager_Core_Directdebit();
				// Save the stuffs
				$ddPayment->save($ddData);

				$this->_formStepCommonNavigate('dd');
				return;
			} elseif($valid && !isset($_POST['dd_confirm'])){
				// Form data is valid but they haven't confirmed their branch details
				$formData = $pageForm->getValues();

				// Switch the form to the confirmation form (essentially the same but with hidden fields)
				$pageForm = new TenantsInsuranceQuoteB_Form_BankConfirmation();

				// Populate the confirmation form with the data from the main DD form and also fill in the branch details
				// This is to show the bank branch details in the confirmation screen
				$bankManager = new Manager_Core_Bank();
				$this->view->branchDetails = $bankManager->getBranchDetail($_POST['bank_sortcode_number']);

				$formData['subform_directdebit']['dd_confirm'] = "yes";

				// The form data is in the wrong subform now because we want to use the confirmation form
				// so we have to switch the key over
				$formData['subform_bankconfirmation'] = $formData['subform_directdebit'];
				unset($formData['subform_directdebit']);

				$pageForm->populate($formData);

			}

			if (isset($_POST['back'])) {
				$this->_formStepCommonNavigate('dd');
				return;
			}
		}

		// Load the element data from the database if we can
		if ($this->_formStepCommonPopulate($pageForm, 'dd'))
		{
			// Render the page unless we have been redirected
			$this->view->form = $pageForm;
			$this->render('step');
		}
    }

	/**
	 * Controller for the quote breakdown popup
	 *
	 * @return void
	 */
	public function viewBreakdownAction() {
        if (!$this->_customerReferenceNumber) return;
        // This controller is called within a popup (facebox style) so doesn't require a layout file
		$this->_helper->getHelper('layout')->disableLayout();
        // It shouldn't be rendered with a view script either
        $this->_helper->viewRenderer->setNoRender();
        echo $this->view->getHelper('viewBreakdown')->viewBreakdown();
	}

	/**
	 * Controller for the save quote popup
	 *
	 * @return void
	 */
	public function saveAction()
    {
		// This controller is called within a popup (facebox style) so doesn't require a layout file
		$this->_helper->getHelper('layout')->disableLayout();

        // Due to the My HomeLet portal, the save function is redundant. Simply return a success message.
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        if (!$pageSession->IsNewCustomer) {
            $this->_helper->viewRenderer('save-success');
        } else {
            $this->_helper->viewRenderer('save-register');
        }

        return;
	}

	/**
	 * Action for the Confirmation of a Successful Credit card Payment
	 *
	 * @return void
	 * @todo The company name need to be fetched from branding
	 *
	 */
	public function ddconfirmationAction() {

        $policyNumber = $this->getRequest()->getParam("pn") ? $this->getRequest()->getParam("pn") : $this->_policyNumber;
        $quoteNumber = str_replace('P', 'Q', $policyNumber);

		// Check to see if this quote has already been converted to a policy - if it has we don't want to do any of the below
		$policyManager = new Manager_Insurance_LegacyPolicy();
		$policy = $policyManager->getByPolicyNumber($policyNumber);

		if (is_null($policy)) {

			$refNo = $this->_customerReferenceNumber;
			$params = Zend_Registry::get('params');

			// Set up the validators and filters
			$filters = array();
			$validators = array ();
			$request = $this->getRequest();
			$input = new Zend_Filter_Input($filters, $validators, $request->getParams());

            if ($input->isValid()) {
	
				// Update Pay Status
				$quote = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber); 
				$quoteObject = new Model_Insurance_LegacyQuote();
				$quoteObject = $quote->getQuoteObject();
	
				$quoteObject->startTime = date("h:j:s");
				$quoteObject->payStatus = "UpToDate";
				$quote->update($quoteObject);
	
				/*$quoteObject->startTime = date("h:j:s");
				$quoteObject->payStatus = "UpToDate";
				$quote->update($quoteObject);*/

                $schedule = new Manager_Insurance_Schedule();
                $schedule->save($refNo, $quote);

				$quoteToPolicyManager = new Manager_Insurance_TenantsContentsPlus_QuoteToPolicy();
				$quoteToPolicyManager->changeQuoteToPolicy($quoteNumber);

				//Update the WebLeads
				$webLeadManager = new Manager_Core_WebLead();
			    $webLeadSummary = $webLeadManager->getSummary($this->_webLeadSummaryId);
			    $webLeadSummary->quoteNumber = $policyNumber;
			    $webLeadSummary->status = Model_Core_WebLeadStatus::COMPLETE;
				$webLeadSummary->completedTime = $this->_offsetDate();
			    $webLeadManager->updateSummary($webLeadSummary);				
				
				$agentSchemeNumber = Manager_Core_Agent::filterAsn($quote->getAgentSchemeNumber());
				
				// Transaction is complete unset the Session to prevent navigation to other steps again
				Zend_Session::namespaceUnset('tenants_insurance_quote');

                // Document management stuff, only runs on first conversion of quote -> policy
                $documentManager = new Manager_Insurance_Document();
                $documentManager->createAndPostDocument($policyNumber, Model_Insurance_TenantsContentsPlus_DocumentTypes::NEW_POLICY_DOCS);
                $documentArray = $documentManager->getDocuments($policyNumber, Model_Insurance_TenantsContentsPlus_DocumentTypes::NEW_POLICY_DOCS);
                $firstDocument = $documentArray[0];

                // Put document number in the view, on any later page loads to the same URL this part will not be available for better security
                if (!is_null($firstDocument)) $this->view->documentNumber = $firstDocument->request_hash;
			}
		} else {
			$agentSchemeNumber = Manager_Core_Agent::filterAsn($policy->agentSchemeNumber);
		}

        $disbursement = new Manager_Core_Disbursement();
        $disbursement->createTermImage($policyNumber);

		$params = Zend_Registry::get('params');

		// Put domain name of the legacy server in the view
		$this->view->domain = $params->homelet->legacyDomain;

		$whiteLabelManager  = new Manager_Core_WhiteLabel();
		$whiteLabelData = new Model_Core_WhiteLabel();
		$whiteLabelData = $whiteLabelManager->fetchByAgentSchemeNumber($agentSchemeNumber);
		$this->view->companyName = $whiteLabelData->companyName;
		$this->view->companyName = "HomeLet";
		
		// Put Policy number in the view
		$this->view->policyNumber = $policyNumber;
		$this->view->openingTimes = $params->cms->openingtimes->insurance->tenants;

        // Extra values passed into view for Google Analytics commerce tracking:
        if (is_null($policy)) {
            $policy = $policyManager->getByPolicyNumber($policyNumber);
        }
        $this->view->policy = $policy; // Policy object

        // todo: Fix disgusting way of passing additional data through two chained views.
        $extraVars = new stdClass();
        $monthMultiplier = (strtolower($policy->paySchedule) == 'monthly') ? 12 : 1;
        $extraVars->quoteAnnual = $policy->quote * $monthMultiplier;
        $extraVars->iptAnnual = $policy->ipt * $monthMultiplier;
        $this->view->extraVars = $extraVars;
	}

    /**
     * Referred action
     *	@param none
     *	This function handles the referred underwriting state, including unsetting the session
     * @return void
     *
     **/
    public function referredAction() {

        // Show the policy number to the end user
        $this->view->policyNumber = $this->_policyNumber;

        // Perform referral - set quote status to referred
        $referralManager = new Manager_Insurance_TenantsContentsPlus_Referral();
        $referralManager->setToRefer($this->_policyNumber);

        // Notify Underwriting
        $notificationManager = new Manager_Core_Notification();
        $notificationManager->notifyUnderwriting($this->_policyNumber, $this->_customerReferenceNumber);

        // Unset the session to prevent navigation to other steps
        Zend_Session::namespaceUnset('tenants_insurance_quote');
    }

	// TODO: All this stuff shouldn't be in here - it's a bloated controller already!

	/**
	 * Helper function to populate the zend form elements with database data
	 *
	 * @param Zend_Form $pageForm form definition for this step
	 * @param int $stepNum current step number
	 *
	 * @return void
	 */
	private function _formStepCommonPopulate($pageForm, $stepNum) {

		$pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
		// First of all check that this form should be viewable and the user isn't trying to skip ahead

		$this->view->stepNum = $stepNum;
		$this->view->stepMax = $this->_stepMax;

		// Check to see if the user is trying to skip ahead in the quote
		$tooFarAhead = false;

		if ((!isset($pageSession->completed) || is_null($pageSession->completed)) && $stepNum != 1)
		{
			$tooFarAhead = true;
			$lastCompleted = 1;
		} elseif ($stepNum > 1) {
            // Check to see if any pages previous to the one the user's trying to get to are incomplete
            $tooFarAhead = false;
            for ($i = 1; $i < $stepNum; $i++)
            {
                if (!isset($pageSession->completed[$i]) || !$pageSession->completed[$i])
                {
                    $tooFarAhead = true;
                    $lastCompleted = $i;
                    break;
					break;
                }
            }
        }

		if ($tooFarAhead) {
			// Drop user onto page that needs completing
			$this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/step' . ($lastCompleted));

			return false;
		}

		// Now check to see if they need to login (using an existing email address without being logged in)
		if ($stepNum > 1) {
			// Before we do ANYTHING we need to check to see if the email address entered matches a customer record
			// we already have - if it does we need to ask them to login before they proceed.
			$customerReferenceNumber = $pageSession->CustomerRefNo;
			$customerManager = new Manager_Core_Customer();
			$legacyCustomer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $customerReferenceNumber);
			$emailAddress = $legacyCustomer->getEmailAddress();

			$customer = $customerManager->getCustomerByEmailAddress($emailAddress);

			if ($customer) {
				// There is already a customer entry for this email address - so we need to see if they are logged in
				// if not we need to force them to login

				$auth = Zend_Auth::getInstance();
				$auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

				/*if ($auth->hasIdentity()) {
					$loggedInEmail = $auth->getStorage()->read()->email_address;
					if ($loggedInEmail != $customer->getEmailAddress()) {
						// They are logged in but not who they should be to do this quote
						$this->_helper->redirector->gotoUrl('/account/login?refer=tenants-insurance&step='. $stepNum);

						return false;
					}
				} else {
					// They aren't logged in and need to
					$this->_helper->redirector->gotoUrl('/account/login?refer=tenants-insurance&step='. $stepNum);

					return false;
				}*/
			}
		}

		// Echo out some debug info if not in production mode
		Application_Core_Logger::log("Policy Number : " . $this->_policyNumber);
		Application_Core_Logger::log("Customer Ref No : " . $this->_customerReferenceNumber);
		Application_Core_Logger::log("agentSchemeNumber : " . $this->_agentSchemeNumber);

		$formData = array();

		// If step 1 and not in session (so producing a quick quote) - we need to pre-populate
		// a few bits if the customer is already logged into the site
		if ($stepNum == 1 && !isset($pageSession->CustomerRefNo)) {
			$auth = Zend_Auth::getInstance();
			$auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

			if ($auth->hasIdentity()) {
				// Customer is logged in and starting a new quote - so we need to pre-populate the customers details from stored details
				$customerID = $auth->getStorage()->read()->id;

				$customerManager = new Manager_Core_Customer();
				$customer = $customerManager->getCustomer(Model_Core_Customer::IDENTIFIER, $customerID);

                // Get the customer's legacy record too as DoB is only in the old system
                $legacyCustomer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $customerID);

				$formData['title']          = $customer->getTitle();
				$formData['first_name']     = $customer->getFirstName();
				$formData['last_name']      = $customer->getLastName();
				$formData['phone_number']   = $customer->getTelephone(Model_Core_Customer::TELEPHONE1);
				$formData['mobile_number']  = $customer->getTelephone(Model_Core_Customer::TELEPHONE2);
				$formData['email_address']  = $customer->getEmailAddress();

                // Fetch DoB from old customer manager
                $formData['date_of_birth_at'] = $legacyCustomer->getDateOfBirth();

				$pageForm->populate($formData);
			}
		}

		// Only populate from DB if we are in session and have a reference number
		if (isset($pageSession->CustomerRefNo)) {
			$customerReferenceNumber = $pageSession->CustomerRefNo;
			$policyNumber = $pageSession->PolicyNumber;

			// Populate $formData with data from model, if available
			switch ($stepNum) {
				case 1:
					// Personal Details section
					$customerManager = new Manager_Core_Customer();
					$customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $customerReferenceNumber);

					$titleOptions = TenantsInsuranceQuoteB_Form_Subforms_PersonalDetails::$titles;
					if(in_array($customer->getTitle(), $titleOptions)) {
						$formData['title'] = $customer->getTitle();
					} else {
						$formData['title'] = "Other";
						$formData['other_title'] = $customer->getTitle();
					}
					
					$formData['first_name']     = $customer->getFirstName();
					$formData['last_name']      = $customer->getLastName();
					$formData['phone_number']   = $customer->getTelephone(Model_Core_Customer::TELEPHONE1);
					$formData['mobile_number']  = $customer->getTelephone(Model_Core_Customer::TELEPHONE2);
					$formData['email_address']  = $customer->getEmailAddress();
                    $dob = $customer->getDateOfBirthAt();
                    if (null != $dob && '0000-00-00' != $dob) {
                        $formData['date_of_birth_at'] = Application_Core_Utilities::mysqlDateToUk($dob);
                    }

					
					// Data Protection section
                    $dpaManager = new Manager_Core_DataProtection();
					$dpaItems = $dpaManager->getItems($customerReferenceNumber, Model_Core_DataProtection_ItemEntityTypes::INSURANCE);

					foreach($dpaItems as $currentItem) {
						
						switch($currentItem->constraintTypeId) {
							
							case Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_PHONEANDPOST:
								
								if($currentItem->isAllowed) {
									$formData['dpa_phone_post'] = 0;
								}
								else {
									$formData['dpa_phone_post'] = 1;
								}
								break;
							
							case Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_SMSANDEMAIL:
								
								if($currentItem->isAllowed) {
									$formData['dpa_sms_email'] = 0; // For Redmine Ref #8003, "Updated marketing preference questions on online quotes"
								}
								else {
									$formData['dpa_sms_email'] = 1; // For Redmine Ref #8003, "Updated marketing preference questions on online quotes"
								}
								break;
								
							case Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_THIRDPARTY:
								
								if($currentItem->isAllowed) {
									$formData['dpa_resale'] = 1;
								}
								else {
									$formData['dpa_resale'] = 0;
								}
								break;
						}
					}
					
					
					// Contents Insurance section
					$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber);;

					// If we've retrieved this quote we need to setup some session variables to match the quote (agent scheme number mostly)
					$session = new Zend_Session_Namespace('homelet_global');
					$session->agentSchemeNumber = Manager_Core_Agent::filterAsn($quoteManager->getAgentSchemeNumber());

					$contentsSI = $quoteManager->getCoverAmount(Manager_Insurance_TenantsContentsPlus_Quote::CONTENTS);

					// TODO: Remove the hard-coded contents insured splits - should be loaded from DB
					if ($contentsSI == '5000' || $contentsSI == '7500' || $contentsSI == '10000' || $contentsSI == '15000') {
						$formData['contents_cover_a'] = $contentsSI;
					} else {
						$formData['contents_cover_a'] = '15000+';
						$formData['contents_cover_b'] = $contentsSI;
					}

					// Shares section
					$sharersManager = new Manager_Insurance_TenantsContentsPlus_Sharers();
					$existingSharers = $sharersManager->getSharers($policyNumber);
					$formData['policy_sharers'] = 0;
					if ($existingSharers)
					{
						if ($existingSharers->getSharerOccupation(1) != '') {
							$formData['policy_sharers'] = 1;
							$formData['policy_sharer1_occupation'] = $existingSharers->getSharerOccupation(1);
						}
						if ($existingSharers->getSharerOccupation(2) != '') {
							$formData['policy_sharers'] = 2;
							$formData['policy_sharer2_occupation'] = $existingSharers->getSharerOccupation(2);
						}
					}

					for ($i = 1; $i <= $pageForm->maxSharers; $i++) {
						$formData["policy_sharer{$i}_occupation"] = $existingSharers->getSharerOccupation($i);
					}

					// Decide max number of sharers allowed based on contents insured value
					$contentsAmount = new Zend_Currency(
						array(
							'value' => $contentsSI,
							'precision' => 0
						)
					);

					$sharersAllowed = $sharersManager->getNoOfSharersAllowed($contentsAmount);

					// Push this into Javascript on the page
					$this->view->headScript()->appendScript(
						"var sharersAllowed = {$sharersAllowed};"
					);

					// Initial Disclosure Agreement section
					// As we have a customer reference number they must have saved step 1 at some point which means
					// they must have agreed to the IDD section
					$formData['idd'] = 1;
					break;

				case 2:
					// Unspecified Possessions section
					$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber); 
					$unspecSI = $quoteManager->getCoverAmount(Manager_Insurance_TenantsContentsPlus_Quote::UNSPECIFIEDPOSSESSIONS);

					// If step has been completed before we can assume some defaults that we'll over-write later
					if ((isset($pageSession->completed[2]) && $pageSession->completed[2] == true)
						or
						($quoteManager->getPropertyPostcode()!=''))
					{
						$formData['away_from_home'] = 'no';
						$formData['above_x'] = 'no';
						$formData['bicycle'] = 'no';
					}

					// TODO: Re-factor this so that it doesn't use hardcoded pricing breaks anymore
					if ($unspecSI > 0) {
						$formData['away_from_home'] = 'yes';
						$formData['possessions_cover'] = $unspecSI;
					}

					//TODO: This should be talking to the quote manager NOT directly to the datasource
					$possessions = new Datasource_Insurance_Policy_SpecPossessions($pageSession->PolicyNumber);
					if ($possessions->countPossessions() > 0) {
						$formData['above_x'] = 'yes';
					}

					// Bicycle section
					//TODO: This should be talking to the quote manager NOT directly to the datasource
					$bicycle = new Datasource_Insurance_Policy_Cycles($this->_customerReferenceNumber, $this->_policyNumber);
					if ($bicycle->countBikes() > 0) {
					    $formData['bicycle'] = 'yes';
					}

					break;

				case 3:
					// Insured Address section

		            // Fetch previously stored address
					$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber); 
					$addressArray = $quoteManager->getPropertyAddress();

					if ($addressArray['postcode'] != '') {
						if (strpos($addressArray['address1'], ',') !== false) {
							$formData['ins_house_number_name'] = preg_replace('/,.*$/', '', $addressArray['address1']);
						} else {
							$formData['ins_house_number_name'] = preg_replace('/ .*$/', '', $addressArray['address1']);
						}
		                $formData['ins_postcode'] = $addressArray['postcode'];

						// Look up address again to populate dropdown
						$postcodeLookup = new Manager_Core_Postcode();
						$addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $formData['ins_postcode']));
						$addressList = array('' => '--- please select ---');
						$filterString = (is_numeric($formData['ins_house_number_name']))?$formData['ins_house_number_name'].", ":$formData['ins_house_number_name'];

						foreach($addresses as $address) {
							$addressList[$address['id']] = $address['singleLineWithoutPostcode'];
							if (stripos($address['singleLineWithoutPostcode'], $filterString) === 0) {
								$addressID = $address['id'];
							}
						}

						// Add some validation
						$ins_address = $pageForm->getSubForm('subform_insuredaddress')->getElement('ins_address');
						$ins_address->setMultiOptions($addressList);
						$validator = new Zend_Validate_InArray(array(
							'haystack' => array_keys($addressList)
						));
						$validator->setMessages(array(
							Zend_Validate_InArray::NOT_IN_ARRAY => 'Insured address does not match with postcode'
						));
						$ins_address->addValidator($validator, true);

						// Set the address to selected
						$ins_address->setValue($addressID);
						$addressID = null; // Make sure we don't use this again for the correspondance address!

						// Upsell Message section
						$pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
						$session = new Zend_Session_Namespace('homelet_global');
						$agentSchemeNumber = $session->agentSchemeNumber;
					}

					// Correspondance Address section
					$customerManager = new Manager_Core_Customer();
					$customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $this->_customerReferenceNumber);

					$addressLine1 = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE1);
		            $addressPostcode = $customer->getPostCode();

					if ($addressPostcode != '') {
					    if (strpos($addressLine1, ',') !== false) {
					        $formData['cor_house_number_name'] = preg_replace('/,.*$/', '', $addressLine1);
					    } else {
					        $formData['cor_house_number_name'] = preg_replace('/ .*$/', '', $addressLine1);
						}
						$formData['cor_postcode'] = $addressPostcode;

						// TODO: Surely if this postcode and house number matches the previous one
						// we can just tick the YES box and hide the correspondance address form??

						$postcodeLookup = new Manager_Core_Postcode();
						$addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $formData['cor_postcode']));
						$addressList = array('' => '--- please select ---');
						$filterString = (is_numeric($formData['cor_house_number_name'])) ? $formData['cor_house_number_name'].", " : $formData['cor_house_number_name'];
		                foreach($addresses as $address) {
							$addressList[$address['id']] = $address['singleLineWithoutPostcode'];
							if (stripos($address['singleLineWithoutPostcode'], $filterString) === 0) {
								$addressID = $address['id'];
							}
						}

						$cor_address = $pageForm->getSubForm('subform_correspondencedetails')->getElement('cor_address');
						$cor_address->setMultiOptions($addressList);
						$validator = new Zend_Validate_InArray(array(
							'haystack' => array_keys($addressList)
						));
						$validator->setMessages(array(
						    Zend_Validate_InArray::NOT_IN_ARRAY => 'Insured address does not match with postcode'
						));
						$cor_address->addValidator($validator, true);
						$cor_address->setValue($addressID);
					}

					if ($addressPostcode != ''&& $addressArray['postcode'] != '') {
						if ($cor_address->getValue() == $ins_address->getValue()) {
							$formData['cor_same_address'] = 'yes';
						} else {
							$formData['cor_same_address'] = 'no';
						}
					}

					// Letting Agent section

					// If the agent scheme number is not our default one - load the agent details
					$params = Zend_Registry::get('params');
					$agentSchemeNumber = Manager_Core_Agent::filterAsn($quoteManager->getAgentSchemeNumber());


					// How did you hear about us section
					$marketQuestion = new Manager_Core_ManagementInformation();
					$formData['how_hear'] = $marketQuestion->getMarketingAnswers($this->_policyNumber);

					//Campaign code section
					$webLeadManager = new Manager_Core_WebLead();
					$webLeadSummary = $webLeadManager->getSummary($this->_webLeadSummaryId);
					$formData['campaign_code'] = $webLeadSummary->promotionCode;

					// Start and end date
					$startDate = $quoteManager->getStartDate();
					if ($startDate != '' && $startDate != '0000-00-00') {
						$formData['policy_start'] = substr($startDate, 8, 2) . '/' . substr($startDate, 5, 2) . '/' . substr($startDate, 0, 4);
					}

					break;

				case 4:
					// Important Information section

					// If this page has previously passed validation, we know what the answers
					//   given must have been without hitting the DB (as anything else refers)
					if (isset($pageSession->completed[$stepNum]) && $pageSession->completed[$stepNum]) {
					    $formData['declaration1'] = 'no';
					    $formData['declaration2'] = 'no';
					    $formData['declaration3'] = 'no';
					    $formData['declaration4'] = 'no';
					    $formData['declaration_confirmation'] = 'yes';

						// If the step is completed we can also assume they said yes to the declaration agreement
						$formData['declaration_statement'] = 1;
					}
					break;

				case 5:
					// Not sure if we should really be loading payment methods back in
					// surely it would be best to just let them choose again

					break;

				case 'dd':
					$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber); 
					$this->view->payMonthly = (strtolower($quoteManager->getPayBy()) == 'monthly');

					break;
            }

			$pageForm->populate($formData);

			// Populate the quick quote box
			$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber); 
			$premiums = $quoteManager->calculatePremiums();
			$fees = $quoteManager->getFees();

			$this->view->premiums = $premiums;
			$this->view->fees = $fees;
		} else {
			// Not in session but there are some defaults we need to set for step 1
			// TODO: Write the javascript better so we don't need to do fudges like this
			$this->view->headScript()->appendScript(
				"var sharersAllowed = 0;"
			);
		}

		return true;
	}

    /**
     * Helper function for common work executed in each form step
     * Checks user is "allowed" on this step, redirecting if not
     * Returns result of form validation
     *
     * @param Zend_Form $pageForm form definition for this step
     * @param int $stepNum current step number
     *
     * @return array two elements: the modified Zend_Form and a boolean indicating validation success
     * @todo Needs a massive cleanup as it's way too slow for the ajax calls!
     */
    private function _formStepCommonValidate($pageForm, $stepNum) {

        $this->view->errorCount = 0;
        $this->view->stepNum = $stepNum;
        $this->view->stepMax = $this->_stepMax;

        $request = $this->getRequest();
		$pageSession = new Zend_Session_Namespace('tenants_insurance_quote');

        if ($pageForm->isValid($request->getPost())) {
			// Page is valid so set the session step to true
            $pageSession->completed[$stepNum] = true;
            return true;
        } else {
            $pageSession->completed[$stepNum] = false; // Mark page as invalid, so user must complete it to progress

			// Output errors to progress section in layout,
            //   and list out IDs that JS can use to highlight error fields
            $errorsJs = "var errorList = " . Zend_Json::encode($pageForm->getMessagesFlattened()) . ";\n";
            $this->view->headScript()->appendScript($errorsJs, $type = 'text/javascript');

            // Mark page as no longer being on its first load, when not present this is used to suppress dynamic AJAX errors in the page when first loaded
            $previouslyLoaded = "var previouslyLoaded = true;\n";
            $this->view->headScript()->appendScript($previouslyLoaded, $type = 'text/javascript');

			$errorMessages = $pageForm->getMessagesFlattened();
			$this->view->errorCount = count($errorMessages);
			$this->view->errorsHtml = $this->view->partial('partials/error-list.phtml', array('errors' => $errorMessages));
			return false;
        }
    }

    /**
     * Helper function for common work executed in each form step
     * Allows navigation between steps
     *
     * @param int $stepNum current step number
     *
     * @return void
     */
    private function _formStepCommonNavigate($stepNum) {
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        $request = $this->getRequest();

        if ($request->isPost()) {
            // Handle moving backwards and forwards through the form
            $response = $this->getResponse();

			if ($stepNum == 'dd') {
				$policyNumber = str_replace('Q', 'P', $this->_policyNumber);
				
				if (isset($_POST['next'])) $this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/ddconfirmation?pn=' . $policyNumber);
				if (isset($_POST['back'])) $this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/step' . $this->_stepMax);
				$response->sendResponse();
			} elseif ($stepNum == 'cc') {
				if (isset($_POST['next'])) $this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/ccconfirmation');
				if (isset($_POST['back'])) $this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/step' . $this->_stepMax);
				$response->sendResponse();
			} else {
				if (isset($_POST['back']) && $stepNum > 1) {
					$this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/step' . ($stepNum - 1));
				} elseif (isset($_POST['next']) && $stepNum < $this->_stepMax && $pageSession->completed[$stepNum]) {
					$this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/step' . ($stepNum + 1));
				// Handle payment screen traversal
				} elseif (isset($_POST['next']) && isset($_POST['payment_method']) && $_POST['payment_method'] == 'cc' && $stepNum == $this->_stepMax) {
					$this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/cc');
				} elseif (isset($_POST['next']) && isset($_POST['payment_method']) && $_POST['payment_method'] == 'dd' && $stepNum == $this->_stepMax) {
					$this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/dd');
				}
			}
        }
    }

	/**
	 * @todo Remove the hardcoded UW question numbers from this method.
	 */
	protected function _processUnderwriting($pageForm) {

		$returnVal = true;

		//First, store the underwriting answers.
		$declaration1 = $pageForm->subform_importantinformation->getElement('declaration1')->getValue();
		$declaration2 = $pageForm->subform_importantinformation->getElement('declaration2')->getValue();
		$declaration3 = $pageForm->subform_importantinformation->getElement('declaration3')->getValue();
		$declaration4 = $pageForm->subform_importantinformation->getElement('declaration4')->getValue();

		$underwritingAnswersArray = array();
		$underwritingAnswersArray[0] = new Model_Insurance_Answer();
        $underwritingAnswersArray[0]->setPolicyNumber($this->_policyNumber);
        $underwritingAnswersArray[0]->setQuestionNumber(49);
        $underwritingAnswersArray[0]->setAnswer($declaration1);
        $underwritingAnswersArray[0]->setDateAnswered(Zend_Date::now());

        $underwritingAnswersArray[1] = new Model_Insurance_Answer();
        $underwritingAnswersArray[1]->setPolicyNumber($this->_policyNumber);
        $underwritingAnswersArray[1]->setQuestionNumber(50);
        $underwritingAnswersArray[1]->setAnswer($declaration2);
        $underwritingAnswersArray[1]->setDateAnswered(Zend_Date::now());

        $underwritingAnswersArray[2] = new Model_Insurance_Answer();
        $underwritingAnswersArray[2]->setPolicyNumber($this->_policyNumber);
        $underwritingAnswersArray[2]->setQuestionNumber(51);
        $underwritingAnswersArray[2]->setAnswer($declaration3);
        $underwritingAnswersArray[2]->setDateAnswered(Zend_Date::now());

        $underwritingAnswersArray[3] = new Model_Insurance_Answer();
        $underwritingAnswersArray[3]->setPolicyNumber($this->_policyNumber);
        $underwritingAnswersArray[3]->setQuestionNumber(52);
        $underwritingAnswersArray[3]->setAnswer($declaration4);
        $underwritingAnswersArray[3]->setDateAnswered(Zend_Date::now());

		$answersManager = new Manager_Insurance_Answers();
		for($i = 0; $i < count($underwritingAnswersArray); $i++){
            if(!$answersManager->getIsAnswerAlreadyStored($underwritingAnswersArray[$i])) {
				$answersManager->insertUnderwritingAnswer($underwritingAnswersArray[$i]);
			}
        }

		//Next apply any necessary endorsements.
		$endorsementsManager = new Manager_Insurance_TenantsContentsPlus_Endorsement();
		$endorsements = $endorsementsManager->getEndorsementsRequired($this->_policyNumber);
		if(!empty($endorsements)) {
			foreach($endorsements as $currentEndorsement) {
				if(!$endorsementsManager->getIsEndorsementAlreadyApplied($currentEndorsement)) {
					$endorsementsManager->insertEndorsement($currentEndorsement);
				}
			}
		}

		//Next store any extra information provided by the user.
		$additionalInfoProvided = array();
		$infoSubmitted = $pageForm->subform_importantinformation->getElement('declaration1_details')->getValue();
		if(!empty($infoSubmitted)) {
			$additionalInfoProvided[] = $infoSubmitted;
		}

		$infoSubmitted = $pageForm->subform_importantinformation->getElement('declaration3_details')->getValue();
		if(!empty($infoSubmitted)) {
			$additionalInfoProvided[] = $infoSubmitted;
		}

		$infoSubmitted = $pageForm->subform_importantinformation->getElement('declaration4_details')->getValue();
		if(!empty($infoSubmitted)) {
			$additionalInfoProvided[] = $infoSubmitted;
		}

		if(!empty($additionalInfoProvided)) {
			//Compile the extra information, if any, into a single string.
			$compiledInformation = '';
			foreach($additionalInfoProvided as $currentInformation) {
				if(empty($compiledInformation)) {
					$compiledInformation = $currentInformation;
				}
				else {
					$compiledInformation .= " $currentInformation";
				}
			}

			$additionalInformationManager = new Manager_Insurance_AdditionalInformation();
			if(!$additionalInformationManager->getIsAdditionalInformationAlreadyStored($this->_policyNumber)) {

				$additionalInformation = new Model_Insurance_AdditionalInformation();
				$additionalInformation->setPolicyNumber($this->_policyNumber);
				$additionalInformation->setAdditionalInformation($compiledInformation);
				$additionalInformationManager->insertAdditionalInformation($additionalInformation);
			}
		}

		//Update the quote object so that the underwritingQuestionSetID is appropriately set.
		$quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null, null, $this->_policyNumber);
		$quoteManager->setUnderwritingQuestionSetID(2);


		//Previous claims are managed on the fly.
		//Check if a referral is required.
		$referralsManager = new Manager_Insurance_TenantsContentsPlus_Referral();
		if($referralsManager->getRequiresReferral($this->_policyNumber)) {

			//Display the referred screen.
			$returnVal = false;

			//Update the quote/policy notes.
			$notesManager = new Manager_Core_Notes();
			$notesManager->save($this->_policyNumber, Model_Core_NoteLabels::REFERRED_BY_CUSTOMER);
		}
		return $returnVal;
	}


	// TODO: I'm pretty sure we don't want this to go live!
    public function webwatchdumpAction() {
        $forms = array('Step1', 'Step2', 'Step3', 'Step4', 'Step5', 'Step6', 'DirectDebit');
        echo "<table cellspacing=\"5\" cellpadding=\"5\" border=\"1\">\n";
        $c = 1;
        foreach($forms as $form) {
            echo "  <tr><td colspan=\"3\">{$form}</td></tr>\n";
            $objectName = "TenantsInsuranceQuoteB_Form_{$form}";
            $pageForm = new $objectName();
            $c = $this->_recursiveDumpElements($pageForm, $c);
        }
        echo "</table>\n";
        return;
    }

	/**
	* Public send quote as email and or post function
	*/
	public function sendAction(){
        // This controller is called within a popup (facebox style) so doesn't require a layout file
		$this->_helper->getHelper('layout')->disableLayout();

		$pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        $this->_customerReferenceNumber = $pageSession->CustomerRefNo;
        $this->_policyNumber = $pageSession->PolicyNumber;
        $this->view->sendForm = new TenantsInsuranceQuoteB_Form_SendQuote();
	}

	/**
	* This function is to catch inbound links and session the get params and bounce on to the step 1
	*/
	public function referAction(){
		// Check to see if we have a referrer code - if we do store it in a session variable
		if ($this->getRequest()->getParam('referrer')!='') {
			$session->referrer = $this->getRequest()->getParam('referrer');
		} elseif(!isset($session->referrer)) {
			// no passed parameter so default
			$session->referrer ="direct";
		}

		// Check to see if we have a agentSchemeNumber code - if we do store it in a session variable
		if ($this->getRequest()->getParam('agentschemeno')!='') {
			$session->agentSchemeNumber = Manager_Core_Agent::filterAsn($this->getRequest()->getParam('agentschemeno'));
		}

		// Check to see if we have a origin code - if we do store it in a session variable
		if ($this->getRequest()->getParam('origin')!='') {
			$session->origin = $this->getRequest()->getParam('origin');
		}

		// Check to see if we have a csuid - if we do store it in a session variable - Sorry Phil
		if ($this->getRequest()->getParam('csu')!='') {
			$session->csu = $this->getRequest()->getParam('csu');
		} elseif(!isset($session->csu)) {
			// no passed parameter so default it to our web user 87
			$session->csu ="87";
		}

		$this->_helper->getHelper('layout')->disableLayout();
        // It shouldn't be rendered with a view script either
        $this->_helper->viewRenderer->setNoRender();
		$this->_helper->redirector->gotoUrl('/tenants/insurance-quote-b/step1');

	}

	/**
	* TODO: This function just butchers the date to subtract 5 hours to make webleads work correctly
	* @param None
	* @return ZendDate object offset by 5 hours
	* @author John Burrin
	*/
	private function _offsetDate(){
		$date = new Zend_Date();
		// changes $date by subtracting 5 hours
		$date->sub('5', Zend_Date::HOUR);
		return $date;
	}
	
	
	/**
	 * Saves the TCI+ data protection values specified by the user.
	 * 
	 * @param mixed $itemGroupId
	 * The identifier against which the DPA values will be stored. May be a legacy customer refno
	 * or a WebLead ID.
	 *
	 * @param integer $entityTypeId
	 * The DPA entity type. Must correspond to one of the consts exposed by the
	 * Model_Core_DataProtection_ItemEntityTypes class.
	 * 
	 * @param integer $phonePost
	 * Indicates whether the user wants marketing by phone/post. Should be taken directly from
	 * the user interface without modification.
	 * 
	 * @param integer $smsEmail
	 * Indicates whether the user wants marketing by sms/email. Should be taken directly from
	 * the user interface without modification.
	 * 
	 * @param integer $thirdParty
	 * Indicates whether the user wants marketing by third parties. Should be taken directly from
	 * the user interface without modification.
	 * 
	 * @return void
	 */
	protected function _saveDPA($itemGroupId, $entityTypeID, $phonePost, $smsEmail, $thirdParty) {
		
		//Capture and store the data protections.
		$dpaList = array();
		
		//Capture and translate the details of the dpa item - marketing by phone and post.
		$dpaItem = new Model_Core_DataProtection_Item();
		$dpaItem->itemGroupId = $itemGroupId;
		$dpaItem->entityTypeId = $entityTypeID;
		$dpaItem->constraintTypeId = Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_PHONEANDPOST;
		if($phonePost == 1) {
	
			$dpaItem->isAllowed = false;
		}
		else {
	
			$dpaItem->isAllowed = true;
		}
		array_push($dpaList, $dpaItem);
	
		
		//Capture and translate the details of dpa item - marketing by sms and email.
		$dpaItem = new Model_Core_DataProtection_Item();
		$dpaItem->itemGroupId = $itemGroupId;
		$dpaItem->entityTypeId = $entityTypeID;
		$dpaItem->constraintTypeId = Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_SMSANDEMAIL;
		if($smsEmail == 1) {
	
			$dpaItem->isAllowed = false; // For Redmine Ref #8003, "Updated marketing preference questions on online quotes"
		}
		else {
	
			$dpaItem->isAllowed = true; // For Redmine Ref #8003, "Updated marketing preference questions on online quotes"
		}
		array_push($dpaList, $dpaItem);
		
		
		//Capture and translate the details of dpa item - third party details resale.
		$dpaItem = new Model_Core_DataProtection_Item();
		$dpaItem->itemGroupId = $itemGroupId;
		$dpaItem->entityTypeId = $entityTypeID;
		$dpaItem->constraintTypeId = Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_THIRDPARTY;
		if($thirdParty == 1) {
	
			$dpaItem->isAllowed = true;
		}
		else {
	
			$dpaItem->isAllowed = false;
		}
		array_push($dpaList, $dpaItem);
		
		
		//Store the data protections.
		$dpaManager = new Manager_Core_DataProtection(Manager_Core_DataProtection::USE_DBASE);
		foreach($dpaList as $currentItem) {
			
			$dpaManager->upsertItem($currentItem);
		}
	}
}

?>
