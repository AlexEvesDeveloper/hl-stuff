<?php

abstract class ConnectAbstractController extends Zend_Controller_Action {

    public $context = 'web';
    protected $_params;
    private $_auth;

    protected $_hasAuth = false;
    protected $_agentObj;
    protected $_agentSchemeNumber;
    protected $_agentId;
    protected $_level;
    protected $_fsastatusabbr;
    protected $_agentrealname;
    protected $_agentUserName;
    protected $_agentsRateID;

    /**
     * @var bool
     */
    protected $_isAgentInIris = false;

    /**
     * @var bool
     */
    protected $_canPerformReferencing;

    /**
     * @var string
     */
    protected $_irisAgentBranchUuid;

    // Switch for menu style
    // (bool)true for "full" menu style (main and sub menus are the main site menus, Connect menu as a sub-sub-menu)
    // (bool)false for Connect menu being the main menu
    protected $_fullMenuStyle = false;

    public function init() {

        $this->_params = Zend_Registry::get('params');

        // If the user isn't logged in - force them onto the login action
        $this->_auth = Zend_Auth::getInstance();
        $this->_auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));

        $this->_hasAuth = $this->_auth->hasIdentity();

        if ($this->_hasAuth) {
            // Set private class vars, including complete agent object
            $this->_agentSchemeNumber   = $this->_auth->getStorage()->read()->agentschemeno;
            $this->_agentId             = $this->_auth->getStorage()->read()->agentid;
            $this->_level               = $this->_auth->getStorage()->read()->level;
            $this->_fsastatusabbr       = $this->_auth->getStorage()->read()->fsastatusabbr;
            $this->_agentrealname       = $this->_auth->getStorage()->read()->realname;
            $this->_agentUserName       = $this->_auth->getStorage()->read()->username;
            $this->_agentsRateID        = $this->_auth->getStorage()->read()->agentsRateID;

            // Pass IRIS detection flag to controller
            $this->_isAgentInIris       = $this->_auth->getStorage()->read()->isInIris;
            $this->_canPerformReferencing       = $this->_auth->getStorage()->read()->canPerformReferencing;
            if ($this->_isAgentInIris) {
                // Pass IRIS agent branch UUID too
                $this->_irisAgentBranchUuid = $this->_auth->getStorage()->read()->agentBranchUuid;
            }

            $agentManager               = new Manager_Core_Agent();
            $this->_agentObj            = $agentManager->getAgent($this->_agentSchemeNumber);
        }

        if ($this->context == 'web') {
            $this->initWeb();
        }
    }

    public function initWeb() {
        // Start the zend layout engine and load the cms admin layout
        Zend_Layout::startMvc();
        $this->_helper->layout->setLayout('default');

        if ($this->_hasAuth) {
            $userAccountManager         = new Manager_Core_Agent_User($this->_agentId);
            $this->view->userresources  = $userAccountManager->getUserResources();

            // Set view parameters that are commonly used
            $this->view->agentUsername      = $this->_auth->getStorage()->read()->username;
            $this->view->userRealName       = $this->_agentrealname;
            $agentNameArray = explode(' ', $this->_agentrealname);
            $this->view->userRealFirstName  = array_shift($agentNameArray);
            $this->view->agentSchemeNumber  = $this->_agentSchemeNumber;
            $this->view->agentId            = $this->_agentId;
            $this->view->level              = $this->_level;
            $this->view->fsastatusabbr      = $this->_fsastatusabbr;
      
            // Pass agent object to view
            $this->view->agentObj = $this->_agentObj;
        }

        // Pass params to view
        $this->view->params = $this->_params;
    }
}