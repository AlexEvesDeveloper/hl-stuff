<?php

class LandlordsInsuranceQuote_IndexController extends Zend_Controller_Action {

	private $_stepMax = 5; // Number of form steps, excluding special pages like payment screens
	private $_quoteID;
    private $_referrer;
    private $_agentSchemeNumber;

    /**
     * @var Zend_Config
     */
    private $_params;

    public function init()
    {
        $session = new Zend_Session_Namespace('homelet_global');

        // Bit of a dirty hack to use a layout from the CMS module
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayoutPath(APPLICATION_PATH . '/modules/cms/layouts/scripts');
        $layout->setLayout('default');
        $this->view->headLink()->setStylesheet('/assets/landlords-insurance-quote/css/landlords-insurance-quote.css');
        $this->view->headScript()->appendFile('/assets/common/js/insurance-forms.js');
        $this->view->headScript()->appendFile('/assets/landlords-insurance-quote/js/landlords_form.js');
        $this->view->headScript()->appendFile('/assets/vendor/bootstrap/js/bootstrap.min.js');
        $this->view->pageTitle = 'Landlords Insurance Quote';
        $this->url = trim($this->getRequest()->getRequestUri(),'/');

        $menuData = array(
            'selected'  => 'landlords',
            'url'   	=> $this->url
        );

        // Check to see if we have a referrer code - if we do store it in a session variable
        // TODO: Check if redundant, remove if so
        if ($this->getRequest()->getParam('referrer') != '') {
            $session->referrer = Manager_Core_Agent::filterAsn($this->getRequest()->getParam('referrer'));
        }
        elseif (!isset($session->referrer)) {
            // no passed parameter so default it to our default account
            $session->referrer ="1403796";
        }

        // Check to see if we have a referrer code - if we do store it in a
        //   session variable
        $this->_params = Zend_Registry::get('params');

        if ($this->getRequest()->getParam('asn') != '') {
            // Use passed ASN if valid
            try {
                $session->agentSchemeNumber = Manager_Core_Agent::filterAsn($this->getRequest()->getParam('asn'));
            } catch (Zend_Exception $e) {
                // Not valid, use default if exception recognised as a bad lookup
                if ($e->getMessage() == 'Get agent failed') {
                    // Bad ASN, use default
                    $session->agentSchemeNumber = $this->_params->homelet->defaultAgent;
                }
                else {
                    // Unknown exception, pass it up the line
                    throw new Zend_Exception('Filter agent ASN failed: ' . $e->getMessage());
                }
            }
        }
        elseif (!isset($session->agentSchemeNumber)) {
            // No ASN passed in so set to default agent
            $session->agentSchemeNumber = $this->_params->homelet->defaultAgent;
        }

        // Check to see if we have a csuid - if we do store it in a session variable - Sorry Phil
        if ($this->getRequest()->getParam('csu') !='') {
            $session->csu = $this->getRequest()->getParam('csu');
        }
        elseif (!isset($session->csu)) {
            // no passed parameter so default it to our web user 87
            $session->csu ="87";
        }

        // This could be quite yucky - I'm just trying to work out how to get the menu structure to work
        $mainMenu = $this->view->partial('partials/homelet-mainmenu.phtml', $menuData);
        $subMenu = $this->view->partial('partials/homelet-submenu.phtml', $menuData);
        $layout = Zend_Layout::getMvcInstance();
        $layout->getView()->mainMenu = $mainMenu;
        $layout->getView()->subMenu = $subMenu;
        if (isset($menuData['selected'])) { $layout->getView()->styling = $menuData['selected']; }

        // Load the site link urls from the parameters and push them into the layout
        $layout->getView()->urls = $this->_params->url->toArray();

        // Load session data into private variables
        $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
        if (isset($pageSession->quoteID)) $this->_quoteID = $pageSession->quoteID;
        if (isset($pageSession->webLeadSummaryId)) $this->_webLeadSummaryId = $pageSession->webLeadSummaryId;
        if (isset($pageSession->customerRefNo)) $this->_customerReferenceNumber = $pageSession->customerRefNo;

        if (isset($session->referrer)) {
            $this->_referrer = $session->referrer;
        }

        if (isset($session->agentSchemeNumber)) {
            $this->_agentSchemeNumber = $session->agentSchemeNumber;
        }
    }

	/**
 	* Throws out debug information for the premiums calculation
 	*
 	* Note: Should only be called from dev/staging!
 	*/
	public function debugAction() {
		if (APPLICATION_ENV != 'production') {
			$this->_helper->viewRenderer->setNoRender();
			$this->_helper->getHelper('layout')->disableLayout();
			
			$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($this->_quoteID);
			echo "Legacy Quote NUmber - ";
			$quote = $quoteManager->getModel();
			echo $quote->legacyID;
			echo "<br />";
			
			// Check what's added to this policy
			echo "Has Building Cover - ";
			echo $quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER)?'yes':'no';
			echo '<br />';
			
			echo "Has Contents Cover - ";
			echo $quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER)?'yes':'no';
			echo '<br />';
			
			echo "Has Stand-Alone EAS - ";
			echo $quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::EMERGENCY_ASSISTANCE)?'yes':'no';
			echo '<br />';
			
			echo "Has Boiler & Heating Cover - ";
			echo $quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::BOILER_HEATING)?'yes':'no';
			echo '<br />';
			
			echo "Has Prestige Rent Guarantee - ";
			echo $quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::RENT_GUARANTEE)?'yes':'no';
			echo '<br />';
			
			echo "Has Legal Expenses Cover - ";
			echo $quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::LEGAL_EXPENSES)?'yes':'no';
			echo '<br />';
			
			// Now run the calculate premiums function in verbose mode!
			$premiums = $quoteManager->calculatePremiums(true);
	
			Zend_Debug::dump($premiums);
		} else {
			// Throw a 404 error
            throw new Zend_Controller_Action_Exception("This page doesn't exist", 404);
		}
	}

	/**
 	* Blank action - just displays a landing page with a choice of portfolio/landlords insurance
 	*/
	public function indexAction () {
		
	}

    public function saveStep1(Zend_Form $pageForm) {
        // Get sub forms
        $subFormPersonalDetails = $pageForm->getSubForm('subform_personaldetails');
        $subFormDataProtection = $pageForm->getSubForm('subform_dataprotection');
        $subFormCorrespondenceDetails = $pageForm->getSubForm('subform_correspondencedetails');
        $subFormInsuredAddress = $pageForm->getSubForm('subform_insuredaddress');
        $subFormPolicyDetails = $pageForm->getSubForm('subform_policydetails');
        $subFormIdd = $pageForm->getSubForm('subform_idd');

        if (isset($this->_quoteID) && !is_null($this->_quoteID)) {
            $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($this->_quoteID);
        } else {
            // Create a new quote
            $quoteManager = new Manager_Insurance_LandlordsPlus_Quote();
            $this->_quoteID = $quoteManager->getModel()->ID;

            // Save the ID in the session for future use
            $session = new Zend_Session_Namespace('landlords_insurance_quote');
            $session->quoteID = $this->_quoteID;
        }

        // Check to see if we have a session
        $customerManager = new Manager_Core_Customer();

        // Check to see if we are already logged in
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
        $pageSession->quoteID = $this->_quoteID;
        if(!isset($this->_customerReferenceNumber)) {
            // Do we already have a legacy customer with this email address?
            $customer = $customerManager->getLegacyCustomerByEmailAddress(
                $subFormPersonalDetails->getElement('email_address')->getValue());

            // We don't have a session so we need to create a legacy customer/quote entry to save against
            if (!$customer) {
                $customer = $customerManager->createNewCustomer(
                    $subFormPersonalDetails->getElement('email_address')->getValue(), Model_Core_Customer::CUSTOMER, true);
            }

            // Now get the reference number from the newly created customer
            $customerRefNo = $customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER);
            $this->_customerReferenceNumber = $customerRefNo;
            $pageSession->customerRefNo = $customerRefNo;
        }
        else {
            // We are in session so just instantiate the customer manager with the existing reference number
            $customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $this->_customerReferenceNumber);
            $customerRefNo = $pageSession->customerRefNo;
        }

        $pageSession->CustomerDob = $subFormPersonalDetails->getElement('date_of_birth_at')->getValue();
        
        $quoteManager->setLegacyCustomerReference($customerRefNo);

        //Capture and store the insurance data protections.
        if ($subFormDataProtection->getElement('dpa_phone_post')->getValue() == 1) {
            $dpaPhonePost = 1;
        } else {
            $dpaPhonePost = 0;
        }

        if ($subFormDataProtection->getElement('dpa_sms_email')->getValue() == 1) {
            $dpaSMSEmail = 1;
        } else {
            $dpaSMSEmail = 0;
        }

        if ($subFormDataProtection->getElement('dpa_resale')->getValue() == 1) {
            $dpaResale = 1;
        } else {
            $dpaResale = 0;
        }


        // Update the customer record with the form data
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
        $customer->setCountry($subFormCorrespondenceDetails->getElement('country')->getValue());
        if ($subFormCorrespondenceDetails->getElement('cor_foreign_address')->getValue() == 1) {
            $customer->setIsForeignAddress(true);
        }
        else {
            $customer->setIsForeignAddress(false);
        }
        $customerManager->updateCustomer($customer);

        if ($auth->hasIdentity()) {
            // Customer is logged in - so we need to link the new customer to the old
            $customerID = $auth->getStorage()->read()->id;
            $customer = $customerManager->getCustomer(Model_Core_Customer::IDENTIFIER, $customerID);
            $customerManager->linkLegacyToNew($customerRefNo, $customerID);
        }

        // Update the customer with the new correspondence address
        $customer->setAddressLine(Model_Core_Customer::ADDRESSLINE1,
            $subFormCorrespondenceDetails->getElement('cor_address_line1')->getValue());
        $customer->setAddressLine(Model_Core_Customer::ADDRESSLINE2,
            $subFormCorrespondenceDetails->getElement('cor_address_line2')->getValue());
        $customer->setAddressLine(Model_Core_Customer::ADDRESSLINE3,
            $subFormCorrespondenceDetails->getElement('cor_address_line3')->getValue());

        $customer->setPostCode($subFormCorrespondenceDetails->getElement('cor_address_postcode')->getValue());

        // Whether a new customer or an existing customer without a DoB stored, allow the DoB supplied in the form to be
        // stored if what's there now is blank
        if (null === $customer->getDateOfBirthAt() || '0000-00-00' == $customer->getDateOfBirthAt()) {
            $customer->setDateOfBirthAt(
                Application_Core_Utilities::ukDateToMysql(
                    $subFormPersonalDetails->getElement('date_of_birth_at')->getValue()
                )
            );
        }

        $customerManager->updateCustomer($customer);

        // Update the quote with new values and save it
        $quoteManager->setStartDate(Application_Core_Utilities::ukDateToMysql(
            $subFormPolicyDetails->getElement('policy_start')->getValue()));
        $quoteManager->setEndDate(Application_Core_Utilities::ukDateToMysql(
            $subFormPolicyDetails->getElement('policy_end')->getValue()));

        // Check to see if the property is a 1000 flood area - if it is force the exclude_flood_cover flag
        $riskAreasDatasource = new Datasource_Insurance_LandlordsPlus_RiskAreas();
        $riskAreas = $riskAreasDatasource->getByPostcode(
            $subFormInsuredAddress->getElement('ins_address_postcode')->getValue());

        if ($riskAreas['floodArea']='1000') {
            $subFormInsuredAddress->getElement('exclude_flood_cover')->setValue(true);
        }

        // Add property details
        $quoteManager->addProperty(
            $subFormInsuredAddress->getElement('ins_address_postcode')->getValue(),

            // Town
            $subFormInsuredAddress->getElement('ins_address_line3')->getValue(),

            // County
            '',

            // Street & house number/name etc.. line1
            $subFormInsuredAddress->getElement('ins_address_line1')->getValue(),

            // Employed tenant
            $subFormInsuredAddress->getElement('tenants_type')->getValue(),

            // Agent managed?
            ($subFormInsuredAddress->getElement('through_letting_agent')->getValue() == 'yes') ? true : false,

            // How long property has been owned for
            $subFormInsuredAddress->getElement('owned_for')->getValue(),

            // NCV
            $subFormInsuredAddress->getElement('no_claims')->getValue(),

            // Exclude flood cover. Where they select 'yes' to say they WANT flood cover
            ($subFormInsuredAddress->getElement('exclude_flood_cover')->getValue() == 'no') ? true : false,

            // line2
            $subFormInsuredAddress->getElement('ins_address_line2')->getValue() // line2
        );

        // Set the new letting agent details if we have them
        $session = new Zend_Session_Namespace('homelet_global');
        $agentSchemeNumber = $session->agentSchemeNumber;

        // Store result to quote manager and session
        $quoteManager->setAgentSchemeNumber($agentSchemeNumber);
        $quoteManager->save();
        $session->agentSchemeNumber = $agentSchemeNumber;

        // Set the contact preferences
        $contactPreferences = new Model_Core_CustomerContactPreferences();
        $contactPreferences->addPreference(Model_Core_CustomerContactPreferences::EMAIL_METHOD);
        $quoteManager->setContactPreference($contactPreferences);

        //Record this WebLead, if not already done so. First create or
        //retrieve the WebLead summary.
        $webLeadManager = new Manager_Core_WebLead();
        if(empty($pageSession->webLeadSummaryId)) {
            $isNewWebLead = true;
            $webLeadSummary = $webLeadManager->createNewSummary();
        }
        else {
            $isNewWebLead = false;
            $webLeadSummary = $webLeadManager->getSummary($pageSession->webLeadSummaryId);
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
        $webLeadSummary->product = Model_Core_WebLeadProduct::LANDLORDSPLUS;
        $webLeadSummary->quoteNumber = $quoteManager->getLegacyID();
        $webLeadManager->updateSummary($webLeadSummary);

        //Update the blob, serialize and store.
        $webLeadBlob->blob = Zend_Json::encode($_POST);
        $webLeadBlob->blobChecksum = crc32($webLeadBlob->blob);
        $webLeadManager->updateBlob($webLeadBlob);

        //Finally, record the WebLead identifier in the page session.
        $pageSession->webLeadSummaryId = $webLeadSummary->webLeadSummaryId;


        //Capture and store the Insurance and WebLead data protections.
        $this->_saveDPA(
            $customerRefNo,
            Model_Core_DataProtection_ItemEntityTypes::INSURANCE,
            $dpaPhonePost,
            $dpaSMSEmail,
            $dpaResale);

        $this->_saveDPA(
            $webLeadSummary->webLeadSummaryId,
            Model_Core_DataProtection_ItemEntityTypes::WEBLEAD,
            $dpaPhonePost,
            $dpaSMSEmail,
            $dpaResale);
    }

    public function step1Action () {

	$pageForm = new LandlordsInsuranceQuote_Form_Step1();

        // Check to see if we are already logged in
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));
		
		// Tell page to use AJAX validation as we go
		$this->view->headScript()->appendScript(
			'var ajaxValidate = true; var ajaxValidatePage = 1;'
		);

        if ($this->getRequest()->isPost() && isset($_POST['next'])) {

            // We need to validate and save the data
            try {
                $valid = $this->_formStepCommonValidate($pageForm, 1);
            }
            catch (LandlordsInsuranceQuote_Form_Exception_RiskAreaNotFoundException $e) {
                // Deal with unknown risk area postcodes by doing something similar to an Underwriting referral

                $postcode = $this->getRequest()->getParam('ins_postcode');
                $postcode = preg_replace('/[^\w ]/', '', $postcode);

                $subject = $this->_params->ins->riskAreaNotFound->notification->subject;
                $subject = str_replace('[postcode]', $postcode, $subject);

                // Send an e-mail to our sales people with all the data from this page
                $this->sendInformationRequiredEmail(
                    $this->getRequest()->getPost(),
                    $this->_params->ins->riskAreaNotFound->notification->toAddress,
                    $this->_params->ins->riskAreaNotFound->notification->toName,
                    $this->_params->ins->riskAreaNotFound->notification->fromAddress,
                    $this->_params->ins->riskAreaNotFound->notification->fromName,
                    $subject,
                    $this->_params->ins->riskAreaNotFound->notification->bodyPrepend
                );

                // Send end user on to info page and stop here
                $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/information-required?page=1');
                return;
            }

			if ($valid) {
                $customerManager = new Manager_Core_Customer();

                $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
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

                    $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/login');
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

        // Set the initial agent scheme number and agent lookup from session if necessary
        $session = new Zend_Session_Namespace('homelet_global');
        $agentSchemeNumber = Manager_Core_Agent::filterAsn($session->agentSchemeNumber);

        // Render the page unless we have been redirected
        $subFormPersonalDetails = $pageForm->getSubForm('subform_personaldetails');
        if ($auth->hasIdentity()) {
            // If logged in, prevent the email field from being modified
            $subFormPersonalDetails->setReadOnly();
        }

        // Render the page unless we have been redirected
        $this->view->form = $pageForm;
        $this->render('step');
	}

    public function loginAction()
    {
        $form = new LandlordsInsuranceQuote_Form_Login();
        $request = $this->getRequest();

        $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
        $pageForm = new LandlordsInsuranceQuote_Form_Step1();
        $pageForm->populate($pageSession->step1FormValues);

        $subFormPersonalDetails = $pageForm->getSubForm('subform_personaldetails');
        $form->getElement('email')->setValue($subFormPersonalDetails->getElement('email_address')->getValue());

        // Look up customer details if possible, note if the customer has already validated their My HomeLet account
        $customerValidated = false;
        $customerFirstName = '';
        $customerManager = new Manager_Core_Customer();
        $customer = $customerManager->getCustomerByEmailAddress($form->getElement('email')->getValue('email'));
        if ($customer) {
            $customerValidated = $customer->getEmailValidated();
            $customerFirstName = $customer->getFirstName();
        }

        if ($request->isPost()) {
            if (isset($_POST['resendValidation'])) {
                // User wants a new validation link
                if ($customer) {
                    $customer->sendAccountValidationEmail();
                    $form->setDescription('Thank you, we&rsquo;ve sent you an email to validate your My HomeLet account, please make sure you check your inbox and your junk folder just in case.');
                }
                else {
                    $form->setDescription('Sorry, we could not find a customer with that email address. Please check the details you entered are correct and try again.');
                }
            }
            else if (isset($_POST['forgottenPassword'])) {
                // User has forgotten password
                if ($customer) {
                    $customer->resetPassword();
                    $customerManager->updateCustomer($customer);
                    $newPassword = $customer->getPassword();
                    $customerID = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);

                    // Now we have a new customer password - we also (sadly) need to update ALL the linked legacy customer entries
                    // or next time an old quote is opened it over-writes this new password (you couldn't make this stuff up!)
                    $legacyCustomerMap = new Datasource_Core_CustomerMaps();
                    $legacyIDs = $legacyCustomerMap->getLegacyIDs($customerID);

                    foreach($legacyIDs as $legacyID) {
                        $oldCustomer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $legacyID);
                        $oldCustomer->setPassword($newPassword);
                        $customerManager->updateLegacyCustomer($oldCustomer);
                    }

                    // That's hopefully done it so we can show a nice message
                    $form->setDescription('Thank you, we&rsquo;ve sent you an email to reset your password, please make sure you check your inbox and your junk folder just in case.');
                }
                else {
                    $form->setDescription('Sorry, we could not find a customer with that email address. Please check the details you entered are correct and try again.');
                }
            }
            else if ($form->isValid($_POST)) {
                // Save step1 form data and move to step 2

                // Null off all reference numbers that may have been set via a new customer who has gone
                // back and used an existing customer email address.
                $this->_customerReferenceNumber = null;
                $pageSession->CustomerRefNo = null;
                $this->quoteID = null;
                $pageSession->quoteID = null;
                $this->_webLeadSummaryId = null;
                $pageSession->webLeadSummaryId = null;

                $this->saveStep1($pageForm);
                $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/step2');
            }
            else {
                $form->setDescription('Sorry, we could not log you in with the details given. Please check the details you entered are correct and try again.');
            }
        }

        $this->view->form = $form;
        $this->view->customerValidated = $customerValidated;
        $this->view->customerFirstName = $customerFirstName;
    }

	public function step2Action () {
		$pageForm = new LandlordsInsuranceQuote_Form_Step2();
		
		// Tell page to use AJAX validation as we go
		$this->view->headScript()->appendScript(
			'var ajaxValidate = true; var ajaxValidatePage = 2;'
		);
		
		$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($this->_quoteID);

		$policynumber=$quoteManager->getQuote();

        // Push the QHLI to something usable by the templates.
        $this->view->qhli = $policynumber;
		$channelManager = new Manager_Core_Channel();
		$channel = $channelManager->getChannelName($policynumber);
		$isNewQuote=1;
		if ($channel) {
			$isNewQuote=0;
		} 
		$channelManager->setChannel($policynumber, 'WEB', $isNewQuote);

		if ($this->getRequest()->isPost()) {
			// We need to validate and save the data
			$valid = $this->_formStepCommonValidate($pageForm, 2);
			
			if ($valid) {
				
                            //Update the WebLead summary and create a STEP2 blob.
                            $session = new Zend_Session_Namespace('landlords_insurance_quote');

				$webLeadManager = new Manager_Core_WebLead();
				$webLeadSummary = $webLeadManager->getSummary($session->webLeadSummaryId);
				$webLeadSummary->lastUpdatedTime = $this->_offsetDate();
				$webLeadManager->updateSummary($webLeadSummary);
				
				//Determine if a new STEP2 blob needs to be created, or an existing one retrieved.
				if($webLeadManager->getBlobExists($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP2)) {
					$webLeadBlob = $webLeadManager->getBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP2);
				}
				else {
					$webLeadBlob = $webLeadManager->createNewBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP2);
				}
				
				//Update the blob and store
				$webLeadBlob->blob = Zend_Json::encode($_POST);
				$webLeadBlob->blobChecksum = crc32($webLeadBlob->blob);
				$webLeadManager->updateBlob($webLeadBlob);
				
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
	

	public function step3Action () {
		$pageForm = new LandlordsInsuranceQuote_Form_Step3();
		
		// Tell page to use AJAX validation as we go
		$this->view->headScript()->appendScript(
			'var ajaxValidate = true; var ajaxValidatePage = 3;'
		);

        // Get the session
        $session = new Zend_Session_Namespace('landlords_insurance_quote');

        // Get customer details
        $customerManager = new Manager_Core_Customer();
        $customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $this->_customerReferenceNumber);

        // Set up the quote manager so we can make use of the QHLI in the templates
        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($this->_quoteID);
        $policynumber=$quoteManager->getQuote();
        $this->view->qhli = $policynumber;

		if ($this->getRequest()->isPost()) {
			// We need to validate and save the data
			$valid = $this->_formStepCommonValidate($pageForm, 3);

			if ($valid) {
                $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
                $data = $pageForm->getValues();

				//Update the WebLead summary and create a STEP3 blob.
				$webLeadManager = new Manager_Core_WebLead();
				$webLeadSummary = $webLeadManager->getSummary($session->webLeadSummaryId);
				$webLeadSummary->lastUpdatedTime = $this->_offsetDate();
				$webLeadManager->updateSummary($webLeadSummary);
				
				//Determine if a new STEP3 blob needs to be created, or an existing one retrieved.
				if($webLeadManager->getBlobExists($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP3)) {
					$webLeadBlob = $webLeadManager->getBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP3);
				}
				else {
					$webLeadBlob = $webLeadManager->createNewBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP3);
				}
				
				//Update the blob and store
				$webLeadBlob->blob = Zend_Json::encode($_POST);
				$webLeadBlob->blobChecksum = crc32($webLeadBlob->blob);
				$webLeadManager->updateBlob($webLeadBlob);

                // Everything has been saved OK so navigate to next step
                $this->_formStepCommonNavigate(3);
                return;
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
	
	public function step4Action () {

		$pageForm = new LandlordsInsuranceQuote_Form_Step4();
		
		// Tell page to use AJAX validation as we go
		$this->view->headScript()->appendScript(
			'var ajaxValidate = true; var ajaxValidatePage = 4;'
		);
		
		// Check to make sure the user has actually added a product - if they haven't stop them going forward
		$session = new Zend_Session_Namespace('landlords_insurance_quote');

        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
        if ($quoteManager->productCount() == 0 && !isset($_POST['back'])) {
            $this->view->stepNum = "no-product";

            // AJD - Not happy about this, but it's the way HLF was built.
            $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
            $policyNumber = $quoteManager->getLegacyID();
            $this->view->quoteID = $policyNumber;
            $this->render('no-cover-selected');
            return;
        }
        elseif ($quoteManager->productCount() == 0 && isset($_POST['back'])) {
            // No products/options selected, send them back to step 2
            $this->_helper->redirector->gotoUrl(
                '/landlords/insurance-quote/step2'
            );
            return;
        }
		elseif ($this->getRequest()->isPost()) {
			// We need to validate and save the data
			$valid = $this->_formStepCommonValidate($pageForm, 4);
			
			if ($valid) {
				//Update the WebLead summary and create a STEP4 blob.
				$session = new Zend_Session_Namespace('landlords_insurance_quote');
                
				$webLeadManager = new Manager_Core_WebLead();
				$webLeadSummary = $webLeadManager->getSummary($session->webLeadSummaryId);
				$webLeadSummary->lastUpdatedTime = $this->_offsetDate();
				$webLeadManager->updateSummary($webLeadSummary);
				
				//Determine if a new STEP4 blob needs to be created, or an existing one retrieved.
				if($webLeadManager->getBlobExists($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP4)) {
					$webLeadBlob = $webLeadManager->getBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP4);
				}
				else {
					$webLeadBlob = $webLeadManager->createNewBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP4);
				}
				
				//Update the blob and store
				$webLeadBlob->blob = Zend_Json::encode($_POST);
				$webLeadBlob->blobChecksum = crc32($webLeadBlob->blob);
				$webLeadManager->updateBlob($webLeadBlob);
				
				//Store the underwriting datas not already stored.
				$pageForm->applyAnswersLogics();
				$pageForm->applyAdditionalInformationLogics();
                $endorsementManager = new Manager_Insurance_LandlordsPlus_Endorsement();
				$endorsements = $endorsementManager->getEndorsementsRequired($session->quoteID);
			
				//Look for a flood endorsement, and if found, identify if it is applicable (the
				//customer may have chosen to pay a premium to cover flood).
				$finalEndorsementArray = array();
				foreach($endorsements as $currentEndorsement) {

					if($currentEndorsement->getEndorsementType()->getName() == Model_Insurance_EndorsementTypes::FLOOD_EXCLUSION) {
				
						$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
						$propertiesArray = $quoteManager->getProperties();
						if($propertiesArray[0]['exclude_flood_cover'] == 0) {
						
							//Flood cover IS provided, which means the endorsement is not applicable.
							continue;
						}
					}
					
					if($endorsementManager->getIsEndorsementAlreadyApplied($currentEndorsement)) {
					
						//No need to add the endorsement again...
						continue;
					}
					
					$finalEndorsementArray[] = $currentEndorsement;
				}

				if(count($finalEndorsementArray) > 0) {

					$endorsementManager->insertEndorsements($finalEndorsementArray);
				}
				
				
				//Execute the underwriting referral logics.
				$referralManager = new Manager_Insurance_LandlordsPlus_Referral();
				$reasons = $referralManager->getReferralReasons($session->quoteID);
				if(!empty($reasons)) {
                                    // Notify Underwriting of the referral.
                                    $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
                                    $policyNumber = $quoteManager->getLegacyID();
                                    $refNo = $quoteManager->getLegacyCustomerReference();
					
                                    $notificationManager = new Manager_Core_Notification();
                                    $notesManager =  new Manager_Core_Notes();

                                    $otherReason = '';
                                    for ($i = 0; $i < count($reasons); $i++) {
                                        if ('Referral due to buildings type' === $reasons[$i]) {
                                            $otherReason = "Policy has been referred due to property type of Other.";
                                            break;
                                        }
                                    }
                                    if ($otherReason) {
                                        $notesManager->save($policyNumber, "Refer to UW - " . $otherReason);
                                        $notificationManager->_reason = "Other";
                                    } else {
                                        $notesManager->save($policyNumber, "Refer to UW - The application has fallen outside UW criteria.");
                                    }
                                    $notificationManager->notifyUnderwriting($policyNumber, $refNo);
					
                                    $referralManager->setToRefer($session->quoteID);
					
                                    //Redirect to referredAction.
                                    $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/referred');
                                    return;
				}
				
				// Everything has been saved ok so navigate to next step
				$this->_formStepCommonNavigate(4);
				return;
			} elseif (isset($_POST['back'])) {
				$this->_formStepCommonNavigate(4);
				return;
			}
		}

        // If there's no quote manager, then set it up so we can make use of the
        // QHLI in the templates.
        if (!isset($quoteManager)) {
            $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
        }
        $this->view->qhli = $quoteManager->getLegacyID();
		
		// Load the element data from the database if we can
		if ($this->_formStepCommonPopulate($pageForm, 4))
		{
			// Render the page unless we have been redirected
			$this->view->form = $pageForm;
			$this->render('step');
		}
	}

    /**
     * Send information required e-mail to landlord sales team using data from current POST.
     *
     * @param array $userData
     * @param string $toAddress
     * @param string $toName
     * @param string $fromAddress
     * @param string $fromName
     * @param string $subject
     * @param string $bodyPrepend
     */
    private function sendInformationRequiredEmail(
        $userData,
        $toAddress,
        $toName,
        $fromAddress,
        $fromName,
        $subject = 'More Information Required',
        $bodyPrepend = ''
    )
    {
        $body = ('' != $bodyPrepend) ? "{$bodyPrepend}\n\n\n\n" : '';

        foreach ($userData as $key => $val) {
            $friendlySafeKey = $key;
            $friendlySafeKey = preg_replace('/\W/', '', $friendlySafeKey);
            $friendlySafeKey = str_replace('_', ' ', $friendlySafeKey);
            $friendlySafeKey = ucwords($friendlySafeKey);

            $safeVal = $val;
            $safeVal = preg_replace('/[^\w \.\,\/\(\)\-\+\+\*\£\$\@\!\?\~\|\\\'\"\:\;\[\]]/', '', $safeVal);

            $body .= sprintf(
                '%s: %s%s',
                $friendlySafeKey,
                $safeVal,
                "\n\n"
            );
        }

        $mail = new Application_Core_Mail();

        $mail
            ->setTo($toAddress, $toName)
            ->setFrom($fromAddress, $fromName)
            ->setSubject($subject)
            ->setBodyText($body)
        ;

        $mail->send();
    }

    /**
     * Display the "information required" page and end the session here.
     */
    public function informationRequiredAction()
    {
        $page = $this->getRequest()->getParam('page');

        $this->view->stepNum = $page;

        // Unset the session to prevent navigation to other steps
        Zend_Session::namespaceUnset('landlords_insurance_quote');
    }

    /**
     * This function handles the referred underwriting state, including unsetting the session
     *
     * @return void
     */
    public function referredAction() {
        // Show the policy number to the end user
		$session = new Zend_Session_Namespace('landlords_insurance_quote');
        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
        $this->view->policyNumber = $quoteManager->getLegacyID();

        // Pop an e-mail to the end user
        $params = Zend_Registry::get('params');

        $customerManager = new Manager_Core_Customer();
        $customer = $customerManager->getCustomer(
            Model_Core_Customer::LEGACY_IDENTIFIER,
            $this->_customerReferenceNumber
        );

        $replacements = array(
            'title' => $customer->getTitle(),
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'fullName' => $customer->getTitle() . ' ' . $customer->getFirstName() . ' ' . $customer->getLastName(),
            'quoteNumber' => $this->view->policyNumber,
            'imageBaseUrl' => $this->_params->weblead->mailer->imageBaseUrl,
        );

        $subjectLine = $params->weblead->mailer->landlord->referredQuote->subject;
        foreach ($replacements as $key => $val) {
            $subjectLine = str_replace("[{$key}]", $val, $subjectLine);
        }

        $replacements['pageTitle'] = $subjectLine;

        $template = $params->weblead->mailer->landlord->referredQuote->template;

        $mail = new Application_Core_Mail();

        $mail
            ->setTo($customer->getEmailAddress(), $replacements['fullName'])
            ->setFrom($params->weblead->mailer->fromAddress, $params->weblead->mailer->fromName)
            ->setSubject($subjectLine)
            ->applyTemplate($template, $replacements, true)
        ;

        $mail->send();

        // Tag quote as mailer sent, stops abandone quote mailer cron from sending a continue mailer
        $webLeadManager = new Manager_Core_WebLead();
        $webLeadManager->setIsMailerSent($quoteManager->getLegacyID(), true);

        // Unset the session to prevent navigation to other steps
        Zend_Session::namespaceUnset('landlords_insurance_quote');
    }
	
    /**
     * Handles the previous claims pop-up on the Underwriting questions form.
     */
	public function claimsDialogAction() {
		$claimsDialogForm = new LandlordsInsuranceQuote_Form_ClaimsDialog();
		
		// This controller is called within a popup (facebox style) so doesn't require a layout file
		$this->_helper->getHelper('layout')->disableLayout();
		$this->view->form = $claimsDialogForm;
		
		//Get the customer reference number and use this to retrieve all previous claims.
		$session = new Zend_Session_Namespace('landlords_insurance_quote');
        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
		$customerReferenceNumber = $quoteManager->getLegacyCustomerReference();
		
		$claimsManager = new Manager_Insurance_PreviousClaims();
		$claimsArray = $claimsManager->getPreviousClaims($customerReferenceNumber);
		
		//Display the previous claims on the dialog.
		$model = array();
		if(!empty($claimsArray)) {
			foreach($claimsArray as $claim) {
				$model[] = array('claim' => $claim);
			}
		}
		
		$this->view->claimsList = $model;
	}
	
	public function bankInterestDialogAction() {
		$dialogForm = new LandlordsInsuranceQuote_Form_BankInterestDialog();
		
		// This controller is called within a popup (facebox style) so doesn't require a layout file
		$this->_helper->getHelper('layout')->disableLayout();
		$this->view->form = $dialogForm;
		
		//Get the customer reference number and policynumber, and use thes to retrieve all
		//bank interests.
		$session = new Zend_Session_Namespace('landlords_insurance_quote');
        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
		
        $customerReferenceNumber = $quoteManager->getLegacyCustomerReference();
		$policyNumber = $quoteManager->getLegacyID();
		
		//Display the bank interests on the dialog.
		$bankInterestManager = new Manager_Insurance_LegacyBankInterest();
		$bankInterestArray = $bankInterestManager->getAllInterests($policyNumber, $customerReferenceNumber);
		$model = array();
		if(!empty($bankInterestArray)) {
			foreach($bankInterestArray as $bankInterest) {
				$model[] = array('bankInterest' => $bankInterest);
			}
		}
		
		$this->view->bankInterestList = $model;
	}

    /**
     * Step 5 - Payment, incorporates direct debit form
     *
     * @return void
     */
    public function step5Action ()
    {
        $pageForm = new LandlordsInsuranceQuote_Form_Step5();

        // Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 5;'
        );

        if ($this->getRequest()->isPost()) {
            // We need to validate and save the data
            $valid = $this->_formStepCommonValidate($pageForm, 5);

            if ($valid) {
                $data = $pageForm->getValues();
                $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($this->_quoteID);

                $quoteManager->setPayBy(strtoupper($data['subform_paymentselection']['payment_method']));
                $quoteManager->setPayFrequency(strtoupper($data['subform_paymentselection']['payment_frequency']));
                $quoteManager->save();

                // Temporarily store payment selection details in session ready for use during bank confirmation step
                //   for DD payers
                $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
                $pageSession->paymentSelectionDetails = $data['subform_paymentselection'];

                // Everything has been saved ok so navigate to next step
                $this->_formStepCommonNavigate(5);
                return;
            }
            elseif (isset($_POST['back'])) {
                $this->_formStepCommonNavigate(5);
                return;
            }
        }

        // If there's no quote manager, then set it up so we can make use of the
        // QHLI in the templates.
        if (!isset($quoteManager)) {
            $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($this->_quoteID);
        }
        $this->view->qhli = $quoteManager->getLegacyID();

        // Load the element data from the database if we can
        if ($this->_formStepCommonPopulate($pageForm, 5))
        {
            // Render the page unless we have been redirected
            $this->view->form = $pageForm;
            $this->render('step');
        }
    }

	public function ccFailedAction(){
		// This function intentionall left blank :oP
	}
	
    public function ccAction()
    {
		$pageForm = new LandlordsInsuranceQuote_Form_CreditCard();
		$pageForm->setAction($this->_params->secpay->get('host'));
		
		$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($this->_quoteID);
		$premiums = $quoteManager->calculatePremiums();
		$fees = $quoteManager->getFees();
		
		$remotePassword = $this->_params->secpay->get('remotepassword');
		// TODO: We need the payment frequency here, for testing I'm assuming monthly
		$this->view->payMonthly = $paymentFequency = $quoteManager->getPayFrequency();
		
		if ($paymentFequency == "MONTHLY") {
			$amount = number_format($premiums['totalGrossMonthlyPremium'] + $premiums ['totalGrossMonthlyIPT'] + $fees['landlords_insurance_plus_monthly_admin'],2,'.','');
		} else {
			$amount = number_format($premiums['totalGrossAnnualPremium'] + $premiums['totalGrossAnnualIPT'] + $fees['landlords_insurance_plus_yearly_admin'],2,'.','');
		}
		$this->view->amount = $amount;
		$policyNumber = str_replace("Q", "P", $quoteManager->getPolicyNumber());
		
		$formData = array();
		$formData['merchant'] = $this->_params->secpay->get('merchant');
		$formData['policynumber'] = $policyNumber;
		$formData['trans_id'] = $policyNumber;
		$formData['refno'] = $this->_customerReferenceNumber ;
		$formData['paymentfrequency'] = $paymentFequency;
		$formData['amount'] = $amount;
		$successCallback = $this->_params->secpay->landlords->get('success_callback');
		$failedCallback = $this->_params->secpay->landlords->get('failed_callback');
		// Secpay uses semicolon seperated values for the two calbacks;
		$formData['callback'] = $successCallback.";".$failedCallback;

		$formData['template'] = $this->_params->secpay->landlords->get('template');
		$formData['repeat'] = $this->_params->secpay->get('repeat');
		$formData['test_status'] = $this->_params->secpay->get('test_status');
		$formData['test_mpi_status'] = $this->_params->secpay->get('test_mpi_status');
		$formData['usage_type'] = $this->_params->secpay->get('usage_type');
		$formData['cb_flds'] = "refno:card_type:policynumber:paymentfrequency:webleadsummaryid";
		$formData['cb_card_type'] =  "";
		$formData['webleadsummaryid'] = $this->_webLeadSummaryId;
		
		$digestkey = md5($policyNumber.$amount.$remotePassword);
		
		$formData['digest'] = $digestkey;
		// Render the page unless we have been redirected
		$this->view->formAction = $this->_params->secpay->get('host');
		$pageForm->populate($formData);
		$html = $pageForm->render();
		$this->view->form = $pageForm;
	}
	
    public function ccconfirmationAction()
    {
        $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');

		$request = $this->getRequest();
		$remotePassword = $this->_params->secpay->get('remotepassword');
		$this->_customerReferenceNumber = $request->getParam('refno');
		$this->_policyNumber = $request->getParam('policynumber');
		
		$hash = $request->getParam('hash');
		
		if ($hash == ""){ 
			$this->_helper->redirector->gotoUrl('/landlords/insurance-quote/cc-failed');
			exit();
		}
		
		$policyNumber = $this->_policyNumber;
		$quoteNumber = str_replace("P", "Q", $policyNumber);
		
		// Check to see if this quote has already been converted to a policy - if it has we don't want to do any of the below
		$policyManager = new Manager_Insurance_LegacyPolicy();
		$policy = $policyManager->getByPolicyNumber($policyNumber);

        // My HomeLet registration/login form and processing
        $this->view->form = $this->registrationFormProcess($policyNumber);

        // Set the completed status of the quote to true
        $this->view->completed = true;

		if (is_null($policy)) {		
			$this->_webLeadSummaryId = $request->getParam('webleadsummaryid');
			
			$url = $request->getRequestUri();
			$pos = strpos($url, "hash=");
			$url = substr($url, 0, $pos);
			$url .= $remotePassword;
	
			// Check that the callback has a valid hash
			if(md5($url) != $hash) {
				// If it doesn't then log an error message and re-direct to fraud page
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
	            $log->crit('Secpay fraud detected (landlords insurance quote)');
				
				$this->_helper->redirector->gotoUrl('/landlords/insurance-quote/fraud-attempt');
				exit();
			}
			
			// All tickety-Boo - save payment card details
			$customerReferenceNumber = $request->getParam('refno');
			$quoteManager = new Manager_Insurance_LandlordsPlus_Quote(null, $quoteNumber);
			$quoteNumber=$quoteManager->getPolicyNumber();
			
			$agentSchemeNumber = Manager_Core_Agent::filterAsn($quoteManager->getAgentSchemeNumber());
			$creditCard = new Datasource_Core_CreditCard_Payment();
			$creditCard->saveDetails($request->getParams());
			
			$schedule = new Manager_Insurance_Schedule();
			$schedule->save($customerReferenceNumber, $quoteManager);
			
			$quoteToPolicyManager = new Manager_Insurance_LandlordsPlus_QuoteToPolicy();
			$quoteToPolicyManager->changeQuoteToPolicy($quoteNumber);
			
			$months=($request->getParam('paymentfrequency')=='MONTHLY') ? 1 : 12;
			
			$disbursement = new Manager_Core_Disbursement();
			$disbursement->processDisbursement($policyNumber,$request->getParam('amount'),$months,"CC");
			
			//Update the WebLeads
			$webLeadManager = new Manager_Core_WebLead();
		    $webLeadSummary = $webLeadManager->getSummary($this->_webLeadSummaryId);
		    $webLeadSummary->quoteNumber = $policyNumber;
		    $webLeadSummary->status = Model_Core_WebLeadStatus::COMPLETE;
			$webLeadSummary->completedTime = $this->_offsetDate();
		    $webLeadManager->updateSummary($webLeadSummary);
			
            // Document management stuff, only runs on first conversion of quote -> policy
            $documentManager = new Manager_Insurance_Document();
            $documentManager->createAndPostDocument($policyNumber, Model_Insurance_LandlordsPlus_DocumentTypes::NEW_POLICY_DOCS);
            $documentArray = $documentManager->getDocuments($policyNumber, Model_Insurance_LandlordsPlus_DocumentTypes::NEW_POLICY_DOCS);
            $firstDocument = $documentArray[0];

            // Put the document number into session so that it can be displayed from this action and later in
            // the registration confirmation action
            if (!is_null($firstDocument)) {
                $pageSession->documentNumber = $firstDocument->request_hash;
                $this->view->documentNumber = $pageSession->documentNumber;
            }

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

		} else {
			$agentSchemeNumber = Manager_Core_Agent::filterAsn($policy->agentSchemeNumber);
		}

		// Fetch the HOMELETUK.COM legacy domain
		// Please don't change this again it is SUPPOSED to be the old crappy php4 box

		$whiteLabelManager  = new Manager_Core_WhiteLabel();
		$whiteLabelData = new Model_Core_WhiteLabel();
		$whiteLabelData = $whiteLabelManager->fetchByAgentSchemeNumber($agentSchemeNumber);
		
		// Put domain name of the legacy server in the view
		$this->view->domain = $this->_params->homelet->legacyDomain;
		
		$this->view->companyName = $whiteLabelData->companyName;
		$this->view->companyName = "HomeLet";
		
		$this->view->policyNumber = $policyNumber;
	}
	
	public function fraudAttemptAction(){
		
	}

    /**
     * Controller for the direct debit payment page
     *
     * @return void
     */
    public function ddAction()
    {
        $pageForm = new LandlordsInsuranceQuote_Form_BankConfirmation();
        $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');

        // Tell page NOT to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = false; var ajaxValidatePage = \'dd\';'
        );

        // Look up bank details to show in view
        $bankManager = new Manager_Core_Bank();
        $this->view->branchDetails = $bankManager->getBranchDetail(
            $pageSession->paymentSelectionDetails['bank_sortcode_number']
        );

        // Drop the branch address lines into an array for accessing on the
        // front-end.
        $this->view->branchLines = array(
            $this->view->branchDetails->bankNameFull,
            $this->view->branchDetails->addressLine1,
            $this->view->branchDetails->addressLine2,
            $this->view->branchDetails->addressLine3,
            $this->view->branchDetails->addressLine4,
            $this->view->branchDetails->town,
            $this->view->branchDetails->county,
            $this->view->branchDetails->postCode,
        );

        if ($this->getRequest()->isPost()) {

            $valid = $this->_formStepCommonValidate($pageForm, 'dd');

            if ($valid && isset($_POST['next'])) {

                // Form is valid and the user has confirmed the bank branch details
                $pageSession->completed['dd'] = true; // Mark page as valid, so user can progress

                $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($this->_quoteID);

                // Save the direct debit details and redirect to confirmation page
                $ddData = new Model_Core_Directdebit();
                $ddData->refNo = $this->_customerReferenceNumber;
                $ddData->policyNumber = $quoteManager->getPolicyNumber();
                $ddData->accountName = $pageSession->paymentSelectionDetails['dd_accountname'];
                $ddData->accountNumber = $pageSession->paymentSelectionDetails['bank_account_number'];
                $ddData->sortCode = str_replace('-', '', $pageSession->paymentSelectionDetails['bank_sortcode_number']);

                $startDate = $quoteManager->getStartDate();
                $firstPayMonth = date('Y-m-d', strtotime("{$startDate} + 1 month"));

                $ddData->paymentDate = $firstPayMonth;
                $ddData->paymentFrequency = ucfirst(strtolower($quoteManager->getPayBy()));

                // Instantiate a DD manager
                $ddPayment = new Manager_Core_Directdebit();
                // Save the stuffs
                $ddPayment->save($ddData);

                $this->_formStepCommonNavigate('dd');
                return;
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
            $this->view->stepNum = 'dd';
            $this->render('step');
        }
    }

    /**
     * Action for the Confirmation of a successful direct debit Payment
     *
     * @return void
     */
    public function ddconfirmationAction()
    {
        $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');

        $policyNumber = $this->getRequest()->getParam('pn');
        $quoteNumber = str_replace('Q', 'P', $policyNumber);

		// Check to see if this quote has already been converted to a policy - if it has we don't want to do any of the below
		$policyManager = new Manager_Insurance_LegacyPolicy();
		$policy = $policyManager->getByPolicyNumber($policyNumber);

        // My HomeLet registration/login form and processing
        $this->view->form = $this->registrationFormProcess($policyNumber);

        // Set the completed status of the quote to true
        $this->view->completed = true;

		if (is_null($policy)) {		
			$refNo = $this->_customerReferenceNumber;
			// Set up the validators and filters
			$filters = array();
			$validators = array ();
			$request = $this->getRequest();
			$input = new Zend_Filter_Input($filters, $validators, $request->getParams());
			if ($input->isValid()) {
				// Update Pay Status
				$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($this->_quoteID);
				$quoteNumber = $quoteManager->getPolicyNumber();
				$schedule = new Manager_Insurance_Schedule();
				$schedule->save($refNo, $quoteManager);
				
				$quoteToPolicyManager = new Manager_Insurance_LandlordsPlus_QuoteToPolicy();
				$quoteToPolicyManager->changeQuoteToPolicy($quoteNumber);
				
				
				//Update the WebLeads
				$session = new Zend_Session_Namespace('landlords_insurance_quote');
				$webLeadManager = new Manager_Core_WebLead();
			    $webLeadSummary = $webLeadManager->getSummary($session->webLeadSummaryId);
			    $webLeadSummary->quoteNumber = $policyNumber;
			    $webLeadSummary->status = Model_Core_WebLeadStatus::COMPLETE;
				$webLeadSummary->completedTime = $this->_offsetDate();
			    $webLeadManager->updateSummary($webLeadSummary);
				
				$agentSchemeNumber = Manager_Core_Agent::filterAsn($quoteManager->getAgentSchemeNumber());

                // Document management stuff, only runs on first conversion of quote -> policy
                $documentManager = new Manager_Insurance_Document();
                $documentManager->createAndPostDocument($policyNumber, Model_Insurance_LandlordsPlus_DocumentTypes::NEW_POLICY_DOCS);
                $documentArray = $documentManager->getDocuments($policyNumber, Model_Insurance_LandlordsPlus_DocumentTypes::NEW_POLICY_DOCS);
                $firstDocument = $documentArray[0];

                // Put the document number into session so that it can be displayed from this action and later in
                // the registration confirmation action
                if (!is_null($firstDocument)) {
                    $pageSession->documentNumber = $firstDocument->request_hash;
                }
            }

            // If a document number exists from before, pass it into the view
            if (isset($pageSession->documentNumber)) {
                $this->view->documentNumber = $pageSession->documentNumber;
            }

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

		} else {
			$agentSchemeNumber = Manager_Core_Agent::filterAsn($policy->agentSchemeNumber);
		}

		// Put Policy number in the view
		$this->view->policyNumber = $policyNumber;
		
		// Fetch the HOMELETUK.COM legacy domain
		// Please don't change this again it is SUPPOSED to be the old crappy php4 box

		// Put domain name of the legacy server in the view
		$this->view->domain = $this->_params->homelet->legacyDomain;
		
		$whiteLabelManager  = new Manager_Core_WhiteLabel();
		$whiteLabelData = new Model_Core_WhiteLabel();
		$whiteLabelData = $whiteLabelManager->fetchByAgentSchemeNumber($agentSchemeNumber);
		$this->view->companyName = $whiteLabelData->companyName;
		$this->view->companyName = "HomeLet";
	}

    /**
     * Action for the confirmation of a successful My HomeLet registration
     *
     * @return void
     */
    public function registrationConfirmationAction()
    {
        $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');

        $policyNumber = $this->getRequest()->getParam('pn');
        $policyNumber = preg_replace('/[^\w\/]/', '', $policyNumber);

        // Put Policy number in the view
        $this->view->policyNumber = $policyNumber;

        // Fetch the HOMELETUK.COM legacy domain
        // Please don't change this again it is SUPPOSED to be the old crappy php4 box

        // Put domain name of the legacy server in the view
        $this->view->domain = $this->_params->homelet->legacyDomain;

        $this->view->companyName = "HomeLet";

        // If a document number exists from before, pass it into the view
        if (isset($pageSession->documentNumber)) {
            $this->view->documentNumber = $pageSession->documentNumber;
        }

        // Transaction is complete - unset the session to prevent navigation to other steps again
        Zend_Session::namespaceUnset('landlords_insurance_quote');
    }

	/**
	 * Controller for the save quote popup
	 *
	 * @return void
	 */
	public function saveAction() {
		// This controller is called within a popup (facebox style) so doesn't require a layout file
		$this->_helper->getHelper('layout')->disableLayout();

        $customerManager = new Manager_Core_Customer();
        $refNo = $this->_customerReferenceNumber;
        $legCustomer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $refNo);
        $customer = $customerManager->getCustomerByEmailAddress($legCustomer->getEmailAddress());

        // Due to the My HomeLet portal, the save function is redundant. Simply return a success message.
//        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        if ($customer) {
            $this->_helper->viewRenderer('save-success');
        } else {
            $this->_helper->viewRenderer('save-register');
        }

        return;
	}
	
	/**
	* Public send quote as email and or post function
	*/
	public function sendAction(){
		// This controller is called within a popup (facebox style) so doesn't require a layout file
		$this->_helper->getHelper('layout')->disableLayout();
		
		$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
		$this->_customerReferenceNumber = $pageSession->CustomerRefNo;
		$this->_policyNumber = $pageSession->PolicyNumber;
		$this->view->sendForm = new LandlordsInsuranceQuote_Form_SendQuote();
	}

    /**
     * Resume an existing quote for customers who either have a temporary auth token for a retrieval with no My HomeLet
     * account, or who are My HomeLet authenticated.  Customers with no form of valid authentication are redirected to
     * the My HomeLet login page.
     *
     * @return void
     */
	public function retrieveAction()
    {
        // Authorisation using no-account My HomeLet retrieval auth token

        if ($this->getRequest()->getParam('auth') != '') {

            $mac = $this->getRequest()->getParam('auth');

            $securityManager = new Application_Core_Security(
                $this->_params->myhomelet->retrieveWithoutAccount->macSecret,
                ($this->_params->myhomelet->retrieveWithoutAccount->macTimestampVariance != 0),
                $this->_params->myhomelet->retrieveWithoutAccount->macTimestampVariance
            );

            $dataKeys = array(
                'quoteNumber'
            );

            $securityCheck = $securityManager->authenticate($mac, $dataKeys);

            if (isset($securityCheck['result']) && $securityCheck['result']) {

                $quoteNumber = $securityCheck['data']['quoteNumber'];

                $quoteManager = new Manager_Insurance_LegacyQuote();
                $customerManager = new Manager_Core_Customer();

                $quote = $quoteManager->getQuoteByPolicyNumber($quoteNumber);
                $quoteRefNo = $quote->refNo;
                $customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $quoteRefNo);
                $customerID = $referenceNumber = $customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER, $quoteRefNo);

                $quoteManager = new Manager_Insurance_LandlordsPlus_Quote(null, $quoteNumber, null, $customerID);
                $quote = $quoteManager->getModel();

                $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
                $pageSession->quoteID = $quote->ID;
                $pageSession->customerRefNo = $referenceNumber;

                //Retrieve the WebLead summary ID so that the WebLead can continue to be updated and important
                //details captured, such as the campaign code.
                $webLeadManager = new Manager_Core_WebLead();
                $pageSession->webLeadSummaryId = $webLeadManager->getSummaryId($quoteNumber);

                $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/step1');
                return;
            }
        }

        // Authorisation using My HomeLet logged in details

        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        if ($auth->hasIdentity()) {
            // Check to see if we have a reference number to load up
            if ($this->getRequest()->getParam('quote') != '') {
                $quoteNumber = $this->getRequest()->getParam('quote');

                // Customer is logged in and is trying to retrieve a specific quote
                // We need to check to make sure they own it
                $customerID = $auth->getStorage()->read()->id;

                // Now we need to get their legacy ID
                $customerManager = new Manager_Core_Customer();
                $customer = $customerManager->getCustomer(Model_Core_Customer::IDENTIFIER, $customerID);
                $referenceNumber = $customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER);

                // Need to find the quote ID by the policy number
                $quotes = new Manager_Insurance_LandlordsPlus_Quote(null, $quoteNumber, null, $customerID);
                $quote = $quotes->getModel();

                $legacyCustomerMap = new Datasource_Core_CustomerMaps();
                $legacyIDs = $legacyCustomerMap->getLegacyIDs($customerID);

                if (in_array($quote->legacyCustomerID, $legacyIDs)) {
                    // This customer does own this reference - so set the page session stuff up and redirect
                    $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');

                    $pageSession->quoteID = $quote->ID;
                    $pageSession->customerRefNo = $referenceNumber;

                    //Retrieve the WebLead summary ID so that the WebLead can continue to be updated and important
                    //details captured, such as the campaign code.
                    $webLeadManager = new Manager_Core_WebLead();
                    $pageSession->webLeadSummaryId = $webLeadManager->getSummaryId($quoteNumber);

                    $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/step1');
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
		Zend_Session::namespaceUnset('landlords_insurance_quote');
		$this->_helper->redirector->gotoUrl('/landlords/insurance-quote/step1');
	}

    /**
     * Handle registering for or sign in to My HomeLet.
     *
     * @param int $policyNumber
     * @return LandlordsInsuranceQuote_Form_MyHomeLetRegistration
     */
    private function registrationFormProcess($policyNumber)
    {
        $pageForm = new LandlordsInsuranceQuote_Form_MyHomeLetRegistration();

        // Tell page NOT to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = false;'
        );

        // Get the session
        $session = new Zend_Session_Namespace('landlords_insurance_quote');

        // Get customer details
        $customerManager = new Manager_Core_Customer();
        $customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $this->_customerReferenceNumber);

        // Hydrate registration form
        if (isset($pageForm->subform_register)) {

            // Grab a new customer to populate the form
            $newCust = $customerManager->getCustomerByEmailAddress($customer->getEmailAddress());

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
        elseif (isset($pageForm->subform_login)) {

            // Get the email address from the legacy customer and pre-populate the login form
            $pageForm->subform_login->email->setValue($customer->getEmailAddress());
        }

        if ($this->getRequest()->isPost() && isset($_POST['register'])) {
            // We need to validate and save the data
            $valid = $this->_formStepCommonValidate($pageForm, 'registration');

            if (isset($pageForm->subform_register)) {
                $pageForm->subform_register->getElement('email')->setValue($emailAddress);
            }

            if ($valid) {

                $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
                $data = $pageForm->getValues();

                // Re-add the DoB remembered from Step 1 because otherwise logging-in landlords' DoBs go missing.
                $customer->setDateOfBirthAt(
                    Application_Core_Utilities::ukDateToMysql(
                        $pageSession->CustomerDob
                    )
                );

                $customerManager->updateCustomer($customer);

                // Perform login/register procedure
                $auth = Zend_Auth::getInstance();
                $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

                if (isset($data['subform_register'])) {
                    // Process registration

                    $newCustomer = $customerManager->getCustomerByEmailAddress($data['subform_register']['email']);

                    if (!$newCustomer) {
                        $newCustomer = $customerManager->createCustomerFromLegacy($data['subform_register']['email'], $this->_customerReferenceNumber);
                        $customerID  = $newCustomer->getIdentifier(Model_Core_Customer::IDENTIFIER);
                        $customerManager->updateCustomerByLegacy($customer, $customerID);
                        $newCustomer = $customerManager->getCustomer(Model_Core_Customer::IDENTIFIER, $customerID);
                    }

                    // Update customer with password and security data

                    $newCustomer->setEmailAddress($data['subform_register']['email']);

                    // Set the new customer's DoB with that remembered from Step 1 otherwise newly registering
                    //   landlords' DoBs become empty.
                    $newCustomer->setDateOfBirthAt(
                        Application_Core_Utilities::ukDateToMysql(
                            $pageSession->CustomerDob
                        )
                    );

                    $newCustomer->setSecurityQuestion($data['subform_register']['security_question']);
                    $newCustomer->setSecurityAnswer($data['subform_register']['security_answer']);
                    $newCustomer->setPassword($data['subform_register']['password']);
                    $newCustomer->setAccountLoadComplete(true);

                    $newCustomer->typeID = Model_Core_Customer::CUSTOMER;

                    $customerManager->updateCustomer($newCustomer);

                    // Create sign-up completion email
                    $newCustomer->sendAccountValidationEmail();

                    // Everything has been saved OK so navigate to registration confirmation step
                    $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/registration-confirmation?pn=' . $policyNumber);
                }
                elseif ($auth->hasIdentity()) {
                    $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/registration-confirmation?pn=' . $policyNumber);
                }

            }

        }

        return $pageForm;
    }

	/**
	 * Helper function to populate the zend form elements with database data
	 *
	 * @param Zend_Form $pageForm form definition for this step
	 * @param int $stepNum current step number
	 *
	 * @return void
	 */
	private function _formStepCommonPopulate($pageForm, $stepNum) {

		$this->view->stepNum = $stepNum;
		$this->view->stepMax = $this->_stepMax;
		
		// Check to see if the user is trying to skip ahead in the quote
		$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
		$tooFarAhead = false;
		
		if ((!isset($pageSession->completed) || is_null($pageSession->completed)) && $stepNum != 1)
		{
			$tooFarAhead = true;
			$lastCompleted = 1;
		} elseif ($stepNum > 1) {
            // Check to see if any pages previous to the one the user's trying to get to are incomplete
            $tooFarAhead = false;
            for ($i = 1; $i < $stepNum; $i++) {
                if (!isset($pageSession->completed[$i]) || !$pageSession->completed[$i]) {
                    $tooFarAhead = true;
					$lastCompleted = $i;
					break;
                }
            }
        }
		
		if ($tooFarAhead) {
			// Drop user onto page that needs completing
			$this->_helper->redirector->gotoUrl('/landlords/insurance-quote/step' . ($lastCompleted));
			return false;
		}

		if ($stepNum > 1) {
			// Before we do ANYTHING we need to check to see if the email address entered matches a customer record
			// we already have - if it does we need to ask them to login before they proceed.
			$customerReferenceNumber = $this->_customerReferenceNumber;
			$customerManager = new Manager_Core_Customer();
			$legacyCustomer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $customerReferenceNumber);
			$emailAddress = $legacyCustomer->getEmailAddress();

			$customer = $customerManager->getCustomerByEmailAddress($emailAddress);

			if ($customer) {
				// There is already a customer entry for this email address - so we need to see if they are logged in
				// if not we need to force them to login

				$auth = Zend_Auth::getInstance();
				$auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

				if ($auth->hasIdentity()) {

					$loggedInEmail = $auth->getStorage()->read()->email_address;
					if ($loggedInEmail != $customer->getEmailAddress()) {
						// They are logged in but not who they should be to do this quote
						$this->_helper->redirector->gotoUrl('/account/login?refer=landlords-insurance&step='. $stepNum);

						return false;
					}
				} else {

                    // TODO: Check that removing the login redirection will not break other processes

					// They aren't logged in and need to
					/*$this->_helper->redirector->gotoUrl('/account/login?refer=landlords-insurance&step='. $stepNum);
					return false;*/
				}
			}
		}
		
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

				$formData['title']          = $customer->getTitle();
				$formData['first_name']     = $customer->getFirstName();
				$formData['last_name']      = $customer->getLastName();
				$formData['phone_number']   = $customer->getTelephone(Model_Core_Customer::TELEPHONE1);
				$formData['mobile_number']  = $customer->getTelephone(Model_Core_Customer::TELEPHONE2);
				$formData['email_address']  = $customer->getEmailAddress();
                $formData['date_of_birth_at'] = $customer->getDateOfBirthAt();

				$pageForm->populate($formData);
			}
		}

		
		if (isset($this->_quoteID) && $this->_quoteID>0) {
			$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($this->_quoteID);
			$premiums = $quoteManager->calculatePremiums();
			
			if ($premiums!='') {
				$this->view->premiums = array(
					'annual' => $premiums['totalGrossAnnualPremium'] + $premiums['totalGrossAnnualIPT'],
					'monthly' => $premiums['totalGrossMonthlyPremium'] + $premiums['totalGrossMonthlyIPT']
				);
				$this->view->premiumsFull = $premiums;
			}
			
			$fees = $quoteManager->getFees();
			$this->view->fees = $fees;
			
			switch ($stepNum) {
				case 1:
					$customerManager = new Manager_Core_Customer();
					$customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $quoteManager->getLegacyCustomerReference());
					
					// Populate the customer details
					
					$titleOptions = LandlordsInsuranceQuote_Form_Subforms_PersonalDetails::$titles;
					if(in_array($customer->getTitle(), $titleOptions)) {
						$formData['title'] = $customer->getTitle();
					} else {
						$formData['title'] = "Other";
						$formData['other_title'] = $customer->getTitle();
					}
					
					$formData['first_name'] = $customer->getFirstName();
					$formData['last_name'] = $customer->getLastName();
					$formData['phone_number'] = $customer->getTelephone(Model_Core_Customer::TELEPHONE1);
					$formData['mobile_number'] = $customer->getTelephone(Model_Core_Customer::TELEPHONE2);
					$formData['email_address'] = $customer->getEmailAddress();
                    $dob = $customer->getDateOfBirthAt();
                    if (null != $dob && '0000-00-00' != $dob) {
                        $formData['date_of_birth_at'] = Application_Core_Utilities::mysqlDateToUk($dob);
                    }

					// Populate the correspondence address details
					$formData['cor_address_line1'] = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE1);
					$formData['cor_address_line2'] = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE2);
					$formData['cor_address_line3'] = $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE3);
					$formData['cor_address_postcode'] = $customer->getPostcode();
                    $formData['country'] = $customer->getCountry();
					
					// Populate the insured property address details
					$properties = $quoteManager->getProperties();
					if (count($properties)>0) {
						$formData['ins_address_line1'] = $properties[0]['line_1'];
						$formData['ins_address_line2'] = $properties[0]['line_2'];
						$formData['ins_address_line3'] = $properties[0]['town'];
						$formData['ins_address_postcode'] = $properties[0]['postcode'];
						
						$formData['owned_for'] = $properties[0]['ownership_length_id'];
						$formData['no_claims'] = $properties[0]['no_claims_years_id'];
						$formData['tenants_type'] = $properties[0]['tenant_type_id'];
                        $formData['have_letting_agent'] = ($quoteManager->getAgentSchemeNumber() != Manager_Core_Agent::filterAsn($quoteManager->getAgentSchemeNumber())) ? 'yes' : 'no';
						$formData['through_letting_agent'] = $properties[0]['letting_agent_managed']?'yes':'no';
						
						// Check to see if this postcode is in a flood risk area - if it is then populate the exclude flood cover data
						// Populating this will also cause the question to be shown on the front end
						$landlordsRiskAreas = new Datasource_Insurance_LandlordsPlus_RiskAreas();
        				$riskAreas = $landlordsRiskAreas->getByPostcode($properties[0]['postcode']);
        				if ($riskAreas['floodArea']=='600') {
							$formData['exclude_flood_cover'] = $properties[0]['exclude_flood_cover']?'no':'yes'; // Backwards true/false stuff - I'm sooo sorry :(
						}
					}
					
					// Populate agent details if one has been chosen
					$agentSchemeNumber = Manager_Core_Agent::filterAsn($quoteManager->getAgentSchemeNumber());
					$defaultASN = $this->_params->homelet->defaultAgent;
					if ($formData['have_letting_agent'] == 'yes') {
						$agents = new Datasource_Core_Agents();
						$agent = $agents->getAgent($agentSchemeNumber);
						$formData['letting_agent_name'] = $agent->name;
						$formData['letting_agent_town'] = $agent->town;
						$formData['letting_agent_asn'] = $agent->agentSchemeNumber;
                        // Fix for Redmine Ref. #10511:
                        $agentDropdown = $pageForm->subform_lettingagent->letting_agent;
                        $agentDropdown->setMultiOptions(array($agent->agentSchemeNumber => $agent->name . ', '. $agent->town));
                        $formData['letting_agent'] = $agent->agentSchemeNumber;
					}
					
					// Load the policy start date
					$startDate = $quoteManager->getStartDate();
					if ($startDate != '' && $startDate != '0000-00-00') {
						$formData['policy_start'] = substr($startDate, 8, 2) . '/' . substr($startDate, 5, 2) . '/' . substr($startDate, 0, 4);
					}
					
					// If step1 has been marked complete - we can assume they said yes to the IDD question
					$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
					if (isset($pageSession->completed[$stepNum]) && $pageSession->completed[$stepNum] == true) {
						$formData['idd']=true;
					}
					
					// Data Protection section
					$customerReferenceNumber = $customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER);
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
				
				case 2:
 					// If step2 has been marked complete - we can assume they said no to the questions unless
 					// they've been set in the quote manager
					if (isset($pageSession->completed[$stepNum]) && $pageSession->completed[$stepNum] == true) {
						$formData['need_building_insurance']='no';
						$formData['need_contents_insurance']='no';
					}
					
 					if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER)) {
 						$formData['need_building_insurance']='yes';
 						$productMeta = $quoteManager->getProductMeta(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER);
						
						$formData['building_built'] = $productMeta['build_year'];
						$formData['building_bedrooms'] = $productMeta['bedroom_quantity'];
						$formData['building_type'] = $productMeta['building_type'];
						$formData['building_insurance_excess'] = $productMeta['excess'];
						$formData['building_accidental_damage'] = $productMeta['accidental_damage'];
						
						$quote = $quoteManager->getModel();
						if ((int)$productMeta['rebuild_value'] > 0) {
							// There's a manually entered rebuild value - need to work out if it is because they
							// chose £500k+ - or if it's because we don't have a dsi
							$premiums=$quoteManager->calculatePremiums();
							if ($premiums['calculatedDSIValue'] > 0) $formData['override_dsi'] = 1;
							$formData['building_value'] = $productMeta['rebuild_value'];
						}
 					}
 					if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER) ||
 					    $quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::UNFURNISHED_CONTENTS_COVER)) {
 						$formData['need_contents_insurance']='yes';
 						if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER)) {
 							$formData['property_furnished']='yes';
							
 							$productMeta = $quoteManager->getProductMeta(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER);

							$formData['contents_amount'] = $productMeta['cover_amount'];
							$formData['contents_excess'] = $productMeta['excess'];
							$formData['contents_accidental_damage'] = $productMeta['accidental_damage'];
 						} else {
 							$formData['property_furnished']='no';
 						}
 					}
					break;

				case 3:
					if (isset($pageSession->completed[$stepNum]) && $pageSession->completed[$stepNum] == true) {
						$formData['need_emergency_assistance'] = 'no';
						$formData['need_prestige_rent_guarantee'] = 'no';
						$formData['need_legal_expenses'] = 'no';
						$formData['need_boiler_heating'] = 'no';
					}
					
					// If we have contents/buildings cover then EAS is already included for free so we can hide the form
					if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER) || $quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER)) {
						// Change the subforms view script to one that just says it's already included for free
						// yeah yeah.. this aint pretty :(
						$emergencyAssistanceForm = $pageForm->getSubForm('subform_emergencyassistance');
						$emergencyAssistanceForm->setDecorators(array(
							array('ViewScript', array('viewScript' => 'subforms/emergency-assistance-free.phtml'))
						));
						if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::BOILER_HEATING)) {
							$formData['need_boiler_heating'] = 'yes';
						}
					} else {
						// We can allow stand-alone EAS - so we hide the boiler and heating section
						// yes... this is waaay too complex... I know :(
						$pageForm->removeSubForm('subform_boilerheating');
						if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::EMERGENCY_ASSISTANCE)) {
							$formData['need_emergency_assistance'] = 'yes';
						}
					}
					
					if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::RENT_GUARANTEE)) {
						$formData['need_prestige_rent_guarantee'] = 'yes';
						$productMeta = $quoteManager->getProductMeta(Manager_Insurance_LandlordsPlus_Quote::RENT_GUARANTEE);
						$formData['rent_amount'] = $productMeta['monthly_rent'];
					} elseif ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::LEGAL_EXPENSES)) {
						$formData['need_legal_expenses'] = 'yes';
					}
		   			break;
					
		   		case 4:
		   			if (isset($pageSession->completed[$stepNum]) && $pageSession->completed[$stepNum] == true) {
						// Load underwriting answers from the database as they've already been answered
		   				$answersManager = new Manager_Insurance_Answers();
		   				$quote = $quoteManager->getModel();
		   				$policyNumber = $quote->legacyID;
		   				$customerReferenceNumber = $quote->legacyCustomerID;
						
		   				$answers = $answersManager->getUnderwritingAnswers($policyNumber);
		   				foreach ($answers as $answer) {
		   					switch($answer->getQuestionNumber()) {
		   						case '53':
		   							$formData['declaration1'] = $answer->getAnswer();
		   							break;
		   						case '54':
		   							$formData['declaration2'] = $answer->getAnswer();
		   							break;
		   						case '55':
		   							$formData['declaration2b'] = $answer->getAnswer();
		   							break;
		   						case '56':
		   							$formData['declaration2c'] = $answer->getAnswer();
		   							break;
		   						case '57':
		   							$formData['declaration2d'] = $answer->getAnswer();
		   							break;
		   						case '58':
		   							$formData['declaration3'] = $answer->getAnswer();
		   							break;
		   						case '59':
		   							$formData['declaration4'] = $answer->getAnswer();
		   							break;
		   						case '60':
		   							$formData['declaration6'] = $answer->getAnswer();
		   							break;
		   						case '61':
		   							$formData['declaration7'] = $answer->getAnswer();
		   							break;
		   						case '62':
		   							$formData['declaration8'] = $answer->getAnswer();
		   							break;
		   						case '63':
		   							$formData['declaration9'] = $answer->getAnswer();
		   							break;
		   						case '64':
		   							$formData['declaration10'] = $answer->getAnswer();
		   							break;
		   					}
		   				}
						
		   				// Also need to see if they said yes or no to bank interest on the propery…
		   				$bankInterestManager = new Manager_Insurance_LegacyBankInterest();
						$bankInterestArray = $bankInterestManager->getAllInterests($policyNumber, $customerReferenceNumber);
						$model = array();
						if(!empty($bankInterestArray)) {
							$formData['declaration11'] = 'yes';
						} else {
							$formData['declaration11'] = 'no';
						}
						
		   				// They must have agreed to the declaration or they wouldn't have been able to continue
		   				$formData['declaration_confirmation'] = 'yes';
		   			}
		   			break;

                case 5:
                    // Payment Selection section
                    if (
                        isset($pageSession->paymentSelectionDetails) &&
                        is_array($pageSession->paymentSelectionDetails)
                    ) {
                        $formData = $pageSession->paymentSelectionDetails;
                    }

                    break;

			}
		}
		
		$pageForm->populate($formData);
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
 	*/
	private function _formStepCommonValidate($pageForm, $stepNum) {
		$valid = $pageForm->isValid($this->getRequest()->getPost());
		if ($valid) {
			// Everything's valid - mark it all completed
			$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
			$pageSession->completed[$stepNum] = true;
		} else {
			$errorsJs = "$(document).ready(function() { if ($('.error').length > 0) { $.scrollTo( '.error', 800 ); } });";
			$this->view->headScript()->appendScript($errorsJs, $type = 'text/javascript');

            // Mark page as no longer being on its first load, when not present this is used to suppress dynamic AJAX errors in the page when first loaded
            $previouslyLoaded = "var previouslyLoaded = true;\n";
            $this->view->headScript()->appendScript($previouslyLoaded, $type = 'text/javascript');

            $errorMessages = $pageForm->getMessagesFlattened();
            $this->view->errorCount = count($errorMessages);
            $this->view->errorsHtml = $this->view->partial('partials/error-list.phtml', array('errors' => $errorMessages));
		}
		return $valid;
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
		$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			// Handle moving backwards and forwards through the form
			$response = $this->getResponse();
			
			if ($stepNum == 'dd') {
				$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($this->_quoteID);
				$quoteNumber = $quoteManager->getPolicyNumber();
				$policyNumber = str_replace('Q', 'P', $quoteNumber);
				
				if (isset($_POST['next'])) $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/ddconfirmation?pn=' . $policyNumber);
				if (isset($_POST['back'])) $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/step' . $this->_stepMax);
				$response->sendResponse();
			} elseif ($stepNum == 'cc') {
				if (isset($_POST['next'])) $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/ccconfirmation');
				if (isset($_POST['back'])) $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/step' . $this->_stepMax);
				$response->sendResponse();
			} else {
				if (isset($_POST['back']) && $stepNum > 1) {
					$this->_helper->redirector->gotoUrl('/landlords/insurance-quote/step' . ($stepNum - 1));
				} elseif (isset($_POST['next']) && $stepNum < $this->_stepMax && $pageSession->completed[$stepNum]) {
					$this->_helper->redirector->gotoUrl('/landlords/insurance-quote/step' . ($stepNum + 1));
				// Handle payment screen traversal
				} elseif (isset($_POST['next']) && isset($_POST['payment_method']) && $_POST['payment_method'] == 'cc' && $stepNum == $this->_stepMax) {
					$this->_helper->redirector->gotoUrl('/landlords/insurance-quote/cc');
				} elseif (isset($_POST['next']) && isset($_POST['payment_method']) && $_POST['payment_method'] == 'dd' && $stepNum == $this->_stepMax) {
					$this->_helper->redirector->gotoUrl('/landlords/insurance-quote/dd');
				}
			}
		}
	}

    /**
     * This function butchers the date to add or subtract x hours to make web leads work correctly.  A burrinj special,
     * but now with added parameterisation!
     *
     * @return Zend_Date
     */
    private function _offsetDate()
    {
        $date = new Zend_Date();

        $offset = $this->_params->weblead->hourOffset;
        if ($offset < 0) {
            $date->sub((string) abs($offset), Zend_Date::HOUR);
        }
        else {
            $date->add((string) $offset, Zend_Date::HOUR);
        }

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
