<?php

require_once('ConnectAbstractController.php');
class Connect_SettingsController extends ConnectAbstractController {

    private $_agentManager;
    private $_userManager;
    private $_extNewsManager;

    private $_functionAccess = array();

    public function init()
    {
        parent::init();

        // Include the settings CSS
        $this->view->headLink()->appendStylesheet('/assets/connect/css/settings.css');

        // Instantiate the agency-level manager, user manager and the external news manager
        $this->_agentManager =  new Manager_Core_Agent($this->_agentSchemeNumber);
        $this->_userManager =   new Manager_Core_Agent_User($this->_agentId);
        $this->_extNewsManager =    new Manager_Cms_ExternalNews();

        // Set any common parameters to pass to the view
        $this->view->userRole = Model_Core_Agent_UserRole::toString($this->_level);

        // Set up function level access depending on user level
        switch ($this->_level) {
            case Model_Core_Agent_UserRole::MASTER:
                $this->_functionAccess = array(
                    'emailAddresses'        => true,
                    'uploadConnectLogo'     => true,
                    'uploadDocumentLogo'    => true,
                    'newsTickerSettings'    => true,
                    'userAdministration'    => true
                );
                break;
            default:
                $this->_functionAccess = array(
                    'emailAddresses'        => false,
                    'uploadConnectLogo'     => false,
                    'uploadDocumentLogo'    => false,
                    // Allowed but there's further granularity:
                    'newsTickerSettings'    => true,
                    'userAdministration'    => false
                );
                break;
        }

        // This is passed to view
        $this->view->functionAccess = $this->_functionAccess;
    }

    public function indexAction() {

        // Fetch request so private methods can check if there's been a POST
        $request = $this->getRequest();

        // Call private methods to handle various forms
        $this->view->formEmailAddresses = $this->_handleEmailAddresses($request);
        $this->view->formMyAccount = $this->_handleMyAccount($request);
        $this->_handleNewsTickerSettings($request);
        $this->_handleLogo($request);
        $this->_handleUserAccounts($request);

        $this->view->connectLogoUrl = $this->_params->connect->settings->logoUpload->path->url . $this->_agentObj->logo;
        $this->view->documentLogoUrl = $this->_params->connect->settings->logoUpload->path->url . $this->_agentObj->documentLogo;
    }

    /**
     * Handle "e-mail addresses" section changes and data population
     */
    private function _handleEmailAddresses($request) {

        // Only available to master users
        if ($this->_level == Model_Core_Agent_UserRole::MASTER) {
            // Instantiate form definition
            $pageFormEmailAddresses = new Connect_Form_SettingsEmailAddresses();

            if ($request->isPost() && isset($_POST['updateEmailAddresses'])) {
                if ($pageFormEmailAddresses->isValid($request->getPost())) {

                    // Save addresses
                    $emailMapArray = array();
                    foreach(Model_Core_Agent_EmailMapCategory::iterableKeys() as $categoryConstName => $val) {
                        $emailMap = new Model_Core_Agent_EmailMap();
                        $emailMap->category = $val;
                        $emailMap->emailAddress = new Model_Core_EmailAddress();
                        $emailMap->emailAddress->emailAddress = $request->getParam("email{$categoryConstName}");
                        $emailMapArray[] = $emailMap;
                    }
                    $this->_agentManager->setEmailAddresses($emailMapArray);

                    // Tell user successful
                    $this->_helper->flashmessages->addMessage('Email addresses updated');
                } else {

                    // Tell user unsuccessful
                    $this->_helper->flashmessages->addMessage('Problem encountered updating e-mail addresses, see below');
                }
            } else {
                // Populate only
                $emailMapArray = $this->_agentManager->getEmailAddresses();
                $emailMapConstants = array_flip(Model_Core_Agent_EmailMapCategory::iterableKeys());
                foreach($emailMapArray as $emailMap) {
                    $pageFormEmailAddresses
                        ->subform_emailaddresses
                        ->getElement("email{$emailMapConstants[$emailMap->category]}")
                        ->setValue($emailMap->emailAddress->emailAddress);
                }
            }

            // Handle any form error messages by passing them into the
            //   flashmessages helper
            $allErrors = $pageFormEmailAddresses->getMessages();
            foreach($allErrors as $field => $errors) {
                $this->_helper->flashmessages->addMessage($errors);
            }

            // Return form
            return $pageFormEmailAddresses;
        } else {
            return null;
        }
    }

    /**
     * Handle "my account" section changes and data population
     */
    private function _handleMyAccount($request) {

        // Instantiate form definition - uses generic single user form
        $pageFormMyAccount = new Connect_Form_SettingsUserAccount($this->_level);

        if ($request->isPost() && isset($_POST['updateMyAccount'])) {

            if ($pageFormMyAccount->isValid($request->getPost())) {

                // Fetch user object and insert new data into it from form
                $userObj = $this->_userManager->getUser();
                $userObj->name =                        $request->getParam('realname');
                $userObj->securityQuestionId =          $request->getParam('question');
                $userObj->securityAnswer =              $request->getParam('answer');

                // Only master users edit[/see] the username, e-mail
                //   address, copy-mail-to address and role/resources
                //   checkboxes
                if ($this->_level == Model_Core_Agent_UserRole::MASTER) {

                    $userObj->username =                    $request->getParam('username');
                    $userObj->email->emailAddress =         $request->getParam('email');
                    $userObj->copyMailTo->emailAddress =    $request->getParam('emailcopyto');
                    $userObj->role =                        ($request->getParam('master') === '1') ? Model_Core_Agent_UserRole::MASTER : Model_Core_Agent_UserRole::BASIC;
                    // Add or remove ability to access "reports" resource
                    if ($request->getParam('reports') === '1') {

                        // Add "reports" resource if not already set
                        if (!in_array(Model_Core_Agent_UserResources::REPORTS, $userObj->resources)) {

                            $userObj->resources[] = Model_Core_Agent_UserResources::REPORTS;
                        }
                    } else {

                        // Remove "reports" resource if set
                        if (in_array(Model_Core_Agent_UserResources::REPORTS, $userObj->resources)) {
                            foreach ($userObj->resources as $key => $val) {

                                if ($val == Model_Core_Agent_UserResources::REPORTS) {
                                    unset($userObj->resources[$key]);
                                }
                            }
                        }
                    }
                    $userObj->status = ($request->getParam('status') === '1') ? Model_Core_Agent_UserStatus::ACTIVATED : Model_Core_Agent_UserStatus::DEACTIVATED;
                }

                $newPassword = $request->getParam('password1');
                if ($newPassword != '') {
                    $userObj->password = $newPassword;
                }

                // Write modified user object back to DB
                $this->_userManager->setUser($userObj);

                // Tell user successful
                $this->_helper->flashmessages->addMessage('Account details updated');
            } else {

                // Tell user unsuccessful
                $this->_helper->flashmessages->addMessage('Problem encountered updating account details, see below');
                // Put error messages into flash messages as they're unable
                //   to be shown in the helper-rendered form
                $allErrors = $pageFormMyAccount->getMessages();
                foreach($allErrors as $field => $errors) {
                    $this->_helper->flashmessages->addMessage($errors);
                }
            }

        } else {

            $userObj = $this->_userManager->getUser();
            $pageFormMyAccount->subform_useraccount->getElement('realname')->setValue($userObj->name);
            $pageFormMyAccount->subform_useraccount->getElement('username')->setValue($userObj->username);
            $pageFormMyAccount->subform_useraccount->getElement('email')->setValue($userObj->email->emailAddress);
            $pageFormMyAccount->subform_useraccount->getElement('emailcopyto')->setValue($userObj->copyMailTo->emailAddress);
            $userSecurity = $this->_userManager->getUserSecurityDetails();
            $pageFormMyAccount->subform_useraccount->getElement('question')->setValue($userSecurity['questionID']);
            $pageFormMyAccount->subform_useraccount->getElement('answer')->setValue($userSecurity['answer']);
            $userRole = ($this->_level == Model_Core_Agent_UserRole::MASTER) ? '1' : '0';
            $pageFormMyAccount->subform_useraccount->getElement('master')->setValue($userRole);
            $userResourcesReports = (in_array(Model_Core_Agent_UserResources::REPORTS, $userObj->resources)) ? '1' : '0';
            $pageFormMyAccount->subform_useraccount->getElement('reports')->setValue($userResourcesReports);
            $userStatus = ($userObj->status == Model_Core_Agent_UserStatus::ACTIVATED) ? '1' : '0';
            $pageFormMyAccount->subform_useraccount->getElement('status')->setValue($userStatus);
        }

        if ($this->_level == Model_Core_Agent_UserRole::BASIC) {
            // For non-master users, username and e-mail are disabled, so always
            //   re-populate
            $pageFormMyAccount->subform_useraccount->getElement('username')->setValue($userObj->username);
            $pageFormMyAccount->subform_useraccount->getElement('email')->setValue($userObj->email->emailAddress);
        }

        $this->view->flashMessages = $this->_helper->flashmessages->getCurrentMessages();
        return $pageFormMyAccount;
    }

   /**
     * Handle "news ticker settings" section changes and data population
     */
    private function _handleNewsTickerSettings($request) {

        // Fetch all available news categories
        $newsCategories = $this->_extNewsManager->fetchCategories();

        // Own news ticker prefs
        if ($request->isPost() && isset($_POST['newsSubmit'])) {
            // Check if user wants to see news
            $allowNewsTickerMine = (isset($_POST['allowNewsMine'])) ? true : false;
            // Read user's category prefs from POST vars
            $newsCategoryPrefs = array();
            foreach($_POST as $key => $val) {
                if (preg_match('/^cat_(\d+)$/', $key, $matches) != 0) {
                    $newsCategoryPrefs[] = $matches[1];
                }
            }
            // Store prefs
            $this->_userManager->setUserExternalNewsPreferences($allowNewsTickerMine, $newsCategoryPrefs);
            // Flush user's news cache so changes show up immediately
            $cache = Zend_Cache::factory(
                'Core',
                'File',
                array(),
                array('cache_dir' => $this->_params->cms->extnews->cachePath)
            );
            $cache->remove('externalNews_user_' . $this->_agentId);

            // Tell user successful
            $this->_helper->flashmessages->addMessage('News ticker preferences updated');

            // Only available to master users
            if ($this->_level == Model_Core_Agent_UserRole::MASTER) {
                // Global news ticker prefs
                if (isset($_POST['newsSubmit'])) {
                    $allowNewsTickerGlobal = (isset($_POST['allowNewsGlobal'])) ? true : false;
                    $this->_agentManager->setExternalNewsPreference($allowNewsTickerGlobal);
                }
            }
        }

        // Fetch user's own news prefs to display, don't filter categories by
        // visibility
        list($allowNewsTickerMine, $newsPrefs) = $this->_userManager->getUserExternalNewsPreferences(false);
        $newsCategoryFilter = array();
        foreach ($newsPrefs as $pref) {
            $newsCategoryFilter[$pref->id] = $pref->id;
        }
        $this->view->allowNewsTickerMine = $allowNewsTickerMine;
        $this->view->newsCategories = $newsCategories;
        $this->view->newsCategoryFilter = $newsCategoryFilter;

        // Fetch global news prefs to display
        $allowNewsTickerGlobal = $this->_agentManager->getExternalNewsPreference();
        $this->view->allowNewsTickerGlobal = $allowNewsTickerGlobal;

        $this->view->flashMessages = $this->_helper->flashmessages->getCurrentMessages();
    }

    /**
     * Handle "company logo" section changes for both connect and document logos
     */
    private function _handleLogo($request) {

        // Only available to master users
        if ($this->_level == Model_Core_Agent_UserRole::MASTER) {

            if ($request->isPost()) {

                // Company logo for Connect - delete
                if (isset($_POST['deleteConnectLogo'])) {

                    $result = $this->_agentManager->deleteConnectLogo(
                        $this->_params->connect->settings->logoUpload
                    );

                    if (!$result) {
                        // Tell user unsuccessful
                        $this->_helper->flashmessages->addMessage('Logo delete unsuccessful');

                        // Show error feedback in page next to form too
                        $this->view->logoMessages = array('Logo delete unsuccessful');
                    } else {
                        $this->_helper->flashmessages->addMessage('Logo deleted successfully - changes may not show immediately');
                    }

                }

                // Company logo for Documents - delete
                if (isset($_POST['deleteDocumentLogo'])) {

                    $result = $this->_agentManager->deleteDocumentLogo(
                        $this->_params->connect->settings->logoUpload
                    );

                    if (!$result) {
                        // Tell user unsuccessful
                        $this->_helper->flashmessages->addMessage('Logo delete unsuccessful');

                        // Show error feedback in page next to form too
                        $this->view->logoMessages = array('Logo delete unsuccessful');
                    } else {
                        $this->_helper->flashmessages->addMessage('Logo deleted successfully - changes may not show immediately');
                    }

                }

                // Company logo for Connect - upload
                if (isset($_POST['uploadConnectLogo'])) {

                    list($result, $errors) = $this->_agentManager->uploadConnectLogo(
                        $this->_params->connect->settings->logoUpload
                    );

                    if (!$result) {
                        // Tell user unsuccessful
                        $this->_helper->flashmessages->addMessage('Logo upload unsuccessful');
                        $this->_helper->flashmessages->addMessage($errors);

                        // Show error feedback in page next to form too
                        $this->view->logoMessages = array('Logo upload unsuccessful') + $errors;
                    } else {
                        $this->_helper->flashmessages->addMessage('Logo uploaded successfully - changes may not show immediately');
                    }

                }

                // Company logo for Documents - upload
                if (isset($_POST['uploadDocumentLogo'])) {

                    list($result, $messages) = $this->_agentManager->uploadDocumentLogo(
                        $this->_params->connect->settings->logoUpload
                    );

                    if (!$result) {
                        // Tell user unsuccessful
                        $this->_helper->flashmessages->addMessage('Logo upload unsuccessful');
                        $this->_helper->flashmessages->addMessage($messages);

                        // Show error feedback in page next to form too
                        $this->view->logoMessages = array('Logo upload unsuccessful') + $messages;
                    }
                    else if ($messages !== true) {
                        $this->_helper->flashmessages->addMessage($messages);
                    }
                    else {
                        $this->_helper->flashmessages->addMessage('Logo uploaded successfully - changes may not show immediately');
                    }
                }
            }

            $this->view->flashMessages = $this->_helper->flashmessages->getCurrentMessages();
        }
    }

    /**
     * Handle "user accounts" section changes
     */
    private function _handleUserAccounts($request) {

        // Only available to master users
        if ($this->_level == Model_Core_Agent_UserRole::MASTER) {

            // User accounts - updates
            if (
                $request->isPost() &&
                (
                    isset($_POST['updateAccount']) ||
                    isset($_POST['insertAccount'])
                )
            ) {

                $upsertType = (isset($_POST['updateAccount'])) ? 'updat'/*[sic]*/ : 'insert';

                // Intantiate form definition - uses generic single user form
                $pageFormUserAccount = new Connect_Form_SettingsUserAccount($this->_level);
                if ($pageFormUserAccount->isValid($request->getPost())) {

                    if ($upsertType == 'updat') {

                        // Check user ID matches an account under this ASN, protects
                        // against blind modification attack across ASNs
                        try {

                            $userObj = $this->_userManager->getUser($request->getParam('userId'), null, $this->_agentSchemeNumber);

                        } catch (Exception $e) {

                            // Tell user unsuccessful
                            $this->_helper->flashmessages->addMessage('Problem encountered updating account details - bad user ID');
                            $this->view->flashMessages = $this->_helper->flashmessages->getCurrentMessages();
                            return;
                        }
                    } else {

                        // Create new user object, set ASN
                        $userObj = new Model_Core_Agent_User();
                        $userObj->agentSchemeNumber = $this->_agentSchemeNumber;
                        $userObj->resources = array();
                    }

                    // Update user object from form-supplied data
                    $userObj->name =                        $request->getParam('realname');
                    $userObj->securityQuestionId =          $request->getParam('question');
                    $userObj->securityAnswer =              $request->getParam('answer');
                    $userObj->username =                    $request->getParam('username');
                    $userObj->email->emailAddress =         $request->getParam('email');
                    $userObj->copyMailTo->emailAddress =    $request->getParam('emailcopyto');
                    $userObj->role =                        ($request->getParam('master') === '1') ? Model_Core_Agent_UserRole::MASTER : Model_Core_Agent_UserRole::BASIC;
                    // Add or remove ability to access "reports" resource
                    if ($request->getParam('reports') === '1') {
                        // Add "reports" resource if not already set
                        if (!in_array(Model_Core_Agent_UserResources::REPORTS, $userObj->resources)) {
                            $userObj->resources[] = Model_Core_Agent_UserResources::REPORTS;
                        }
                    } else {
                        // Remove "reports" resource if set
                        if (in_array(Model_Core_Agent_UserResources::REPORTS, $userObj->resources)) {
                            foreach ($userObj->resources as $key => $val) {
                                if ($val == Model_Core_Agent_UserResources::REPORTS) {
                                    unset($userObj->resources[$key]);
                                }
                            }
                        }
                    }
                    $userObj->status = ($request->getParam('status') === '1') ? Model_Core_Agent_UserStatus::ACTIVATED : Model_Core_Agent_UserStatus::DEACTIVATED;

                    $newPassword = $request->getParam('password1');
                    if ($newPassword != '') {
                        $userObj->password = $newPassword;
                    }

                    // Write modified user object back to DB, or possibly add via IRIS
                    try {
                        if (
                            $this->_isAgentInIris &&
                            'insert' == $upsertType
                        ) {
                            // This is an IRIS agent being added, use the API

                            $irisContainer = \Zend_Registry::get('iris_container');
                            $response = $irisContainer
                                ->get('iris_sdk_client_registry')
                                ->getAgentContext()
                                ->getBranchClient()
                                ->createBranchUser(array(
                                    'agentBranchUuId' => $this->_irisAgentBranchUuid,
                                    'name' => $userObj->name,
                                    'username' => $userObj->username,
                                    'password' => $userObj->password,
                                    'email' => $userObj->email->emailAddress,
                                    'hasReports' => ($request->getParam('reports') === '1'),
                                    'hasAccounts' => false, // Not asked for, assume "no"
                                    'status' => $userObj->status,
                                    'isExternalNewsEnabled' => true, // Not asked for, assume "yes"
                                ));
                        } else {
                            // Use old fashioned user manager to add or update the user object
                            $this->_userManager->setUser($userObj);
                        }
                    } catch (Exception $e) {
                        $this->_helper->flashmessages->addMessage("Problem encountered {$upsertType}ing account details: " . $e->getMessage());
                        $this->view->flashMessages = $this->_helper->flashmessages->getCurrentMessages();
                        return;
                    }

                    // Tell user successful
                    $this->_helper->flashmessages->addMessage('Account details for ' . $request->getParam('realname') . ' (username: ' . $request->getParam('username') . ") {$upsertType}ed");
                } else {

                    // Tell user unsuccessful
                    $this->_helper->flashmessages->addMessage("Problem encountered {$upsertType}ing account details");

                    // Put error messages into flash messages as they're unable
                    //   to be shown in the helper-rendered form
                    $allErrors = $pageFormUserAccount->getMessages();
                    foreach($allErrors as $field => $errors) {
                        $this->_helper->flashmessages->addMessage($errors);
                    }

                }

                $this->view->flashMessages = $this->_helper->flashmessages->getCurrentMessages();
            }
        }
    }

}

