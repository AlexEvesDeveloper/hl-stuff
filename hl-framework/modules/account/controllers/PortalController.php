<?php

/**
 * Customer portal controller. Please note that this controller is NOT exposed 
 * to anonymous users
 * 
 * @package Account_PortalController
 */
class Account_PortalController extends Zend_Controller_Action
{
    /**
     * @var Zend_Auth
     */
    private $auth = null;

    public $url;
    
    /**
     * Initialise the controller, settign the layout 
     * and primary navigation
     * 
     * @return void 
     */
    public function init()
    {
        // Set the default layout
        $layout = Zend_Layout::getMvcInstance();                                
        $layout->setLayoutPath(APPLICATION_PATH . '/modules/cms/layouts/scripts');
        $layout->setLayout('default');
        $this->view->headLink()->setStylesheet('/assets/account/css/account.css');

        // Check authorisation for this controller
        $this->auth = $this->_checkAuthorisation();

        // Get the customer session
        $customerSession = $this->auth->getStorage()->read();

        $referenceCustomerMap = new Manager_Referencing_Reference();
        $referenceIds = $referenceCustomerMap->getAllReferenceIds($customerSession->id);

        // Get customer's name into the view
        $customermgr = new Manager_Core_Customer();
        $customer = $customermgr->getCustomer(Model_Core_Customer::IDENTIFIER, $customerSession->id);

        $this->view->firstName = htmlentities($customer->getFirstName());

        // Get current URL
        $this->url = $this->getRequest()->getRequestUri();

        // Trim any GET parameters off
        if (($questionMarkPos = strpos($this->url, '?')) !== false) {
            $this->url = substr($this->url, 0, $questionMarkPos);
        }

        // Trim any leading or trailing slashes off
        $this->url = trim($this->url, '/');

        // Menu data
        $menuData = array(
            'selected' => 'home',
            'url' => $this->url,
        );

        // Get final segment only
        $urlSegment = $this->url;
        if (($slashPos = strpos($urlSegment, '/')) !== false) {
            $urlSegment = substr($urlSegment, $slashPos + 1);
        }

        $portalNavData = array(
            'urlSegment' => $urlSegment,
            'displayReferences' => (count($referenceIds) > 0 ? 1 : 0),
        );
        
        // Pass the menu partials to the layout
        $mainMenu = $this->view->partial('partials/homelet-mainmenu.phtml', 'cms', $menuData);
		$subMenu = $this->view->partial('partials/homelet-submenu.phtml', 'cms', $menuData);

        $layout->getView()->mainMenu = $mainMenu;
		$layout->getView()->subMenu = $subMenu;
        
        // Add navigation & breadcrumbs partial
        $this->view->navigation = $this->view->partial('partials/portal-navigation.phtml', $portalNavData);
        $this->view->displayReferences = (count($referenceIds) > 0 ? 1 : 0);

        // Set hidden search form fields (for sort order only at this point)
        $this->view->hiddenSearchFields = array();

        foreach ($this->getRequest()->getParams() as $k => $v) {
            if ('order' == $k && is_array($v)) {
                foreach ($v as $name => $value) {
                    $this->view->hiddenSearchFields[sprintf('order[%s]', $name)] = $value;
                }
            }
        }

        // Pass the last search terms to the view
        $this->view->searchTerms = $this->getRequest()->getParam('id');

        // Get the HomeLet brochureware URL into the view by taking the HTTPS
        //   HLF base URL and modifying it to HTTP.
        $params = Zend_Registry::get('params');
        $brochurewareBaseUrl = trim($params->homelet->domain, '/') . '/';
        $brochurewareBaseUrl = str_replace(
            'https://',
            'http://',
            $brochurewareBaseUrl
        );
        $this->view->brochurewareBaseUrl = $brochurewareBaseUrl;
    }
    
    /**
     * Edit account action
     * 
     * @return void 
     */
    public function editAccountAction()
    {
        $this->_setMetaTitle('My HomeLet | Edit Account');
        
        $this->_setBreadcrumbs(array(
            '/' => 'Home',
            '/my-homelet' => 'My HomeLet',
            '/my-homelet/edit-account' => 'My Account Details',
        ));
        
        $form = new Account_Form_EditAccount();

        // Populate the form with the security question options
        $securityQuestionModel = new Datasource_Core_SecurityQuestion();
        $securityQuestionOptions = $securityQuestionModel->getOptions();

        foreach ($securityQuestionOptions as $option) {
            $form->security_question->addMultiOption($option['id'], $option['question']);
        }

        // Get the customer session
        $customerSession = $this->auth->getStorage()->read();

        // Retrieve the customer record
        $customermgr = new Manager_Core_Customer();
        $customer = $customermgr->getCustomer(Model_Core_Customer::IDENTIFIER, $customerSession->id);

        if ($this->getRequest()->isPost()) {
            // Set the current password for validation
            $form->setCurrentPassword($customer->getPassword());

            // Validate the form
            if ($form->isValid($this->getRequest()->getPost())) {
                // Update the customer
                if ($form->password->getValue() != '') {
                    // Set new password
                    $customer->setPassword($form->password->getValue());
                }

                $customer->setSecurityQuestion($form->security_question->getValue());
                $customer->setSecurityAnswer($form->security_answer->getValue());

                $this->view->accountUpdated = true;

                $customermgr->updateCustomer($customer);
            }
        }
        else {
            // Populate the form with customers data
            $form->security_question->setValue($customer->getSecurityQuestion());
            $form->security_answer->setValue($customer->getSecurityAnswer());
        }

        $form->email->setValue($customer->getEmailAddress());
        $form->title->setValue($customer->getTitle());
        $form->first_name->setValue($customer->getFirstName());
        $form->last_name->setValue($customer->getLastName());

        $this->view->form = $form;
    }
    
    /**
     * Quote list action
     * 
     * @return void
     */
    public function quotesAction()
    {
        $this->_setMetaTitle('My HomeLet | Quotes');
        
        $this->_setBreadcrumbs(array(
            '/' => 'Home',
            '/my-homelet' => 'My HomeLet',
            '/my-homelet/quotes' => 'My Quotes',
        ));

        $request = $this->getRequest();

        // Get the customer session
        $customerSession = $this->auth->getStorage()->read();

        // Search and ordering
        $filteredOrderBy = array();
        $orderBy = $request->getParam('order');
        $quoteNumberSearch = $request->getParam('id');

        // Validate order by to restricted fields to those displayed on the front end
        if (is_array($orderBy)) {
            foreach ($orderBy as $orderByField => $orderByDirection) {
                if (in_array($orderByField, array('policynumber', 'policyname', 'startdate', 'premium', 'validuntildate'))) {
                    // Copy field into new array
                    $filteredOrderBy[$orderByField] = $orderByDirection;
                }
            }
        }

        // Get list of linked customer reference numbers
        $policyCoverDatasource = new Datasource_Insurance_LegacyPolicyCovers();
        $legacyCustomerMap = new Datasource_Core_CustomerMaps();
        $legacyIDs = $legacyCustomerMap->getLegacyIDs($customerSession->id);

        // Retrieve all quotes for the linked customer reference numbers
        $quoteDatasource = new Datasource_Insurance_LegacyQuotes();
        $quotes = $quoteDatasource->getActiveQuotes($legacyIDs, $quoteNumberSearch, $filteredOrderBy);

        // Build the list of policy covers.
        // Should be done in a manager, but the quote manager has been written with the row data gateway
        // design pattern in mind.
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
        }

        $this->view->quotes = $quotes;
    }
    
    /**
     * Policy list action 
     * 
     * @return void
     */
    public function policiesAction()
    {
        $this->_setMetaTitle('My HomeLet | Policies');
        
        $this->_setBreadcrumbs(array(
            '/' => 'Home',
            '/my-homelet' => 'My HomeLet',
            '/my-homelet/policies' => 'My Policies',
        ));

        $request = $this->getRequest();

        // Get the customer session
        $customerSession = $this->auth->getStorage()->read();

        // Search and ordering
        $filteredOrderBy = array();
        $orderBy = $request->getParam('order');
        $quoteNumberSearch = $request->getParam('id');

        // Validate order by to restricted fields to those displayed on the front end
        if (is_array($orderBy)) {
            foreach ($orderBy as $orderByField => $orderByDirection) {
                if (in_array($orderByField, array('policynumber', 'policyname', 'startdate', 'premium', 'renewaldate', 'paystatus'))) {
                    // Copy field into new array
                    $filteredOrderBy[$orderByField] = $orderByDirection;
                }
            }
        }

        // Get list of linked customer reference numbers
        $policyCoverDatasource = new Datasource_Insurance_LegacyPolicyCovers();
        $legacyCustomerMap = new Datasource_Core_CustomerMaps();
        $legacyIDs = $legacyCustomerMap->getLegacyIDs($customerSession->id);

        // Retrieve all quotes for the linked customer reference numbers
        $quoteDatasource = new Datasource_Insurance_LegacyPolicies();
        $policies = $quoteDatasource->getActivePolicies($legacyIDs, $quoteNumberSearch, $filteredOrderBy);

        // Build the list of policy covers.
        // Should be done in a manager, but the quote manager has been written with the row data gateway
        // design pattern in mind.
        foreach ($policies as $policy) {
            // Create list of policy covers
            $policyCoverList = array();
            $policyOptionsplit = explode('|', $policy->policyOptions);
            $sumInsuredSplit = explode('|', $policy->amountsCovered);

            for ($i = 0; $i < count($policyOptionsplit); $i++) {
                if ($sumInsuredSplit[$i] == 'yes' || floatval($sumInsuredSplit[$i]) > 0) {
                    // A sum insured value has been set so assume cover is in force
                    $policyCover = $policyCoverDatasource->getPolicyCoverByLabel($policyOptionsplit[$i]);

                    if ($policyCover) {
                        array_push($policyCoverList, $policyCover->getName());
                    }
                }
            }

            $policy->policyCovers = $policyCoverList;
        }

        $this->view->policies = $policies;
    }

    /**
     * Reference list action
     *
     */
    public function referencesAction()
    {
        $this->_setMetaTitle('My HomeLet | References');

        $this->_setBreadcrumbs(array(
            '/' => 'Home',
            '/my-homelet' => 'My HomeLet',
            '/my-homelet/references' => 'My References',
        ));

        // Get the customer session
        $customerSession = $this->auth->getStorage()->read();
        $request = $this->getRequest();

        // Search and ordering
        $filteredOrderBy = array();
        $orderBy = $request->getParam('order');
        $refnoSearch = $request->getParam('id');

        // Validate order by to restricted fields to those displayed on the front end
        if (is_array($orderBy)) {
            foreach ($orderBy as $orderByField => $orderByDirection) {
                if (in_array($orderByField, array('start_date', 'lastname', 'address1', 'externalrefno', 'status'))) {
                    // Copy field into new array
                    $filteredOrderBy[$orderByField] = $orderByDirection;
                }
            }
        }

        // Get list of external reference numbers
        $referencesAndReports = array();
        $referenceManager = new Manager_Referencing_Reference();
        $referenceIds = $referenceManager->getAllReferenceIds($customerSession->id);

        // Get all reference details
        $legacyRefManager = new Manager_ReferencingLegacy_Munt();
        $references = $legacyRefManager->getAllReferences($referenceIds, $refnoSearch, $filteredOrderBy);

        foreach ($references as $reference) {
            $report = $legacyRefManager->getLatestReport($reference->externalId);
            array_push($referencesAndReports, array('reference' => $reference,
                                                    'report' => $report));
        }

        $this->view->references = $referencesAndReports;
    }

    /**
     * Sets the page meta title
     * 
     * @param string $title
     * @return void
     */
    private function _setMetaTitle($title)
    {
        $this->view->pageTitle = $title;
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
    
    /**
     * Checks the authorisation of the current customer
     * 
     * @return Zend_Auth 
     */
    private function _checkAuthorisation()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        if (!$auth->hasIdentity()) {
            // Check if session expired
            $account_session = new Zend_Session_Namespace('account_logged_in');
            if ($account_session->loggedIn) {
                // Session must have expired, warn user and clear simple session used to track this
                Zend_Session::namespaceUnset('account_logged_in');
                return $this->_helper->redirector->gotoUrl('/login?message=session-expired&referrerUrl=' . urlencode($_SERVER['REQUEST_URI']));
            } else {
                // User was not logged in before
                return $this->_helper->redirector->gotoUrl('/login?referrerUrl=' . urlencode($_SERVER['REQUEST_URI']));
            }
        }

        return $auth;
    }
}
