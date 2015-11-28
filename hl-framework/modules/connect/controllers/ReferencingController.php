<?php
require_once('ConnectAbstractController.php');
class Connect_ReferencingController extends ConnectAbstractController
{

    /**
     * Jump securely to [legacy] referencing to begin a new reference.
     *
     * @todo The legacy URL format string should be parameterised.
     *
     * @return void
     */
    public function newAction()
    {
        // Instantiate security manager for generating MAC
        $securityManager = new Application_Core_Security($this->_params->connect->ref->security->securityString->agent);
        $macToken = $securityManager->generate(
            array(
                $this->_agentSchemeNumber,
                $this->_agentId
            )
        );

        $baseReferencingUrl = $this->_params->connect->baseUrl->referencing;

        $jumpUrl = "{$baseReferencingUrl}frontEnd/referencingController.php?"
            . "refno=&originalrefno=&isGuarantor=&"
            . "agentschemeno={$this->_agentSchemeNumber}&"
            . "agentid={$this->_agentId}&"
            . "rentGuarantee=true&refbrand=connecthomelet&origin=connect&" // Why is rentGuarantee hardcoded to true?
            . "agentToken={$macToken}=&newRefBegin=1";
        $this->_helper->redirector->gotoUrlAndExit($jumpUrl);
    }

    /**
     * Show the HomeLet Verify page - used to be CheckRight
     */
    public function homeletVerifyAction()
    {
        $agent = new Datasource_Core_AgentUser();
        $canDisplayCheckRight = $agent->canDisplayCheckRight($this->_agentSchemeNumber, $this->_agentUserName);
        if ( ! $canDisplayCheckRight) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $this->view->phoneNumber = $this->_params->connect->settings->checkRight->phoneNumber;
        $this->view->blogUrl = $this->_params->connect->settings->checkRight->blogUrl;
        $this->view->faqsUrl = $this->_params->connect->settings->checkRight->faqsUrl;
        $this->view->homeOfficeUrl = $this->_params->connect->settings->checkRight->homeOfficeUrl;
        $this->view->checkUrl = $this->_params->connect->settings->checkRight->checkUrl;
    }

    /**
     * Validates the search
     */
    public function validateSearchAction()
    {
        $this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $pageForm = new Connect_Form_ReferencingSearch();
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
     * Run a reference search from the legacy DB in a pop-up.
     *
     * @return void
     */
    public function searchAction()
    {
        // Intantiate form definition
        $pageForm = new Connect_Form_ReferencingSearch();

        // Validate form if GETed
        $request = $this->getRequest();
        if ($request->isGet() && !is_null($request->getParam('submit')) && $request->getParam('submit') == 'Search') {
            $getData = $request->getQuery();
            $this->_helper->viewRenderer('search-results');
            if ($pageForm->isValid($getData)) {
                //The page number, if requested, indicates which page of search
                //results to display.
                if(empty($getData['pageNumber'])) {
                    $pageNumber = 1;
                } else {
                    $pageNumber = $getData['pageNumber'];
                }

                //Assemble the search criteria into an array.
                $criteria = array(
                    'refno'     => ((isset($getData['refno'])) ? $getData['refno'] : ''),
                    'firstname' => ((isset($getData['firstname'])) ? $getData['firstname'] : ''),
                    'lastname'  => ((isset($getData['lastname'])) ? $getData['lastname'] : ''),
                    'address'   => ((isset($getData['address'])) ? $getData['address'] : ''),
                    'town'      => ((isset($getData['town'])) ? $getData['town'] : ''),
                    'postcode'  => ((isset($getData['postcode'])) ? $getData['postcode'] : ''),
                    'state'     => ((isset($getData['state'])) ? $getData['state'] : ''),
                    'type'      => ((isset($getData['type'])) ? $getData['type'] : '')
                );

                $orderBy = (isset($getData['sort'])) ? $getData['sort'] : null;

                $rowLimit = $getData['rows'];
                if(empty($rowLimit)) {
                    $rowLimit = Model_Referencing_SearchResult::ALL_ROWS;
                }

                //Perform search using the Referencing MUNT Manager class
                $refMuntManager = new Manager_ReferencingLegacy_Munt();
                $searchResult = $refMuntManager->searchLegacyReferences(
                    $this->_agentSchemeNumber,
                    $criteria,
                    $orderBy,
                    $pageNumber,
                    $rowLimit);

                // Show user search results
                $this->view->searchResults = $searchResult->results;
                $this->view->previousPageNumber = $searchResult->previousPageNumber;
                $this->view->currentPageNumber = $searchResult->currentPageNumber;
                $this->view->nextPageNumber = $searchResult->nextPageNumber;
                $this->view->maxPageNumber = $searchResult->maxPageNumber;
            } else {
                $this->view->searchResults = array();
                $this->view->previousPageNumber = null;
                $this->view->currentPageNumber = 1;
                $this->view->nextPageNumber = null;
                $this->view->maxPageNumber = 1;
            }
        } else {
            // Pop-up results need pop-up layout. This suppresses the layout
            //and displays just the view associated with this method.
            $this->_helper->layout->setLayout('popup');
        }

        $this->view->form = $pageForm;
    }

    /**
     * Displays the "ticks and crosses" reference summary + dynamic options for
     * a reference plucked from the legacy DB by its refno taken from a GET
     * parameter.
     *
     * @return void
     */
    public function summaryAction()
    {
        $this->view->headLink()->appendStylesheet('/assets/connect/css/referencingSummary.css');

        // See if there's a GET-based refno
        $request = $this->getRequest();
        if ($request->isGet() && !is_null($request->getParam('refno'))) {
            // Fetch reference by refno using the Referencing MUNT Manager class
            $refMuntManager = new Manager_ReferencingLegacy_Munt();
            $reference = $refMuntManager->getReference($request->getParam('refno'));


            // Check the owner of this reference
            if ($this->_agentSchemeNumber != $reference->customer->customerId) {
                // The session user is not the owner of this reference.
                // Renider auth failed status code and present error screen
                $this->getResponse()->setHttpResponseCode(401);
                throw new Exception('Authorisation failed when retreiving reference');
                return;
            }


            // Find related references
            $searchResult = $refMuntManager->searchLegacyReferences(
                $this->_agentSchemeNumber,
                array('proprefno' => $reference->propertyLease->id),
                Model_Referencing_SearchResult::STARTDATE_ASC,
                1,
                Model_Referencing_SearchResult::TWENTY_FIVE_ROWS
            );

            // Use the linked references array to determine if the current
            //   reference is complete
            // TODO: De-dirty this, shouldn't use the (even dirtier) linked refs
            //   array
            $refComplete = true;
            foreach($searchResult->results as $linkedRef) {
                if ($linkedRef['RefNo'] == $reference->externalId) {
                    if (substr($linkedRef['refStatus'], 0, 10) == 'Incomplete') {
                        $refComplete = false;
                    }
                }
            }

            // Find if final report built or sent, and if so how long ago (to
            //   drive the availability of the "Add Guarantor" and "Print
            //   Guarantors Form" buttons)
            $interimReportBuiltItem = Manager_Referencing_Progress::findSpecificProgressItem(
                $reference->progress,
                Model_Referencing_ProgressItemVariables::INTERIM_REPORT_BUILT
            );

            $finalReportBuilt = Manager_Referencing_Progress::findSpecificProgressItem(
                $reference->progress,
                Model_Referencing_ProgressItemVariables::FINAL_REPORT_BUILT
            );

            // Fetch final-report-sent progress item
            $finalReportSent = Manager_Referencing_Progress::findSpecificProgressItem(
                $reference->progress,
                Model_Referencing_ProgressItemVariables::FINAL_REPORT_SENT
            );
            $finalReportDaysAgo = null;
            $allowAddGuarantor = true;
            if (!is_null($finalReportSent) && $finalReportSent->itemState == Model_Referencing_ProgressItemStates::COMPLETE) {
                $date = new Zend_Date();
                $finalReportDaysAgo = $date->sub($finalReportSent->itemCompletionTimestamp)->toValue();
            } elseif (!is_null($finalReportBuilt) && $finalReportBuilt->itemState == Model_Referencing_ProgressItemStates::COMPLETE) {
                $date = new Zend_Date();
                $finalReportDaysAgo = $date->sub($finalReportBuilt->itemCompletionTimestamp)->toValue();
            }
            if (!is_null($finalReportDaysAgo)) {
                $finalReportDaysAgo = round($finalReportDaysAgo / (60 * 60 * 24));
                if ($finalReportDaysAgo > $this->_params->connect->referencing->disableGuarantorButtonAfterXDays) {
                    $allowAddGuarantor = false;
                }
            }

            // Instantiate security manager for generating MAC
            $securityManager = new Application_Core_Security($this->_params->connect->ref->security->securityString->agent);
            $macToken = $securityManager->generate(
                array(
                    $this->_agentSchemeNumber,
                    $this->_agentId
                )
            );

            // Pass vars into view
            $this->view->reference = $reference;
            $this->view->refComplete = $refComplete;
            $this->view->linkedRefs = $searchResult->results;
            $this->view->allowAddGuarantor = $allowAddGuarantor;

            // Set of URLs that link securely back to referencing
            $baseReferencingUrl = $this->_params->connect->baseUrl->referencing;

            $continueReferenceString = "{$baseReferencingUrl}frontEnd/referencingController.php?"
                    . "refno={$reference->externalId}&"
                    . "agentschemeno={$this->_agentSchemeNumber}&"
                    . "agentid={$this->_agentId}&"
                    . 'origin=connect&'
                    . "agentToken={$macToken}=&"
                    . 'refContinued=1';

            $applicationFormString = "{$baseReferencingUrl}frontEnd/displayForm.php?"
                    . "agentid={$this->_agentId}&"
                    . "refno={$reference->externalId}&"
                    . 'form=application&'
                    . "agentschemeno={$this->_agentSchemeNumber}&"
                    . "agentToken={$macToken}";

            $addGuarantorString = "{$baseReferencingUrl}frontEnd/referencingController.php?"
                    . "originalrefno={$reference->externalId}&"
                    . 'isGuarantor=1&'
                    . "agentschemeno={$this->_agentSchemeNumber}&"
                    . "agentid={$this->_agentId}&"
                    . 'origin=connect&'
                    . "agentToken={$macToken}&"
                    . 'newRefBegin=1';

            $addTenantString = "{$baseReferencingUrl}frontEnd/displayForm.php?"
                    . "agentid={$this->_agentId}&"
                    . 'isAddTenant=true&'
                    . "startrefno={$reference->externalId}&"
                    . 'form=product&'
                    . "agentschemeno={$this->_agentSchemeNumber}&"
                    . "agentToken={$macToken}";

            $viewCaseLogString = "{$baseReferencingUrl}frontEnd/displayForm.php?"
                    . "agentid={$this->_agentId}&"
                    . "refno={$reference->externalId}&"
                    . 'form=caselog&'
                    . "agentschemeno={$this->_agentSchemeNumber}&"
                    . "agentToken={$macToken}";

            $emailAssessorString = "{$baseReferencingUrl}frontEnd/displayForm.php?"
                    . "agentid={$this->_agentId}&"
                    . "refno={$reference->externalId}&"
                    . 'form=emailassessor&'
                    . 'suppressStatus=1&'
                    . "agentschemeno={$this->_agentSchemeNumber}&"
                    . "agentToken={$macToken}";

            $printGuarantorFormString = "/referencing/agent-form?form=Agent-Guarantor&refno={$reference->externalId}";

            if($reference->status->state == Model_Referencing_ReferenceStates::COMPLETE) {
                $tenantQuoteString = "{$baseReferencingUrl}cgi-bin/connect/tenantQuote.pl?"
                    . "agentid={$this->_agentId}&"
                    . "refno={$reference->externalId}&"
                    . "form=tenantquote&"
                    . "agentschemeno={$this->_agentSchemeNumber}&"
                    . "agentToken={$macToken}";
            } else {
                $tenantQuoteString = '';
            }

            if(($interimReportBuiltItem != null) && ($interimReportBuiltItem->itemState == Model_Referencing_ProgressItemStates::COMPLETE)) {
/*
            $retrieveReportString = "{$baseReferencingUrl}frontEnd/referencingController.php?"
                    . "agentid={$this->_agentId}&"
                    . "refno={$reference->externalId}&"
                    . "agentschemeno={$this->_agentSchemeNumber}&"
                    . "action=newReference&"
                    . "brand=connect&"
                    . "summaryConfirmed=1&"
                    . "agentToken={$macToken}";
*/                    
                // Find if interim/final report built, and if so view the report via connect           
                if ($interimReportBuiltItem != null && $interimReportBuiltItem->itemState === Model_Referencing_ProgressItemStates::COMPLETE) {
                    $repType='interim';
                }
                if ($finalReportBuilt != null && $finalReportBuilt->itemState === Model_Referencing_ProgressItemStates::COMPLETE) {
                    $repType='final';
                }            
                $retrieveReportString = "{$this->_params->connectUrl->connectRootUrl}reports/view-report-pdf?refno={$reference->externalId}&repType=$repType&contentDisposition=attachment";            

                $this->view->refLinks = array(
                    'tenantQuote' => $tenantQuoteString,
                    'retrieveReport' => $retrieveReportString,
                    'applicationForm' => $applicationFormString,
                    'addGuarantor' =>  $addGuarantorString,
                    'printGuarantorForm' => $printGuarantorFormString,
                    'viewCaseLog' => $viewCaseLogString,
                    'addTenant' => $addTenantString,
                    'emailAssessor' => $emailAssessorString
                );
            } else {
                $this->view->refLinks = array(
                    'continueReference' =>  $continueReferenceString,
                    'applicationForm' => $applicationFormString,
                    'addGuarantor' =>  $addGuarantorString,
                    'printGuarantorForm' => $printGuarantorFormString,
                    'viewCaseLog' => $viewCaseLogString,
                    'addTenant' => $addTenantString,
                    'emailAssessor' => $emailAssessorString
                );
            }
        }
    }

    /**
     * Action for when an "e-mail to tenant/guarantor" link has been sent out
     * and completed, and the agent notified.  The agent notification e-mail
     * link comes to here to enforce Connect authentication (handled elsewhere
     * by framework) and reference ownership before bouncing on to the usual
     * "continue reference" URL in legacy referencing system.  Grabs the refno
     * from a GET parameter.
     *
     * @return void
     */
    public function completeReferenceAction()
    {
        // Disable view
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        // See if there's a GET-based refno
        $request = $this->getRequest();
        if ($request->isGet() && !is_null($request->getParam('refno'))) {
            // Fetch reference by refno using the Referencing MUNT Manager class
            $refMuntManager = new Manager_ReferencingLegacy_Munt();
            $reference = $refMuntManager->getReference($request->getParam('refno'));

            // Check the owner of this reference
            if ($this->_agentSchemeNumber != $reference->customer->customerId) {
                // The session user is not the owner of this reference.
                // Renider auth failed status code and present error screen
                $this->getResponse()->setHttpResponseCode(401);
                throw new Exception('Authorisation failed when retrieving reference');
                return;
            }

            // Instantiate security manager for generating MAC
            $securityManager = new Application_Core_Security($this->_params->connect->ref->security->securityString->agent);
            $macToken = $securityManager->generate(
                array(
                    $this->_agentSchemeNumber,
                    $this->_agentId
                )
            );

            // Set of URLs that link securely back to referencing
            $baseReferencingUrl = $this->_params->connect->baseUrl->referencing;

            // Generate continue reference URL (same as in summaryAction())
            $continueReferenceString = "{$baseReferencingUrl}frontEnd/referencingController.php?"
                    . "refno={$reference->externalId}&"
                    . "agentschemeno={$this->_agentSchemeNumber}&"
                    . "agentid={$this->_agentId}&"
                    . 'origin=connect&'
                    . "agentToken={$macToken}=&"
                    . 'refContinued=1';

            // Bounce user on to new URL
            $this->_helper->redirector->gotoUrlAndExit($continueReferenceString);
        }
    }

    public function resendEmailAction() {
        // Pop-up results need pop-up layout
        $this->_helper->layout->setLayout('popup');

        // Get refno from GET var, look up applicant details
        $refno = (isset($_GET['refno'])) ? $_GET['refno'] : '';
        $refMuntManager = new Manager_ReferencingLegacy_Munt();
        $reference = $refMuntManager->getReference($refno);

        $applicantTypes = array_flip(Model_Referencing_ReferenceSubjectTypes::iterableKeys());
        $applicantType = ucwords(strtolower($applicantTypes[$reference->referenceSubject->type]));
        $applicantType = ($applicantType == 'Tenant') ? 'Applicant' : $applicantType;

        // Intantiate form definition
        $pageForm = new Connect_Form_ReferencingResendEmail();

        // Validate form if POSTed
        $request = $this->getRequest();
        if ($request->isPost() && !is_null($request->getParam('fromForm')) && $request->getParam('fromForm') == '1') {
            $postData = $request->getPost();
            if ($pageForm->isValid($postData)) {
                // Instantiate security manager for generating MAC
                $securityManager = new Application_Core_Security($this->_params->connect->ref->security->securityString->user);
                $macToken = $securityManager->generate(
                    array(
                        $this->_agentSchemeNumber,
                        $this->_agentId
                    )
                );
                // cURL original page in old ref system, bleurgh
                $baseReferencingUrl = $this->_params->connect->baseUrl->referencing;
                $to = $pageForm->getElement('email')->getValue();
                $url = "{$baseReferencingUrl}frontEnd/emailtenantlink.php?refno={$refno}&tempemail={$to}&brand=default&agentToken={$macToken}";
                // TODO: Use Zend_Http_Client and Zend_Http_Client_Adapter_Curl
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($status != 200) {
                    // Show user there was a problem
                    $this->view->error = "({$status}): " . curl_error($ch);
                    $this->_helper->viewRenderer('resend-email-failed');
                } else {
                    curl_close($ch);
                    // TODO: Check for error being returned
                    if (false) {
                        // Show user there was a fatal problem
                        $this->_helper->viewRenderer('resend-email-failed');
                    } else {
                        //Update e-mail address.
                        if ($pageForm->getElement('replace')->getValue() == '1') {
                            //Get the legacy Tenant ID, then use that to identify the
                            //Tenant record to update in the legacy Tenant table.
                            $legacyEnquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
                            $legacyTenantId = $legacyEnquiryDatasource->getTenantId($reference->externalId);

                            $rsds = new Datasource_ReferencingLegacy_ReferenceSubject();
                            $rsds->updateField(
                                $legacyTenantId,
                                Datasource_ReferencingLegacy_ReferenceSubject::FIELD_EMAIL,
                                $to);
                        }

                        // Show user all was successful
                        $this->_helper->viewRenderer('resend-email-confirmation');
                    }
                }
            } else {
                // Show errors back to user
                $allErrors = $pageForm->getMessages();
                foreach($allErrors as $field => $errors) {
                    foreach($errors as $errorType => $errorMessage) {
                        $this->_helper->flashmessages->addMessage($errorMessage);
                    }
                }
            }
        } else {
            // Pre-fill in refno, e-mail address and replacement checkbox
            $pageForm->getElement('email')->setValue($reference->referenceSubject->contactDetails->email1);
            $pageForm->getElement('replace')->setValue(1);
        }

        $this->view->refno = $refno;
        $this->view->applicantName = "{$reference->referenceSubject->name->title} {$reference->referenceSubject->name->firstName} {$reference->referenceSubject->name->lastName}";
        $this->view->applicantType = $applicantType;
        $this->view->form = $pageForm;
        $this->view->flashMessages = $this->_helper->flashmessages->getCurrentMessages();
    }

    /**
     * Handles display, validation and sending of company application form
     */
    public function companyApplicationAction()
    {
        // Instantiate form
        $pageForm = new Connect_Form_ReferencingCompanyApplication();

        $request = $this->getRequest();
        if ($request->isPost()) {
            // We have post data from the company app form - so attempt
            //   validation
            if ($pageForm->isValid($request->getPost())) {
                // Form valid!

                // Format text data ready for e-mail
                $agentManager = new Manager_Core_Agent($this->_agentSchemeNumber);
                $address = $agentManager->getPhysicalAddressByCategory(Model_Core_Agent_ContactMapCategory::OFFICE);
                $agentAddress = $address->toString();
                switch($request->getParam('how_rg_offered')){
                	case 1: $howOffered = "Free of charge"; break;
                	case 2: $howOffered = "Included in Management Fees"; break;
                	case 3: $howOffered = "Separate charge for Rent Guarantee to the landlord"; break;
                	case 4: $howOffered = "Referening Only"; break;
                }
                
                switch($request->getParam('property_managed')){
                	case 1: $howManaged = "Let Only"; break;
                	case 2: $howManaged = "Managed"; break;
                	case 3: $howManaged = "Rent Collect"; break;
                }
                
                //Convert the product id into a product name
                $cleanData = $pageForm->getValues();
                $productManager = new Manager_Referencing_Product();
                $product = $productManager->getById($cleanData['subform_product']['product']);
                $productName = (empty($product->name)) ? '' : $product->name;

                $message =
                    'Agent Scheme Number: ' . $this->_agentSchemeNumber . "\n" .
                    'Agent Name: ' . $this->_agentObj->name . "\n" .
                    'Agent address: ' . $agentAddress . "\n\n" .
                    'Product: ' . $product->name . "\n" .

                    "\nProperty To Let:\n" .
                    'Address: ' . $request->getParam('property_address') . "\n" .
                    'Postcode: ' . $request->getParam('property_postcode') . "\n" .
                    'Managed Property: ' . $howManaged . "\n" .
                	'How is Rent Guarantee offered to the landlord: ' . $howOffered . "\n" .
                    'Total Rent: ' . $request->getParam('tenant_renttotal') . "\n" .
                    'Rent Share: ' . $request->getParam('tenant_rentshare') . "\n" .
                    'Tenancy Term: ' . $request->getParam('tenant_term') . "\n" .
                    'Start Date: ' . $request->getParam('tenant_startdate') . "\n" .
                    'Total Reference Count: ' . $request->getParam('tenant_number') . "\n" .

                    "\nLandlord Details: \n" .
                    'First Name: ' . $request->getParam('landlord_firstname') . "\n" .
                    'Last Name: ' . $request->getParam('landlord_lastname') . "\n" .
                    'Address: ' . $request->getParam('landlord_address') . "\n" .
                    'Postcode: ' . $request->getParam('landlord_postcode') . "\n" .
                    'Telno: ' . $request->getParam('landlord_landlinenumber') . "\n" .
                    'Mobile: ' . $request->getParam('landlord_mobilenumber') . "\n" .

                    "\nCompany Details: \n" .
                    'Company Name: ' . $request->getParam('company_name') . "\n" .
                    'Trading Name: ' . $request->getParam('company_tradingname') . "\n" .
                    'Registered Number: ' . $request->getParam('company_registration') . "\n" .
                    'Incorporation Date: ' . $request->getParam('company_incorporation') . "\n" .
                    'Contact Name: ' . $request->getParam('company_contactname') . "\n" .
                    'Telno: ' . $request->getParam('company_phone') . "\n" .

                    "\nCompany Registered Address: \n" .
                    'Address: ' . $request->getParam('registered_address') . "\n" .
                    'Postcode: ' . $request->getParam('registered_postcode') . "\n" .
                    'Period at Address: ' . $request->getParam('registered_years') . " years, " . $request->getParam('registered_months') . " months\n" .

                    "\nCompany Trading Address: \n" .
                    'Address: ' . $request->getParam('trading_address') . "\n" .
                    'Postcode: ' . $request->getParam('trading_postcode') . "\n" .
                    'Period at Address: ' . $request->getParam('trading_years') . " years, " . $request->getParam('trading_months') . " months\n" .

                    "\nAdditional Info: \n" .
                    $request->getParam('additional_info') . "\n" .

                    "\nCompany Signature Details: \n" .
                    'Name: ' . $request->getParam('representive_name') . "\n" .
                    'Position: ' . $request->getParam('representive_position') . "\n" .
                    'Date: ' . $request->getParam('application_date') . "\n"
                ;

                // Instantiate mailer manager
                $mailManager = new Application_Core_Mail();
                $mailManager
                    ->setTo($this->_params->connect->companyapps->emailToAddress, $this->_params->connect->companyapps->emailToName)
                    ->setFrom($this->_params->connect->companyapps->emailFromAddress, $this->_params->connect->companyapps->emailFromName)
                    ->setSubject($this->_params->connect->companyapps->emailSubject)
                    ->setBodyText($message);

                // Check for uploaded file and persist it if so
                list($fileResult, $fileData) = $this->_uploadPersistentCompanyApplicationFile();

                if ($fileResult === true) {
                    $this->view->fileUploaded = $fileData;
                    // If there's a file, attach it
                    if (isset($fileData['pathToFile'])) {
                        $mailManager->addAttachment($fileData['pathToFile'], $fileData['originalName']);
                    }

                    $mailManager->send();

                    // Clean up uploaded file
                    $this->_deleteCompanyApplicationFile();

                    // Show user confirmation that form submission has been successful
                    $this->_helper->viewRenderer('company-confirmation');
                } else {
                    $this->_helper->flashmessages->addMessage('Problem(s) uploading file:');
                    $this->_helper->flashmessages->addMessage($fileData);
                }
            } else {
                // Tell user there are problems
                $this->_helper->flashmessages->addMessage('Problem(s) in form data:');
                $this->_helper->flashmessages->addMessage($pageForm->getMessagesFlattened(true));

                // Check for uploaded file and persist it if so
                list($fileResult, $fileData) = $this->_uploadPersistentCompanyApplicationFile();

                if ($fileResult === true) {
                    $this->view->fileUploaded = $fileData;
                } else {
                    $this->_helper->flashmessages->addMessage('Problem(s) uploading file:');
                    $this->_helper->flashmessages->addMessage($fileData);
                }
            }
        } else {
            // Form first shown, set a couple of default values
            $pageForm->subform_additional->getElement('additional_info')->setValue('Use this space to provide any additional information that may help us when processing your application.');
            $pageForm->subform_declaration->getElement('application_date')->setValue(date('d/m/Y'));

            // Ensure any previously uploaded file is deleted
            $this->_deleteCompanyApplicationFile();
        }

        $this->view->flashMessages = $this->_helper->flashmessages->getCurrentMessages();
        $this->view->form = $pageForm;
    }

    /**
     * Pipes a PDF to the end user, with some agent-specific injected content.
     */
    public function agentFormAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        if ($request->isGet() && !is_null($request->getParam('form'))) {
            $formName = $request->getParam('form');
            /* // This is the correct code, but over HTTPS IE doesn't like it.
            $this->getResponse()
                ->setHeader('Pragma', 'public') // required
                ->setHeader('Expires', '0')
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                ->setHeader('Cache-Control', 'private', false) // required for certain browsers
                ->setHeader('Content-Disposition', 'inline; filename=' . $formName . '.pdf')
                ->setHeader('Content-type', 'application/pdf');
            */
            // This is the dirty way of doing it, but it works in IE.  IE sucks.
            header('Pragma: public'); // required
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false); // required for certain browsers
            header('Content-Disposition: inline; filename=' . $formName . '.pdf');
            header('Content-type: application/pdf');

            // Check if there's a refno, used only to populate the guarantor
            //   form from the referencing summary page with tenant and property
            //   to let info.  Sanity check that refno is valid and belongs to
            //   ASN happens in manager
            $refno = (!is_null($request->getParam('refno'))) ? $request->getParam('refno') : null;

            $agentFormManager = new Manager_Connect_AgentForm();
            $agentFormManager->populateAndOuput($formName, $this->_agentSchemeNumber, $this->_agentId, 'browser', $refno);
        }
    }

    public function dashboardGraphsAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->getHelper('layout')->disableLayout();

        // Instantiate agent stats data source
        $agentStatsDatasource = new Datasource_Core_Agent_Stats();

        // Fetch new references data for this agent
        $newRefs = array();
        $stats = $agentStatsDatasource->getStat(Model_Core_Agent_StatType::NEW_REFS_BY_DAY, $this->_agentSchemeNumber);
        foreach($stats as $stat) {
            $newRefs[substr($stat->dateApplicable, 8, 2)] = $stat->value;
        }

        // Fetch open references by type data for this agent
        $openRefTypes = array();
        $stats = $agentStatsDatasource->getStat(Model_Core_Agent_StatType::OPEN_REFS_BY_PRODUCT, $this->_agentSchemeNumber);
        foreach($stats as $stat) {
            $openRefTypes[$stat->variant] = $stat->value;
        }

        // Fetch open references by age data for this agent
        $openRefProgress = array();
        $stats = $agentStatsDatasource->getStat(Model_Core_Agent_StatType::OPEN_REFS_BY_PROGRESS, $this->_agentSchemeNumber);
        foreach($stats as $stat) {
            $age = sprintf('%d day%s ago', $stat->variant, (($stat->variant == 1) ? '' : 's'));
            $openRefProgress[$age] = $stat->value;
        }

        // Output referencing dashboard graphs partial
        echo $this->view->partial(
            'partials/dashboard-referencing-graphs.phtml',
            array(
                'newRefs'           => $newRefs,
                'openRefTypes'      => $openRefTypes,
                'openRefProgress'   => $openRefProgress,
            )
        );
    }

    /**
     * Upload a new file for this user if one is present in the form data, or if
     * not, check if one has been uploaded previously.  If there's one known
     * about, return its name.  Uses session to persist an uploaded file's
     * details between form submission attempts, in the case the overall form
     * doesn't validate.
     *
     * @todo Move somewhere else.
     *
     * @return array Array indicating boolean true for success plus and an
     * associative array with uploaded file information (or empty array if no
     * file), or boolean false for failure and an array of error messages.
     */
    private function _uploadPersistentCompanyApplicationFile()
    {
        // For storing original filename intact
        $session = new Zend_Session_Namespace('homelet_connect_referencing');

        $tempFile = "{$this->_params->connect->tempPrivatePath}companyApp_{$this->_agentSchemeNumber}_{$this->_agentId}";

        // Is a new file being sent?
        $upload = new Zend_File_Transfer('http');

        if ($upload->isUploaded()) {
            $upload->getValidator('Upload')->setMessages(
                array(
                    Zend_Validate_File_Upload::INI_SIZE     => 'The uploaded file size exceeds system maximum (' . ini_get('upload_max_filesize') . ')',
                    Zend_Validate_File_Upload::FORM_SIZE    => 'The uploaded file size exceeds the HTML form maximum',
                    Zend_Validate_File_Upload::PARTIAL      => 'The uploaded file was only partially uploaded',
                    Zend_Validate_File_Upload::NO_FILE      => 'No file was uploaded',
                    Zend_Validate_File_Upload::NO_TMP_DIR   => 'Missing a temporary folder',
                    Zend_Validate_File_Upload::CANT_WRITE   => 'Failed to write file to disk',
                    Zend_Validate_File_Upload::EXTENSION    => 'File upload stopped by extension',
                    Zend_Validate_File_Upload::UNKNOWN      => 'Unknown upload error'
                )
            );
            $upload->addValidator('Count', true, 1);
            $upload->addValidator('Size', false, $this->_params->connect->companyapps->fileUpload->maxSize->file);
            $upload->getValidator('Size')->setMessages(
                array(
                    Zend_Validate_File_Size::TOO_SMALL  => 'File \'%value%\' below minimum size',
                    Zend_Validate_File_Size::TOO_BIG    => 'File \'%value%\' above maximum size'
                )
            );
            $upload->addValidator('MimeType', false, $this->_params->connect->companyapps->fileUpload->mimeTypes);
            $upload->getValidator('MimeType')->setMessages(
                array(
                    Zend_Validate_File_MimeType::FALSE_TYPE => 'File \'%value%\' of incorrect MIME type'
                )
            );
            $upload->addValidator('Extension', true, $this->_params->connect->companyapps->fileUpload->extensions);
            $upload->getValidator('Extension')->setMessages(
                array(
                    Zend_Validate_File_Extension::FALSE_EXTENSION => 'File \'%value%\' of incorrect extension'
                )
            );

            if ($upload->isValid()) {
                // First delete any old file that may have been previously
                //   uploaded
                $this->_deleteCompanyApplicationFile();

                // Upload new one
                $session->companyAppFile->originalFilename = $upload->getFileName(null, false);

                $upload->addFilter('Rename', $tempFile);
                if ($upload->receive()) {
                    $session->companyAppFile->uploadedFile = $tempFile;
                } else {
                    unset($session->companyAppFile);
                }
            } else {
                // Send back validation messages
                return array(false, $upload->getMessages());
            }
        }

        // Is there one stored, perhaps already?  If yes, return original
        //   filename
        $returnVal = array();
        if (isset($session->companyAppFile->originalFilename)) {
            $returnVal = array(
                'originalName'  => $session->companyAppFile->originalFilename,
                'pathToFile'    => $tempFile
            );
        }

        return array(true, $returnVal);
    }

    /**
    * Public send quote as email and or post function
    */
    public function affordabilityCalculatorAction()
    {
        $session = new Zend_Session_Namespace('homelet_connect_referencing');
        // This controller is called within a popup (facebox style) so doesn't require a layout file
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->form = new Connect_Form_AffordabilityCalculator();
    }

    /**
     * Deletes a company application file that may be in the temporary store and
     * unsets the associated session key.
     *
     * @todo Move somewhere else.
     *
     * @return void
     */
    private function _deleteCompanyApplicationFile()
    {
        @unlink("{$this->_params->connect->tempPrivatePath}companyApp_{$this->_agentSchemeNumber}_{$this->_agentId}");

        $session = new Zend_Session_Namespace('homelet_connect_referencing');
        unset($session->companyAppFile);
    }
}
