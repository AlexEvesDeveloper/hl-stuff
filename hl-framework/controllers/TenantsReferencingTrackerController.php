<?php
class TenantsReferencingTrackerController extends Zend_Controller_Action {

    private $_pageSession;
    private $_logInAttempts; // Zero-indexed counter
    private $_enquiryId;
    private $_asn;
    
    
    /**
     * Initialise the TAT controller
     *
     * @return void
     *
     * @todo
     * Use Zend_Auth
     */
    public function init() {

        // If there's a linkRef in the request parameters then this is likely to be an IRIS-based application, so bounce
        //   the user to the new module, carrying the linkRef along
        $request = $this->getRequest();
        $linkRef = $request->getParam('linkRef');
        if (null !== ($linkRef)) {
            $this->_helper->redirector->gotoUrl('/tenant-application-tracker/?linkRef=' . $linkRef);
        }

        Zend_Layout::startMvc();
        
        // Use the CMS layout
        Zend_Layout::getMvcInstance()->setLayoutPath( APPLICATION_PATH . '/modules/cms/layouts/scripts/' );
        $this->view->headLink()->setStylesheet('/assets/tenant-application-tracker/css/tenant-application-tracker.css');
        $this->view->headScript()->appendFile('/assets/tenant-application-tracker/js/tat_form.js');
        $this->view->pageTitle = 'HomeLet Tenant Referencing Application Tracker';
        $this->url = trim($this->getRequest()->getRequestUri(),'/');
        
        $menuData = array(
            'selected'  => 'tenants',
            'url'       => 'tenants/reference-tracker'
        );
        
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
        
        // Start session, check if user "logged in" or has had too many attempts at logging in
        $this->_pageSession = new Zend_Session_Namespace('tenants_referencing_tracker');
        if(isset($this->_pageSession->logInAttempts)) {
            $this->_logInAttempts = $this->_pageSession->logInAttempts;
        }
        else {
            //Retrieve the number of login attempts allowed. The code uses this value as a
            //zero-indexed value (0 counts as a login attempt), so subtract one from the value
            //before use.
            $params = Zend_Registry::get('params');
            $loginAttempts = $params->tat->loginAttempts - 1;
            
            $this->_pageSession->logInAttempts = $loginAttempts;
            $this->_logInAttempts = $loginAttempts;
        }
        
        //Identify if the user has used up their permitted number of login attempts.
        if ($this->_logInAttempts <= 0) {
            
            // Send too-many-login-attempts users to warning page
            if ($this->url != 'tenants/reference-tracker/toomanyattempts') {
                $this->_helper->redirector->gotoUrl('/tenants/reference-tracker/toomanyattempts');
                
                return;
            }
        }
        
        //Check to see if the user has previously been locked out. If yes, then unset the flag
        //responsible for re-abling login after 2 minutes.
        if($this->_logInAttempts == 2) {
            unset($this->_pageSession->isCountingDownLockOut);
        }
        
        if (isset($this->_pageSession->enquiryId)) {
            $this->_enquiryId = $this->_pageSession->enquiryId;
            $this->_asn = $this->_pageSession->asn;
        } else {
            // Send non-logged in user to login page
            if ($this->url != 'tenants/reference-tracker/login' && $this->_logInAttempts > 0) {
                $this->_helper->redirector->gotoUrl('/tenants/reference-tracker/login');
                return;
            }
        }
    }
    
    /**
     * Main TAT screen (protected by login)
     *
     * @return void
     */
    public function indexAction()
    {
        if ($this->_enquiryId == null) return;
        
        //Fetch user's TAT information.
        $tatManager = new Manager_Referencing_Tat($this->_enquiryId);
        $tat = $tatManager->getTat();
        
        //Set the reference subject name.
        $referenceSubjectName = $tat->referenceSubject->name;
        $this->view->name = $referenceSubjectName->firstName . ' ' . $referenceSubjectName->lastName;
        
        //Set the property lease address.
        $propertyAddress = $tat->propertyLease->address;
        $this->view->address1 = $propertyAddress->addressLine1;
        $this->view->address2 = $propertyAddress->addressLine2;
        $this->view->town = $propertyAddress->town;
        $this->view->postCode = $propertyAddress->postCode;
        
        //Set the status fields.
        $this->view->status             = $tat->enquiryStatus;
        $this->view->primaryIncome      = $tat->currentOccupationReferenceStatus;
        $this->view->additionalIncome   = $tat->secondOccupationReferenceStatus;
        $this->view->futureIncome       = $tat->futureOccupationReferenceStatus;
        $this->view->landlord           = $tat->currentResidentialReferenceStatus;
        
        //Set the missing information.
        $this->view->missingInformation = $tat->missingInformation;

        // Set the ASN
        $this->view->asn = $this->_asn;
    }
    
    /**
     * Login screen
     *
     * @return void
     */
    public function loginAction()
    {
        $this->view->pageTitle = 'Login | HomeLet Tenant Referencing Application Tracker';
        $pageForm = new Form_TenantsReferencingTracker_Login();
        
        $request = $this->getRequest();
        $formData = $request->getPost();
        $pageForm->populate($formData);

        if ($request->isPost()) {

            // If this is a valid IRIS login, store credentials in session and redirect to new IRIS TAT section
            if (Form_TenantsReferencingTracker_Login::IRIS_LOGIN === $pageForm->isValid($formData)) {

                $globalSession = new Zend_Session_Namespace('homelet_global');
                $globalSession->legacy_tat_login = $formData;

                $this->_helper->redirector->gotoUrl('/tenant-application-tracker/login');
                return;
            }

            // Legacy HRT TAT login
            if ($pageForm->isValid($formData)) {
                
                // Log in successful
                // TODO: Use Zend_Auth
                $data = $pageForm->getValues();
                $this->_enquiryId = $this->_pageSession->enquiryId = $data['tenant_reference_number'];
                // Get the ASN from the enquiry number
                // TODO: Requires a manager rather than using direct access to data source
                $enquiryDS = new Datasource_ReferencingLegacy_Enquiry();
                $enquiry = $enquiryDS->getEnquiry($this->_enquiryId);
                $this->_asn = $this->_pageSession->asn = $enquiry->customer->customerId;

                // Set the ASN for the "Get a Quote" links - note these links do not directly go to the TCI+ process since OBC 1051 - Tenancy Liability
                // Put the ASN into the global session for insurance products that lay below the new insurance product selection page to pick up.
                $globalSession = new Zend_Session_Namespace('homelet_global');
                $globalSession->agentSchemeNumber = $this->_asn;

                // Log Activity
                Application_Core_ActivityLogger::log('TAT Login', 'complete', 'TAT', null, "IRN: {$this->_enquiryId}");
                // Redirect user to index page    
                $this->_helper->redirector->gotoUrl('/tenants/reference-tracker');
                return;
            }
            else {
                // Unable to log in, pass form-level errors to view
                $this->view->errors = $pageForm->getErrorMessages();
                // Decrement log in attempt counter
                $this->_logInAttempts = $this->_pageSession->logInAttempts = --$this->_logInAttempts;
                // Log MI event
                Application_Core_ActivityLogger::log('TAT Login', 'failure', 'TAT', null, print_r($this->view->errors, true));
            }
        }
        
        $this->view->form = $pageForm;
    }
    
    /**
     * Call me back screen
     *
     * @return void
     */
    public function callmeAction() {
        
        if ($this->_enquiryId == null) return;
        
        $pageForm = new Form_TenantsReferencingTracker_CallMe();
        
        $request = $this->getRequest();
        $formData = $request->getPost();
        $pageForm->populate($formData);
        
        //Fetch user's TAT information.
        $tatManager = new Manager_Referencing_Tat($this->_enquiryId);
        $tat = $tatManager->getTat();
        
        $tatMailManager = new Manager_Referencing_TatMail($tatManager->_reference);
        
        // Set the reference subject details
        $referenceSubjectName = $tat->referenceSubject->name;
        $this->view->firstName = $referenceSubjectName->firstName;
        $this->view->lastName = $referenceSubjectName->lastName;
        $this->view->asn = $this->_asn;
        
        if ($request->isPost()) {
            // Check if user's going back to the TAT index, or is submitting the form
            if (isset($formData['back'])) {
                // Redirect to index page
                $this->_helper->redirector->gotoUrl('/tenants/reference-tracker');
                return;
            } else {
                if ($pageForm->isValid($formData)) {
                    // Successful set of data, send e-mail and show message to user
                    $data = $pageForm->getValues();
                    $content  = '';
                    $content .= "Name: {$referenceSubjectName->firstName} {$referenceSubjectName->lastName}\r\n\r\n";
                    $content .= "Mobile number: {$data['mobile_number']}\r\n\r\n";
                    $content .= "Landline number: {$data['landline_number']}\r\n\r\n";
                    $content .= "Additional information:\r\n{$data['additional_info']}\r\n\r\n";
                    $content .= "Best time to call: {$data['time_to_call']}\r\n\r\n";
                    $content .= "Agent Scheme Number: {$this->_asn}\r\n\r\n";
                    
                    $tatMailManager->notifyCampaignTeam($content);
                    
                    // Log MI event
                    Application_Core_ActivityLogger::log('TAT Call Me', 'complete', 'TAT', null, "IRN: {$this->_enquiryId}");
                    
                    // Redirect to confirmation page, the redirect prevents
                    //   multiple submissions if user refreshes browser
                    $this->_helper->redirector->gotoUrl('/tenants/reference-tracker/callmesent');
                    return;
                }
            }
        } else {
            // Populate mobile and landline numbers
            $referenceSubjectContactDetails = $tat->referenceSubject->contactDetails;
            $pageForm->landline_number->setValue($referenceSubjectContactDetails->telephone1);
            $pageForm->mobile_number->setValue($referenceSubjectContactDetails->telephone2);
        }
        
        $this->view->form = $pageForm;
    }
    
    /**
     * Send e-mail to HomeLet screen
     *
     * @return void
     */
    public function emailAction()
    {
        if ($this->_enquiryId == null) return;
        
        $pageForm = new Form_TenantsReferencingTracker_Email();
        
        $request = $this->getRequest();
        $formData = $request->getPost();
        $pageForm->populate($formData);
        
        $tatManager = new Manager_Referencing_Tat($this->_enquiryId);
        $tat = $tatManager->getTat();
        
        $tatMailManager = new Manager_Referencing_TatMail($tatManager->_reference);
        
        if ($request->isPost()) {
             
            // Check if user's going back to the TAT index, or is submitting the form
            if (isset($formData['back'])) {
                
                // Redirect to index page
                $this->_helper->redirector->gotoUrl('/tenants/reference-tracker');
                return;
            }
            else {
                
                // Check if this is to add/remove attachments, or is a full submit
                // TODO: It'd be nice to handle this by separating it out into a smooth non-page-refreshing AJAX method
                if (isset($formData['attachButton']) || isset($formData['deleteButton'])) {
                    // Handle attachments
                    if (isset($formData['attachButton'])) {
                        
                        $tatMailManager->addAttachments();
                    }
                    else {
                        
                        $tatMailManager->deleteAttachments();
                    }
                }
                else {
                    if ($pageForm->isValid($formData)) {
                        // Successful set of data, send e-mail and show message to user
                        $data = $pageForm->getValues();
                        $content  = '';
                        $content .= "Enquiry ID: {$this->_enquiryId}\r\n\r\n";
                        $content .= "Name: {$data['name']}\r\n\r\n";
                        $content .= "Contact number or e-mail address: {$data['contact_info']}\r\n\r\n";
                        $content .= "Message to assessor:\r\n{$data['message']}\r\n\r\n";
                        $attachmentInfo = $tatMailManager->detailAttachments();
                        if (count($attachmentInfo) > 0) {
                            $tatMailManager->notifyAssessorWithAttachments($content);
                            $tatMailManager->deleteAttachments();
                        } else {
                            $tatMailManager->notifyAssessor($content);
                        }
                        // Log MI event
                        Application_Core_ActivityLogger::log('TAT Email HomeLet', 'complete', 'TAT', null, "IRN: {$this->_enquiryId}");
                        // Redirect to confirmation page, the redirect prevents
                        //   multiple submissions if user refreshes browser
                        $this->_helper->redirector->gotoUrl('/tenants/reference-tracker/emailsent');
                        return;
                    }
                }
            }
        }
        
        $this->view->reference = $tatManager->_reference;
        $this->view->form = $pageForm;
    }
    
    /**
     * View e-mails from HomeLet screen
     *
     * @return void
     */
    public function viewemailsAction()
    {
        if ($this->_enquiryId == null) return;
    }
    
    /**
     * Too many login attempts screen
     * @todo: Info-only screen can be condensed into one action
     *
     * @return void
     */
    public function toomanyattemptsAction() {
        
        $this->_pageSession = new Zend_Session_Namespace('tenants_referencing_tracker');
        if(!isset($this->_pageSession->isCountingDownLockOut)) {
            
            //Set the lock-out duration so that the user can login after a parameterized amount
            //of time.
            $params = Zend_Registry::get('params');
            $lockoutDuration = $params->tat->lockoutDuration;
            
            $this->_pageSession->isCountingDownLockOut = true;
            $this->_pageSession->setExpirationSeconds($lockoutDuration, 'logInAttempts');
        }
        
        if ($this->_enquiryId == null) return;
    }
    
    /**
     * Email sent to HomeLet screen
     * @todo: Info-only screen can be condensed into one action
     *
     * @return void
     */
    public function emailsentAction() {
        if ($this->_enquiryId == null) return;
    }
    
    /**
     * Call back request successful screen
     * @todo: Info-only screen can be condensed into one action
     *
     * @return void
     */
    public function callmesentAction() {
        if ($this->_enquiryId == null) return;
    }

}
