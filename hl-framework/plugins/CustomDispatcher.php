<?php

class Plugin_CustomDispatcher extends Application_Controller_Plugin_Abstract {
	
	/**
	 * Post dispatch debug info
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request) {		
		if (DATABASE_DEBUGGING && Zend_Layout::getMvcInstance()->isEnabled()) {		
			$dbAdapter = Zend_Registry::get('db_legacy_homelet');
			$profiler = $dbAdapter->getProfiler();
			
            //$this->_outputToScreen($profiler, 'Legacy HomeLet');
			
			$dbAdapter = Zend_Registry::get('db_homelet');
			$profiler = $dbAdapter->getProfiler();
			$this->_outputToScreen($profiler, 'HomeLet');
			
			$dbAdapter = Zend_Registry::get('db_homelet_cms');
			$profiler = $dbAdapter->getProfiler();
			$this->_outputToScreen($profiler, 'HomeLet CMS');
			
			$dbAdapter = Zend_Registry::get('db_homelet_admin');
			$profiler = $dbAdapter->getProfiler();
			$this->_outputToScreen($profiler, 'HomeLet Admin');
			
			$dbAdapter = Zend_Registry::get('db_homelet_insurance_com');
			$profiler = $dbAdapter->getProfiler();
			$this->_outputToScreen($profiler, 'HomeLet Insurance');
			
			$dbAdapter = Zend_Registry::get('db_referencing');
			$profiler = $dbAdapter->getProfiler();
			$this->_outputToScreen($profiler, 'HomeLet Referencing');
			
			$dbAdapter = Zend_Registry::get('db_legacy_referencing');
			$profiler = $dbAdapter->getProfiler();
			$this->_outputToScreen($profiler, 'Legacy HomeLet Referencing');
			
			$dbAdapter = Zend_Registry::get('db_legacy_webleads');
			$profiler = $dbAdapter->getProfiler();
			$this->_outputToScreen($profiler, 'HomeLet WebLeads');
			
			$dbAdapter = Zend_Registry::get('db_legacy_homeletDW');
			$profiler = $dbAdapter->getProfiler();
			$this->_outputToScreen($profiler, 'HomeLet DataWarehouse');
			
			$dbAdapter = Zend_Registry::get('db_portfolio');
			$profiler = $dbAdapter->getProfiler();
			$this->_outputToScreen($profiler, 'HomeLet Portfolio');
			
			$dbAdapter = Zend_Registry::get('db_keyhouse');
			$profiler = $dbAdapter->getProfiler();
			$this->_outputToScreen($profiler, 'Keyhouse');
			
			$dbAdapter = Zend_Registry::get('db_letting_agents');
			$profiler = $dbAdapter->getProfiler();
			$this->_outputToScreen($profiler, 'HomeLet Letting Agents');
		}
		 
	}
	
	protected function _outputToScreen($profiler, $databaseName) {
		$totalTime    = $profiler->getTotalElapsedSecs();
		$queryCount   = $profiler->getTotalNumQueries();
		$longestTime  = 0;
		$longestQuery = null;
		$queries = array();
		
		$data = array();
		if ($queryCount>0) {
			foreach ($profiler->getQueryProfiles() as $query) {
			    if ($query->getElapsedSecs() > $longestTime) {
			        $longestTime  = $query->getElapsedSecs();
			        $longestQuery = $query->getQuery();
			    }
			    $queries[] = $query->getQuery();
			}
			$response = $this->getResponse();
			
			$response->appendBody('
				<center><table width="80%">
					<tr>
						<td>Database</td>
						<td>' . $databaseName . '</td>
					</tr>
					<tr>
						<td>Total Queries</td>
						<td>' . $queryCount . '</td>
					</tr>
					<tr>
						<td>Total Execution Time</td>
						<td>' .  $totalTime . ' seconds</td>
					</tr>
					<tr>
						<td>Average Query Length</td>
						<td>' . $totalTime / $queryCount . ' seconds</td>
					</tr>
					<tr>
						<td>Queries per Second</td>
						<td>' . $queryCount / $totalTime . '</td>
					</tr>
					<tr>
						<td>Longest Query Length</td>
						<td>' . number_format($longestTime,8) . ' seconds</td>
					</tr>
					<tr>
						<td>Longest Query</td>
						<td>' . $longestQuery . '</td>
					</tr>
					<tr>
						<td>Query Log</td>
						<td></td>
					</tr>
					');
					foreach ($queries as $query) {
						$response->appendBody('<tr><td></td><td>' . $query . '</td></tr>');
					}
					$response->appendBody('
				</table></center>
				<br /><br />
			');
		}
	}
	
	/**
	 * Re-routes traffic appropriately.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);
        
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $module = $request->getModuleName();
			
        // Perform authentication
        switch($module) {
            case 'cms-admin':
                $auth = Zend_Auth::getInstance();
                $auth->setStorage(new Zend_Auth_Storage_Session('hl_admin'));
                if (!$auth->hasIdentity()) {
                    $request->setControllerName('index');
                    $request->setActionName('login');
                }
                break;

            case 'connect':
            	$params = Zend_Registry::get('params');
            	$auth = Zend_Auth::getInstance();
                $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));
                
                // Set the session expiry timeout time
                $sessionTimeOutSeconds = $params->connect->loginexpiry->sessionTimeOutSeconds;
                $session = new Zend_Session_Namespace('hl_connect'); 
                $session->setExpirationSeconds($sessionTimeOutSeconds);
                
                if (!$auth->hasIdentity() && $action != 'lost-login' && $action != 'reset-password')
                {
                    $request->setControllerName('index');
                    $request->setActionName('login');
                }
                else if ($auth->hasIdentity())
                {
                    // Ignore logout - for when redirecting back to login, and own account expiration/deactivated actions.
                    if (!in_array($action, array('logout', 'account-deactivated', 'account-expired')))
                    {
                        // Perform account validation checks and display an error message in
                        // the event of...
                        // - the account is deactivated
                        // - the account expiry time from the last login has passed
                        $user_status              = $auth->getStorage()->read()->status;
                        $user_lastlogindate       = $auth->getStorage()->read()->lastlogindate;
                        $userlevel                = $auth->getStorage()->read()->level;
                        $agentschemenumber        = $auth->getStorage()->read()->agentschemeno;
                        $fsastatusabbr            = $auth->getStorage()->read()->fsastatusabbr;
						$agentAccountStatus		  = $auth->getStorage()->read()->agentAccountStatus;

						if($agentAccountStatus == Model_Core_Agent_Status::ON_HOLD) {
							
							$request->setControllerName('index');
                            $request->setActionName('agent-fsa-nostatus');
						}
                        else if ($user_status == 'deactivated')
                        {
                            // Deactivated, forward to new action to deal with deactivated users
                            $request->setControllerName('index');
                            $request->setActionName('account-deactivated');
                            
                            // Important! Clears the successful authentication token
                            // given now that we know that the users session has expired
                            // and should not be permitted access.
                            $storage = $auth->getStorage();
                            $storage->clear();
                            
                            break;
                        }
                        else if ($fsastatusabbr == null || $fsastatusabbr == '')
                        {
                            // Check FSA status. If the user is of level 3, display a message about their status
                            // Otherwise display a generic error
                            if ($userlevel == Model_Core_Agent_UserRole::MASTER)
                            {
                                $request->setControllerName('index');
                                $request->setActionName('agent-fsa-nostatus');
                            }
                            else
                            {
                                $request->setControllerName('index');
                                $request->setActionName('account-deactivated');
                            }
                            
                            // Important! Clears the successful authentication token
                            // given now that we know that the users session has expired
                            // and should not be permitted access.
                            $storage = $auth->getStorage();
                            $storage->clear();
                            
                            break;
                        }
                        else if ($user_lastlogindate != '0000-00-00')
                        {
                            // Check account expiry
                            $expiry = 0;
                            
                            if (@isset($params->connect->loginexpiry->time))
                                $expiry = $params->connect->loginexpiry->time;
                            
                            // Add x worth days as per configuration and convert the unix
                            // timestamp to mysql date format for easy comparison below.
                            $unixTimeStamp = strtotime("+$expiry day", strtotime($user_lastlogindate));
                            $permissableDate = date("Y-m-d", $unixTimeStamp);
                            
                            // If the current date is greater than the last login period
                            // + x days, the account has not been used for x days and so
                            // has expired
                            if(date("Y-m-d") > $permissableDate)
                            {
                                //The user account is expired. Update the User entity to
								//reflect this.
								$userManager = new Manager_Core_Agent_User();
								$user = $userManager->getUser($auth->getStorage()->read()->agentid);
								$user->status = Model_Core_Agent_UserStatus::DEACTIVATED;
								$userManager->setUser($user);
								
								// forward to new action to deal with expired user accounts
                                $request->setControllerName('index');
                                $request->setActionName('account-expired');
                                
                                // Important! Clears the successful authentication token
                                // given now that we know that the users session has expired
                                // and should not be permitted access.
                                $storage = $auth->getStorage();
                                $storage->clear();
                                
                                break;
                            }
                        }
                        
                        // Update the existing last login time in the database and the session data to the current date
                        $agentid = $auth->getStorage()->read()->agentid;
                        $currentdate = new Zend_Date();
                        
                        $agentuser = new Datasource_Core_Agent_UserAccounts();
                        $agentuser->setLastLoginDate($currentdate, $agentid);
                        
                        $storage = $auth->getStorage();
                        $data = $storage->read();
                        $data->lastlogindate = $currentdate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
                        $storage->write($data);
                    }
                }
                
                break;
            
            case 'agent-admin-suite':
            	$auth = Zend_Auth::getInstance();
        		$auth->setStorage(new Zend_Auth_Storage_Session('hl_admin'));
       			if (!$auth->hasIdentity()) {
            		$request->setControllerName('index');
                    $request->setActionName('login');
        		}
            	break;
            	
            case 'landlords-referencing':
	            	// TODO: This is fairly dirty - it's excluding pages that we want to use in the CMS.
	            	//       Could do with restructuring referencing at some point so the application process
	            	//       isn't in the way for CMS pages

	            	if ($action!='products' && $action!='rent-guarantee-products') {
	            	    $this->_referencingPreDespatch($request);
	            	}

                    break;

            default:
                // Set default expiration seconds for homelet customer portal access
                $params = Zend_Registry::get('params');
                $sessionTimeOutSeconds = $params->myhomelet->loginexpiry->sessionTimeOutSeconds;
                $session = new Zend_Session_Namespace('homelet_customer');
                $session->setExpirationSeconds($sessionTimeOutSeconds);
        }

        $front = Zend_Controller_Front::getInstance();
        // Check to see if this request is actually dispatchable
		
        if( !$this->_actionExists($request) ) {
			
            // Is this a connect request?
            $module = $request->getModuleName();
            if ($module == 'connect') {
                $request->setControllerName('index');
                $request->setActionName('view-static-page');
            } else {
                // Not a Connect request, into the CMS we go!
                $request->setModuleName('cms');
                $request->setControllerName('index');
                $request->setActionName('view-page');
            }
        }
        
        // Set a custom layout route
        $layoutPath = APPLICATION_PATH . '/modules/' . $request->getModuleName() . '/layouts/scripts/';
        Zend_Layout::getMvcInstance()->setLayoutPath($layoutPath);
    }


    /**
     * Dedicated Referencing pre-despatcher.
     *
     * Despatches Customer and ReferenceSubject data entry. Customer data entry is
     * where the reference is logged by the customer. ReferenceSubject data entry is
     * where the reference is logged by the reference subject, after they have been
     * sent a link to complete the reference.
     *
     * @param Zend_Controller_Request_Abstract $request
     *
     * @return void
     *
     * @todo
     * Decode the customerToken
     */
    protected function _referencingPreDespatch(Zend_Controller_Request_Abstract $request) {

    	//Identify if the user is the ReferenceSubject. The ReferenceSubject does not
    	//need to log in.
    	$session = new Zend_Session_Namespace('referencing_global');
    	if($session->userType == Model_Referencing_ReferenceUserTypes::REFERENCE_SUBJECT) {

    		return;
		}
	    
    	$customerToken = $request->getParam('customerToken');
    	$userToken = $request->getParam('userToken');
    	$refNo = $request->getParam('refNo');

        if($session->customerToken){
            $customerToken = $session->customerToken;
            $refNo = $session->refNo;
        }		
    	//Pre-despatch checks for the reference subject user submitting an email-link-to-tenant
    	//reference.
    	if(!empty($userToken) && !empty($refNo)) {

    		if(!$this->_referenceSubjectLinkPreDespatch($request, $userToken, $refNo)) {

    			//User credentials incorrect. Re-route to start.
    			$request->setActionName('error');
    		}
    		return;
    	}


    	//Pre-despatch checks for PLL user finalizing an email-link-to-tenant reference.
    	if(!empty($customerToken) && !empty($refNo)) {

    		if(!$this->_privateLandlordLinkPreDespatch($request, $customerToken, $refNo)) {

    			//User credentials incorrect. Re-route to start.
    			$request->setActionName('error');
    		}
    		return;
    	}
    	//If here then the user is the reference customer (Private Landlord). Ensure
    	//they are correctly logged in.
    	$session->userType = Model_Referencing_ReferenceUserTypes::PRIVATE_LANDLORD;

        // Set default expiration seconds for homelet customer portal access
        $params = Zend_Registry::get('params');
        $sessionTimeOutSeconds = $params->myhomelet->loginexpiry->sessionTimeOutSeconds;
        $session = new Zend_Session_Namespace('homelet_customer');
        $session->setExpirationSeconds($sessionTimeOutSeconds);

        $refSession = new Zend_Session_Namespace('referencing_global');

    	$auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        if (!$auth->hasIdentity() && $refSession->awaitingvalidation != 1) {

            // User is not logged in. Unless the user is proceeding to the
            //   registration page or needs direct access to the application
            //   download, re-rowt them to the login page.
            if (!preg_match(
                '/^(start|login|register|download-application)$/i',
                $request->getActionName()
            )) {
                $request->setActionName('login');
            }
        }
    }


    /**
     * Executes checks when the user is a reference subject completing an email-link-to-tenant.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param string $userToken
     * @param string $refNo
     *
     * @return boolean
     */
    protected function _referenceSubjectLinkPreDespatch(Zend_Controller_Request_Abstract $request, $userToken, $refNo) {

    	$session = new Zend_Session_Namespace('referencing_global');


    	//Check the validity of the access.
    	$params = Zend_Registry::get('params');
    	$hashingString = $params->pll->emailLink->security->securityString;
    	$leeWay = $params->pll->emailLink->security->securityTokenTimeLeewayUser;

    	$securityManager = new Application_Core_Security($hashingString, true, $leeWay);
        $securityCheck = $securityManager->authenticate($userToken, array('refNo' => $refNo));
        if($securityCheck['result']) {// && ($securityCheck['data']['refno'] == $refNo)) {
            //Security token from a tenant or guarantor e-mail link successfully passed, note in session
            if (!isset($session->security)) {
                $session->security = new StdClass();
            }

            $session->security = new StdClass();
            $session->security->origin = Model_Referencing_ReferenceUserTypes::REFERENCE_SUBJECT;
            $session->security->refNo = $refNo;
        }
        else {
            // Something went wrong, eg, hash didn't match or time was out of bounds
            if (!isset($session->security)) {
                $session->security = new StdClass();
            }

            $session->security->error = $securityCheck['error'];
        	return false;
        }


        //Security has passed, next load up the reference.
    	$referenceManager = new Manager_Referencing_Reference();
    	$reference = $referenceManager->getMinimalReference($refNo);

    	if(empty($reference)) {

    		unset($session->userType);
    		return false;
    	}
    	else {

    		$session->userType = Model_Referencing_ReferenceUserTypes::REFERENCE_SUBJECT;
    		$session->referenceId = $reference->internalId;
    	}

    	return true;
    }


    /**
     * Executes checks when the user is a PLL finalizing an email-link-to-tenant.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param string $customerToken
     * @param string $refNo
     *
     * @return boolean
     */
    protected function _privateLandlordLinkPreDespatch(Zend_Controller_Request_Abstract $request, $customerToken, $refNo) {

       	$session = new Zend_Session_Namespace('referencing_global');


       	$referenceManager = new Manager_Referencing_Reference();
    	$reference = $referenceManager->getReference($refNo);


    	//Check the validity of the access.
    	$params = Zend_Registry::get('params');
    	$hashingString = $params->pll->emailLink->security->securityString;
    	$leeWay = $params->pll->emailLink->security->securityTokenTimeLeewayUser;

    	$securityManager = new Application_Core_Security($hashingString, true, $leeWay);
        $securityCheck = $securityManager->authenticate(
        	$customerToken, array('refNo', 'customerId'));

        if($securityCheck['result']) {
        	//Ensure the customer identifier extracted from the $customerToken matches the identifier
        	//stored in the reference.
        	$customerId = $securityCheck['data']['customerId'];
        	if($customerId != $reference->customer->customerId) {

        		$session->security->error = 'Customer identifier does not match';
        		return false;
        	}
        } else {
        	// Something went wrong, eg, hash didn't match or time was out of bounds
            $session->security->error = $securityCheck['error'];
        	return false;
        }

    	//Log the customer in.
    	$customerManager = new Manager_Referencing_Customer();
    	$customer = $customerManager->getCustomer($customerId);

    	$loginManager = new Manager_Referencing_Login();
		$loginManager->logUserIn(
			$customer->getEmailAddress(),
			$customer->getPassword()
		);


        //Set the relevant session variables so that the PLL can proceed the reference.
    	$session->referenceId = $reference->internalId;
        $session->productName = $reference->productSelection->product->key;
        $session->userType = Model_Referencing_ReferenceUserTypes::PRIVATE_LANDLORD;
        $session->customerToken = $customerToken;
        $session->refNo = $refNo;
        return true;
    }
}
