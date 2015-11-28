<?php

/**
 * Action Helper for logging into Connect
 * 
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Connect_Controller_Action_Helper_Auth extends Zend_Controller_Action_Helper_Abstract
{

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
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db_legacy_homelet'));
        $authAdapter
            ->setTableName('agentid')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment("?");
        $authAdapter->setIdentity($params['username']);
        $authAdapter->setCredential($params['password']);
        $dbselect = $authAdapter->getDbSelect();
        
        // Link against the agent scheme number
        $dbselect->where('agentschemeno = ?', array($params['agentschemeno']));
        
        return $authAdapter;
    }
    
	public function attemptLogin($loginForm)
    {
		$request = $this->getRequest();
		$auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));
        
        // We have post data from the login form - so attempt a login
        if ($loginForm->isValid($request->getPost()))
        {
            // The forms passed validation so we now need to check the identity of the user
            $adapter = $this->_getAuthAdapter($loginForm->getValues());
            $result = $auth->authenticate($adapter);
            
            if (!$result->isValid())
            {
                // Invalid credentials
                $loginForm->setDescription('Invalid credentials provided');
                return false;
            }
            else
            {

                // Valid credentials - store the details we need from the database and move the user to the index page
                $storage = $auth->getStorage();
                $resultRowObject = $adapter->getResultRowObject
                (
                    array
                    (
                        'agentid',
                        'username',
                        'realname',
                        'level',
                        'agentschemeno',
                        'STATUS', // Status id of user - activated/deactivated
                        'LASTLOGINDATE', // Date of last login, checked for expiry
                    )
                );
                
                // Rewrite LASTLOGINDATE to lastlogindate
                $resultRowObject->lastlogindate = $resultRowObject->LASTLOGINDATE;
                unset($resultRowObject->LASTLOGINDATE);
                
                // Get correct status name from ID
                $agentuser = new Datasource_Core_Agent_UserAccounts();
                $user = $agentuser->getUser($resultRowObject->agentid);
                
                $userstatus = new Model_Core_Agent_UserStatus();
                $resultRowObject->status = strtolower($userstatus->toString($user->status));
                unset($resultRowObject->STATUS);
                
                $agentManager = new Manager_Core_Agent();
                try {

					$resultRowObject->fsastatusabbr = $agentManager->getFsaStatusCode($resultRowObject->agentschemeno);
					$agent = $agentManager->getAgent($resultRowObject->agentschemeno);
					$resultRowObject->agentAccountStatus = $agent->status;
                }
				catch (Exception $e) {
                	// FSA Server is down so we can't currently log agent in
                	$auth->clearIdentity();
                	return false;
                }
                // 'level' is not mapped in the DB to the correct framework
                // constants, do so now.
                // TODO: Fix this so it's not having to mess with
                // translating raw legacy DB values
                switch ($resultRowObject->level)
                {
                    case 1:
                        $resultRowObject->level = Model_Core_Agent_UserRole::BASIC;
                        break;
                    
                    case 3:
                        $resultRowObject->level = Model_Core_Agent_UserRole::MASTER;
                        break;
                }

                // Detect if agent exists in IRIS
                // If the agent has decommission_in_hrt_at set in newagents then this means agent exists in IRIS
                $resultRowObject->isInIris = false;
                if ($agent->decommissionInHrtAt) {
                    $resultRowObject->isInIris = true;
                }

                // If this is an IRIS agent, try to authenticate them
                if ($resultRowObject->isInIris) {

                    /** @var \Iris\Authentication\Authentication $irisAuthentication */
                    $irisAuthentication = \Zend_Registry::get('iris_container')->get('iris.authentication');

                    $authenticationParams = $loginForm->getValues();

                    $authenticateAgent = $irisAuthentication->authenticateAgent(
                        $authenticationParams['agentschemeno'],
                        $authenticationParams['username'],
                        $authenticationParams['password']
                    );

                    if (false === $authenticateAgent) {
                        $auth->clearIdentity();
                        $loginForm->setDescription('Failed to login to referencing system');
                        return false;
                    }

                    $resultRowObject->agentBranchUuid = $authenticateAgent->getAgentBranchUuid();

                    $resultRowObject->canPerformReferencing = true;

                    if ($agent->hasProductAvailabilityMapping) {

                        // Determine if this agent can use referencing
                        /** @var \Guzzle\Common\Collection $products */
                        $products = \Zend_Registry::get('iris_container')
                            ->get('iris.product')
                            ->getProducts(1, 1)
                        ;

                        // If the product count is greater than zero then the agent can perform referencing
                        $resultRowObject->canPerformReferencing = ($products->count() > 0);
                    }
                }
                $resultRowObject->agentsRateID = $agent->agentsRateID;

                $storage->write($resultRowObject);
                return true;
            }
        }
	}

}
