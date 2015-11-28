<?php

require_once('ConnectAbstractController.php');
class Connect_IndexController extends ConnectAbstractController
{
    /**
     * Default action for connect - shows the dashboard.
     *
     * @todo: Move MotD and blog view stuff into view helpers.
     *
     * @return void
     */
    public function indexAction() {

        $this->view->headLink()->appendStylesheet('/assets/connect/css/salesperson.css');

        // Set some standard vars for view to use
        $this->view->params = $this->_params;
        $this->view->agentSchemeNumber = $this->_agentSchemeNumber;
        $this->view->agentId = $this->_agentId;
        $this->view->level = $this->_level;

        // Instantiate MotD manager
        $motdManager = new Manager_Connect_Motd();

        // Instantiate MotD acceptance logger if a MotD has been accepted
        if (isset($_GET['motdaccept'])) {
            $motdId = preg_replace('/[^\d]/', '', $_GET['motdaccept']);
            $motdManager->markMotdAccepted($motdId, $this->_agentId);
        }

        // Fetch any active MotDs that may be applicable to this agent user
        $motd = $motdManager->getMotd($this->_agentId, $this->_agentSchemeNumber);
        // Should a MotD be displayed?

        $motdOutput = array();
        if (!is_null($motd)) {
            // Populate MotD details into the welcome screen
            $motdOutput['defaultState'] = 'open';
            $motdOutput['id']       = $motd->getId();
            $motdOutput['title']    = $motd->getTitle();
            $motdOutput['message']  = $motd->getMessage();
            $motdOutput['width']    = $motd->getDisplayWidth();
        } else {
            // No MotD, tell GUI this
            $motdOutput['defaultState'] = 'close';
            $motdOutput['id']       =
            $motdOutput['title']    =
            $motdOutput['message']  = '';
            $motdOutput['width']    = '0';
        }
        $this->view->motd = $motdOutput;

        // Instantiate blog manager
        $blogManager = new Manager_Connect_Blog();

        // Retrieve the main blog details
        $mainBlog = $blogManager->getMainBlog();
        if (!empty($mainBlog)) {

            $this->view->blogMainImage = $mainBlog->getIcon();
            $this->view->blogMainTitle = $mainBlog->getTitle();
            $this->view->blogMainContent = $mainBlog->getContent();
        }

        // Retrieve recent blog entries
        $blogSummaries = array();
        for ($i = 0; $i < 4; $i++) {

            $summaryBlog = $blogManager->getSummaryBlog($i + 1);
            if (!empty($summaryBlog)) {
                $blogSummaries[$i] = array(
                    'link'          => '/news/single/' . ($i + 1),
                    'title'         => $summaryBlog->getTitle(),
                    'description'   => $summaryBlog->getSummary(),
                    'image'         => $summaryBlog->getIcon()
                );
            }
        }
        $this->view->blogSummaries = $blogSummaries;

        // Decide what criteria to offer for searching blog entries
        $this->view->blogSearchKeywords = $blogManager->getAllTagsInUse();
        $this->view->blogSearchMonths   = $blogManager->getAllBlogMonths();
        $this->view->blogSearchYears    = $blogManager->getAllBlogYears();
    }

    /**
     * This action is called whenever a "static" "brochureware" page is to be
     * rendered, eg, when no dispatchable action is found by the custom
     * dispatcher, but a corresponding .phtml file is present.
     */
    public function viewStaticPageAction() {
        // Get name of static page from URI
        $action = trim($this->getRequest()->getRequestUri(), '/');
        $action = preg_replace('/\?.*$/', '', $action);
        $action = str_replace('/', '-', $action);
        $action = strtolower($action);
        $action = preg_replace('/[^a-z\-]/', '', $action);

        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));

        // Intercept old referencing URL for IRIS agent branches
        if ('referencing' == $action && $auth->getStorage()->read()->isInIris) {
            $this->_redirect('/iris-referencing');
        }

        if (file_exists(APPLICATION_PATH . '/modules/connect/views/scripts/index/static-pages/' . $action . '.phtml')) {
            // Set some standard vars for view to use
            $this->view->params = $this->_params;
            $this->view->agentSchemeNumber = $this->_agentSchemeNumber;
            $this->view->agentId = $this->_agentId;
            $this->view->agentUserName = $this->_agentUserName;
            $this->view->level = $this->_level;

            $this->view->isAgentInIris = $this->_isAgentInIris;
            $this->view->canPerformReferencing = $this->_canPerformReferencing;

            // Set panel content, if any
            $this->_helper->panelcontent->fetch('connect-brochureware-' . $action);

            // Render "static" page
            $this->render('static-pages/' . $action);
        } else {
            // Throw a 404 error
            throw new Zend_Controller_Action_Exception("This page doesn't exist", 404);
        }
    }

    /***************************************************************************************/
    /* LOGIN FUNCTIONS                                                                     */
    /***************************************************************************************/

    /**
     * This function handles a login attempt and validates the credentials
     *
     * @return void
     */
    public function loginAction ()
    {
        // Force user over to SSL if not already
       # if ($_SERVER['SERVER_PORT'] != 443) {
        #    $this->_redirect($this->_params->url->connectLogin);
       # }

        $this->_helper->layout->setLayout('login');
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));

        if ($auth->hasIdentity()) {
            // User is already logged in so just push them into the system
            $this->_redirect('/');
        }

        // Intantiate form definition
        $pageForm = new Connect_Form_Login();

        // If we have a refno, try to prepopulate the form with ASN
        if (preg_match('/^([0-9]+\.[0-9]+)$/', $this->getRequest()->getParam('refno'), $matches)) {

            if (isset($matches[1])) {

                $refno = $matches[1];

                $enquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
                $agentID = $enquiryDatasource->getReferenceAgentID($this->getRequest()->getParam('refno'));

                if ($agentID) {
                    $pageForm->agentschemeno->setValue($agentID);
                }
            }
        }

        $request = $this->getRequest();
        if ($request->isPost()) {            
            if ($this->_helper->auth->attemptLogin($pageForm)){                            
              $this->_redirect($this->getRequest()->getRequestUri());
            }else{              
            	$this->view->loginErrorMessage = "Invalid user or password";
            }
        }
        $params = Zend_Registry::get('params');
        $this->view->pageTitle = 'Log In';

        $this->view->homePage = $params->homelet->get('domain');
        $this->view->form = $pageForm;
    }


    /**
     * This function clears the stored identity in the Zend_Auth object and logs the user out
     *
     * @return void
     */
    public function logoutAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->getHelper('layout')->disableLayout();
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));

        $auth->clearIdentity();
        $this->_redirect('/login');
    }

    /**
     * Account deactivated, display a friendly error message to the user,
     * informing them that their account has been deactived.
     *
     * @return void
     */
    public function accountDeactivatedAction()
    {
        $this->_helper->layout->setLayout('login');
    }

    /**
     * Account expired, display a friendly error message to the user,
     * informing them that their account has expired due to inactivity.
     *
     * @return void
     */
    public function accountExpiredAction()
    {
        $this->_helper->layout->setLayout('login');
    }

    /**
     * Agents FSA status is missing or none
     *
     * @return void
     */
    public function agentFsaNostatusAction()
    {
        $this->_helper->layout->setLayout('login');
    }


    /**
     * Password retrieval - generate password reset link.
     *
     * @return void
     */
    public function lostLoginAction() {
        $this->_helper->layout->setLayout('login');
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));

        if ($auth->hasIdentity()) {
            // User is already logged in so just push them into the system
            $this->_redirect('/');
        }

        // Instantiate form definition
        $pageForm = new Connect_Form_LostLogin();

        // Validate form if POSTed
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            if ($pageForm->isValid($postData)) {

                try {

                    $agentUserManager = new Manager_Core_Agent_User();
                    $agentUser = $agentUserManager->searchByFuzzyCredentials($postData);

                    // Send password reset link details
                    $success = $agentUserManager->sendPasswordResetLink($agentUser);

                    // Show user confirmation that reset details have been sent
                    $this->_helper->viewRenderer('lost-login-sent');

                } catch (Zend_Exception $e) {

                }

            }
        }

        $this->view->form = $pageForm;
    }

    /**
     * Password retrieval - reset password after following reset password link.
     *
     * @return void
     */
    public function resetPasswordAction() {

        $this->_helper->layout->setLayout('login');
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));

        if ($auth->hasIdentity()) {
            // User is already logged in so just push them into the system
            $this->_redirect('/');
        }

        // Instantiate form definition
        $pageForm = new Connect_Form_ResetPassword();

        // Instantiate an agent user object for querying and updating
        $agentUserManager = new Manager_Core_Agent_User();

        // Check if a valid reset code is present
        if (
            !is_null($this->getRequest()->getParam('code')) &&
            $agentUserManager->checkPasswordResetCodeValid($this->getRequest()->getParam('code')) === true
        ) {
            $agentUser = $agentUserManager->getUser();
            // Set variables for display
            $this->view->realname = $agentUser->name;
            $this->view->agentschemeno = $agentUser->agentSchemeNumber;
            $this->view->username = $agentUser->username;

            // Validate form if POSTed
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postData = $request->getPost();
                if ($pageForm->isValid($postData)) {
                    // Set password
                    $agentUserManager->resetPassword($postData['password1'], $agentUser->id);

                    // Output for quick login "continue" button
                    $this->view->password = $postData['password1'];

                    // Show user confirmation that password has been reset
                    $this->_helper->viewRenderer('reset-password-success');
                }
            }

        } else {
            // Code invalid, show error message
            $this->view->error = 'The password reset link you followed is either invalid, been used or has expired.';

            // Show user the error page
            $this->_helper->viewRenderer('reset-password-invalid');
        }

        $this->view->form = $pageForm;
    }
    
}
