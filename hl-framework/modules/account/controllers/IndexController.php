<?php
// Provides account functionality for HomeLet customers

class Account_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        Zend_Layout::startMvc();
        // Use the CMS layout
        Zend_Layout::getMvcInstance()->setLayoutPath( APPLICATION_PATH . '/modules/cms/layouts/scripts/' );
        $this->view->headLink()->setStylesheet('/assets/account/css/account.css');
        $this->view->pageTitle = 'Sign in to My HomeLet';
        $this->url = trim($this->getRequest()->getRequestUri(),'/');

        $menuData = array(
            'selected'  => null,
            'url'       => $this->url
        );

        // This could be quite yucky - I'm just trying to work out how to get the menu structure to work
        $mainMenu = $this->view->partial('partials/homelet-mainmenu.phtml', $menuData);
        $subMenu = $this->view->partial('partials/homelet-submenu.phtml', $menuData);
        $layout = Zend_Layout::getMvcInstance();
        $layout->getView()->mainMenu = $mainMenu;
        $layout->getView()->subMenu = $subMenu;
        if (isset($menuData['selected'])) {
            $layout->getView()->styling = $menuData['selected'];
        }

        // Load the site link urls from the parameters and push them into the layout
        $params = Zend_Registry::get('params');
        $layout->getView()->urls = $params->url->toArray();
    }

    /**
     * Logout of customer account
     *
     * @return void
     */
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));
        $auth->clearIdentity();

        // Clear any session data as well
        Zend_Session::namespaceUnset('homelet_global');
        Zend_Session::namespaceUnset('tenants_insurance_quote');
        Zend_Session::namespaceUnset('landlords_insurance_quote');
        Zend_Session::namespaceUnset('referencing_global');
        Zend_Session::namespaceUnset('account_logged_in');

        // If there's a "redirect" URL param then redirect to it
        if ($this->getRequest()->getParam('redirect')) {
            return $this->_redirect($this->getRequest()->getParam('redirect'));
        }
        else {
            // Redirect to the login screen.
            $this->_helper->redirector->gotoUrl('/my-homelet/login?message=logout');
        }
    }

    /**
     * Forgot password (password reset) action
     *
     * @return void
     */
    public function forgotPasswordAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('layout')->disableLayout();
        }

        $form = new Account_Form_ForgotPassword();
        $form->setAction('/my-homelet/forgotpassword');

        if ($this->getRequest()->isPost()) {

            if ($form->isValid($this->getRequest()->getPost())) {

                // User has forgotten password
                $customerManager = new Manager_Core_Customer();
                $customer = $customerManager->getCustomerByEmailAddress($form->email->getValue());

                if ($customer) {

                    $customer->resetPassword();
                    $customerManager->updateCustomer($customer);
                    $newPassword = $customer->getPassword();
                    $customerID = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);

                    // Now we have a new customer password - we also (sadly) need to update ALL the linked legacy customer entries
                    // or next time an old quote is opened it over-writes this new password (you couldn't make this stuff up!)
                    $legacyCustomerMap = new Datasource_Core_CustomerMaps();
                    $legacyIDs = $legacyCustomerMap->getLegacyIDs($customerID);

                    foreach ($legacyIDs as $legacyID) {

                        $oldCustomer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $legacyID);
                        $oldCustomer->setPassword($newPassword);
                        $customerManager->updateCustomer($oldCustomer);
                    }

                    // That's hopefully done it so we can show a nice message
                    $form->setDescription("Thank you, we have sent a new password to your email address.");
                }
                else {
                    $form->setDescription("Sorry, we could not find a customer with that email address. Please check the details you entered are correct and try again");
                }

            }
        }

        $this->view->form = $form;
    }

    /**
     * Register action
     *
     * @return void
     */
    public function registerAction()
    {
        $this->_setBreadcrumbs(array(
                '/' => 'Home',
                '/my-homelet' => 'My HomeLet',
                '/my-homelet/register' => 'Register',
            ));

        $params = Zend_Registry::get('params');
        $form = new Account_Form_Register();

        // Populate the form with the security question options
        $securityQuestionModel = new Datasource_Core_SecurityQuestion();
        $securityQuestionOptions = $securityQuestionModel->getOptions();

        foreach ($securityQuestionOptions as $option) {
            $form->security_question->addMultiOption($option['id'], $option['question']);
        }

        if ($this->getRequest()->isPost()) {

            if ($form->isValid($this->getRequest()->getPost())) {
                $customermgr = new Manager_Core_Customer();

                // Detect if the customer has already registered with this email address
                $customer = $customermgr->getCustomerByEmailAddress($form->email->getValue());

                if ($customer) {

                    // Customer already exists, flag form in error
                    // Ideally this should go in the form as an overridden validation method, but this would
                    // tightly couple the form to the customer manager anyway, which itself is bad.
                    // Alternatively I could inject the found customer object into the form, but then this doesn't change
                    // much to using the code here anyway.
                    $form->email->addError('This email is already in use. Have you signed up before?')->markAsError();
                }
                else {
                    // Create customer. Because this is the generic registration page, we use  a generic customer type
                    $customer = $customermgr->createNewCustomer($form->email->getValue(), Model_Core_Customer::CUSTOMER);

                    // Update customer with password and security data
                    $customer->setTitle($form->title->getValue());
                    $customer->setFirstName($form->first_name->getValue());
                    $customer->setLastName($form->last_name->getValue());
                    $customer->setSecurityQuestion($form->security_question->getValue());
                    $customer->setSecurityAnswer($form->security_answer->getValue());
                    $customer->setPassword($form->password->getValue());
                    $customer->setAccountLoadComplete(true);
                    $customer->typeID = Model_Core_Customer::CUSTOMER;
                    $customermgr->updateCustomer($customer);

                    // Create sign-up completion email
                    $customer->sendAccountValidationEmail();

                    // Forward request to registration confirmation page
                    $this->_helper->redirector->gotoUrl('/my-homelet/registration-awaiting-confirmation');
                }
            }
        }

        $this->view->form = $form;
    }

    public function registrationAwaitingConfirmationAction()
    {
        // Nothing to see here

        $this->_setBreadcrumbs(array(
                '/' => 'Home',
                '/my-homelet' => 'My HomeLet',
                '/my-homelet/registration-awaiting-confirmation' => 'Awaiting Confirmation',
            ));
    }

    public function activateAccountAction()
    {
        $params = Zend_Registry::get('params');

        // Validate mac digest from client
        $mac = new Application_Core_Security($params->myhomelet->activation_mac_secret, false);
        $digest = $mac->generate(array('email' => $_GET['email']));

        if ($digest != $_GET['mac']) {
            // Render error page if invalid mac
            $this->render('activate-account-invalidmac');
            return;
        }

        // Check if the account has already been activated
        $customerManager = new Manager_Core_Customer();
        $customer = $customerManager->getCustomerByEmailAddress($_GET['email']);

        if ($customer->getEmailValidated() === true) {
            // Customer has already validated, display already validated notification page
            $this->_helper->redirector->gotoUrl('/my-homelet/login?message=account-already-validated');
            return;
        }

        // Digests match and customer has yet to validate, activate the account
        $customer->setEmailValidated(true);
        $customerManager->updateCustomer($customer);

        // Create welcome email
        $mail = new Application_Core_Mail();
        $mail->setTo($_GET['email'], null);
        $mail->setFrom('hello@homelet.co.uk', 'HomeLet');
        $mail->setSubject('Registration for My HomeLet');

        // Apply template
        $mail->applyTemplate('core/account-welcome',
            array(
                'homeletWebsite' => $params->homelet->domain,
                'templateId'     => 'HL2443 12-12',
                'firstname'      => $customer->getFirstName(),
                'heading'        => 'Your registration for My HomeLet is complete!',
                'imageBaseUrl' => $params->weblead->mailer->imageBaseUrl,
            ),
            false,
            '/email-branding/homelet/portal-footer.phtml',
            '/email-branding/homelet/portal-header.phtml');

        $mail->applyTextTemplate('core/account-welcometxt',
            array('homeletWebsite' => $params->homelet->domain,
                'templateId'     => 'HL2443 12-12',
                'firstname'      => $customer->getFirstName(),
                'heading'        => 'Your registration for My HomeLet is complete!'),
            false,
            '/email-branding/homelet/portal-footer-txt.phtml',
            '/email-branding/homelet/portal-header-txt.phtml');

        // Send email
        $mail->send();

        // Find all customers in mysql4 insurance that have the same email address
        $legacyCustomers = $customerManager->getAllLegacyCustomersByEmailAddress($_GET['email']);
        $customerIdentifier = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);

        foreach ($legacyCustomers as $legacyCustomer) {
            // For each customer found, insert a record into the mysql5 customer_legacy_customer_map table
            $legacyIdentifier = $legacyCustomer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER);
            $customerMap = new Datasource_Core_CustomerMaps();
            if ( ! $customerMap->getMap(Model_Core_Customer::LEGACY_IDENTIFIER,$legacyIdentifier)) {
                $customerManager->linkLegacyToNew($legacyIdentifier, $customerIdentifier);
            }
        }

        $this->_helper->redirector->gotoUrl('/my-homelet/login?message=registration-complete');
    }

    public function myHomeletAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        if ($auth->hasIdentity()) {
            // Logged in, forward to My Quotes
            $this->_helper->redirector->gotoUrl('/my-homelet/quotes');
        }
        else {
            // Not logged in, forward to login page
            $this->_helper->redirector->gotoUrl('/my-homelet/login');
        }
    }

    /**
     * Login to add another quote to an existing customer account
     *
     * @return mixed
     */
    public function loginAction()
    {
        $this->_setBreadcrumbs(array(
                '/' => 'Home',
                '/my-homelet' => 'My HomeLet',
                '/my-homelet/login' => 'Registration & Login',
            ));

        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        $loginForm = new Account_Form_Login();
        $loginForm->submit->setLabel('Login');

        $request = $this->getRequest();
        $params = $request->getParams();

        $stepNum = $request->getParam('step');
        $referrer = $request->getParam('refer');
        $message = $request->getParam('message');
        $statusMessage = '';
        $referrerUrl = $request->getParam('referrerUrl');

        if ($this->getRequest()->isPost()) {
            if (isset($params['resendValidation'])) {
                // User wants a new validation link
                $customerManager = new Manager_Core_Customer();
                $customer = $customerManager->getCustomerByEmailAddress($params['email']);

                if ($customer) {
                    $customer->sendAccountValidationEmail();
                    $loginForm->setDescription('Thank you, we have sent a new account validation link to your email address.');
                }
                else {
                    $loginForm->setDescription('Sorry, we could not find a customer with that email address. Please check the details you entered are correct and try again');
                }
            }
            elseif (isset($params['forgottenPassword'])) {
                // User has forgotten password
                $customerManager = new Manager_Core_Customer();
                $customer = $customerManager->getCustomerByEmailAddress($params['email']);

                if ($customer) {
                    $customer->resetPassword();
                    $customerManager->updateCustomer($customer);
                    $newPassword = $customer->getPassword();
                    $customerID = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);

                    // Now we have a new customer password - we also (sadly) need to update ALL the linked legacy customer entries
                    // or next time an old quote is opened it over-writes this new password (you couldn't make this stuff up!)
                    $legacyCustomerMap = new Datasource_Core_CustomerMaps();
                    $legacyIDs = $legacyCustomerMap->getLegacyIDs($customerID);

                    foreach ($legacyIDs as $legacyID) {
                        $oldCustomer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $legacyID);
                        $oldCustomer->setPassword($newPassword);
                        $customerManager->updateLegacyCustomer($oldCustomer);
                    }

                    // That's hopefully done it so we can show a nice message
                    $loginForm->setDescription("Thank you, we have sent a new password to your email address.");
                }
                else {
                    $loginForm->setDescription("Sorry, we could not find a customer with that email address. Please check the details you entered are correct and try again");
                }
            }
            elseif ($loginForm->isValid($_POST)) {
                // Values are valid - attempt a customer login
                // The forms passed validation so we now need to check the identity of the user
                $customerManager = new Manager_Core_Customer();
                $adapter = $customerManager->getAuthAdapter($loginForm->getValues());
                $result = $auth->authenticate($adapter);

                if ( ! $result->isValid()) {
                    // Invalid credentials
                    $loginForm->setDescription('Sorry, those login details seem to be incorrect');
                }
                else {
                    $storage = $auth->getStorage();
                    $storage->write($adapter->getResultRowObject(array(
                                'title',
                                'first_name',
                                'last_name',
                                'email_address',
                                'id')));

                    // Check the customer has validated their account
                    // Must be done after successful authentication to protect against unauthorised data exposure
                    $customer = $customerManager->getCustomerByEmailAddress($_POST['email']);

                    if ($customer->getEmailValidated() !== true) {
                        $auth->clearIdentity(); // Clear authentication performed to prevent login

                        // Customer has not validated their user account. Put the form in to an error status
                        // For some reason, this login form must use the form description to pass errors
                        // through to the view.
                        $loginForm
                            ->setDescription('Hello, at the moment you\'re unable to access My HomeLet  because you haven\'t validated your email address yet. We\'ve sent you an email which includes a link to confirm your email address and validate your My HomeLet account. If you\'ve not received your validation email or if you\'re unable to access your account, please call us on 0845 117 6000 - we\'re always happy to help!')
                            ->markAsError();
                    }
                    else {
                        // Valid credentials - store the details we need from the database and move the user to the index page
                        if ($stepNum) {
                            // We were sent here from a quote step - so we need to link that quote to the newly logged in customer and redirect
                            if ($referrer !='') {
                                if ($referrer == 'tenants-insurance') {
                                    $customerManager = new Manager_Core_Customer();
                                    $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
                                    $legacyCustomerReference = $pageSession->CustomerRefNo;

                                    // This will create a customer record as we don't currently have one (only a legacy one)
                                    $customerManager->linkLegacyToNew($legacyCustomerReference, $auth->getStorage()->read()->id, Model_Core_Customer::CUSTOMER);

                                    $this->_helper->redirector->gotoUrl('/tenants/insurance-quote/step' . $stepNum);
                                }
                                elseif ($referrer == 'landlords-insurance') {
                                    $customerManager = new Manager_Core_Customer();
                                    $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
                                    $legacyCustomerReference = $pageSession->customerRefNo;

                                    // This will create a customer record as we don't currently have one (only a legacy one)
                                    $customerManager->linkLegacyToNew($legacyCustomerReference, $auth->getStorage()->read()->id, Model_Core_Customer::CUSTOMER);

                                    $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/step' . $stepNum);
                                }
                            }
                        }

                        // Set the customer id in the referencing session. This allows referencing to continue working
                        // with the new customer table
                        $referencing_session = new Zend_Session_Namespace('referencing_global');
                        $referencing_session->customerId = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);

                        // Simple session to track that an account logged in, but this does not expire with the real log in details, it's only cleared on logout or actual session close - this is used to accurately know when the real session has expired and to say so.
                        $account_session = new Zend_Session_Namespace('account_logged_in');
                        $account_session->loggedIn = true; // This is the only parameter it ever sets

                        $referrerUrl = $loginForm->getElement('referrerUrl')->getValue();
                        if ($referrerUrl != '') {
                            $this->_helper->redirector->gotoUrl($referrerUrl);
                        }
                        else {
                            $this->_helper->redirector->gotoUrl('/my-homelet');
                            return;
                        }
                    }
                }
            }
        }
        else {
            // Validate the referer url is relative to the current server
            if (preg_match('/\/*/', $referrerUrl)) {
                $loginForm->getElement('referrerUrl')->setValue($referrerUrl);
            }

            if ($message == 'session-expired') {
                $this->getResponse()->setHttpResponseCode(403); // Required to allow ajax to detect session expiration
            }
            $statusMessage = "";
            if ($message != '') {
                $statusMessage = $message;
            }
        }

        $systemParams = Zend_Registry::get('params');

        $this->view->connectRootUrl = $systemParams->connectUrl->connectRootUrl;
        $this->view->message = $statusMessage;
        $this->view->stepNum = $stepNum;
        $this->view->ref = $referrer;
        $this->view->form = $loginForm;
    }

    /**
     * View quote history
     *
     * @return void
     */
    public function historyAction()
    {
        // Redirect any request for the old quotes history page on to the new My HomeLet - My Quote page.
        $this->_helper->redirector->gotoUrl('/my-homelet/quotes');
    }

    /**
     * Register action
     *
     * @return void
     */
    public function partialRegistrationAction()
    {
        $this->_setBreadcrumbs(array(
                '/' => 'Home',
                '/my-homelet' => 'My HomeLet',
                '/my-homelet/partial-registration' => 'Continue Registration',
            ));

        $params = Zend_Registry::get('params');
        $form = new Account_Form_Register();

        // Populate the form with the security question options
        $securityQuestionModel = new Datasource_Core_SecurityQuestion();
        $securityQuestionOptions = $securityQuestionModel->getOptions();

        foreach ($securityQuestionOptions as $option) {
            $form->security_question->addMultiOption($option['id'], $option['question']);
        }

        $customerManager = new Manager_Core_Customer();
        if ( ! $this->getRequest()->isPost()) {
            $refno = $_GET['refno'];
            $email = $_GET['email'];
            $mac = new Application_Core_Security($params->myhomelet->activation_mac_secret, false);
            $digest = $mac->generate(array('email' =>$email));

            if ($refno) {
                // Try by legacy customer refno
                $customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $refno);
            }
            else {
                // Try by email
                $customer = $customerManager->getCustomerByEmailAddress($email);
            }

            $formData = array();
            $formData['title'] = $customer->getTitle();
            $formData['first_name'] = $customer->getFirstName();
            $formData['last_name']  = $customer->getLastName();
            $formData['email'] = $email;
            $formData['refno'] = $refno;
            #$form->title->setAttrib('readonly','readonly');
            #$form->first_name->setAttrib('readonly','readonly');
            #$form->last_name->setAttrib('readonly','readonly');
            $form->email->setAttrib('readonly','readonly');
            $form->populate($formData);

            if ($digest != $_GET['mac']) {
                // Render error page if invalid mac
                $this->render('activate-account-invalidmac');
                return;
            }
        }
        else {
            if ($form->isValid($this->getRequest()->getPost())) {
                // Detect if the customer has already registered with this email address
                $customer = $customerManager->getCustomerByEmailAddress($form->email->getValue());

                if ($customer) {
                    // Customer already exists, flag form in error
                    // Ideally this should go in the form as an overridden validation method, but this would
                    // tightly couple the form to the customer manager anyway, which itself is bad.
                    // Alternatively I could inject the found customer object into the form, but then this doesn't change
                    // much to using the code here anyway.
                    $form->email->addError('This email is already in use. Have you signed up before?')->markAsError();
                }
                else {
                    // Create customer. Because this is the generic registration page, we use a generic customer type
                    $customer = $customerManager->createCustomerFromLegacy($form->email->getValue(), $form->refno->getValue());
                    $custID = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);
                    $leg = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER,$form->refno->getValue());
                    // Update customer with password and security data

                    $customerManager->updateCustomerByLegacy($leg, $custID);
                    $customer = $customerManager->getCustomer(Model_Core_Customer::IDENTIFIER, $custID);
                    $customer->setSecurityQuestion($form->security_question->getValue());
                    $customer->setSecurityAnswer($form->security_answer->getValue());

                    $customer->setPassword($form->password->getValue());

                    $customer->setEmailValidated(true);
                    $customerManager->updateCustomer($customer);

                    // Create welcome email
                    $mail = new Application_Core_Mail();
                    $mail->setTo($_GET['email'], null);
                    $mail->setFrom('hello@homelet.co.uk', 'HomeLet');
                    $mail->setSubject('Registration for My HomeLet');

                    // Apply template
                    $mail->applyTemplate('core/account-welcome',
                        array(
                            'homeletWebsite' => $params->homelet->domain,
                            'templateId'     => 'HL2443 12-12',
                            'firstname'      => $customer->getFirstName(),
                            'heading'        => 'Your registration for My HomeLet is complete!',
                            'imageBaseUrl' => $params->weblead->mailer->imageBaseUrl,
                        ),
                        false,
                        '/email-branding/homelet/portal-footer.phtml',
                        '/email-branding/homelet/portal-header.phtml');

                    $mail->applyTextTemplate('core/account-welcometxt',
                        array('homeletWebsite' => $params->homelet->domain,
                            'templateId'     => 'HL2443 12-12',
                            'firstname'      => $customer->getFirstName(),
                            'heading'        => 'Your registration for My HomeLet is complete!'),
                        false,
                        '/email-branding/homelet/portal-footer-txt.phtml',
                        '/email-branding/homelet/portal-header-txt.phtml');

                    // Send email
                    $mail->send();

                    // Find all customers in mysql4 insurance that have the same email address
                    $legacyCustomers = $customerManager->getAllLegacyCustomersByEmailAddress($_GET['email']);
                    $customerIdentifier = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);

                    foreach ($legacyCustomers as $legacyCustomer) {
                        // For each customer found, insert a record into the mysql5 customer_legacy_customer_map table
                        $legacyIdentifier = $legacyCustomer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER);
                        $customerMap = new Datasource_Core_CustomerMaps();

                        if ( ! $customerMap->getMap(Model_Core_Customer::LEGACY_IDENTIFIER,$legacyIdentifier)) {
                            $customerManager->linkLegacyToNew($legacyIdentifier, $customerIdentifier);
                        }
                    }

                    $this->_helper->redirector->gotoUrl('/my-homelet/login?message=registration-complete');
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * Retrieve TCI+ or LI+ quote.
     */
    public function retrieveQuoteAction()
    {
        $this->_setBreadcrumbs(array(
                '/' => 'Home',
                '/my-homelet' => 'My HomeLet',
                '/my-homelet/retrieve-quote' => 'Retrieve Quote',
            ));

        $params = Zend_Registry::get('params');
        $form = new Account_Form_RetrieveQuote();

        $quoteManager = new Manager_Insurance_LegacyQuote();
        $customerManager = new Manager_Core_Customer();

        // If there's a quote number in the GET vars then sanitise and uppercase it, and place it in the form

        if (isset($_GET['number'])) {

            // Sanitise and uppercase supplied quote number, place in form
            $quoteNumber = strtoupper(
                preg_replace('/[^\w\/]/', '', $_GET['number'])
            );
            $form->quote_number->setValue($quoteNumber);

            // Also pre-populate the form with first name and last name if the quote number is valid
            $quote = $quoteManager->getQuoteByPolicyNumber($quoteNumber);

            if ($quote) {
                // Get customer details from quote refNo
                $quoteRefNo = $quote->refNo;

                $customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $quoteRefNo);

                if ($customer) {
                    $form->first_name->setValue($customer->getFirstName());
                    $form->last_name->setValue($customer->getLastName());
                }
            }
        }

        // Get email address, place in form if it looks valid
        if (isset($_GET['email'])) {

            $getEmail = $_GET['email'];
            $emailValidator = new Zend_Validate_EmailAddress();

            if ($emailValidator->isValid($getEmail)) {
                $form->email->setValue($getEmail);
            }

        }

        $request = $this->getRequest();
        $postData = $request->getPost();

        // Handle retrieve attempts
        if ($request->isPost()) {

            if ($form->isValid($postData)) {

                // Are we looking up by quote number or by e-mail address?  If a quote number is present it takes
                // precedence
                $quotes = array();
                $customer = null;
                $quoteNumber = $form->quote_number->getValue();
                $email = $form->email->getValue();

                if ('' != $quoteNumber) {

                    $quote = $quoteManager->getQuoteByPolicyNumber($quoteNumber);

                    if ($quote) {
                        // Look up customer from quote retrieved
                        $quoteRefNo = $quote->refNo;
                        $customer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $quoteRefNo);

                        $quotes = array($quote);
                    }

                }
                else {

                    // Get all legacy quote IDs by customer e-mail address
                    $legacyIDs = array();
                    // Try to look up a customer record's quotes' IDs by the e-mail provided
                    $newCustomer = $customerManager->getCustomerByEmailAddress($email);
                    if ($newCustomer) {
                        $legacyCustomerMap = new Datasource_Core_CustomerMaps();
                        $legacyIDs = $legacyCustomerMap->getLegacyIDs($newCustomer->getIdentifier(Model_Core_Customer::IDENTIFIER));
                    }

                    // Also check in the legacy DB only to ensure landlords quotes are found
                    $customer = $customerManager->getLegacyCustomerByEmailAddress($email);
                    if ($customer)  {
                        $legacyCustomerId = $customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER);
                        if ( ! in_array($legacyCustomerId, $legacyIDs)) {
                            $legacyIDs[] = $legacyCustomerId;
                        }
                    }

                    // Retrieve all quotes for the linked customer reference numbers
                    $quoteDatasource = new Datasource_Insurance_LegacyQuotes();
                    $quotes = $quoteDatasource->getActiveQuotes($legacyIDs, '', array('policynumber', 'startdate'));
                }

                // Do we have at least one quote and the customer details
                if (count($quotes) > 0 && $customer) {

                    // Check that the security requirements are met (matching email, first name, last name, postcode and
                    // DOB)
                    if (
                        trim($customer->getEmailAddress()) == trim($form->email->getValue()) &&
                        Application_Core_Utilities::simplifiedStringCompare(
                            $customer->getFirstName(),
                            $form->first_name->getValue()
                        ) &&
                        Application_Core_Utilities::simplifiedStringCompare(
                            $customer->getLastName(),
                            $form->last_name->getValue()
                        ) &&
                        Application_Core_Utilities::simplifiedStringCompare(
                            $customer->getPostCode(),
                            $form->cor_postcode->getValue()
                        ) &&
                        Application_Core_Utilities::mysqlDateToUk($customer->getDateOfBirthAt()) == trim($form->date_of_birth_at->getValue())
                    ) {

                        // If this is a single quote then generate an auth token and bounce them on
                        if (count($quotes) == 1) {

                            // Ensure there's a quote number to use in the security token (because there won't be one if
                            // it was a single match based only on an e-mail address)
                            if ('' == $quoteNumber) {
                                $quoteNumber = $quotes[0]->policyNumber;
                            }

                            // Generate an authentication token for a single policy
                            $securityManager = new Application_Core_Security(
                                $params->myhomelet->retrieveWithoutAccount->macSecret,
                                ($params->myhomelet->retrieveWithoutAccount->macTimestampVariance != 0),
                                $params->myhomelet->retrieveWithoutAccount->macTimestampVariance
                            );
                            $securityData = array(
                                'quoteNumber' => $quoteNumber
                            );
                            $authToken = $securityManager->generate($securityData);

                            // Bounce to the right Q&B depending on quote type
                            if ($quotes[0]->getProductName() == 'tenants') {
                                $this->_helper->redirector->gotoUrl('/tenants/insurance-quote/retrieve?auth=' . $authToken);
                                return;
                            }
                            elseif ($quotes[0]->getProductName() == 'landlords') {
                                $this->_helper->redirector->gotoUrl('/landlords/insurance-quote/retrieve?auth=' . $authToken);
                                return;
                            }
                            else {
                                $form->setDescription('Sorry, we don\'t yet allow resuming the type of quote you have - please call us.');
                            }
                        }

                        // If customer has multiple quotes then bounce user to the selection action

                        else {

                            // Generate an authentication token for the customer email
                            $securityManager = new Application_Core_Security(
                                $params->myhomelet->retrieveWithoutAccount->macSecret,
                                ($params->myhomelet->retrieveWithoutAccount->macTimestampVariance != 0),
                                $params->myhomelet->retrieveWithoutAccount->macTimestampVariance
                            );
                            $securityData = array(
                                'customerEmail' => $email
                            );
                            $authToken = $securityManager->generate($securityData);

                            $this->_helper->redirector->gotoUrl('/my-homelet/retrieve-multiple-quotes?auth=' . $authToken);
                            return;

                        }

                    }
                    else {
                        // Security check failed, show error
                        $form->setDescription('Sorry, we could not find your quote from the details provided - please check them and try again.');
                    }

                }
                else {
                    // Lookup failed, show error
                    $form->setDescription('Sorry, we could not find your quote from the details provided - please check them and try again.');
                }

            }
        }

        $this->view->form = $form;
    }

    /**
     * Display a list of quotes that can be retrieved after a user has identified themselves with an auth token via
     * retrieveQuoteAction().  Having this separate action allows an end user to press "Back" for up to an hour if they
     * have chosen to continue the wrong quote.
     *
     * @return void
     */
    public function retrieveMultipleQuotesAction()
    {
        if ($this->getRequest()->getParam('auth') != '') {
            $params = Zend_Registry::get('params');

            $mac = $this->getRequest()->getParam('auth');

            $securityManager = new Application_Core_Security(
                $params->myhomelet->retrieveWithoutAccount->macSecret,
                ($params->myhomelet->retrieveWithoutAccount->macTimestampVariance != 0),
                $params->myhomelet->retrieveWithoutAccount->macTimestampVariance
            );

            $dataKeys = array(
                'customerEmail'
            );

            $securityCheck = $securityManager->authenticate($mac, $dataKeys);

            if (isset($securityCheck['result']) && $securityCheck['result']) {

                // Customer has multiple quotes associated with their email address - look them up and generate a set of
                // auth tokens, show user the selection

                $email = $securityCheck['data']['customerEmail'];

                $policyCoverDatasource = new Datasource_Insurance_LegacyPolicyCovers();
                $customerManager = new Manager_Core_Customer();

                // Get all legacy quote IDs by customer e-mail address
                $legacyIDs = array();
                // Try to look up a customer record's quotes' IDs by the e-mail provided
                $newCustomer = $customerManager->getCustomerByEmailAddress($email);
                if ($newCustomer) {
                    $legacyCustomerMap = new Datasource_Core_CustomerMaps();
                    $legacyIDs = $legacyCustomerMap->getLegacyIDs($newCustomer->getIdentifier(Model_Core_Customer::IDENTIFIER));
                }

                // Also check in the legacy DB only to ensure landlords quotes are found
                $customer = $customerManager->getLegacyCustomerByEmailAddress($email);
                if ($customer)  {
                    $legacyCustomerId = $customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER);
                    if ( ! in_array($legacyCustomerId, $legacyIDs)) {
                        $legacyIDs[] = $legacyCustomerId;
                    }
                }

                // Retrieve all quotes for the linked customer reference numbers
                $quoteDatasource = new Datasource_Insurance_LegacyQuotes();
                $quotes = $quoteDatasource->getActiveQuotes($legacyIDs, '', array('policynumber', 'startdate'));

                // Build the list of policy covers and generate auth tokens for each policy
                // Should be done in a manager, but the quote manager has been written with the row data gateway
                // design pattern in mind.
                $authTokens = array();
                foreach ($quotes as $quote) {
                    // Create list of policy covers
                    $policyCoverList = array();
                    $policyOptionsplit = explode('|', $quote->policyOptions);
                    $sumInsuredSplit = explode('|', $quote->amountsCovered);

                    for ($i = 0; $i < count($policyOptionsplit); $i++) {
                        if ($sumInsuredSplit[$i] == 'yes' || floatval($sumInsuredSplit[$i]) > 0) {
                            // A sum insured value has been set so assume cover is in force
                            $policyCover = $policyCoverDatasource->getPolicyCoverByLabel($policyOptionsplit[$i]);

                            if ($policyCover) {
                                array_push($policyCoverList, array('cover' => $policyOptionsplit[$i], 'name' => $policyCover->getName()));
                            }
                        }
                    }

                    $quote->policyCovers = $policyCoverList;

                    // Generate a policy-specific authentication token
                    $securityManager = new Application_Core_Security(
                        $params->myhomelet->retrieveWithoutAccount->macSecret,
                        ($params->myhomelet->retrieveWithoutAccount->macTimestampVariance != 0),
                        $params->myhomelet->retrieveWithoutAccount->macTimestampVariance
                    );
                    $securityData = array(
                        'quoteNumber' => $quote->policyNumber
                    );
                    $authTokens[$quote->policyNumber] = $securityManager->generate($securityData);
                }

                // Pass quotes and auth tokens into view and finish
                $this->view->quotes = $quotes;
                $this->view->authTokens = $authTokens;

                return;

            }
        }

        // Failover for non-auth or other issue - go to main retrieve quote form
        $this->_helper->redirector->gotoUrl('/my-homelet/retrieve-quote');
    }

    /**
     * Exportable login status - generates a complete HTML5 mini-page for
     * embedding in a remote or a local iframe.  Note that URL to this action is
     * set in application/routes/config/account.ini as /my-homelet/login-status
     *
     * @return void
     */
    public function exportableLoginStatusAction()
    {
        // No layout desired, outputs a complete HTML5 page.
        $this->_helper->getHelper('layout')->disableLayout();

        // Ensure cross-site origin is allowed
        //header('Access-Control-Allow-Origin: *');

        // Pass in the base URL to the view so links are absolute to the HLF
        $params = Zend_Registry::get('params');
        $this->view->hlfBaseUrl = $params->homelet->domain;

        // Check if user logged in
        $this->view->loggedIn = false;
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        if ($auth->hasIdentity()) {
            // Logged in
            $this->view->loggedIn = true;

            // Get customer's name

            // Get the customer session
            $customerSession = $auth->getStorage()->read();

            // Retrieve the customer record
            $customermgr = new Manager_Core_Customer();
            $customer = $customermgr->getCustomer(Model_Core_Customer::IDENTIFIER, $customerSession->id);

            $this->view->firstName = htmlentities($customer->getFirstName());
        }
    }

    /**
     * Set partial breadcrumbs
     *
     * @param array $breadcrumbs
     */
    private function _setBreadcrumbs(array $breadcrumbs)
    {
        $this->view->breadcrumbs = $this->view->partial('partials/portal-breadcrumbs.phtml', array(
                'breadcrumbs' => $breadcrumbs,
            ));
    }
}
