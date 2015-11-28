<?php
/**
* Portfolio Insurance Quote Controller
* @param
* @return
* @author John Burrin
* @since 1.3
*/
class PortfolioInsuranceQuoteController extends Zend_Controller_Action {
    private $_stepMax = 5; // Number of form steps, excluding special pages like payment screens
    private $_customerReferenceNumber;
    private $_policyNumber;
    private $_referrer;
    private $_webLeadSummaryId;
    
    public function init() {
        $session = new Zend_Session_Namespace('homelet_global');
        
        Zend_Layout::startMvc();
        // Use the CMS layout
        Zend_Layout::getMvcInstance()->setLayoutPath( APPLICATION_PATH . '/modules/cms/layouts/scripts/' );
        // Extra form css for portfolio
        $this->view->headLink()->setStylesheet('/assets/portfolio-insurance-quote/css/portfolio-insurance-quote.css');
        $this->view->headScript()->appendFile('/assets/common/js/insurance-forms.js');
        $this->view->headScript()->appendFile('/assets/portfolio-insurance-quote/js/portfolio_form.js');
        $this->view->pageTitle = 'Portfolio Insurance Quote';
        $this->url = trim($this->getRequest()->getRequestUri(),'/');
        
        $menuData = array(
            'selected'  => 'landlords',
            'url'       => $this->url
        );
        
        // Check to see if we have a referrer code - if we do store it in a session variable
        if ($this->getRequest()->getParam('referrer')!='') {
            $session->referrer = Manager_Core_Agent::filterAsn($this->getRequest()->getParam('referrer'));
        } elseif(!isset($session->referrer)) {
            // no passed parameter so default it to our default account
            $session->referrer ="1403796";
        }
        
        // Check to see if we have a csuid - if we do store it in a session variable - Sorry Phil
        if ($this->getRequest()->getParam('csu')!='') {
            $session->csu = $this->getRequest()->getParam('csu');
        } elseif(!isset($session->csu)) {
            // no passed parameter so default it to our web user 87
            $session->csu ="87";
        }
        
        // Check to see if we have a origin - if we do store it in a session variable - Sorry Phil
        if ($this->getRequest()->getParam('origin')!='') {
            $session->csu = $this->getRequest()->getParam('origin');
        } elseif(!isset($session->origin)) {
            // no passed parameter so default it to internet 0
            $session->origin ="0";
        }
        
        
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
        
        // Load session data into private variables
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        if(isset($pageSession->CustomerRefNo)) $this->_customerReferenceNumber = $pageSession->CustomerRefNo;
        if(isset($pageSession->PolicyNumber)) $this->_policyNumber = $pageSession->PolicyNumber;
        if(isset($pageSession->webLeadSummaryId)) $this->_webLeadSummaryId = $pageSession->webLeadSummaryId;
        
        $session = new Zend_Session_Namespace('homelet_global');
        if(isset($session->referrer)) $this->_referrer = $session->referrer;
    }
    
    /**
     * Initialise the step 1 form
     *
     * @return void
     */
    public function step1Action() {
        // Append the address lookup javascript
        $this->view->headScript()->appendFile(
            '/assets/common/js/addressLookup.js',
            'text/javascript'
        );
        
        // Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 1;'
        );
        
        $pageForm = new Form_PortfolioInsuranceQuote_Step1();
        if ($this->getRequest()->isPost()) {
            // We need to validate and save the data
            // TODO: Need to re-factor this to make it quicker
            $valid = $this->_formStepCommonValidate($pageForm, 1);

            // As these items aren't stored in the DB, assume that if user has validated step 1 in the past
            // then DPA and IDD are ticked
            $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
            $pageForm->subform_idd->getElement('idd')->setValue($pageSession->step1->idd);
            
            
            if ($valid) {
                // Save the data and continue to next step
                $data = $pageForm->getValues(); // According to the Zend manual these *should* be the clean values
                $pageSession->step1->idd = $data["subform_idd"]["idd"];
                
                //Capture and store the insurance data protections.
                $this->_saveInsuranceDPA(
                    $data["subform_dataprotection"]["dpa_phone_post"],
                    $data["subform_dataprotection"]["dpa_sms_email"],
                    $data["subform_dataprotection"]["dpa_resale"]);
                
                
                $customerObject = new Model_Insurance_Portfolio_LegacyCustomer();
                $customerObject->title = $data["subform_personaldetails"]["title"] ;
                $customerObject->first_name = $data["subform_personaldetails"]["first_name"];
                $customerObject->last_name = $data["subform_personaldetails"]["last_name"];
                
                // Look up address again to populate dropdown
                $customerObject->address1 = $data["subform_correspondenceaddress"]["cor_address_line1"] ;
                $customerObject->address2 = $data["subform_correspondenceaddress"]["cor_address_line2"] ;
                $customerObject->address3 = $data["subform_correspondenceaddress"]["cor_address_line3"] ;
                $customerObject->postcode = $data["subform_correspondenceaddress"]["cor_postcode"] ;
                $customerObject->telephone1 = $data["subform_personaldetails"]["phone_number"];
                $customerObject->telephone2 = $data["subform_personaldetails"]["mobile_number"];
                $customerObject->email_address = $data["subform_personaldetails"]["email_address"];
                $customerObject->date_of_birth_at = Application_Core_Utilities::ukDateToMysql(
                    $data['subform_personaldetails']['date_of_birth_at']
                );
                
                // Check to see if we have a session
                if(!isset($pageSession->CustomerRefNo)) {
                    // We don't have a session so we need to create a new customer/quote entry to save against
                    // Now get the reference number from the newly created customer
                    // This is not a standard referecnce number
                    // Saved customer now create portfolio reference number
                    $NumberTracker = new Datasource_Core_NumberTracker();
                    $customerRefNo= $NumberTracker->getNextPortfolioNumber();
                    
                    $customerObject->refNo = $customerRefNo;
                    $customerManager = new Manager_Insurance_Portfolio_LegacyCustomer();
                    $customerManager->save($customerObject);
                    
                    $this->_customerReferenceNumber = $customerRefNo;
                    $pageSession->CustomerRefNo = $customerRefNo;
                } else {
                    // We are in session so just instantiate the customer manager with the existing reference number
                    $customerManager = new Manager_Insurance_Portfolio_LegacyCustomer();
                    $customerObject->refNo = $pageSession->CustomerRefNo;
                }
                
                $customerManager->save($customerObject);
                
                //Record this WebLead, if not already done so. First create or retrieve the
                //WebLead summary.
                $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
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
                $webLeadSummary->title = $data["subform_personaldetails"]["title"];
                $webLeadSummary->firstName = $data["subform_personaldetails"]["first_name"];
                $webLeadSummary->lastName = $data["subform_personaldetails"]["last_name"];
                
                if(!empty($data["subform_personaldetails"]["phone_number"])) {
                    $contactNumber = $data["subform_personaldetails"]["phone_number"];
                }
                else {
                    $contactNumber = $data["subform_personaldetails"]["mobile_number"];
                }
                
                $webLeadSummary->contactNumber = $contactNumber;
                $webLeadSummary->emailAddress = $data["subform_personaldetails"]["email_address"];
                
                if($isNewWebLead) {
                    $webLeadSummary->startTime = $this->_offsetDate();
                }
                
                $webLeadSummary->lastUpdatedTime = $this->_offsetDate();
                $webLeadSummary->product = Model_Core_WebLeadProduct::PORTFOLIO;
                $webLeadSummary->quoteNumber = $customerRefNo;
                $webLeadManager->updateSummary($webLeadSummary);
                
                //Update the blob, serialize and store.
                $webLeadBlob->blob = Zend_Json::encode($_POST);
                $webLeadBlob->blobChecksum = crc32($webLeadBlob->blob);
                $webLeadManager->updateBlob($webLeadBlob);
                
                //Finally, record the WebLead identifier in the page session.
                $pageSession->webLeadSummaryId = $webLeadSummary->webLeadSummaryId;
                
                //Capture and store the WebLead data protections.
                $this->_saveWebLeadDPA(
                    $webLeadSummary->webLeadSummaryId,
                    $data["subform_dataprotection"]["dpa_phone_post"],
                    $data["subform_dataprotection"]["dpa_sms_email"],
                    $data["subform_dataprotection"]["dpa_resale"]);
                
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
     * Step 2 Action
     *
     * @throws Zend_Exception
     * @author John Burrin
     */
    public function step2Action(){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        // Append the address lookup javascript
        $this->view->headScript()->appendFile(
            '/assets/common/js/addressLookup.js',
            'text/javascript'
        );
        
        // Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 2;'
        );
        $pageForm = new Form_PortfolioInsuranceQuote_Step2();
        if ($this->getRequest()->isPost()) {
            // We need to validate and save the data
            // TODO: Need to re-factor this to make it quicker
            $valid = $this->_formStepCommonValidate($pageForm, 2);
            
            if ($valid) {
                //Update the WebLead summary and create a STEP2 blob.
                $webLeadManager = new Manager_Core_WebLead();
                $webLeadSummary = $webLeadManager->getSummary($pageSession->webLeadSummaryId);
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
    
    /**
    * Step 3 Action
    * @param
    * @return
    * @author John Burrin
    * @since 1.3
    */
    public function step3Action(){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        
        // Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 3;'
        );
        
        $pageForm = new Form_PortfolioInsuranceQuote_Step3();
        
        if ($this->getRequest()->isPost()) {
            // We need to validate and save the data
            // TODO: Need to re-factor this to make it quicker
            $valid = $this->_formStepCommonValidate($pageForm, 3);
            
            if ($valid) {
                // None of this information is ever saved so sessionis(z)e it
                $data = $pageForm->getValues();
                $pageSession->step3->existing_insurer = $data['existing_insurer'];
                $pageSession->step3->target_premium = $data['target_premium'];
                $pageSession->step3->next_renwal_date = $data['next_renwal_date'];
                $pageSession->step3->how_hear = $data['how_hear'];
                $pageSession->step3->other = $data['other'];
                
                //Update the WebLead summary and create a STEP3 blob.
                $webLeadManager = new Manager_Core_WebLead();
                $webLeadSummary = $webLeadManager->getSummary($pageSession->webLeadSummaryId);
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
            $quoteManager = new Manager_Insurance_Portfolio_Quote();
               $this->view->premiums = $quoteManager->quote($this->_customerReferenceNumber);
            // Render the page unless we have been redirected
            $this->view->form = $pageForm;
            $this->render('step');
        }
    }
    
    /**
    * Step 4 Action
    * @param
    * @return
    * @author John Burrin
    * @since 1.3
    */
    public function step4Action(){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $pageForm = new Form_PortfolioInsuranceQuote_Step4();
        
        // Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 4;'
        );
        
        if ($this->getRequest()->isPost()) {
            // We need to validate and save the data
            // TODO: Need to re-factor this to make it quicker
            $valid = $this->_formStepCommonValidate($pageForm, 4);
            if ($valid) {
                // Everything has been saved ok so navigate to next step
                $uwQuestions = new Manager_Insurance_Portfolio_UnderwritingAnswers();
                $formData = $pageForm->getValues();
                
                while ($uwElement = current($formData['subform_importantinformation'])) {
                    $key = key($formData['subform_importantinformation']);
                    $uwData = new Model_Insurance_Portfolio_UnderwritingAnswers();
                    $uwData->refNo = $this->_customerReferenceNumber;
                    $uwData->questionID = str_replace("declaration", "", $key);
                    $uwData->answerGiven = $uwElement;
                    $uwData->dateAnswered = date("Y-m-d");
                    $uwQuestions->save($uwData);
                  //  echo $key.'<br />';
                    next($formData['subform_importantinformation']);
                }
                
                
                //Update the WebLead summary and create a STEP4 blob.
                $webLeadManager = new Manager_Core_WebLead();
                $webLeadSummary = $webLeadManager->getSummary($pageSession->webLeadSummaryId);
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
            $quoteManager = new Manager_Insurance_Portfolio_Quote();
            $this->view->premiums = $quoteManager->quote($this->_customerReferenceNumber);
            // Render the page unless we have been redirected
            $this->view->form = $pageForm;
            $this->render('step');
        }
    }
    
    /**
    *Step 5 Action
    * @param
    * @return
    * @author John Burrin
    * @since
    */
    
    public function step5Action(){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $pageForm = new Form_PortfolioInsuranceQuote_Step5();
        
        if ($this->_formStepCommonPopulate($pageForm,5)){
            $quoteManager = new Manager_Insurance_Portfolio_Quote();
            $quoteData = $quoteManager->quote($this->_customerReferenceNumber);
            
            $this->_doMail($quoteData);
            
            // Want to capture all the data and shove it all the old tables (Muntify)
            $quoteManager->convertLegacy($this->_customerReferenceNumber);
            
            //Update the WebLead summary and create a STEP4 blob.
            $webLeadManager = new Manager_Core_WebLead();
            $webLeadSummary = $webLeadManager->getSummary($pageSession->webLeadSummaryId);
            $webLeadSummary->lastUpdatedTime = $this->_offsetDate();
            $webLeadManager->updateSummary($webLeadSummary);
            
            //Determine if a new STEP5 blob needs to be created, or an existing one retrieved.
            if($webLeadManager->getBlobExists($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP5)) {
                $webLeadBlob = $webLeadManager->getBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP5);
            }
            else {
                $webLeadBlob = $webLeadManager->createNewBlob($webLeadSummary->webLeadSummaryId, Model_Core_WebLeadStep::STEP5);
            }
            
            //Update the blob and store
            $webLeadBlob->blob = Zend_Json::encode($_POST);
            $webLeadBlob->blobChecksum = crc32($webLeadBlob->blob);
            $webLeadManager->updateBlob($webLeadBlob);
            
            if($quoteData['referred'] == true ){
                $this->_helper->redirector->gotoUrl('/portfolio/insurance-quote/referred');
            }else{
                $this->_helper->redirector->gotoUrl('/portfolio/insurance-quote/complete');
            }
            exit();
        }
    }
    
    public function referredAction(){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $this->view->refNo = $pageSession->CustomerRefNo;
    }
    
    public function completeAction(){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $this->view->refNo = $pageSession->CustomerRefNo;
    }
    
    /**
    * This function is responsible for the content of the Add Property facebox popup window
    * @param
    * @return
    * @author John Burrin
    */
    public function propertiesAction(){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $output = array();
        // This controller is called within a popup (facebox style) so doesn't require a layout file
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->form = new Form_PortfolioInsuranceQuote_insuredAddress();
    }
    
    /**
    * This function is responsible for the content of the previous claims facebox popup window
    * @param
    * @return
    * @author John Burrin
    */
    public function claimsDialogAction(){
        $pageForm = new Form_PortfolioInsuranceQuote_claimsDialog();
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $output = array();
        // This controller is called within a popup (facebox style) so doesn't require a layout file
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_formStepCommonPopulate($pageForm, "claimsDialog");
        $this->view->form = $pageForm;
    }
    
    /**
    * This function is responsible for the content of the Additional information facebox popup window
    * @param
    * @return
    * @author John Burrin
    */
    public function additionalDialogAction(){
        $pageForm = new Form_PortfolioInsuranceQuote_additionalDialog();
        $pageForm->getElement('questionId')->setValue($this->getRequest()->getParam('qid'));
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $output = array();
        // This controller is called within a popup (facebox style) so doesn't require a layout file
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_formStepCommonPopulate($pageForm, "additionalDialog");
        $this->view->form = $pageForm;
    }
    
    /**
     * 
     */
    public function insuredAddressAction(){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        // Append the address lookup javascript
        $this->view->headScript()->appendFile(
            '/assets/common/js/addressLookup.js',
            'text/javascript'
        );
        
        // Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = "add";'
        );
        
        $pageForm = new Form_PortfolioInsuranceQuote_insuredAddress();
        if ($this->getRequest()->isPost()) {
                $request = $this->getRequest();
                $postdata = $request->getPost();
                $valid = $pageForm->isValid($postdata);
                
                if($valid){                    
                /**************************/
                // Create a new property manager and a new property data object
                $property = new Manager_Insurance_Portfolio_Property();
                $propertyObject = new Model_Insurance_Portfolio_Property();
                
                $cleanData = $pageForm->getValues(); // According to the Zend manual these *should* be the clean values
                $propertyObject->building = $cleanData['ins_house_number_name'];
                
                $propertyObject->buildingsAccidentalDamage = $cleanData['buildings_accidental_damage'];
                $propertyObject->buildingsNilExcess = $cleanData['buildings_nil_excess'];
                $propertyObject->buildingsSumInsured = $cleanData['buildings_cover'];
                
                $propertyObject->contentsAccidentalDamage = $cleanData['contents_accidental_damage'];
                $propertyObject->contentsNilExcess = $cleanData['contents_nil_excess'];
                $propertyObject->contentsSumInsured = $cleanData['contents_cover'];
                // $propertyObject->id
                $propertyObject->limitedContents = $cleanData['limited_contents'];
                
                // if we are editing this will be set
                if (isset($cleanData['propertyid']))  $propertyObject->id =   $cleanData['propertyid'];
                
                // Populate the propertyObject property details
                $propertyObject->address1 = $cleanData["ins_address_line1"] ;
                $propertyObject->address2 = $cleanData["ins_address_line2"] ;
                $propertyObject->address3 = $cleanData["ins_address_line3"] ;
                $propertyObject->postcode = $property->formatPostcode($cleanData["ins_postcode"]);
                $propertyObject->refno = $pageSession->CustomerRefNo;
                $propertyObject->tenantOccupation = $cleanData['employment_status'];
                
                $lastId = $property->save($propertyObject);
                // Everything has been saved ok so navigate to next step
                
                $this->_helper->redirector->gotoUrl('/portfolio/insurance-quote/step2');
                exit();
            }elseif (isset($_POST['cancel'])) {
                $this->_formStepCommonNavigate('2');
                return;
            }
        }elseif (isset($_GET['id'])){
            $this->_formStepCommonPopulate($pageForm, 'editInsuredAddress');
        }
        // Load the element data from the database if we can
        if ($this->_formStepCommonPopulate($pageForm, 'add'))
        {
            // Render the page unless we have been redirected
            $this->view->form = $pageForm;
            $this->render('step');
        }
    }
    
    /**
    * This function is responsible for the content of the remove property facebox popup window
    * @param
    * @return
    * @author John Burrin
    */
    public function removePropertyDialogAction(){
        $pageForm = new Form_PortfolioInsuranceQuote_removePropertyDialog();
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $output = array();
        // This controller is called within a popup (facebox style) so doesn't require a layout file
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_formStepCommonPopulate($pageForm, "removePropertyDialog");
        $this->view->form = $pageForm;
    }
    
    public function iddAction(){
        $output = array();
        // This controller is called within a popup (facebox style) so doesn't require a layout file
        $this->_helper->getHelper('layout')->disableLayout();
    }
    
    /**
    * This function is responsible for the content of the bank Interest dialog facebox popup window
    * @param
    * @return
    * @author John Burrin
    */
    public function bankInterestDialogAction(){
        $pageForm = new Form_PortfolioInsuranceQuote_bankInterestDialog();
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $output = array();
        // This controller is called within a popup (facebox style) so doesn't require a layout file
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_formStepCommonPopulate($pageForm, "bankInterestDialog");
        $this->view->form = $pageForm;
    }
    
    /**
    * Private function to handle form population
    */
    private function _formStepCommonPopulate($pageForm, $stepNum) {
        
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        // First of all check that this form should be viewable and the user isn't trying to skip ahead
        
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
            $this->_helper->redirector->gotoUrl('/portfolio/insurance-quote/step' . ($lastCompleted));
            return false;
        }
        
        // Echo out some debug info if not in production mode
        #Application_Core_Logger::log("Policy Number : " . $this->_policyNumber);
        #Application_Core_Logger::log("Customer Ref No : " . $this->_customerReferenceNumber);
        #Application_Core_Logger::log("Referrer : " . $this->_referrer);
        // Only populate from DB if we are in session and have a reference number
        if (isset($pageSession->CustomerRefNo)) {
            $customerReferenceNumber = $pageSession->CustomerRefNo;
            $policyNumber = $pageSession->PolicyNumber;
            $formData = array();
            
            // Populate $formData with data from model, if available
            switch ($stepNum) {
                case 1:
                    // Personal Details section
                    $customerManager = new Manager_Insurance_Portfolio_LegacyCustomer();
                    $customer = $customerManager->fetchByRefNo($customerReferenceNumber);
                    
                    $formData['title']          = $customer['title'];
                    $formData['first_name']     = $customer['first_name'];
                    $formData['last_name']      = $customer['last_name'];
                    $formData['phone_number']   = $customer['telephone1'];
                    $formData['mobile_number']  = $customer['telephone2'];
                    $formData['email_address']  = $customer['email_address'];
                    $formData['date_of_birth_at']            = Application_Core_Utilities::mysqlDateToUk(
                        $customer['date_of_birth_at']
                    );
                    
                    // Get correspondance Address data
                    // Look up address again to populate dropdown
                    $formData['cor_house_number_name'] = $customer['address1'];
                    // Populate the correspondence address details
                    $formData['cor_address_line1'] = $customer['address1'];
                    $formData['cor_address_line2'] = $customer['address2'];
                    $formData['cor_address_line3'] = $customer['address3'];
                    $formData['cor_address_postcode'] = $customer['postcode'];
                    
                    $formData['cor_postcode'] = $customer['postcode'];
                    $postcodeLookup = new Manager_Core_Postcode();
                    $addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $formData['cor_postcode']), $customer['address1']);
                    
                    $addressList = array('' => '--- please select ---');
                    $filterString = (is_numeric($formData['cor_house_number_name']))?$formData['cor_house_number_name'].", ":$formData['cor_house_number_name'];
                    
                    foreach($addresses as $address) {
                        $addressList[$address['id']] = $address['singleLineWithoutPostcode'];
                        if (stripos($address['singleLineWithoutPostcode'], $filterString) === 0) {
                            $addressID = $address['id'];
                        }
                    }
                    
                    // Add some validation
                    $cor_address = $pageForm->getSubForm('subform_correspondenceaddress')->getElement('cor_address');
                    $cor_address->setMultiOptions($addressList);
                    $validator = new Zend_Validate_InArray(array(
                        'haystack' => array_keys($addressList)
                    ));
                    $validator->setMessages(array(
                        Zend_Validate_InArray::NOT_IN_ARRAY => 'Insured address does not match with postcode'
                    ));
                    $cor_address->addValidator($validator, true);
                    
                    // Set the address to selected
                    $cor_address->setValue($addressID);
                    
                    // Data Protection section
                    //Extract the DPA values from the session.
                    $dpaManager = new Manager_Core_DataProtection(Manager_Core_DataProtection::USE_SESSION);
                    $dpaItemArray = $dpaManager->getItems(null, Model_Core_DataProtection_ItemEntityTypes::INSURANCE);
                    #Zend_Debug::dump($dpaItemArray);die();
                    foreach($dpaItemArray as $currentItem) {
                            
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
                                    $formData['dpa_sms_email'] = 1;
                                }
                                else {
                                    $formData['dpa_sms_email'] = 0;
                                }
                                break;
                                
                            case Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_THIRDPARTY:
                                
                                if($currentItem->isAllowed) {
                                    $formData['dpa_resale']  = 1;
                                }
                                else {
                                    $formData['dpa_resale']  = 0;
                                }
                                break;
                        }
                    }
                    
                    // Initial Disclosure Agreement section
                    // As we have a customer reference number they must have saved step 1 at some point which means
                    // they must have agreed to the IDD section
                    $formData['idd'] = 1;
                    $pageForm->populate($formData);
                    break;
                
                case 2:
                    // Step needs to retrieve any properties that may have been added to this portfolio quote
                    // This data is NOT form data, just display data since the ajax has added the properties to the
                    // portfolio_properties table
                    
                    $propertyArray = array();
                    
                    $propertyManager = new Manager_Insurance_Portfolio_Property();
                    $propertyArray = $propertyManager->fetchAllProperties($customerReferenceNumber);
                    $propNumb = count($propertyArray->toArray());
                    $formData['propNumb'] = count($propertyArray->toArray());
                    $this->view->propertyList = $propertyArray;
                    $this->view->stepNum = 2;
                    $pageForm->populate($formData);
                    break;
                
                case 3:
                    /*
                      This step display the properties and displays the quote value
                    */
                    $propertyArray = array();
                    $propertyManager = new Manager_Insurance_Portfolio_Property();
                    $propertyArray = $propertyManager->fetchAllProperties($customerReferenceNumber);
                    $this->view->propertyList = $propertyArray;
                    $this->view->stepNum = 3;
                    break;
                
                case 4:
                    break;
                
                /*
                    populates the remove property dialog
                */
                case "removePropertyDialog":
                    $request = $this->getRequest();
                    $formData['propertyid'] = $request->getParam('id');
                    
                    $pageForm->populate($formData);
                break;
                
                case "editInsuredAddress":
                    $request = $this->getRequest();
                    
                    $formData['propertyid'] = $request->getParam('id');
                    
                    $propertyManager = new Manager_Insurance_Portfolio_Property();
                    $propertyObj = $propertyManager->getPropertyById($formData['propertyid']);
                    $formData['ins_house_number_name'] = $propertyObj->houseNumber;
                    $formData['ins_postcode'] = $propertyObj->postcode;
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
                    $ins_address = $pageForm->getElement('ins_address');
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
                    //Zend_Debug::dump($propertyObj);die();
                    // Get Insured Address data
                    // Look up address again to populate dropdown
                    // Populate the correspondence address details
                    
                    $formData['ins_address_line1'] = $propertyObj->address1;
                    $formData['ins_address_line2'] = $propertyObj->address2;
                    $formData['ins_address_line3'] = $propertyObj->address3;
                    $formData['ins_address_postcode'] = $propertyObj->postcode;
                    $formData['employment_status'] = $propertyObj->tenantOccupation;
                    if($propertyObj->buildingsSumInsured == 0) {
                        $formData['buildings_cover'] = "";
                    }else{
                        $formData['buildings_cover'] = $propertyObj->buildingsSumInsured;
                        $formData['comprehensive_buildings_insurance'] = 1; 
                    }
                    
                    if($propertyObj->buildingsAccidentalDamage == 'Yes'){
                        $formData['buildings_accidental_damage'] = 1;
                    }else{
                        $formData['buildings_accidental_damage'] = 0;
                    }
                    
                    if($propertyObj->buildingsNilExcess == 'Yes'){
                        $formData['buildings_nil_excess'] = 1;
                    }else{
                        $formData['buildings_nil_excess'] = 0;
                    }
                    
                    if($propertyObj->contentsSumInsured == 0){
                        $formData['contents_cover'] = "";
                    }else{
                        $formData['contents_cover'] = $propertyObj->contentsSumInsured;
                        $formData['full_contents_insurance'] = 1;
                    }
                    
                    if($propertyObj->contentsAccidentalDamage == 'Yes'){
                        $formData['contents_accidental_damage'] = 1;
                    }else{
                        $formData['contents_accidental_damage'] = 0;
                    }
                    
                    if($propertyObj->contentsNilExcess == 'Yes'){
                        $formData['contents_nil_excess'] = 1;
                    }else{
                        $formData['contents_nil_excess'] = 0;
                    }
                    
                    if($propertyObj->limitedContents == 'Yes'){
                        $formData['limited_contents'] = 1;
                    }else{
                        $formData['limited_contents'] = 0;
                    }
                    
                    // Populate the form
                    $pageForm->populate($formData);
                    break;
                
                case "bankInterestDialog":
                    $propertyManager = new Manager_Insurance_Portfolio_Property();
                    $propertyObjects = $propertyManager->fetchAllProperties($customerReferenceNumber);
                    $propertyArray = $propertyObjects->toArray();
                    $optionList = array('' => '--- please select ---');
                    
                    foreach($propertyArray as $property){
                        $optionList[$property['id']] =
                                    ($property['address1'])  ." ".
                                    ($property['address2'])  ." ".
                                    ($property['address3'])  ." ".
                                    ($property['postcode']);
                        }
                    
                    // Get the subform element for property address that the bank may have interest in
                    $propertyAddressSelect = $pageForm->getSubForm('subform_bank-details-form')->getElement('bank_property');
                    $propertyAddressSelect->setMultiOptions($optionList);
                    
                    $validator = new Zend_Validate_InArray(array(
                        'haystack' => array_keys($optionList)
                    ));
                    $validator->setMessages(array(
                        Zend_Validate_InArray::NOT_IN_ARRAY => 'Property not in list'
                    ));
                    $propertyAddressSelect->addValidator($validator, true);
                    // Set the selected to 0
                    $propertyAddressSelect->setValue('0');
                    
                    // Now fetch any bank interests we have already stored
                    $bankInterestManager = new Manager_Insurance_Portfolio_BankInterest();
                    $this->view->interestList = $bankInterestManager->fetchAllInterests($customerReferenceNumber);
                    $pageForm->populate($formData);
                    break;
                
                case "additionalDialog":
                    $propertyManager = new Manager_Insurance_Portfolio_Property();
                    $propertyObjects = $propertyManager->fetchAllProperties($customerReferenceNumber);
                    $propertyArray = $propertyObjects->toArray();
                    $optionList = array('' => '--- please select ---');
                    
                    foreach($propertyArray as $property){
                        $optionList[$property['id']] =
                                    ($property['address1'])  ." ".
                                    ($property['address2'])  ." ".
                                    ($property['address3'])  ." ".
                                    ($property['postcode']);
                        }
                    
                    // Get the subform element for property address that the bank may have interest in
                    $propertyAddressSelect = $pageForm->getElement('property');
                    $propertyAddressSelect->setMultiOptions($optionList);
                    
                    $validator = new Zend_Validate_InArray(array(
                        'haystack' => array_keys($optionList)
                    ));
                    $validator->setMessages(array(
                        Zend_Validate_InArray::NOT_IN_ARRAY => 'Not in list'
                    ));
                    $propertyAddressSelect->addValidator($validator, true);
                    // Set the selected to 0
                    $propertyAddressSelect->setValue('0');
                    
                    // Now fetch any Additionals we have already stored
                    $manager = new Manager_Insurance_Portfolio_AdditionalInformation();
                    $qid = $_GET['qid'];
                    $array = $manager->fetchAllByRefNo($customerReferenceNumber,$qid);
                    
                    $this->view->additionalList = $array;
                    $pageForm->populate($formData);
                    
                    break;
                
                case "claimsDialog":
                    $propertyManager = new Manager_Insurance_Portfolio_Property();
                    $propertyObjects = $propertyManager->fetchAllProperties($customerReferenceNumber);
                    $propertyArray = $propertyObjects->toArray();
                    $optionList = array('' => '--- please select ---');
                    
                    foreach($propertyArray as $property){
                        $optionList[$property['id']] =
                                    ($property['address1'])  ." ".
                                    ($property['address2'])  ." ".
                                    ($property['address3'])  ." ".
                                    ($property['postcode']);
                        }
                    
                    // Get the form element for property address
                    $propertyAddressSelect = $pageForm->getSubForm('subform_previous-claims-form')->getElement('claim_property');
                    $propertyAddressSelect->setMultiOptions($optionList);
                    
                    $validator = new Zend_Validate_InArray(array(
                        'haystack' => array_keys($optionList)
                    ));
                    $validator->setMessages(array(
                        Zend_Validate_InArray::NOT_IN_ARRAY => 'Not in list'
                    ));
                    $propertyAddressSelect->addValidator($validator, true);
                    // Set the selected to 0
                    $propertyAddressSelect->setValue('0');
                    
                    $claimTypeList = array('' => '--- please select ---');
                    $claimTypesSelect = $pageForm->getSubForm('subform_previous-claims-form')->getElement('claim_type');
                    $claimTypes = new Datasource_Insurance_PreviousClaimTypes();
                    $claimTypeObjects = $claimTypes->getPreviousClaimTypes(Model_Insurance_ProductNames::LANDLORDSPLUS);
                    
                    foreach($claimTypeObjects as $ClaimType){
                        $claimTypeList[$ClaimType->getClaimTypeID()] = $ClaimType->getClaimTypeText();
                    }
                    $claimTypesSelect->setMultiOptions($claimTypeList);
                    $pageForm->populate($formData);
                    
                    // Now fetch any claims we have already stored
                    $claimsManager = new Manager_Insurance_Portfolio_PreviousClaims();
                    //$array = $claimsManager->fetchAllClaims($customerReferenceNumber);
                    
                    $this->view->claimsList = $claimsManager->fetchAllClaims($customerReferenceNumber);
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
     * Allows navigation between steps
     *
     * @param int $stepNum current step number
     *
     * @return void
     */
    private function _formStepCommonNavigate($stepNum) {
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            // Handle moving backwards and forwards through the form
            if ($stepNum == 'dd') {
                $this->_helper->redirector->gotoUrl('/portfolio/insurance-quote/dd');
            }elseif(isset($_POST['back']) && $stepNum == '2') {
                $this->_helper->redirector->gotoUrl('/portfolio/insurance-quote/step1');
            }elseif(isset($_POST['cancel']) && $stepNum == '2') {
                $this->_helper->redirector->gotoUrl('/portfolio/insurance-quote/step2');
            }else {
                if (isset($_POST['back']) && $stepNum > 1) {
                    $this->_helper->redirector->gotoUrl('/portfolio/insurance-quote/step' . ($stepNum - 1));
                    // exit();
                } elseif (isset($_POST['next']) && $stepNum < $this->_stepMax && $pageSession->completed[$stepNum]) {
                    $this->_helper->redirector->gotoUrl('/portfolio/insurance-quote/step' . ($stepNum + 1));
                    // exit();
                // Handle payment screen traversal
                } elseif (isset($_POST['next']) && isset($_POST['payment_method']) && $_POST['payment_method'] == 'cc' && $stepNum == $this->_stepMax) {
                    $this->_helper->redirector->gotoUrl('/portfolio/insurance-quote/cc');
                    // exit();
                } elseif (isset($_POST['next']) && isset($_POST['payment_method']) && $_POST['payment_method'] == 'dd' && $stepNum == $this->_stepMax) {
                    $this->_helper->redirector->gotoUrl('/portfolio/insurance-quote/dd');
                    // exit();
                }
            }
        }
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
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
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
            $this->view->errorsHtml = $this->view->partial('portfolio-insurance-quote/partials/error-list.phtml', array('errors' => $errorMessages));
            return false;
        }
    }
    
    private function _doMail($data){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $customerRefNo = $pageSession->CustomerRefNo;
        
        // Get Customer
        $customerManager = new Manager_Insurance_Portfolio_LegacyCustomer();
        $customerObject = new Model_Insurance_Portfolio_LegacyCustomer();
        $customerObject = $customerManager->fetchByRefNo($customerRefNo);
        
        // Get Properties
        $propertyManager= new Manager_Insurance_Portfolio_Property();
        $properties = array();
        
        // Fetch all the properties related to this customer refNo
        $properties = $propertyManager->fetchAllProperties($customerRefNo)->toArray();
        $propertyHtml = $this->view->partialLoop('portfolio-insurance-quote/partials/email-templates/property-details.phtml',$properties);
        
        // Fetch claims releted to this customer refNo
        $claimsManager = new Manager_Insurance_Portfolio_PreviousClaims();
        $claims = $claimsManager->fetchWithClaimTypes($customerRefNo);
        $claimsHtml = $this->view->partialLoop('portfolio-insurance-quote/partials/email-templates/claims.phtml',$claims);
        
        // Fetch bank interest related to this customer refNo
        $bankInterestManager = new Manager_Insurance_Portfolio_BankInterest();
        $bankInterest = $bankInterestManager->fetchAllInterests($customerRefNo);
        $bankInterestHtml = $this->view->partialLoop('portfolio-insurance-quote/partials/email-templates/bank-interest.phtml',$bankInterest);
        
        $uwManager = new Manager_Insurance_Portfolio_UnderwritingAnswers();
        $uwAnswers = $uwManager->fetchByRefNo($customerRefNo);
        
        // Merge the claim and Interest info into the UW template
        $uwQuestionsHtml = $this->view->partial('portfolio-insurance-quote/partials/email-templates/uw-questions.phtml',array(
                                                                                        'claimsHtml' => $claimsHtml,
                                                                                        'bankInterestHtml' => $bankInterestHtml,
                                                                                        'uwAnswers' => $uwAnswers->toArray()
                                                                                        ));
        // Merge all the html together
        $mailBody = $this->view->partial('portfolio-insurance-quote/partials/email-templates/emailQuote.phtml',array(
                        'theData' => $data,
                        'theCustomer' => $customerObject->toArray(),
                        'propertyHtml' => $propertyHtml,
                        'uwQuestionsHtml' => $uwQuestionsHtml
                    ));
        
        // Get some parameter stuffs
        $params = Zend_Registry::get('params');
        $emailArray = explode(",",$params->email->portfolioAdmin);
        $toAddress = $emailArray[0];
        $ccAddress = $emailArray[1];
        $fromAddress = $params->email->noreply;
        
        // Mail that bad boy
        if (isset($data['referred'])) $referred = " - REFERRED";
        $email = new Application_Core_Mail();
        $email->setFrom($fromAddress, "PORTFOLIO NEW BUSINESS $referred");
        $email->setTo($toAddress, "Underwriting");
        $email->setCC($ccAddress);
        $email->setSubject("Portfolio Website Query - ref: $customerRefNo");
        $email->setBodyHtml($mailBody);
        $email->send();
        return;
    }
    
    /**
    * This function just butchers the date to subtract 5 hours to make webleads work correctly
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
     * @param string $customerRefNo
     * The customer reference number.
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
    protected function _saveInsuranceDPA($phonePost, $smsEmail, $thirdParty) {
        $dpaList = array();
        
        //Capture and translate the details of the dpa item - marketing by phone and post.
        $dpaItem = new Model_Core_DataProtection_Item();
        $dpaItem->entityTypeId = Model_Core_DataProtection_ItemEntityTypes::INSURANCE;
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
        $dpaItem->entityTypeId = Model_Core_DataProtection_ItemEntityTypes::INSURANCE;
        $dpaItem->constraintTypeId = Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_SMSANDEMAIL;
        if($smsEmail == 1) {
            $dpaItem->isAllowed = true;
        }
        else {
            $dpaItem->isAllowed = false;
        }
        array_push($dpaList, $dpaItem);
        
        //Capture and translate the details of dpa item - third party details resale.
        $dpaItem = new Model_Core_DataProtection_Item();
        $dpaItem->entityTypeId = Model_Core_DataProtection_ItemEntityTypes::INSURANCE;
        $dpaItem->constraintTypeId = Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_THIRDPARTY;
        if($thirdParty == 1) {
            $dpaItem->isAllowed = true;
        }
        else {
            $dpaItem->isAllowed = false;
        }
        array_push($dpaList, $dpaItem);
        
        //Store the data protections.
        $dpaManager = new Manager_Core_DataProtection(Manager_Core_DataProtection::USE_SESSION);
        foreach($dpaList as $currentItem) {
            $dpaManager->upsertItem($currentItem);
        }
    }
    
    /**
     * Saves the TCI+ data protection values specified by the user.
     *
     * @param mixed $itemGroupId
     * The WebLead identifier.
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
    protected function _saveWebLeadDPA($itemGroupId, $phonePost, $smsEmail, $thirdParty) {
        //Capture and store the data protections.
        $dpaList = array();
        
        //Capture and translate the details of the dpa item - marketing by phone and post.
        $dpaItem = new Model_Core_DataProtection_Item();
        $dpaItem->itemGroupId = $itemGroupId;
        $dpaItem->entityTypeId = Model_Core_DataProtection_ItemEntityTypes::WEBLEAD;
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
        $dpaItem->entityTypeId = Model_Core_DataProtection_ItemEntityTypes::WEBLEAD;
        $dpaItem->constraintTypeId = Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_SMSANDEMAIL;
        if($smsEmail == 1) {
            $dpaItem->isAllowed = true;
        }
        else {
            $dpaItem->isAllowed = false;
        }
        array_push($dpaList, $dpaItem);
        
        //Capture and translate the details of dpa item - third party details resale.
        $dpaItem = new Model_Core_DataProtection_Item();
        $dpaItem->itemGroupId = $itemGroupId;
        $dpaItem->entityTypeId = Model_Core_DataProtection_ItemEntityTypes::WEBLEAD;
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
