<?php

class LandlordsReferencing_IndexController extends Zend_Controller_Action
{
	public function init()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		
		// Bit of a dirty hack to use a layout from the CMS module
		$layout = Zend_Layout::getMvcInstance();                                
        $layout->setLayoutPath(APPLICATION_PATH . '/modules/cms/layouts/scripts');
        $layout->setLayout('default');
        
        // Top level styles and Javacript
        $this->view->headLink()->appendStylesheet('/assets/landlords-referencing/css/landlords-referencing.css');
        $this->view->headScript()->appendFile('/assets/common/js/insurance-forms.js');
		$this->view->headScript()->appendFile('/assets/landlords-referencing/js/referencing_form.js');
		$this->view->pageTitle = 'Tenant Referencing';
		$this->url = trim($this->getRequest()->getRequestUri(),'/');
		
		//Menu selection
		$menuData = array(
			'selected'  => 'landlords',
			'url'       => $this->url
		);
		
		// Munting menu layout logics.
		$mainMenu = $this->view->partial('partials/homelet-mainmenu.phtml', $menuData);
		$subMenu = $this->view->partial('partials/homelet-submenu.phtml', $menuData);
		$layout = Zend_Layout::getMvcInstance();
		$layout->getView()->mainMenu = $mainMenu;
		$layout->getView()->subMenu = $subMenu;
		if (isset($menuData['selected'])) { $layout->getView()->styling = $menuData['selected']; }
		
		// Load the site link urls from the parameters and push them into the layout
		$params = Zend_Registry::get('params');
		$layout->getView()->urls = $params->url->toArray();
	}
	
	
	/**
	 *  Starting point for the referencing system.
	 *  
	 *  Allows the user to view brochureware, or to decide if they wish to log
	 *  RG or non-RG references.
	 *  
	 *  @return void
	 */
	public function startAction()
    {
        // Application url
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        if ($auth->hasIdentity()) {
            $this->view->applicationUrl = '/landlords/referencing/property-lease';
        }
        else {
            $this->view->applicationUrl = '/landlords/referencing/login';
        }
	}
	
	/**
	 * Allows the user to download referencing application forms.
	 */
	public function downloadApplicationAction()
    {
		$downloadAppForm = new LandlordsReferencing_Form_DownloadApplication();

        $request = $this->getRequest();
		if ($request->isPost()) {
			
			if ($downloadAppForm->isValid($request->getPost())) {
				
				$response = $this->getResponse();
				$data = $downloadAppForm->getValues();
				
				//Reroute to individual application form.
				switch($data['application_select']) {
					
					case 1:
						$this->_helper->redirector->gotoUrl('/assets/legacy-cms/media/referencing/direct-individual.pdf');
						break;
						
					case 2:
						$this->_helper->redirector->gotoUrl('/assets/legacy-cms/media/referencing/direct-student.pdf');
						break;
						
					case 3:
						$this->_helper->redirector->gotoUrl('/assets/legacy-cms/media/referencing/direct-unemployed.pdf');
						break;
						
					case 4:
						$this->_helper->redirector->gotoUrl('/assets/legacy-cms/media/referencing/direct-company.pdf');
						break;
						
					case 5:
						$this->_helper->redirector->gotoUrl('/assets/legacy-cms/media/referencing/direct-guarantor.pdf');
						break;
				}
				
				$response->sendResponse();
				return;
			}
		}
        
		// set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 0;
		$this->view->form = $downloadAppForm;
	}
	
	/**
	 * Initializes a new PLL data entry flow by partially clearing the session.
	 * 
	 * Can be called directly by methods of this class. Should be called through the
	 * pcsAction() method if the request is made via an Ajax request.
	 */
	protected function _partiallyClearSession()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		
		$session->referenceId = null;
		unset($session->referenceId);		
		$session->productName = Model_Referencing_ProductKeys::INSIGHT;
        	
		//Set the flow item to be the very first form.
		$flowManager = Manager_Referencing_DataEntry_Flow_FlowFactory::createFlowManager($session->productName);
        $session->currentFlowItem = $flowManager->currentFlowItem;
		return true;
	}
	
	
	/**
	 * Login function to add references to the system.
	 *
	 * @return void
	 */
	public function loginAction()
    {
        $this->view->pageTitle = 'Tenant Referencing Login';
		$loginForm = new LandlordsReferencing_Form_Login();
				
		// Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "login";'
        );

        $request = $this->getRequest();
		if ($request->isPost()) {
			if ($loginForm->isValid($request->getPost())) {
				$data = $loginForm->getValues();
				
				//Delegate login actions to the LoginForm.
				if ($loginForm->login($data['email'], $data['password'])) {
				    $this->_despatchToNext();
					return;
				}
				else {
                    $customerManager = new Manager_Referencing_Customer();
                    $customer = $customerManager->getByEmailAddress($data['email']);

                    if (!$customer) {
                        //None of the customer records matched the details provided by the user.
                        $loginForm->setDescription("Sorry, we've been unable to find these details.
                    	Please check the details you entered are correct and try again");
                    }
                    else {
                        $loginForm->setDescription('Hello, at the moment you\'re unable to access My HomeLet  because
                        you haven\'t validated your email address yet. We\'ve sent you an email which includes a link
                        to confirm your email address and validate your My HomeLet account. If you\'ve not received
                        your validation email or if you\'re unable to access your account, please call us on 0845 117
                        6000 - we\'re always happy to help!');
                    }

				}
			}
		}
        else {
            $this->_setProductsToDisplay($this->getRequest());
        }
		
		// Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 0;
		$this->view->form = $loginForm;
	}
	
	
	/**
	 * Sets the 'displayRentGuaranteeProducts' flag in the session to indicate the products to display.
	 * 
	 * The products to display are indicated in the GET param, which will be analysed from
	 * the $request object passed in.
	 * 
	 * @param Zend_Request $request
	 * 
	 * @return void
	 */
	protected function _setProductsToDisplay($request)
    {
		$productTypeRequested = $request->getParam('pt');
		if(empty($productTypeRequested)) {
			
			return;
		}
		
		//Identify which products the user has requested via the GET param. If none requested,
		//assume rent guarantee products.
		$session = new Zend_Session_Namespace('referencing_global');
		
		if($productTypeRequested == 'rg') {

			$session->displayRentGuaranteeProducts = true;
		}
		else if($productTypeRequested == 'nrg') {

			$session->displayRentGuaranteeProducts = false;
		}
	}
	
	
	/**
	 * Allows the private landlord (PLL) to register with HomeLet.
	 */
	public function registerAction()
    {
        $request = $this->getRequest();
		$registerForm = new LandlordsReferencing_Form_Register();

        // Populate the form with the security question options
        $securityQuestionModel = new Datasource_Core_SecurityQuestion();
        $securityQuestionOptions = $securityQuestionModel->getOptions();

        foreach ($securityQuestionOptions as $option) {
            $registerForm->security_question->addMultiOption($option['id'], $option['question']);
        }
		
		// Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "register";'
        );


        
		// Extract clean values, save them and validate
        if ($request->isPost()) {
            if ($registerForm->isValid($request->getPost())) {
                $registerForm->saveData();
                $this->_despatchToNext();
                return;
            }
            else {
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript($previouslyLoaded, $type = 'text/javascript');
            }
        }
		
		//Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 0;
		$this->view->form = $registerForm;
	}
	
	
	/**
	 * Despatches according to the user type, thereby ensuring the relevant forms are
	 * displayed to the user.
	 */
	protected function _despatchToNext()
    {
		$session = new Zend_Session_Namespace('referencing_global');

		// Identify the referencing user, and despatch to the appropriate form despatcher.
		if ($session->userType == Model_Referencing_ReferenceUserTypes::PRIVATE_LANDLORD) {
			$this->_privateLandlordDespatcher();
		}
		else {
			$this->_referenceSubjectDespatcher();
		}
	}
	

	/**
	 * Despatches the user (which is the reference subject, in this case) to the 
	 * next form.
	 */
	protected function _referenceSubjectDespatcher()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		
		if(empty($session->currentFlowItem)) {
		
			//The starting point.
			$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::REFERENCE_SUBJECT;
		}
		else {
		
			//Identify if the user has completed the data entry.
			if($session->currentFlowItem == Model_Referencing_DataEntry_FlowItems::TERMS) {

				$this->_helper->redirector->gotoUrl('/landlords/referencing/email-link-end');
				return;
			}
			
			
			//Set the FlowManager to point to the current form. To do this, identify the product
			//on which the subject is being referenced.						
			$referenceManager = new Manager_Referencing_Reference();
			$reference = $referenceManager->getReference($session->referenceId);

			$flowManager = Manager_Referencing_DataEntry_Flow_FlowFactory::createFlowManager(
				$reference->productSelection->product->key);
			
			//Use the FlowManager to calculate the next form.
			$flowManager->currentFlowItem  = $session->currentFlowItem;
			if(!$flowManager->moveToNext($session->referenceId)) {
				
				//The system has failed to move to the next form. Error?
				throw new Zend_Exception("Failed to move to next form.");
			}
		
			//Record the pointer to the new form.
			$session->currentFlowItem = $flowManager->currentFlowItem;
		}
		
        
		//Navigate to the next form.
        switch($session->currentFlowItem) {
        		
        	case Model_Referencing_DataEntry_FlowItems::PRODUCT:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/product-selection');
        		break;
        	case Model_Referencing_DataEntry_FlowItems::PROSPECTIVE_LANDLORD:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/prospective-landlord');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::REFERENCE_SUBJECT:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/reference-subject');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE:
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/first-residence');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/second-residence');
        		break;
        		
         	case Model_Referencing_DataEntry_FlowItems::THIRD_RESIDENCE:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/third-residence');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::CURRENT_LANDLORD:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/current-landlord');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/current-occupation');
        		break;
        		
            case Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/second-occupation');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/future-occupation');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::TERMS:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/terms');
        		break;
        		
        	default:
        		$errorMessage = "NAVIGATION FAILED----:{$session->currentFlowItem}";
        		Application_Core_Logger::log($errorMessage, 'error');
        		exit;
        }
	}
	
	
	/**
	 * Despatches to the user (which is the PLL in this case) to the appropriate form.
	 */
	protected function _privateLandlordDespatcher()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		
		//Ensure values exist for the _productName and the _currentFlowItem. These are integral to 
		//navigating from one form to another.
		if(empty($session->productName)) {
			$this->_partiallyClearSession();
		}
		else {
			//Set the flow manager to point to the current form.
			$flowManager = Manager_Referencing_DataEntry_Flow_FlowFactory::createFlowManager($session->productName);
			$flowManager->currentFlowItem = $session->currentFlowItem;

			
			//Use the FlowManager to calculate the next form.			
			if(!$flowManager->moveToNext($session->referenceId)) {
				
				//The system has failed to move to the next form. Error?
				throw new Zend_Exception("Failed to move to next form.");
			}
		
			//Record the pointer to the new form.
			$session->currentFlowItem = $flowManager->currentFlowItem;
		}

		//Navigate to the next form.
        switch($session->currentFlowItem) {
        	
        	case Model_Referencing_DataEntry_FlowItems::PROPERTY_LEASE:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/property-lease');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::PRODUCT:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/product-selection');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::PROSPECTIVE_LANDLORD:
        		//Re-route
        		
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/prospective-landlord');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::REFERENCE_SUBJECT:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/reference-subject');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/first-residence');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/second-residence');
        		break;
        		
         	case Model_Referencing_DataEntry_FlowItems::THIRD_RESIDENCE:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/third-residence');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::CURRENT_LANDLORD:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/current-landlord');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/current-occupation');
        		break;
        		
            case Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/second-occupation');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/future-occupation');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::TERMS:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/terms');
        		break;
        		
        	case Model_Referencing_DataEntry_FlowItems::PRICE_CONFIRMATION:
        		//Re-route
        		$this->_helper->redirector->gotoUrl('/landlords/referencing/price-confirmation');
        		break;
        		
        	default:
        		$errorMessage = "NAVIGATION FAILED----:{$session->currentFlowItem}";
        		Application_Core_Logger::log($errorMessage, 'error');
        		exit;
        }
	}
	
	
	/**
	 * Controls the PropertyLease form.
	 */
	public function propertyLeaseAction()
    {

		$session = new Zend_Session_Namespace('referencing_global');
		$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::PROPERTY_LEASE;
		$propertyLeaseForm = new LandlordsReferencing_Form_PropertyLease();
		
		
		//Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "property-lease";'
        );

        // Since the login page has been removed if the user is already logged in through the customer portal,
        // we have to initialise the workflow system.
        if ($session->userType == Model_Referencing_ReferenceUserTypes::PRIVATE_LANDLORD && empty($session->productName)) {
            $this->_partiallyClearSession();
        }
        
        //Extract clean values, save them and validate
        $request = $this->getRequest();
        $data = $request->getPost();
        if ($request->isPost()) {
            if ($propertyLeaseForm->isValid($data)) {
                //Save the details.
                $propertyLeaseForm->saveData();

                $this->_despatchToNext();
                return;
            }
            else {
                //Ensure the system displays only those products requested for display by the
                //customer.
                $this->_setProductsToDisplay($request);
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript(
                    $previouslyLoaded,
                    $type = 'text/javascript'
                );
            }
        }

		$propertyLeaseForm->populate($propertyLeaseForm->getValues());
		
		//Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 0;
		$this->view->form = $propertyLeaseForm;
	}
	
	
	/**
	 * Controls the ProductSelection form.
	 */
	public function productSelectionAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');		
        $session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::PRODUCT;
		$productSelectionForm = new LandlordsReferencing_Form_ProductSelection();

		
		//Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "product-selection";'
        );


        //Extract clean values, save them and validate
        $request = $this->getRequest();
        $data = $request->getPost();
        if ($request->isPost()) {
            if ($productSelectionForm->isValid($data)) {

                //Store the data.
                $productSelectionForm->saveData();

                //Reference is being completed by the PLL. Despatch to the next step.
                $this->_despatchToNext();
                return;
            }
            else {
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript(
                    $previouslyLoaded,
                    $type = 'text/javascript'
                );
            }
        }

		$productSelectionForm->populate($productSelectionForm->getValues());
		
		//Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 10;
				
		$session = new Zend_Session_Namespace('referencing_global');
		if($session->displayRentGuaranteeProducts) {
			$this->view->productHelp = 'rent-guarantee';
		} else {
			$this->view->productHelp = 'referencing';
		}
		$this->view->form = $productSelectionForm;
	}
	
	
	/**
	 * Controls the ProspectiveLandlord form.
	 */
	public function prospectiveLandlordAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::PROSPECTIVE_LANDLORD;
		$prospectiveLandlordForm = new LandlordsReferencing_Form_ProspectiveLandlord();
		
		//Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "prospective-landlord";'
        );

        //Extract clean values, save them and validate
        $request = $this->getRequest();
        $data = $request->getPost();
        if ($request->isPost()) {
            if ($prospectiveLandlordForm->isValid($data)) {

                //Save the form data.
                $prospectiveLandlordForm->saveData();

                //See if the the reference should be emailed to reference subject (and therefore involve
                //multiple users).
                $referencingManager = new Manager_Referencing_Reference();
                $reference = $referencingManager->getMinimalReference(
                    $session->referenceId
                );
                if ($reference->completionMethod == Model_Referencing_ReferenceCompletionMethods::TWO_STEP) {

                    //Email link to reference subject then stop.
                    $this->_helper->redirector->gotoUrl(
                        '/landlords/referencing/email-link-start'
                    );

                    return;
                }

                //Everything has been saved ok so navigate to next step.
                $this->_despatchToNext();
                return;
            }
            else {
                $this->view->errors = true;
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript(
                    $previouslyLoaded,
                    $type = 'text/javascript'
                );
            }
        }

        //If here then populate the ProspectiveLandlord form.
        $prospectiveLandlordForm->forcePopulate($prospectiveLandlordForm->getValues());
		
        //Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 25;
		$this->view->form = $prospectiveLandlordForm;
	}
	
	
	/**
	 * Emails a link to the Reference Subject.
	 */
	public function emailLinkStartAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$params = Zend_Registry::get('params');

		$referenceManager = new Manager_Referencing_Reference();
		$reference = $referenceManager->getReference($session->referenceId);
		
		
		//Generate the security token to be appended to the URL.
		$hashingString = $params->pll->emailLink->security->securityString;
		$securityManager = new Manager_Core_Security($hashingString);
		$userToken = $securityManager->generate(array('refNo' => $reference->externalId));
		
		
		//Retrieve name details to populate into the email.
		$metaData = array();
		$metaData['firstName'] = $reference->referenceSubject->name->firstName;
		$metaData['lastName'] = $reference->referenceSubject->name->lastName;
		$recipientName = $metaData['firstName'] . ' ' . $metaData['lastName'];
		
		
		//Get address details.
		$metaData['addressLine1'] = $reference->propertyLease->address->addressLine1;
		$metaData['town'] = $reference->propertyLease->address->town;
		$metaData['postCode'] = $reference->propertyLease->address->postCode;
		
		
		//Retrieve email and link details.
		$emailFromName = $params->pll->emailLink->email->fromname;
        $emailFrom = $params->pll->emailLink->email->from;
        $emailSubject = $params->pll->emailLink->email->subject;

        	
        $metaData['linkUrl'] = $params->pll->emailLink->email->linkStartUrl;
        $metaData['linkUrl'] .= '?';
        $metaData['linkUrl'] .= "refNo={$reference->externalId}";
        $metaData['linkUrl'] .= '&';
        $metaData['linkUrl'] .= "userToken=$userToken";
        
        
        //Send email link to reference subject.
        $emailer = new Application_Core_Mail();
        $emailer->setTo($reference->referenceSubject->contactDetails->email1, $recipientName);
        $emailer->setFrom($emailFrom, $emailFromName);
        $emailer->setSubject($emailSubject);
        $emailer->applyTemplate('landlordsreferencing_emaillinkstart', $metaData, false);
        $emailer->send();
		
		
		//Populate the form with the external reference number.
		$this->view->externalId = $reference->externalId;
		
		//Dont log the PLL out, but clear the session variables applicable to the
		//reference just logged.
		$this->_partiallyClearSession();
        $this->view->fractionComplete = 20;
	}
	
	/**
	 * Controls the form responsible for capturing Tenant/Guarantor details.
	 */
	public function referenceSubjectAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::REFERENCE_SUBJECT;
		$referenceSubjectForm = new LandlordsReferencing_Form_ReferenceSubject();
		
		
		//Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "reference-subject";'
        );
        
        
        $request = $this->getRequest();
        $data = $request->getPost();
        if ($request->isPost()) {
            if ($referenceSubjectForm->isValid($data)) {

                //Extract clean values and save.
                $referenceSubjectForm->saveData();

                //Everything has been saved ok so navigate to next step.
                $this->_despatchToNext();
                return;
            }
            else {
                $this->view->errors = true;
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript(
                    $previouslyLoaded,
                    $type = 'text/javascript'
                );
            }
        }

        $referenceSubjectForm->forcePopulate($referenceSubjectForm->getValues());
		
        //Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 30;
		$this->view->form = $referenceSubjectForm;
	}
	
	
	/**
	 * Controls the residence forms.
	 */
	public function firstResidenceAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE;
		$residenceForm = new LandlordsReferencing_Form_Residence();
		
		
		//Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "first-residence";'
        );

        
        //Extract clean values, save them and validate
        $request = $this->getRequest();
        $data = $request->getPost();
        if ($request->isPost()) {
            if ($residenceForm->isValid($data)) {

                $residenceForm->saveData();

                //Everything has been saved ok so navigate to next step.
                $this->_despatchToNext();
                return;
            }
            else {
                $this->view->errors = true;
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript(
                    $previouslyLoaded,
                    $type = 'text/javascript'
                );
            }
        }
        
        $residenceForm->populate($residenceForm->getValues());

        //Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 40;
		$this->view->form = $residenceForm;
	}
	
	
	public function secondResidenceAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE;
		$residenceForm = new LandlordsReferencing_Form_Residence();
		
		
		//Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "second-residence";'
        );
		
		
		//Assume by default the address is not a duplicate of the current address.
		$this->view->isDuplicateAddress = false;

        
        $request = $this->getRequest();
        $data = $request->getPost();
        if ($request->isPost()) {
            if ($residenceForm->isValid($data)) {

                //Apply business rules. The first previous address must not be the same
                //as the current address, and the second previous address must not be
                //the same as the first previous.
                $residenceManager = new Manager_Referencing_Residence();
                $isResidenceAllowed = $residenceManager->isResidenceAllowed(
                    $session->referenceId,
                    Model_Referencing_ResidenceChronology::FIRST_PREVIOUS,
                    $residenceForm->getAddressFromForm()
                );

                if ($isResidenceAllowed) {

                    $residenceForm->saveData();

                    //Everything has been saved ok so navigate to next step.
                    $this->_despatchToNext();
                    return;
                }

                //If here then the address is not allowed. Make the user aware of this.
                $this->view->isDuplicateAddress = true;
            }
            else {
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript(
                    $previouslyLoaded,
                    $type = 'text/javascript'
                );
            }
        }

        $residenceForm->populate($residenceForm->getValues());
		
        //Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 50;
		$this->view->form = $residenceForm;
	}
	
	
	public function thirdResidenceAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::THIRD_RESIDENCE;
		$residenceForm = new LandlordsReferencing_Form_Residence();
		
		
		//Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "third-residence";'
        );
		
		
		//Assume by default the address is not a duplicate of the current address.
		$this->view->isDuplicateAddress = false;

        
        $request = $this->getRequest();
        $data = $request->getPost();
        if ($request->isPost()) {
            if ($residenceForm->isValid($data)) {

                //Apply business rules. The second previous address must not be the same
                //as the first previous address.
                $residenceManager = new Manager_Referencing_Residence();
                $isResidenceAllowed = $residenceManager->isResidenceAllowed(
                    $session->referenceId,
                    Model_Referencing_ResidenceChronology::SECOND_PREVIOUS,
                    $residenceForm->getAddressFromForm()
                );

                if ($isResidenceAllowed) {

                    $residenceForm->saveData();

                    //Everything has been saved ok so navigate to next step.
                    $this->_despatchToNext();
                    return;
                }

                //If here then the address is not allowed. Make the user aware of this.
                $this->view->isDuplicateAddress = true;
            }
            else {
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript(
                    $previouslyLoaded,
                    $type = 'text/javascript'
                );
            }
        }

        $residenceForm->populate($residenceForm->getValues());
		
        //Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 60;
		$this->view->form = $residenceForm;
	}
	
	
	/**
	 * Controls the CurrentLandlord form.
	 */
	public function currentLandlordAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::CURRENT_LANDLORD;
		$currentLandlordForm = new LandlordsReferencing_Form_CurrentLandlord();
		
		
		//Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "current-landlord";'
        );
        
        
        $request = $this->getRequest();
        $data = $request->getPost();
        if ($request->isPost()) {
            if ($currentLandlordForm->isValid($data)) {

                //Extract clean values and save them
                $currentLandlordForm->saveData();

                //Everything has been saved ok so navigate to next step.
                $this->_despatchToNext();
                return;
            }
            else {
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript(
                    $previouslyLoaded,
                    $type = 'text/javascript'
                );
            }
        }
        
        $currentLandlordForm->populate($currentLandlordForm->getValues());
		
        //Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 70;
		$this->view->form = $currentLandlordForm;
	}
	
	
	/**
	 * Controls the current Occupation form.
	 */
	public function currentOccupationAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION;
		$occupationForm = new LandlordsReferencing_Form_Occupation();
		
		//Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "current-occupation";'
        );
        
        
        $request = $this->getRequest();
        $data = $request->getPost();
        if ($request->isPost()) {
            if ($occupationForm->isValid($data)) {

                //Extract clean values and save them
                $thisOccupation = $occupationForm->saveData();

                //Everything has been saved ok so navigate to next step.
                $this->_despatchToNext();
                return;
            }
            else {
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript(
                    $previouslyLoaded,
                    $type = 'text/javascript'
                );
            }
        }
        
        $occupationForm->populate($occupationForm->getValues());
		
        //Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 80;
		$this->view->form = $occupationForm;
	}
	
	
	/**
	 * Controls the second Occupation form.
	 */
	public function secondOccupationAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION;
		$occupationForm = new LandlordsReferencing_Form_Occupation();
		
		
		//Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 1;'
        );
        
        
        $request = $this->getRequest();
        $data = $request->getPost();
        if ($request->isPost()) {
            if ($occupationForm->isValid($data)) {

                //Extract clean values and save them
                $thisOccupation = $occupationForm->saveData();

                //Everything has been saved ok so navigate to next step.
                $this->_despatchToNext();
                return;
            }
            else {
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript(
                    $previouslyLoaded,
                    $type = 'text/javascript'
                );
            }
        }
        
        $occupationForm->populate($occupationForm->getValues());

        //Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 85;
		$this->view->form = $occupationForm;
	}
	
	
	/**
	 * Controls the future Occupation form.
	 */
	public function futureOccupationAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION;
		$occupationForm = new LandlordsReferencing_Form_Occupation();
		
		
		//Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "future-occupation";'
        );
        
        $request = $this->getRequest();
		if($request->isPost()) {
						
        	//Extract values and determine if the user has cancelled the future
        	//occupation.
        	$data = $request->getPost();
		    if(!empty($data['cancel_future_employment'])) {
            	
            	//The user wishes to cancel their future occupation details, so do
            	//this and despatch to next.
		        $referenceManager = new Manager_Referencing_Reference();
		        $reference = $referenceManager->getReference($session->referenceId);
		        
                foreach($reference->referenceSubject->occupations as $occupation) {
		        	
		        	if($occupation->chronology == Model_Referencing_OccupationChronology::FUTURE) {
		        		
		        		if($occupation->classification == Model_Referencing_OccupationImportance::FIRST) {
		
		        			$occupationManager = new Manager_Referencing_Occupation();
            				$occupationManager->deleteOccupation($occupation);
		        			break;
		        		}
		        	}
		        }
            	
            	$this->_despatchToNext();
				return;
            }
        	
            //The user does not wish to cancel the occupation, so ensure that the details
            //are valid and save.
        	if($occupationForm->isValid($data)) {
        		
        		$occupationForm->saveData();
        		$this->_despatchToNext();
				return;
        	}
            else {
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript($previouslyLoaded, $type = 'text/javascript');
            }
        }
      
        $occupationForm->populate($occupationForm->getValues());
		
        //Set this to whatever you want the progress bar to how in percents
		$this->view->fractionComplete = 90;
		$this->view->form = $occupationForm;
	}
	
	
	/**
	 * Controls the terms form.
	 */
	public function termsAction()
    {
	    $session = new Zend_Session_Namespace('referencing_global');

	    if($session->userType == Model_Referencing_ReferenceUserTypes::PRIVATE_LANDLORD) {
	        $this->_privateLandlordTerms();
	    }
	    else {
	    
	        $this->_referenceSubjectTerms();
	    }

        // Change the terms view depending on declaration_version in legacy enquiry table
        $this->_helper->viewRenderer(sprintf('terms-%d', $this->_getDeclarationVersion()));
	}

	protected function _referenceSubjectTerms() {
	    
	    $session = new Zend_Session_Namespace('referencing_global');
	    $session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::TERMS;
	    $termsForm = new LandlordsReferencing_Form_Terms();
	    
	    //Tell page to use AJAX validation as we go
	    $this->view->headScript()->appendScript(
	        'var ajaxValidate = true; var ajaxValidatePage = "terms";'
	    );    

        $request = $this->getRequest();
        $data = $request->getPost();
        if ($request->isPost()) {
            if ($termsForm->isValid($data)) {

                //Extract clean values, save them and validate
                $data = $termsForm->getValues();

                //Capture the data.
                if ($data['terms_agreed'] == 'Yes') {

                    $this->_storeDataProtections();

                    //Update the progress.
                    $progressManager = new Manager_Referencing_Progress();
                    $referenceManager = new Manager_Referencing_Reference();
                    $reference = $referenceManager->getReference(
                        $session->referenceId
                    );

                    if (empty($reference->progress)) {

                        //Create new progress object.
                        $reference->progress = $progressManager->createNewProgress(
                            $reference->internalId
                        );

                        //Add a new progress item to the progress object.
                        $reference->progress->items[] = $progressManager->createNewProgressItem(
                            Model_Referencing_ProgressItemVariables::TERMS_AGREED,
                            Model_Referencing_ProgressItemStates::COMPLETE
                        );
                    }
                    else {

                        //Retrieve possible existing TERMS_AGREED progress item variable.
                        $progressItem = $progressManager->findSpecificProgressItem(
                            $reference->progress,
                            Model_Referencing_ProgressItemVariables::TERMS_AGREED
                        );

                        if (empty($progressItem)) {

                            //Doesn't exist, so create.
                            $reference->progress->items[] = $progressManager->createNewProgressItem(
                                Model_Referencing_ProgressItemVariables::TERMS_AGREED,
                                Model_Referencing_ProgressItemStates::COMPLETE
                            );
                        }
                        else {

                            //Exists so update.
                            $progressItem->itemState = Model_Referencing_ProgressItemStates::COMPLETE;
                        }
                    }

                    $referenceManager->updateReference($reference);
                    $this->_despatchToNext();
                    return;
                }

                //If here then the user clicked 'No' to the terms and conditions. As per the
                //behaviour of the previous generation of this system, simply re-display
                //the form.
            }
            else {
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript(
                    $previouslyLoaded,
                    $type = 'text/javascript'
                );
            }
        }

	    $termsForm->populate($termsForm->getValues());
	    
	    //Set this to whatever you want the progress bar to how in percents
	    $this->view->fractionComplete = 95;
	    $this->view->form = $termsForm;
	}

	protected function _privateLandlordTerms()
    {
	    $session = new Zend_Session_Namespace('referencing_global');
	    $session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::TERMS;
	    $termsForm = new LandlordsReferencing_Form_Terms();
	    
	    $request = $this->getRequest();
		$data = $request->getPost();
	    
	    $referenceManager = new Manager_Referencing_Reference();
	    $reference = $referenceManager->getReference($session->referenceId);
	    
	    //Check if the terms were previously agreed by the tenant. If yes, disable the controls and require the PLL to agree to the terms only.
	    $progressManager = new Manager_Referencing_Progress();
		$progressItem = $progressManager->findSpecificProgressItem(
			$reference->progress, Model_Referencing_ProgressItemVariables::TERMS_AGREED);

        if(!empty($progressItem) && $progressItem->itemState == Model_Referencing_ProgressItemStates::COMPLETE) {
            if ($request->isPost()) {
                $termsForm->setClearValidators(true);
                if ($termsForm->isValid($data)) {

                    //Capture the data.
                    if ($data['terms_agreed'] == 'Yes') {

                        //Push to munt now that all data is completed and the PLL has agreed to
                        //the terms.
                        $this->_pushToMunt($reference);
                        $this->_despatchToNext();
                        return;
                    }
                }
                else {
                    $previouslyLoaded = "var previouslyLoaded = true;\n";
                    $this->view->headScript()->appendScript(
                        $previouslyLoaded,
                        $type = 'text/javascript'
                    );
                }
            }

            // Get the declaration version
            $declarationVersion = $this->_getDeclarationVersion();

	        //Check or uncheck tbe DPA controls according to the reference subject's preferences.
	    	$dpaManager = new Manager_Core_DataProtection();
			$dpaItems = $dpaManager->getItems($session->referenceId, Model_Core_DataProtection_ItemEntityTypes::REFERENCING);
			foreach($dpaItems as $item) {
			    
			    if($item->isAllowed) {
			        
			        $isChecked = true;
			    }
			    else {
			    
			        $isChecked = false;
			    }
			    
			    $element = null;
			    switch($item->constraintTypeId) {
			    
			        case Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_NONDIGITAL_MEANS:
			            $element = $termsForm->consent_nondigital_marketing;

                        $element->setChecked((3 == $declarationVersion) ? !$isChecked : $isChecked);
                        $element->setAttrib('disabled', 'disabled');
			            break;
			            
			        case Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_DIGITAL_MEANS:
			            $element = $termsForm->consent_digital_marketing;

                        // If this is declaration version 3, invert preference
                        $element->setChecked($isChecked);
                        $element->setAttrib('disabled', 'disabled');
			            break;
			            
			        default: throw new Zend_Exception();
			    }
			}

			$termsForm->consent_information_stored->setChecked(true);
			$termsForm->consent_information_stored->setAttrib('disabled', 'disabled');
			
			$termsForm->consent_referee->setChecked(true);
			$termsForm->consent_referee->setAttrib('disabled', 'disabled');
	    }
	    else {
	        
			//Tell page to use AJAX validation as we go
			$this->view->headScript()->appendScript(
				'var ajaxValidate = true; var ajaxValidatePage = "terms";'
			);
		
	        //If here then the PLL has logged the reference without emailing a link to the tenant.
	        if ($termsForm->isValid($data)) {
	        
	        	//Capture the data.
	        	if($data['terms_agreed'] == 'Yes') {

					
		        	$this->_storeDataProtections();
					$this->_pushToMunt($reference);					
					$this->_despatchToNext();
		            return;
                }
            }
            else {
                $previouslyLoaded = "var previouslyLoaded = true;\n";
                $this->view->headScript()->appendScript($previouslyLoaded, $type = 'text/javascript');
            }
	    }
	    
	    $termsForm->populate($termsForm->getValues());
	    
	    //Set this to whatever you want the progress bar to how in percents
	    $this->view->fractionComplete = 95;
	    $this->view->form = $termsForm;
	}
	
	
	/**
	 * Writes the Reference object to the legacy datasources.
	 *
	 * @param Model_Referencing_Reference $reference
	 * The reference to write to the Munt.
	 *
	 * @return void
	 */
	protected function _pushToMunt(Model_Referencing_Reference $reference)
    {
        // Check the legacy customer id is set
        if ($reference->customer->legacyCustomerId == null &&
            $reference->customer->customerType == Model_Referencing_CustomerTypes::LANDLORD) {
            // Create a new legacy customer for the private landlord
            $customerManager = new Manager_Core_Customer();
            $customer = $customerManager->getCustomer(Model_Core_Customer::IDENTIFIER, $reference->customer->customerId);

            $legacyCustomer = $customerManager->createNewCustomer($customer->getEmailAddress(), Model_Core_Customer::CUSTOMER, true);

            $legacyCustomer->setFirstName($customer->getFirstName());
            $legacyCustomer->setLastName($customer->getLastName());

            $legacyCustomer->setAddressLine(Model_Core_Customer::ADDRESSLINE1, $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE1));
            $legacyCustomer->setAddressLine(Model_Core_Customer::ADDRESSLINE2, $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE2));
            $legacyCustomer->setAddressLine(Model_Core_Customer::ADDRESSLINE3, $customer->getAddressLine(Model_Core_Customer::ADDRESSLINE3));

            $legacyCustomer->setTelephone(Model_Core_Customer::TELEPHONE1, $customer->getTelephone(Model_Core_Customer::TELEPHONE1));
            $legacyCustomer->setTelephone(Model_Core_Customer::TELEPHONE2, $customer->getTelephone(Model_Core_Customer::TELEPHONE2));

            $legacyCustomer->setPassword($customer->getPassword());

            $customerManager->updateCustomer($legacyCustomer);

            // Drop the legacy customer refno into the reference customer map object
            $reference->customer->legacyCustomerId = $legacyCustomer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER);
        }

		$muntManager = new Manager_ReferencingLegacy_Munt();
		$muntManager->updateReference($reference);
	}

	protected function _storeDataProtections()
    {
	    $session = new Zend_Session_Namespace('referencing_global');
	    
	    $request = $this->getRequest();
		$data = $request->getPost();
	    
	    //Capture and store the data protections.
	    $dpaList = array();
	    
	    //Capture and translate the details of the dpa item - non-digital marketing.
	    $dpaItem = new Model_Core_DataProtection_Item();
	    $dpaItem->itemGroupId = $session->referenceId;
	    $dpaItem->entityTypeId = Model_Core_DataProtection_ItemEntityTypes::REFERENCING;
	    $dpaItem->constraintTypeId = Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_NONDIGITAL_MEANS;
	    if($data['consent_nondigital_marketing'] == 1) {
	
	        $dpaItem->isAllowed = true;
	    }
		else {
			
			$dpaItem->isAllowed = false;
		}

        // If this is version 3 of the terms, invert the preference
        if (3 == $this->_getDeclarationVersion()) {
            $dpaItem->isAllowed = !$dpaItem->isAllowed;
        }

	    array_push($dpaList, $dpaItem);
	    
	    
	    //Capture and translate the details of dpa item - marketing by sms and email.
	    $dpaItem = new Model_Core_DataProtection_Item();
	    $dpaItem->itemGroupId = $session->referenceId;
	    $dpaItem->entityTypeId = Model_Core_DataProtection_ItemEntityTypes::REFERENCING;
	    $dpaItem->constraintTypeId = Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_DIGITAL_MEANS;
	    if($data['consent_digital_marketing'] == 1) {
	
	        $dpaItem->isAllowed = true;
	    }
	    else {
	
	        $dpaItem->isAllowed = false;
	    }

	    array_push($dpaList, $dpaItem);

	    //Data protections stored directly without being attached to the Reference object.
	    //This should be refactored so that that data protections are stored in their
	    //own object which is linked from the Reference object.
	    $dpaManager = new Manager_Core_DataProtection(Manager_Core_DataProtection::USE_DBASE);
	    foreach($dpaList as $currentItem) {
	        
	        $dpaManager->upsertItem($currentItem);
	    }
	}
	
	/**
	 * This method is called when the Reference Subject completes an email link.
	 */
	public function emailLinkEndAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$params = Zend_Registry::get('params');
		
		
		$referenceManager = new Manager_Referencing_Reference();
		$reference = $referenceManager->getReference($session->referenceId);
		$customerId = $reference->customer->customerId;
		
		$customerManager = new Manager_Core_Customer();
		$customer = $customerManager->getCustomer(Model_Core_Customer::IDENTIFIER, $customerId);
		$customerEmail = $customer->getEmailAddress();
		$recipientName = $customer->getFirstName() . ' ' . $customer->getLastName();
		
		
		//Generate the security token to be appended to the URL.
		$hashingString = $params->pll->emailLink->security->securityString;
		$securityManager = new Manager_Core_Security($hashingString);
		$customerToken = $securityManager->generate(
			array('refNo' => $reference->externalId, 'customerId' => $customerId));
		
		
		//Prepare the meta data.
		$metaData = array();
		$metaData['firstName'] = $customer->getFirstName();
		$metaData['lastName'] = $customer->getLastName();
		
		$metaData['linkUrl'] = $params->pll->emailLink->email->linkEndUrl;
        $metaData['linkUrl'] .= '?';
        $metaData['linkUrl'] .= "refNo={$reference->externalId}";
        $metaData['linkUrl'] .= '&';
        $metaData['linkUrl'] .= "customerToken=$customerToken";
		
		
		//Send email to landlords
		$emailer = new Application_Core_Mail();
        $emailer->setTo($customerEmail, $recipientName);
        
        //Set the email from details.
        $emailFrom = $params->pll->emailLink->email->from;
        $emailFromName = $params->pll->emailLink->email->fromname;
        $emailer->setFrom($emailFrom, $emailFromName);
        
        //Set the subject line
		$addressLine1 = $reference->propertyLease->address->addressLine1;
		$town = $reference->propertyLease->address->town;
		$postCode = $reference->propertyLease->address->postCode;
        $emailer->setSubject("Re: $addressLine1, $town, $postCode");
        
        //Apply template and send.
        $emailer->applyTemplate('landlordsreferencing_emaillinkend', $metaData, false);
        $emailer->send();
        
		
		//Delete session
		unset($session->userType);
		unset($session->referenceId);
		
		$this->view->fractionComplete = 95;
	}
	
	/**
	 * Controls the price confirmation form.
	 */
	public function priceConfirmationAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
		$session->currentFlowItem = Model_Referencing_DataEntry_FlowItems::PRICE_CONFIRMATION;
		
		//Obtain the prices to display on the webpage.
        $referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($session->referenceId);
  //Zend_Debug::dump($reference);	
		$params = Zend_Registry::get('params');
        $productSelectionManager = new Manager_Referencing_ProductSelection();
        $price = $productSelectionManager->getPrice($reference->productSelection);
  //Zend_Debug::dump($reference->productSelection);
        //Display the prices.
        $this->view->priceExcludingVat = $price->getValue();        		
        $this->view->priceIncludingVat = ($price->getValue() * $params->vat->reversed);
		
		$this->view->fractionComplete = 95;
	}
	
	/*
	 * Redirect to secpay to take the payment
	 */
	public function processPaymentAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');
      	$params = Zend_Registry::get('params');
		$remotePassword = $params->secpay->get('remotepassword');
		
		$referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($session->referenceId);
        $productSelectionManager = new Manager_Referencing_ProductSelection();
        $price = $productSelectionManager->getPrice($reference->productSelection);
        $amount = ($price->getValue() * $params->vat->reversed);
        
		$pageForm = new TenantsInsuranceQuote_Form_Subforms_CreditCard();
		$this->view->secFormUrl = $params->secpay->get('host');
		$this->view->merchant = $params->secpay->get('merchant');
		$this->view->trans_id = $reference->externalId;
		$this->view->refno = $reference->externalId;
		$this->view->amount = $amount;
		$this->view->callback = $params->secpay->reference->get('success_callback') .";" . $params->secpay->reference->get('failed_callback');

		$this->view->template = $params->secpay->reference->get('template');
		$this->view->repeat = $params->secpay->get('repeat');
		$this->view->test_status = $params->secpay->get('test_status');
		$this->view->test_mpi_status = $params->secpay->get('test_mpi_status');
		$this->view->usage_type = $params->secpay->get('usage_type');
		$this->view->cb_flds = "refno:card_type:policynumber:paymentfrequency";
		$this->view->cb_card_type =  "";
		
		$digestkey = md5($reference->externalId.$amount.$remotePassword);
		
		$this->view->digest = $digestkey;
		$this->view->confirmationcode = "";
		$this->view->dups = "";
		
		$this->view->fractionComplete = 100;
		
		// Clear the session as we're off for payment
		$this->_partiallyClearSession();
	}
	
    /**
     * Controller for the credit card failed payment page
     *
     * @return void
     */
    public function ccFailedAction()
    {
  
    }	
	
	/**
     * Displays the reference summary details page as a popup.
     */
	public function summaryDialogAction()
    {
		$this->_helper->getHelper('layout')->disableLayout();
		

		$session = new Zend_Session_Namespace('referencing_global');
		$referenceManager = new Manager_Referencing_Reference();
		$reference = $referenceManager->getReference($session->referenceId);
		
		
		//Prepare the prospective landlord details.
		$prospectiveLandlord = $reference->propertyLease->prospectiveLandlord;
		
		$pllArray = array();
		$pllArray['name'] = $prospectiveLandlord->name->firstName .
			' ' .
			$prospectiveLandlord->name->lastName;
		
		$pllArray['contactName'] = '';
		$pllArray['address'] = $prospectiveLandlord->address->addressLine1 .
			' ' .
			$prospectiveLandlord->address->addressLine2 .
			' ' .
			$prospectiveLandlord->address->town;
		
		$pllArray['postCode'] = $prospectiveLandlord->address->postCode;
		$pllArray['telephone'] = $prospectiveLandlord->contactDetails->telephone1;
		$pllArray['mobile'] = $prospectiveLandlord->contactDetails->telephone2;
		$pllArray['email'] = $prospectiveLandlord->contactDetails->email1;
		
		$this->view->prospectiveLandlord = $pllArray;
		
		
		//Prepare the property details.
		$propertyArray = array();
		$propertyArray['address'] = $reference->propertyLease->address->addressLine1 .
			' ' .
			$reference->propertyLease->address->addressLine2 .
			' ' .
			$reference->propertyLease->address->town;
		
		$propertyArray['postCode'] = $reference->propertyLease->address->postCode;
		$propertyArray['rent'] = $reference->propertyLease->rentPerMonth->getValue();
		$propertyArray['startDate'] = $reference->propertyLease->tenancyStartDate->toString();
		$propertyArray['duration'] = $reference->propertyLease->tenancyTerm;
		$propertyArray['noOfTenants'] = $reference->propertyLease->noOfTenants;
		$this->view->property = $propertyArray;
		
		
		//Prepare the reference subject details
		$referenceSubject = $reference->referenceSubject;
		$referenceSubjectArray = array();
		$referenceSubjectArray['name'] = $referenceSubject->name->firstName .
			' ' .
			$referenceSubject->name->lastName;
			
		$referenceSubjectArray['maidenName'] = $referenceSubject->name->maidenName;
		$referenceSubjectArray['dob'] = $referenceSubject->dob->toString();
		if(!empty($referenceSubject->bankAccount)) {
			
			$referenceSubjectArray['bankAccountNumber'] = $referenceSubject->bankAccount->accountNumber;
			$referenceSubjectArray['bankSortCode'] = $referenceSubject->bankAccount->sortCode;
		}
		else {
			
			$referenceSubjectArray['bankAccountNumber'] = '';
			$referenceSubjectArray['bankSortCode'] = '';
		}
		$referenceSubjectArray['telephone'] = $referenceSubject->contactDetails->telephone1;
		$referenceSubjectArray['mobile'] = $referenceSubject->contactDetails->telephone2;
		$referenceSubjectArray['email'] = $referenceSubject->contactDetails->email1;
		
		
		$residenceManager = new Manager_Referencing_Residence();
		$residence = $residenceManager->findSpecificResidence(
			$reference->referenceSubject->residences,
			Model_Referencing_ResidenceChronology::CURRENT);
		$referenceSubjectArray['currentResidentialStatus'] = Model_Referencing_ResidenceStatus::toString($residence->status);
		
		
		$occupationManager = new Manager_Referencing_Occupation();
		$occupation = $occupationManager->findSpecificOccupation(
			$reference->referenceSubject->occupations,
			Model_Referencing_OccupationChronology::CURRENT,
			Model_Referencing_OccupationImportance::FIRST);
		
		//Convert the occupation type to an 'occupation status' for display purposes.
		switch($occupation->type) {
			
			case Model_Referencing_OccupationTypes::EMPLOYMENT: $occupationStatus = 'Employed'; break;
			case Model_Referencing_OccupationTypes::CONTRACT: $occupationStatus = 'On Contract'; break;
			case Model_Referencing_OccupationTypes::SELFEMPLOYMENT: $occupationStatus = 'Self Employed'; break;
			case Model_Referencing_OccupationTypes::INDEPENDENT: $occupationStatus = 'Financially Independent'; break;
			case Model_Referencing_OccupationTypes::RETIREMENT: $occupationStatus = 'Retired'; break;
			case Model_Referencing_OccupationTypes::STUDENT: $occupationStatus = 'Student'; break;
			case Model_Referencing_OccupationTypes::UNEMPLOYMENT: $occupationStatus = 'Unemployed'; break;
			case Model_Referencing_OccupationTypes::OTHER: $occupationStatus = 'Other'; break;
			default: throw new Zend_Exception(get_class() . '::' . __FUNCTION__ . ': unknonwn type provided.');
		}
		
		$referenceSubjectArray['currentOccupationStatus'] = $occupationStatus;
		$referenceSubjectArray['isOccupationLikelyToChange'] = $occupation->isPermanent ? 'No' : 'Yes';
		
		//Calculate the total income.
		$totalIncome = new Zend_Currency(array('value' => 0, 'precision' => 0));
		foreach($reference->referenceSubject->occupations as $occupation) {
			
			$totalIncome->add($occupation->income);
		}
		
		$referenceSubjectArray['income'] = $totalIncome->getValue();
		$referenceSubjectArray['shareOfRent'] = $referenceSubject->shareOfRent->getValue();
		$this->view->referenceSubject = $referenceSubjectArray;
		
		
		//Prepare the current landlord details.
		$currentResidence = $residenceManager->findSpecificResidence(
			$reference->referenceSubject->residences,
			Model_Referencing_ResidenceChronology::CURRENT);
		
		$currentLandlordArray = array();
		if(!empty($currentResidence->refereeDetails)) {

			$currentLandlordArray['name'] =
				$currentResidence->refereeDetails->name->firstName .
				' ' .
				$currentResidence->refereeDetails->name->lastName;
				
			$currentLandlordArray['address'] =
				$currentResidence->refereeDetails->address->addressLine1 .
				' ' .
				$currentResidence->refereeDetails->address->addressLine2 .
				' ' .
				$currentResidence->refereeDetails->address->town;
			
			$currentLandlordArray['postCode'] = $currentResidence->refereeDetails->address->postCode;
			$currentLandlordArray['telephoneDay'] = $currentResidence->refereeDetails->contactDetails->telephone1;
			$currentLandlordArray['telephoneEvening'] = $currentResidence->refereeDetails->contactDetails->telephone2;
			$currentLandlordArray['fax'] = $currentResidence->refereeDetails->contactDetails->fax1;
			$currentLandlordArray['email'] = $currentResidence->refereeDetails->contactDetails->email1;
		}
		else {

			$currentLandlordArray['name'] = '';
			$currentLandlordArray['address'] = '';
			$currentLandlordArray['postCode'] = '';
			$currentLandlordArray['telephoneDay'] = '';
			$currentLandlordArray['telephoneEvening'] = '';
			$currentLandlordArray['fax'] = '';
			$currentLandlordArray['email'] = '';
		}
		$this->view->currentLandlord = $currentLandlordArray;

		
		//Prepare the occupation details.
		for($i = 0; $i < 2; $i++) {
			
			switch($i) {
				
				case 0:
					$thisOccupation = $occupationManager->findSpecificOccupation(
						$reference->referenceSubject->occupations,
						Model_Referencing_OccupationChronology::CURRENT,
						Model_Referencing_OccupationImportance::FIRST);
					break;
				
				case 1:
					$thisOccupation = $occupationManager->findSpecificOccupation(
						$reference->referenceSubject->occupations,
						Model_Referencing_OccupationChronology::FUTURE,
						Model_Referencing_OccupationImportance::FIRST);
					break;
			}
			
			
			$thisOccupationArray = array();
			if(empty($thisOccupation)) {

				$thisOccupationArray['organisationName'] = '';
				$thisOccupationArray['contactName'] = '';
				$thisOccupationArray['contactPosition'] = '';
				$thisOccupationArray['oranisationAddress'] = '';
				$thisOccupationArray['postCode'] = '';
				$thisOccupationArray['telephone'] = '';
				$thisOccupationArray['fax'] = '';
				$thisOccupationArray['email'] = '';
				$thisOccupationArray['salary'] = '';
				$thisOccupationArray['positionHeld'] = '';
				$thisOccupationArray['payrollNumber'] = '';
				$thisOccupationArray['startDate'] = '';
				$thisOccupationArray['endDate'] = '';
				$thisOccupationArray['isPermanent'] = '';	
			}
			else {

				if(!empty($thisOccupation->refereeDetails)) {
				
					$thisOccupationArray['organisationName'] = $thisOccupation->refereeDetails->organisationName;
					$thisOccupationArray['contactName'] = $thisOccupation->refereeDetails->name->firstName . ' ' . $thisOccupation->refereeDetails->name->lastName;
					$thisOccupationArray['contactPosition'] = $thisOccupation->refereeDetails->position;
					$thisOccupationArray['oranisationAddress'] = $thisOccupation->refereeDetails->address->addressLine1 .
						' ' .
						$thisOccupation->refereeDetails->address->addressLine2 .
						' ' .
						$thisOccupation->refereeDetails->address->town;
					
					$thisOccupationArray['postCode'] = $thisOccupation->refereeDetails->address->postCode;
					$thisOccupationArray['telephone'] = $thisOccupation->refereeDetails->contactDetails->telephone1;
					$thisOccupationArray['fax'] = $thisOccupation->refereeDetails->contactDetails->fax1;
					$thisOccupationArray['email'] = $thisOccupation->refereeDetails->contactDetails->email1;
					
				}
				else {

					$thisOccupationArray['organisationName'] = '';
					$thisOccupationArray['contactName'] = '';
					$thisOccupationArray['contactPosition'] = '';
					$thisOccupationArray['oranisationAddress'] = '';
					$thisOccupationArray['postCode'] = '';
					$thisOccupationArray['telephone'] = '';
					$thisOccupationArray['fax'] = '';
					$thisOccupationArray['email'] = '';
				}
				
				if(empty($thisOccupation->variables)) {
					
					$thisOccupationArray['positionHeld'] = '';
					$thisOccupationArray['payrollNumber'] = '';
				}
				else {
					
					$thisOccupationArray['positionHeld'] = $thisOccupation->variables[Model_Referencing_OccupationVariables::POSITION];
					$thisOccupationArray['payrollNumber'] = $thisOccupation->variables[Model_Referencing_OccupationVariables::PAYROLL_NUMBER];
				}
				
				$thisOccupationArray['salary'] = $thisOccupation->income->getValue();
				$thisOccupationArray['positionHeld'] = '';
				$thisOccupationArray['payrollNumber'] = '';
				$thisOccupationArray['startDate'] = empty($thisOccupation->startDate) ? '' : $thisOccupation->startDate->toString();
				$thisOccupationArray['endDate'] = '';
				$thisOccupationArray['isPermanent'] = $thisOccupation->isPermanent ? 'Yes' : 'No';
			}
			
			
			switch($i) {
				
				case 0: $this->view->currentOccupation = $thisOccupationArray; break;
				case 1: $this->view->futureOccupation = $thisOccupationArray; break;
			}
		}
		

		//Prepare the residential details.
		for($i = 0; $i < 3; $i++) {

			switch($i) {
				
				case 0:
					$thisResidence = $residenceManager->findSpecificResidence(
						$reference->referenceSubject->residences,
						Model_Referencing_ResidenceChronology::CURRENT);
					break;
				
				case 1:
					$thisResidence = $residenceManager->findSpecificResidence(
						$reference->referenceSubject->residences,
						Model_Referencing_ResidenceChronology::FIRST_PREVIOUS);
					break;
				
				case 2:
					$thisResidence = $residenceManager->findSpecificResidence(
						$reference->referenceSubject->residences,
						Model_Referencing_ResidenceChronology::SECOND_PREVIOUS);
					break;
			}
			
			
			$thisResidenceArray = array();
			if(empty($thisResidence)) {

				$thisResidenceArray['address'] = '';
				$thisResidenceArray['postCode'] = '';
				$thisResidenceArray['duration'] = '';
			}
			else {

				if($thisResidence->address->isOverseasAddress) {

					$thisResidenceArray['address'] = 'Overseas';
					$thisResidenceArray['postCode'] = 'Overseas';
				}
				else {

					$thisResidenceArray['address'] = $thisResidence->address->addressLine1 .
							' ' .
							$thisResidence->address->addressLine2 .
							' ' .
							$thisResidence->address->town;

					$thisResidenceArray['postCode'] = $thisResidence->address->postCode;
				}
				$thisResidenceArray['duration'] = $thisResidence->durationAtAddress;
			}
			
			
			switch($i) {
				
				case 0: $this->view->firstResidence = $thisResidenceArray; break;
				case 1: $this->view->secondResidence = $thisResidenceArray; break;
				case 2: $this->view->thirdResidence = $thisResidenceArray; break;
			}
		}
	}
    
	
	/**
	 * Called from the legacy HRT system when the PLL's reference fails for some reason.
	 * 
	 * This can be called only after payment has been attempted.
	 */
	public function noticeAction()
    {
		$session = new Zend_Session_Namespace('referencing_global');        
	    if(empty($session->referenceId)) {
        	
        	//User is logged in, but they do not have a reference number, so ignore this and go to 
        	//the start of the data entry process.
        	return $this->_helper->redirector('property-lease');
        }
        
        
        //Retrieve the latest notice flagged against the reference.       
        $noticeManager = new Manager_Referencing_UserNotices();
        $notice = $noticeManager->getLatestNotice($session->referenceId);
        if(empty($notice)) {
        	
        	//Not notices flagged against the reference, so go to the start page of the
        	//data entry process.
        	return $this->_helper->redirector('property-lease');
        }
        
        
        //Retrieve the referencing details so that relevant details can be replaced into the
        //notice.
        $referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($session->referenceId);
        
        
        //Replace placeholders appropriate.
        $notice = preg_replace("/\[--EXTERNALID--\]/", $reference->externalId, $notice);
        $notice = preg_replace("/\[--INTERNALID--\]/", $reference->internalId, $notice);
        $this->view->notice = $notice;
        
        
        //Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 1;'
        );
	}
    
    /**
     * Product matrix
     */
    public function productMatrixAction()
    {
    }

    /**
     * Gets the current reference Enquiry declaration version
     *
     * @return int
     */
    private function _getDeclarationVersion()
    {
        $session = new Zend_Session_Namespace('referencing_global');
        $referencingLegacyEnquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
        $reference = $referencingLegacyEnquiryDatasource->getEnquiry($session->referenceId);
        return (int)$reference->declarationVersion;
    }
}
