<?php
require_once('ConnectAbstractController.php');
class Connect_InsuranceController extends ConnectAbstractController
{
    /**
     * List of FSA statuses that cannot access *most* insurance functionality.
     * The commission calculator is accessible to all.
     * TODO: Parameterise.
     *
     * @var array
     */
    protected $_blockFsaStatus;

    public function init()
    {
        $this->_blockFsaStatus = array('IO', 'IAR');

        parent::init();
    }

    /**
     * Responsible for ajax validation of search criteria.
     */
    public function validateSearchAction()
    {
        // Agents with wrong FSA status cannot access insurance functionality.
        if (in_array($this->_fsastatusabbr, $this->_blockFsaStatus)) {
            return;
        }

        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $pageForm = new Connect_Form_InsuranceSearchCustomer();
        $postData = $this->getRequest()->getParams();

        if($pageForm->isValid($postData)) {
            $return['errorJs'] = '';
            $return['errorCount'] = '';
            $return['errorHtml'] = '';
            $return['postData'] = '';
        } else {
            $errorMessages = $pageForm->getMessages();
            $return['errorJs'] = $errorMessages;
            $return['errorCount'] = count($errorMessages);
            $return['errorHtml'] = $this->view->partial('error/error-listing.phtml', array('errors' => $errorMessages));
            $return['postData'] = $postData;
        }

        echo Zend_Json::encode($return);
    }

    /**
     * Run an insurance customer search from the legacy DB in a pop-up.
     *
     * @return void
     */
    public function searchCustomerAction()
    {
        // Agents with wrong FSA status cannot access insurance functionality.
        if (in_array($this->_fsastatusabbr, $this->_blockFsaStatus)) {
            return;
        }

        // Instantiate form definition
        $pageForm = new Connect_Form_InsuranceSearchCustomer();

        // Validate form if GETed
        $request = $this->getRequest();
        if ($request->isGet() && !is_null($request->getParam('submit')) && $request->getParam('submit') == 'Search') {
            //Whether valid or invalid, display the results on the main screen rather
            //than the pop-up.
            $this->_helper->viewRenderer('search-customer-results');

            $getData = $request->getQuery();
            if ($pageForm->isValid($getData)) {
                // Put search criteria into munting array
                $criteria = array(
                    'firstName' => ((isset($getData['firstName'])) ? $getData['firstName'] : ''),
                    'lastName'  => ((isset($getData['lastName'])) ? $getData['lastName'] : ''),
                    'address1'  => ((isset($getData['address1'])) ? $getData['address1'] : ''),
                    'address2'  => ((isset($getData['address2'])) ? $getData['address2'] : ''),
                    'postcode'  => ((isset($getData['postcode'])) ? $getData['postcode'] : ''),
                    'telephone' => ((isset($getData['telephone'])) ? $getData['telephone'] : ''),
                    'email'     => ((isset($getData['email'])) ? $getData['email'] : '')
                );

                // Perform search using the Insurance MUNT Manager class
                $insMuntManager = new Manager_Insurance_Munt();
                $searchResults = $insMuntManager->getCustomers($this->_agentSchemeNumber, $criteria);
                $this->view->searchResults = $searchResults;
            } else {
                $this->view->searchResults = array();
            }
        }
        else {
            // Pop-up results need pop-up layout. This suppresses the layout
            //and displays just the view associated with this method.
            $this->_helper->layout->setLayout('popup');
        }

        $this->view->form = $pageForm;
    }

    /**
     * Run an insurance policy search from the legacy DB in a pop-up.
     *
     * @return void
     */
    public function searchPolicyAction()
    {
        // Agents with wrong FSA status cannot access insurance functionality.
        if (in_array($this->_fsastatusabbr, $this->_blockFsaStatus)) {
            return;
        }

        // Instantiate form definition
        $pageForm = new Connect_Form_InsuranceSearchPolicy();

        // Validate form if GETed
        $request = $this->getRequest();
        if ($request->isGet() && !is_null($request->getParam('submit')) && $request->getParam('submit') == 'Search') {
            $getData = $request->getQuery();
            if ($pageForm->isValid($getData)) {
                // Put search criteria into munting array
                $validatedData = $pageForm->getValues();
                $criteria = array(
                    'campRefNo'     => ((isset($validatedData['campRefNo'])) ? $validatedData['campRefNo'] : ''),
                    'policyNo'      => ((isset($validatedData['policyNo'])) ? $validatedData['policyNo'] : ''),
                    'address1'      => ((isset($validatedData['address1'])) ? $validatedData['address1'] : ''),
                    'address3'      => ((isset($validatedData['address3'])) ? $validatedData['address3'] : ''),
                    'postcode'      => ((isset($validatedData['postcode'])) ? $validatedData['postcode'] : ''),
                    'paymentRef'    => ((isset($validatedData['paymentRef'])) ? $validatedData['paymentRef'] : '')
                );

                // Any particular sorting?
                $sort = (isset($getData['sort'])) ? $getData['sort'] : '';

                // Perform search using the Insurance MUNT Manager class
                $insMuntManager = new Manager_Insurance_Munt();
                $searchResults = $insMuntManager->searchLegacyPolicies($this->_agentSchemeNumber, $criteria, $sort);

                // Show user search results
                $this->_helper->viewRenderer('search-policy-results');
                $this->view->searchResults = $searchResults;
            }
        } else {
            // Pop-up results need pop-up layout
            $this->_helper->layout->setLayout('popup');
        }

        $this->view->form = $pageForm;
    }

    /**
     * Displays the customer and policy summary + dynamic options for a policy
     * plucked from the legacy DB by its policyno taken from a GET parameter.
     *
     * @return void
     */
    public function showPolicyAction()
    {
        // Agents with wrong FSA status cannot access insurance functionality.

        $this->view->pageTitle = 'Policy Details';

        $baseInsuranceUrl = $this->_params->connect->baseUrl->insurance;
        // Non SSL URL must be used for document production.  :-/
        $baseInsuranceUrlNoSsl = preg_replace('/^https?(.*)/i', 'http$1', $baseInsuranceUrl);
        $request = $this->getRequest();
        
        // See if there's a GET-based policyno
        if ($request->isGet() && !is_null($request->getParam('policyno')))
        {
            
            $usermanager = new Manager_Core_Agent_User();
            $user = $usermanager->getUser($this->_agentId);

            // Fetch policy by policyno using the Insurance MUNT Manager class
            $insMuntManager = new Manager_Insurance_Munt();
            $policyResults = $insMuntManager->getPolicy($request->getParam('policyno'));

            if ($policyResults['companyschemenumber'] == $this->_agentSchemeNumber)
            {
                if (in_array($this->_fsastatusabbr, $this->_blockFsaStatus)) {
                    return;
                }
                $coverResults = $insMuntManager->getCover($request->getParam('policyno'));
                $customerResults = $insMuntManager->getCustomer($policyResults['refno']);

                // Update start/end dates for Zend Dates
                if($policyResults['startdate'] == '0000-00-00') {

                    $policyResults['startdate'] = null;
                }
                else {

                    $policyResults['startdate'] = new Zend_Date($policyResults['startdate']);
                }

                if($policyResults['enddate'] == '0000-00-00') {

                    $policyResults['enddate'] = null;
                }
                else {

                    $policyResults['enddate'] = new Zend_Date($policyResults['enddate']);
                }
                
                $documentManager = new Manager_Insurance_Document();
                $docHistoryResults = $documentManager->getDocuments($request->getParam('policyno'), null, array('holder', 'agent'));

                // Show user search results
                $this->view->policy         = $policyResults;
                $this->view->cover          = $coverResults;
                $this->view->customer       = $customerResults;
                $this->view->baseUrl        = $baseInsuranceUrl;
                $this->view->baseUrlNoSsl   = $baseInsuranceUrlNoSsl;
                $this->view->agentsEmail    = $user->email->emailAddress;
                $this->view->docHistoryResults = $docHistoryResults;
            }
            else
            {
                // Not the agents policy, protect the display of data, report an error
                $this->view->policynumber = $policyResults['policynumber'];
                $this->render('show-policy-denied');
            }
        }
    }

    /**
     * Generate a new Landlord's Low Cost policy - popup window to legacy
     * system.
     *
     * @todo The legacy URL format string should be parameterised.
     *
     * @return void
     */
    public function landlordsLowCostAction()
    {
        // Agents with wrong FSA status cannot access insurance functionality.
        if (in_array($this->_fsastatusabbr, $this->_blockFsaStatus)) {
            return;
        }

        $baseInsuranceUrl = $this->_params->connect->baseUrl->insurance;
        $request = $this->getRequest();
        $instruction = 'displayFrontPage';
        $extra = '';
        if (!is_null($request->getParam('policyno'))){
            $instruction="calculateQuote";
            $extra="&existingQuote=true&"
                    ."policyNumber_1={$request->getParam('policyno')}&"
                    ."policyNumber=" . substr($request->getParam('policyno'), 0, -3)
                    ."&refNo={$request->getParam('refno')}";
        }   
        $iddid=$this->iddsupport($request);
        $jumpUrl = "{$baseInsuranceUrl}cgi-bin/frontEnd.cgi?"
            . "useBrandedFrontend=yes&"
            . "instruction={$instruction}&"
            . "agentSchemeNumber={$this->_agentSchemeNumber}&"
            . "policiesToDisplay=lowcostlandlords&"
            . "brand=connect&"
            . "origin=17&"
            . "source=newCustomer&"
            . "iddsupportid={$iddid}&"
            . "agentid={$this->_agentId}{$extra}";

        $this->_helper->redirector->gotoUrlAndExit($jumpUrl);
    }

/**
     * Generate a new Landlord's Plus policy - popup window to legacy
     * system.
     *
     * @todo The legacy URL format string should be parameterised.
     *
     * @return void
     */
    public function landlordsPlusAction()
    {
        // Agents with wrong FSA status cannot access insurance functionality.
        if (in_array($this->_fsastatusabbr, $this->_blockFsaStatus)) {
            return;
        }

        $request = $this->getRequest();
        $iddid=$this->iddsupport($request);
        if($request->isGet() && !is_null($request->getParam('policyno')))
        {
            // Convert
            $baseInsuranceUrl = $this->_params->connectUrl->convertLandlordsPlusPolicy;

            $jumpUrl = "{$baseInsuranceUrl}?"
                      ."&existingQuote=true&"
                      ."agentschemeno={$this->_agentSchemeNumber}&"
                      ."policyNumber_1={$request->getParam('policyno')}&"
                      ."policyNumber=".$request->getParam('policyno')
                      ."&refNo={$request->getParam('refno')}";
        }
        else
        {
            // Incept            
            $baseInsuranceUrl = $this->_params->connect->baseUrl->lip;
            
            $jumpUrl = $baseInsuranceUrl."landlords/insurance-quote?"                
                . "agentschemeno={$this->_agentSchemeNumber}&"
                . "agentid={$this->_agentId}&"
                . "action=newLandlordsPolicy&"
                . "brand=default&"               
                . "isPopUp=true&"
                . "origin=17&"
                . "iddsupportid={$iddid}&"
                . "onclick=loading%28false%29&customerRefno=";
        }

        $this->_helper->redirector->gotoUrlAndExit($jumpUrl);
    }

    /**
     * Generate a new Tenant's Contents Plus policy - popup window to legacy
     * system.
     *
     * @todo The legacy URL format string should be parameterised.
     *
     * @return void
     */
    public function tenantsContentsPlusAction()
    {
        // Agents with wrong FSA status cannot access insurance functionality.
        if (in_array($this->_fsastatusabbr, $this->_blockFsaStatus)) {
            return;
        }

        $baseInsuranceUrl = $this->_params->connect->baseUrl->insurance;
        $request = $this->getRequest();
        $instruction = 'displayFrontPage';
        $extra = '';
        if (!is_null($request->getParam('policyno'))){
            $instruction="calculateQuote";
            $extra="&existingQuote=true&"
                    ."policyNumber_1={$request->getParam('policyno')}&"
                    ."policyNumber=" . substr($request->getParam('policyno'), 0, -3)
                    ."&refNo={$request->getParam('refno')}";
        }
        $iddid=$this->iddsupport($request);
        $jumpUrl = "{$baseInsuranceUrl}cgi-bin/frontEnd.cgi?"
            . "useBrandedFrontend=yes&"
            . "instruction={$instruction}&"
            . "agentSchemeNumber={$this->_agentSchemeNumber}&"
            . "policiesToDisplay=tenantsp&"
            . "brand=connect&"
            . "origin=17&"
            . "source=newCustomer&"
            . "iddsupportid={$iddid}&"
            . "agentid={$this->_agentId}{$extra}&"
            . "covertype={$request->getParam('covertype')}";

        $this->_helper->redirector->gotoUrlAndExit($jumpUrl);
    }
    

    /**
     * Heinous copy of the old Connect insurance commission calculator.
     *
     * @todo: Rewrite, restyle.
     *
     * @return void
     */
    public function commissionCalculatorAction()
    {
        // Pop-up results need pop-up layout
        $this->_helper->layout->setLayout('popup');

        // Calculate commission rates if a GET or POST var sets the policy
        //   amount
        $request = $this->getRequest();
        $amount = preg_replace('/[^\d\.]/', '', $request->getParam('amount'));

        $commRate = $this->_agentObj->commissionRate;
        $newCommRate = $this->_agentObj->newBusinessCommissionRate;

        if ($amount != '') {
             $this->view->values = array(
                'amount' => $amount,
                'commissionRate' =>         number_format($commRate * 100, 2),
                'newBusCommissionRate' =>   number_format($newCommRate * 100, 2),
                'inceptionMonthly' =>       number_format($amount * $newCommRate / 12, 2),
                'inceptionAnnual' =>        number_format($amount * $newCommRate, 2),
                'renewalMonthly' =>         number_format($amount * $commRate / 12, 2),
                'renewalAnnual' =>          number_format($amount * $commRate, 2)
            );
        } else {
            $this->view->values = array(
                'amount' => '',
                'commissionRate' =>         number_format($commRate * 100, 2),
                'newBusCommissionRate' =>   number_format($newCommRate * 100, 2),
                'inceptionMonthly' =>       '0.00',
                'inceptionAnnual' =>        '0.00',
                'renewalMonthly' =>         '0.00',
                'renewalAnnual' =>          '0.00'
            );
        }
    }

    /**
     * collect info for idd document.
     *
     * @todo: Rewrite, restyle.
     *
     * @return void
     */
    private function iddsupport($request)
    {
        $idd = new Datasource_Insurance_IddSupport();
        $insertArray = array();
        if (!is_null($request->getParam('policyno'))){
              
            $insertArray['policynumber']=$request->getParam('policyno');
        }
        else{
            $insertArray['policynumber']="";
        }
        $insertArray['agentschemeno']=$this->_agentSchemeNumber;
        $insertArray['csuid']=0;
        $insertArray['FSA_status']=$this->_fsastatusabbr;
        $insertArray['origsaleid']=10;
        $insertArray['callerid']=1;
        $iddid=$idd->setIddSupport($insertArray);
        return $iddid;
    }
    
    public function fetchDocumentAction()
    {
        $request = $this->getRequest();        
        $hash = $request->getParam('requestHash');
        $docName = $request->getParam('documentName');
        
        $docs = new Service_Insurance_Document;        
        
        // Insurance Document Service Call - fetchDocument()
        $response = $docs->fetchDocument($hash, $docName);
                    
        $this->_helper->redirector->gotoUrlAndExit($response);
    }
    
    /**
     * Quote my tenant function
     */
    public function  quoteMyTenantAction(){
    	// Intantiate form definition
    	$pageForm = new Connect_Form_InsuranceQuoteMyTenant();
    	$request = $this->getRequest();
		
    	if ($request->isPost()) {
    		if ($pageForm->isValid($request->getPost())) {
    			// Stuff is valid - Write to DB and redirect to confirmation
    			$this->_saveQuote ($request->getPost());
    			$this->_helper->getHelper('Redirector')->goToUrl('/insurance/request-sent');
    			die();
    		}else{
    			// Tell user there are problems
    			$this->_helper->flashmessages->addMessage('Problem(s) in form data:');
    			$this->_helper->flashmessages->addMessage($pageForm->getMessagesFlattened(true));
    		}
    	}
    	$this->view->flashMessages = $this->_helper->flashmessages->getCurrentMessages();
    	$this->view->form = $pageForm;
  		
    }
    
    /**
     * Quote my landlord function
     */
    public function  quoteMyLandlordAction(){
    	// Intantiate form definition
    	$pageForm = new Connect_Form_InsuranceQuoteMyLandlord();
    	$request = $this->getRequest();
		
    	if ($request->isPost()) {
    		if ($pageForm->isValid($request->getPost())) {
    			// Stuff is valid - Write to DB and redirect to confirmation
    			$this->_saveQuote ($request->getPost());
    			$this->_helper->getHelper('Redirector')->goToUrl('/insurance/request-sent');
    			die();
    		}else{
    			// Tell user there are problems
    			$this->_helper->flashmessages->addMessage('Problem(s) in form data:');
    			$this->_helper->flashmessages->addMessage($pageForm->getMessagesFlattened(true));
    		}
    	}
    	$this->view->flashMessages = $this->_helper->flashmessages->getCurrentMessages();
    	$this->view->form = $pageForm;
    }
    
    /**
     * Request sent confirmation page for quote my tenant/landlord
     * 
     */
    
    public function requestSentAction(){
    	
    }
    
    /**
     * Save the request information
     */
    private function _saveQuote ($quoteData){
    	$cleanData = array
    	(
    			'title' => $quoteData['title'],
    			'first_name' => $quoteData['first_name'],
    			'last_name' => $quoteData['last_name'],
    			'phone_number' => $quoteData['phone_number'],
    			'mobile_number' => $quoteData['mobile_number'],
    			'ins_house_number_name' => $quoteData['ins_house_number_name'],
    			'ins_postcode' => $quoteData['ins_postcode'],
    			'ins_address' => $quoteData['ins_address'],
    			'additional_information' => $quoteData['additional_information'],
    			'confirmation_statement' => $quoteData['confirmation_statement'],
    			'prospector' => ($quoteData['prospector'] == "landlord") ? Model_Insurance_QuoteEnquiry::LANDLORD : Model_Insurance_QuoteEnquiry::TENANT,
    			'agentschemeno' => $this->_agentSchemeNumber
    			
    	);

    	$rsEnquiry = new Datasource_Connect_QuoteEnquiryRequest();
    	$rsEnquiry->addEnquiry($cleanData);
    }
}
