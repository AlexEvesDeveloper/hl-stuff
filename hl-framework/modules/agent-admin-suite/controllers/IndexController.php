<?php    

class AgentAdminSuite_IndexController extends Zend_Controller_Action
{
    
    /***************************************************************************************/
    /* LOGIN FUNCTIONS                                                                     */
    /***************************************************************************************/
    
    /**
     * This function returns an auth adapter for the login systems
     *
     * @param array params
     * @return Zend_Auth_Adapter_DbTable
     *
     * This function takes a params array (which should be login form values)
     * and creates a zend auth adapter linked to the correct database
     * and users table. If the params array has come from a login form and has
     * a username and password fields it will set them as the identity
     * and credentials in the auth adapter so that we can check to see if they
     * are valid
     */
    protected function _getAuthAdapter(array $params)
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db_homelet_admin'));
        $authAdapter
            ->setTableName('users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('MD5(?)');
        
        $authAdapter->setIdentity($params['username']);
        $authAdapter->setCredential($params['password']);
        
        return $authAdapter;
    }
    
    
    /**
     * This function creates a zend form for a login box with the relevant validation
     *
     * @return Zend_Form
     */
    protected function getLoginForm () {
        $form = new Zend_Form();
        $form->setAction('/cmsadmin/login')
             ->setMethod('post');
        
        $username = $form->createElement('text', 'username');
        $username->addValidator('alnum')
                 ->addValidator('regex', false, array('/^[a-z]+/'))
                 ->addValidator('stringLength', false, array(6, 64))
                 ->setRequired(true)
                 ->addFilter('StringToLower');
        
        $password = $form->createElement('password', 'password');
        $password->addValidator('StringLength', false, array(6))
                 ->setRequired(true);
        
        $form->addElement($username)
             ->addElement($password)
             ->addElement('submit', 'login', array('label' => 'Login'));
        
        return $form;
    }
    
    
    /**
     * This function handles a login attempt and validates the credentials
     *
     * @return void
     */
    public function loginAction () {
        $this->_helper->layout->setLayout('login');
        if (Zend_Auth::getInstance()->hasIdentity()) {
            // User is already logged in so just push them into the system
            $this->_helper->redirector('index');
        }
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            // We have post data from the login form - so attempt a login
            $form = $this->getLoginForm();
            
            if (!$form->isValid($_POST)) {
                // Form is invalid
                $this->view->form = $form;
                return $this->render();
            }
            
            // The forms passed validation so we now need to check the identity of the user
            $adapter = $this->_getAuthAdapter($form->getValues());
            $auth    = Zend_Auth::getInstance();
            $auth->setStorage(new Zend_Auth_Storage_Session('hl_admin'));
            $result  = $auth->authenticate($adapter);
            if (!$result->isValid()) {
                // Invalid credentials
                $form->setDescription('Invalid credentials provided');
                $this->view->form = $form;
                return $this->render('index'); // re-render the login form
            } else {
                // Valid credentials - store the details we need from the database and move the user to the index page
                $storage = $auth->getStorage();
                $storage->write($adapter->getResultRowObject(array(
                    'username',
                    'real_name')));
                $this->_helper->redirector('index');
            }
            
        }
    }
    
    
    /**
     * This function clears the stored identity in the zend auth object and logs the user otu
     *
     * @return void
     */
    public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_admin'));
        $auth->clearIdentity();
        $this->_helper->redirector('login');
    }
    
    
    public function indexAction() {
		
    }
    
}
