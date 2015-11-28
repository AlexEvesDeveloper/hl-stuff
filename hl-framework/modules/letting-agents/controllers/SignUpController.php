<?php

class LettingAgents_SignUpController extends Zend_Controller_Action
{
	private $_stepMax = 5; // Number of form steps, excluding special pages like payment screens
	public function init(){
		$session = new Zend_Session_Namespace('homelet_global');
		
		
		// Bit of a dirty hack to use a layout from the CMS module
		$layout = Zend_Layout::getMvcInstance();                                
        $layout->setLayoutPath(APPLICATION_PATH . '/modules/cms/layouts/scripts');
        $layout->setLayout('default');
		
		
		// Extra form css for Letting Agents
		//$this->view->headLink()->appendStylesheet('/assets/cms/css/portfolio_form.css');
		$this->view->headScript()->appendFile('/assets/cms/js/letting-agents/letting-agents_form.js');
		$this->url = trim($this->getRequest()->getRequestUri(),'/');
		$this->view->pageTitle = 'Letting Agents Application';
		$menuData = array(
			'selected'  => 'letting-agents',
			'url'       => $this->url
		);

		// This could be quite yucky - I'm just trying to work out how to get the menu structure to work
		$mainMenu = $this->view->partial('partials/homelet-mainmenu.phtml', $menuData);
		$subMenu = $this->view->partial('partials/homelet-submenu.phtml', $menuData);
		$layout = Zend_Layout::getMvcInstance();
		$layout->getView()->mainMenu = $mainMenu;
		$layout->getView()->subMenu = $subMenu;
		if (isset($menuData['selected'])) { $layout->getView()->styling = $menuData['selected']; }

		// Load the site link urls from the parameters and push them into the layout
		$params = Zend_Registry::get('params');
		$layout->getView()->urls = $params->url->toArray();

		// Some Session Stuff
		$pageSession = new Zend_Session_Namespace('letting_agents_application');
		// Unique Id
		if(!isset($pageSession->agentUniqueId)) $pageSession->agentUniqueId = uniqid();
	}
	
    /**
     * Default function for Zend - all this does currently is redirect to the home page
     *
     * @return void
     */
    public function indexAction()
    {
        // Default controller and action doesn't do anything except redirect to the home page for our CMS
        // In the future this could possibly dynamically redirect based on domain or something... Fish?

        $this->_helper->redirector->gotoUrl('/home');
    }
    
    /**
     * 
     * This is the letting agents signup page
     */

    public function introductionAction(){
    	$pageForm = new LettingAgents_Form_Signup();
    	$this->view->stepNum = "signup"; 
    	$this->view->form = $pageForm;
    	if ($this->getRequest()->isPost()) {
    		$this->_formStepCommonNavigate($this->view->stepNum);
    	}
    	$this->render('step');
    }

     
    /**
	 * Initialise the step 1 form
	 *
	 * @return void
	 */
	public function step1Action() {
		// Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 1;'
        );

      		
		$pageForm = new LettingAgents_Form_Step1();
		if ($this->getRequest()->isPost()) {
			// We need to validate and save the data
			// TODO: Need to re-factor this to make it quicker
			$valid = $this->_formStepCommonValidate($pageForm, 1);

			$pageSession = new Zend_Session_Namespace('letting_agents_application');

			if ($valid) {
				$postData = $pageForm->getValues(); // According to the Zend manual these *should* be the clean values
				$application = new LettingAgents_Manager_AgentApplication();			
				$agencyData = new LettingAgents_Object_AgentApplication();
				// populate the agentcy data with any data that may have been set in future steps
				// as we may have returned to this step from step 2 onwards
				
				$agencyData = $application->fetchByUid($pageSession->agentUniqueId);
				if($agencyData == false){
					$agencyData = new LettingAgents_Object_AgentApplication();
				}
				//Zend_Debug::dump($agencyData);die("$pageSession->agentUniqueId");
				// now we can update any date that may have changes
				$agencyData->set_uid($pageSession->agentUniqueId);

				if(isset($postData['subform_campaign']['is_previous_client']))  
    				$agencyData->set_is_previous_client($postData['subform_campaign']['is_previous_client']);
    			
				if(isset($postData['subform_campaign']['campaign_code']))  
					$agencyData->set_campaign_code($postData['subform_campaign']['campaign_code']);
					
				if(isset($postData['subform_companyname']['legal_name'])) 
					$agencyData->set_legal_name($postData['subform_companyname']['legal_name']);
					
				if(isset($postData['subform_companyname']['trading_name'])) 
					$agencyData->set_trading_name($postData['subform_companyname']['trading_name']);
					
				if(isset($postData['subform_personaldetails']['organisation_type'])) 
					$agencyData->set_organisation_type($postData['subform_personaldetails']['organisation_type']);
					
				if(isset($postData['subform_personaldetails']['date_established'])) 
					$agencyData->set_date_established($postData['subform_personaldetails']['date_established']);
					
				if(isset($postData['subform_personaldetails']['is_associated'])) 
					$agencyData->set_is_associated($postData['subform_personaldetails']['is_associated']);
					
				if(isset($postData['subform_personaldetails']['associated_text'])) 
					$agencyData->set_associated_text($postData['subform_personaldetails']['associated_text']);

				if(isset($postData['subform_personaldetails']['contact_name'])) 
					$agencyData->set_contact_name($postData['subform_personaldetails']['contact_name']);
					
				if(isset($postData['subform_personaldetails']['contact_number'])) 
					$agencyData->set_contact_number($postData['subform_personaldetails']['contact_number']);
					
				if(isset($postData['subform_personaldetails']['general_email'])) 
					$agencyData->set_contact_email($postData['subform_personaldetails']['general_email']);

				$application->save($agencyData);
				$this->_formStepCommonNavigate(1);
				return;
			} elseif (isset($_POST['back'])) {
				$this->_formStepCommonNavigate(1);
				return;
			}
        }

		// Load the element data from the database if we can
		if ($this->_formStepCommonPopulate($pageForm, 1))
		{
			// Render the page unless we have been redirected
			$this->view->form = $pageForm;
			$this->render('step');
		}
    }

    /**
	 * Initialise the step 2 form
	 *
	 * @return void
	 */
	public function step2Action() {
		 $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 2;'
        );
		// Append the address lookup javascript
		$this->view->headScript()
			->appendFile(
            '/assets/common/js/addressLookup.js',
            'text/javascript'
        	)
        	->appendFile(
            '/assets/cms/js/letting-agents/letting-agents_form.js',
            'text/javascript'
        );

		$pageSession = new Zend_Session_Namespace('letting_agents_application');
		$agent = new LettingAgents_Manager_AgentApplication();
		$agentData = new LettingAgents_Object_AgentApplication();
		$agentData = $agent->fetchByUid($pageSession->agentUniqueId);
		
		/*
		 *  What we need to present as stage 2 is dependant on the selection of organisation type
		 *  step 2 will need to load different subforms
		 * so pass organisation_type to the form   
		 */
		$organisation_type = $agentData->get_organisation_type();
		$pageForm = new LettingAgents_Form_Step2(array(
			'organisationId' => $organisation_type
			));

		if ($this->getRequest()->isPost()) {
			// We need to validate and save the data
			// TODO: Need to re-factor this to make it quicker
			$valid = $this->_formStepCommonValidate($pageForm, 2);

			if ($valid) {
				$postData = $pageForm->getValues();
				
				switch($organisation_type){
					case LettingAgents_Object_CompanyTypes::LimitedCompany:
						$agentData->set_uid($pageSession->agentUniqueId);
						
						// Assign subform_limited company
						if(isset($postData['subform_limitedcompanyregistration']['registration_number']))
							$agentData->set_company_registration_number($postData['subform_limitedcompanyregistration']['registration_number']);
						$agent->save($agentData);
						
						break;
					
					case LettingAgents_Object_CompanyTypes::LimitedLiabilityPartnership:							
					case LettingAgents_Object_CompanyTypes::Partnership: 
						break;
						
					case LettingAgents_Object_CompanyTypes::SoleTrader:
						$data = new LettingAgents_Object_SoleTraderContact();

						// Assign agenty_id, this is the index NOT the agent scheme number
						$data->set_agency_id($agentData->get_id());
						// Set unique ID
						if(isset($postData['subform_common']['uid'])){
							$data->set_uid($postData['subform_common']['uid']);
						}
						// Assign subform_soletrader							
						if(isset($postData['subform_soletrader']['contact_name']))
							$data->set_contact_name($postData['subform_soletrader']['contact_name']);
							
						if(isset($postData['subform_soletrader']['ni_number']))
							$data->set_ni_number($postData['subform_soletrader']['ni_number']);
							
						if(isset($postData['subform_soletrader']['passport_number']))
							$data->set_passport_number($postData['subform_soletrader']['passport_number']);
							
						if(isset($postData['subform_soletrader']['birth_date']))
							$data->set_birth_date($postData['subform_soletrader']['birth_date']);
						
						// Assign subform_address
						if(isset($postData['subform_address']['address_line1']))
							$data->set_address1($postData['subform_address']['address_line1']);
							
						if(isset($postData['subform_address']['address_line2']))
							$data->set_address2($postData['subform_address']['address_line2']);
							
						if(isset($postData['subform_address']['address_line3']))
							$data->set_address3($postData['subform_address']['address_line3']);
						
						if(isset($postData['subform_address']['postcode']))
							$data->set_postcode($postData['subform_address']['postcode']);				
							
					//	Zend_Debug::dump($data);die();
						$contactManager = new LettingAgents_Manager_Contacts();
						$contactManager->save($data);
						break;
				}

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
	 * Initialise the step 3 form
	 *
	 * @return void
	 */
	public function step3Action() {
		// Append the address lookup javascript
		 $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 3;'
        );		
		$this->view->headScript()->appendFile(
            '/assets/cms/js/letting-agents/addressLookup.js',
            'text/javascript'
        );
        
		$pageForm = new LettingAgents_Form_Step3();
		$pageSession = new Zend_Session_Namespace('letting_agents_application');
		// Setup an agency object
		$agent = new LettingAgents_Manager_AgentApplication();
		$agentData = new LettingAgents_Object_AgentApplication();
		$agentData = $agent->fetchByUid($pageSession->agentUniqueId);

		if ($this->getRequest()->isPost()) {
			// We need to validate and save the data
			// TODO: Need to re-factor this to make it quicker
			$valid = $this->_formStepCommonValidate($pageForm, 3);
		
			if ($valid) {
				$postData = $pageForm->getValues();
				//Insert Remaining data for the agency application
				//Zend_Debug::dump($postData);die();
				$agentData->set_current_referencing_supplier($postData['subform_companydetail']['current_referencing_supplier']);
				$agentData->set_number_of_branches($postData['subform_companydetail']['no_of_branches']);
				$agentData->set_number_of_employees($postData['subform_companydetail']['no_of_staff']);
				$agentData->set_number_of_lets($postData['subform_companydetail']['no_of_properties_managed']);
				$agentData->set_number_of_landlords($postData['subform_companydetail']['no_of_landlords']);
				$agentData->set_fax_number($postData['subform_companyfax']['fax_number']);
				$agentData->set_company_website_address($postData['subform_companyfax']['company_website_address']);
				$agentData->set_contact_email($postData['subform_multipleemails']['general_email_address']);
				$agent->save($agentData);
				
				// Multiple email stuff
				// Delete all the currect email for this agency
				$emails = new LettingAgents_Datasource_Email();
				$emails->deleteById($agentData->get_id());
				foreach($postData['subform_multipleemails'] as $address_type => $email_address){
					$saveData = new LettingAgents_Object_AgencyEmail();
					$saveData->setAddress_type($address_type);
					$saveData->setAgency_id($agentData->get_id());
					$saveData->setEmail_address($email_address);
					$emails->save($saveData);
				}
				//die();
				

				// Insert the office data in to the agency_office table
				// First the Trading Address
				$officeManager = new LettingAgents_Manager_Office();
				$data = new LettingAgents_Object_AgencyOffice();
				$data->set_uid($postData['subform_tradingaddress']['trading_uid']);
				$data->set_address_1($postData['subform_tradingaddress']['trading_address_line1']);
				$data->set_address_2($postData['subform_tradingaddress']['trading_address_line2']);
				$data->set_address_3($postData['subform_tradingaddress']['trading_address_line3']);
				$data->set_postcode($postData['subform_tradingaddress']['trading_address_postcode']);
				$data->set_agency_id($agentData->get_id());
				$data->set_office_type(LettingAgents_Object_OfficeTypes::BranchOffice);
				$officeManager->save($data);
				
				// Reset the data Object
				$data = new LettingAgents_Object_AgencyOffice();
				// Insert the Accounts Office address 
				$data->set_uid($postData['subform_accountsaddress']['accounts_uid']);
				$data->set_address_1($postData['subform_accountsaddress']['accounts_address_line1']);
				$data->set_address_2($postData['subform_accountsaddress']['accounts_address_line2']);
				$data->set_address_3($postData['subform_accountsaddress']['accounts_address_line3']);
				$data->set_postcode($postData['subform_accountsaddress']['accounts_address_postcode']);
				$data->set_agency_id($agentData->get_id());
				$data->set_office_type(LettingAgents_Object_OfficeTypes::HeadOffice);
				$officeManager->save($data);

				// Now the Head Office
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
    
    /**
	 * Initialise the step 4 form
	 *
	 * @return void
	 */
	public function step4Action() {
		// Append the address lookup javascript
		 $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 4;'
        );		
	
		$pageForm = new LettingAgents_Form_Step4();
		$pageSession = new Zend_Session_Namespace('letting_agents_application');
		// Setup an agency object
		$agent = new LettingAgents_Manager_AgentApplication();
		$agentData = new LettingAgents_Object_AgentApplication();
		$agentData = $agent->fetchByUid($pageSession->agentUniqueId);
				
		if ($this->getRequest()->isPost()) {
			// We need to validate and save the data
			// TODO: Need to re-factor this to make it quicker
			$valid = $this->_formStepCommonValidate($pageForm, 4);

			if ($valid) {
				$postData = $pageForm->getValues();
				//Zend_Debug::dump($postData);die();
				switch($postData['subform_agenttype']['agent_type']){
					case "standard" :
						$agentType = LettingAgents_Object_AgentTypes::Standard; 
						break;
					case "premier" : 
						$agentType = LettingAgents_Object_AgentTypes::Premier;
						break;
					default :
						$agentType = LettingAgents_Object_AgentTypes::Standard;
				}
				
				$agentData->set_application_type($agentType);
				// Stuff gets saved here
				$agent->save($agentData);
				$this->_formStepCommonNavigate(4);
				return;
			} elseif (isset($_POST['back'])) {
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
	 * Initialise the terms and conditions step
	 *
	 * @return void
	 */
	public function termsAndConditionsAction() {

		$pageForm = new LettingAgents_Form_TermsAndConditions();
		if ($this->getRequest()->isPost()) {
			// We need to validate and save the data
			// TODO: Need to re-factor this to make it quicker
			$valid = $this->_formStepCommonValidate($pageForm, 'terms');

			// As these items aren't stored in the DB, assume that if user has validated step 1 in the past
			// then DPA and IDD are ticked
			$pageSession = new Zend_Session_Namespace('letting_agents_application');

			if ($valid) {

				$this->_formStepCommonNavigate('terms');
				return;
			} elseif (isset($_POST['back'])) {
				$this->_formStepCommonNavigate('terms');
				return;
			}
        }

		// Load the element data from the database if we can
		if ($this->_formStepCommonPopulate($pageForm, 'terms'))
		{
			// Render the page unless we have been redirected
			$this->view->form = $pageForm;
			$this->render('step');
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
        $pageSession = new Zend_Session_Namespace('letting_agents_application');
        $request = $this->getRequest();

//		Application_Core_Logger::log($stepNum);
        if ($request->isPost()) {
            // Handle moving backwards and forwards through the form
            $response = $this->getResponse();
			if ($stepNum == 'dd') {
				$response->setRedirect('/letting-agents/sign-up/dd');
				$response->sendResponse();
			}elseif ($stepNum == 'sign-up'){
				$response->setRedirect('/letting-agents/sign-up/step1');
				$response->sendResponse();
			}else {
				if (isset($_POST['back']) && $stepNum > 1) {
					$response->setRedirect('/letting-agents/sign-up/step' . ($stepNum - 1));
					$response->sendResponse();
					// exit();
				} elseif (isset($_POST['next']) && $stepNum < $this->_stepMax && $pageSession->completed[$stepNum]) {
					$response->setRedirect('/letting-agents/sign-up/step' . ($stepNum + 1));
					$response->sendResponse();
					// exit();
				// Handle payment screen traversal
				} elseif (isset($_POST['next']) && isset($_POST['payment_method']) && $_POST['payment_method'] == 'cc' && $stepNum == $this->_stepMax) {
					$response->setRedirect('/letting-agents/sign-up/cc');
					$response->sendResponse();
					// exit();
				} elseif (isset($_POST['next']) && isset($_POST['payment_method']) && $_POST['payment_method'] == 'dd' && $stepNum == $this->_stepMax) {
					$response->setRedirect('/letting-agents/sign-up/dd');
					$response->sendResponse();
					// exit();
				}
			}
        }
    } 

    /**
    * Private function to handle form population
    */
    private function _formStepCommonPopulate($pageForm, $stepNum) {
		$pageSession = new Zend_Session_Namespace('letting_agents_application');
		// First of all check that this form should be viewable and the user isn't trying to skip ahead
	//	$agentData = new LettingAgents_Object_AgentApplication(); 
		$this->view->stepNum = $stepNum;
		$this->view->stepMax = $this->_stepMax;

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
			$response = $this->getResponse();
			$response->setRedirect('/letting-agents/sign-up/step' . ($lastCompleted));
			$response->sendResponse();

			return false;
		}
		
		// Only populate from DB if we are in session and have a reference number
		if (isset($pageSession->agentUniqueId)) {

			$formData = array();

			// Populate $formData with data from model, if available
			switch ($stepNum) {
				case 1:
					$agent = new LettingAgents_Manager_AgentApplication();
					if($agentData = $agent->fetchByUid($pageSession->agentUniqueId)){
						#Zend_Debug::dump($agentData);die();
			    		$formData['is_previous_client'] = $agentData->get_is_previous_client();
						$formData['campaign_code'] = $agentData->get_campaign_code();
						$formData['legal_name'] = $agentData->get_legal_name();
						$formData['trading_name'] = $agentData->get_trading_name();
						$formData['organisation_type'] = $agentData->get_organisation_type();
						
						list($year,$month,$day) = explode("-",$agentData->get_date_established());
						$formData['date_established'] =  "$day/$month/$year";
						
						$formData['is_associated'] = $agentData->get_is_associated();
						$formData['associated_text'] = $agentData->get_associated_text();
						$formData['contact_name'] = $agentData->get_contact_name();
						$formData['contact_number'] = $agentData->get_contact_number();
						$formData['general_email'] = $agentData->get_contact_email();
					}
					
					$pageForm->populate($formData);
					break;
				case 2:			
					$agent = new LettingAgents_Manager_AgentApplication();
					$agentData = $agent->fetchByUid($pageSession->agentUniqueId);
					
				//	Zend_Debug::dump($agentData);die();
					// There could be multiple contacts here depending on the organisation type
					$contactManager = new LettingAgents_Manager_Contacts();
					$contactData = $contactManager->fetchByAgencyUid($pageSession->agentUniqueId);
					switch($agentData->get_organisation_type()){
						
						// Fetch Agency contacts when agency type is LimitedCompany						
						case LettingAgents_Object_CompanyTypes::LimitedCompany :
							$formData['registration_number'] = $agentData->get_company_registration_number();
							
							$contactsHtml = $this->view->partialLoop('partials/limited-company-list.phtml',$contactData);
							$this->view->contacts = $contactsHtml;
						break;
						
						// Fetch Agency contacts when agency type is LimitedLiabilityPartnership
						case LettingAgents_Object_CompanyTypes::LimitedLiabilityPartnership :
							$contactsHtml = $this->view->partialLoop('partials/partnership-list.phtml',$contactData);
							$this->view->contacts = $contactsHtml;
						break;
						
						// Fetch Agency contacts when agency type is Partnership
						case LettingAgents_Object_CompanyTypes::Partnership :
							$contactsHtml = $this->view->partialLoop('partials/partnership-list.phtml',$contactData);
							$this->view->contacts = $contactsHtml;
						break;
						
						// Fetch Agency contacts when agency type is SoleTrader
						case LettingAgents_Object_CompanyTypes::SoleTrader :
							$formData['uid'] = $contactData[0]['uid'];
							$formData['contact_name'] = $agentData->get_contact_name();
							
							list($year,$month,$day) = explode("-",$contactData[0]['birth_date']);
							
							$formData['birth_date'] = "$day/$month/$year";
		
							$formData['ni_number'] = $contactData[0]['ni_number'];
							$formData['passport_number'] = $contactData[0]['passport_number'];
							$formData['address_line1'] = $contactData[0]['address1'];
							$formData['address_line2'] = $contactData[0]['address2'];
							$formData['address_line3'] = $contactData[0]['address3'];
							$formData['address_postcode'] = $contactData[0]['postcode'];
							$formData['postcode'] = $contactData[0]['postcode'];
						break;
					}
					
					$pageForm->populate($formData);
					break;
				case 3:
					// Fetch Agent data
					$agent = new LettingAgents_Manager_AgentApplication();
					$agentData = $agent->fetchByUid($pageSession->agentUniqueId);
					//Zend_Debug::dump($agentData);die();
					
					$formData['current_referencing_supplier'] = $agentData->get_current_referencing_supplier();
					$formData['general_email_address'] = $agentData->get_contact_email();
					
					($agentData->get_number_of_branches() == 0) ?
							$formData['no_of_branches'] = "" :
							$formData['no_of_branches'] = $agentData->get_number_of_branches();
					
					($agentData->get_number_of_employees() == 0 ) ?
							$formData['no_of_staff'] = "" :
							$formData['no_of_staff'] = $agentData->get_number_of_employees();
					
					($agentData->get_number_of_lets() == 0) ?
							$formData['no_of_properties_managed'] = "" :
							$formData['no_of_properties_managed'] = $agentData->get_number_of_lets();
					
					($agentData->get_number_of_landlords() == 0) ?
							$formData['no_of_landlords'] = "" :	
							$formData['no_of_landlords'] = $agentData->get_number_of_landlords();
					
					
					// Fetch agency office data
					$officeManager = new LettingAgents_Manager_Office();
					// This only returns two items
					$offices = $officeManager->fetchAllByAgencyUid($pageSession->agentUniqueId);
					$officeManager = new LettingAgents_Manager_Office();
					foreach ($offices as $office){
						//Zend_Debug::dump($office);die();
						switch($office->office_type){
							case  LettingAgents_Object_OfficeTypes::BranchOffice:
								$formData['trading_uid'] = $office->uid;
								$formData['trading_address_line1'] = $office->address_1;
								$formData['trading_address_line2'] = $office->address_2;
								$formData['trading_address_line3'] = $office->address_3;
								$formData['trading_address_postcode'] = $office->postcode;
								$formData['trading_postcode'] = $office->postcode;
							break;
							
							case  LettingAgents_Object_OfficeTypes::HeadOffice:
								$formData['accounts_uid'] = $office->uid;
								$formData['accounts_address_line1'] = $office->address_1;
								$formData['accounts_address_line2'] = $office->address_2;
								$formData['accounts_address_line3'] = $office->address_3;
								$formData['accounts_address_postcode'] = $office->postcode;
								$formData['accounts_postcode'] = $office->postcode;
							break;
						}
					}
					
					// Fetch the multiple emails
					$multiEmail = new LettingAgents_Datasource_Email();
					$emails = array();
					$emails = $multiEmail->fetchById($agentData->get_id());
					foreach ($emails as $email) {

						switch($email['address_type']){
			   				case LettingAgents_Object_EmailTypes::General: 
			   					$formData['general_email_address'] = $email['email_address'];
			   					break;
			   				case LettingAgents_Object_EmailTypes::Referencing:
			   					$formData['email_for_referencing'] = $email['email_address'];
			   					break;
			   				case LettingAgents_Object_EmailTypes::Insurance:
			   					$formData['email_for_insurance'] = $email['email_address'];
			   					break;
			   				case LettingAgents_Object_EmailTypes::RentGuaranteeRenewals:
			   					$formData['email_for_rg_renewals'] = $email['email_address'];
			   					break;
			   				case LettingAgents_Object_EmailTypes::Invoicing:
			   					$formData['email_for_invoicing'] = $email['email_address'];
			   					break;
			   				case LettingAgents_Object_EmailTypes::HomeLetUpdates:
			   					$formData['email_for_marketing'] = $email['email_address'];
			   					break;
				   			}   
					}
					
					$pageForm->populate($formData);
					break;
				case 4:
					// Fetch Agent data
					$agent = new LettingAgents_Manager_AgentApplication();
					$agentData = $agent->fetchByUid($pageSession->agentUniqueId);
					// ensure that agent_type has a value
					$agent_type = $agentData->get_application_type();
					($agent_type == "") ? $formData['agent_type'] = "standard" : $formData['agent_type'] = $agent_type;
					
					$pageForm->populate($formData);
					break;
				
			}

        }

		$this->view->stepNum = $stepNum;
		$this->view->stepMax = $this->_stepMax;

		$tooFarAhead = false;
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
		$pageSession = new Zend_Session_Namespace('letting_agents_application');
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

			$errorMessages = $pageForm->getMessagesFlattened();
			$this->view->errorCount = count($errorMessages);
			$this->view->errorsHtml = $this->view->partial('partials/error-list.phtml', array('errors' => $errorMessages));
			return false;
        }
    }    
 
}
