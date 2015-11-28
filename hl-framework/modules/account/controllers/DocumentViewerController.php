<?php

/**
 * Customer portal controller. Please note that this controller is NOT exposed
 * to anonymous users
 *
 * @package Account_PortalController
 */
class Account_DocumentViewerController extends Zend_Controller_Action
{
    /**
     * @var Zend_Auth
     */
    private $auth = null;

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

        // Add controller stylesheet(s)
        $this->view->headLink()->appendStylesheet('/assets/account/css/portal.css');

        // Check authorisation for this controller
        $this->auth = $this->_checkAuthorisation();
    }

    /**
     * View quote documents
     *
     * @return void
     */
    public function quotesViewDocumentsAction()
    {
        $quoteDocuments = array();
        $this->_setMetaTitle('View Documents');

        $this->view->isAjaxRequest = $this->getRequest()->isXmlHttpRequest();

        if ($this->view->isAjaxRequest) {
            $this->_helper->getHelper('layout')->disableLayout();
        }

        $request = $this->getRequest();
        $quoteNumber = base64_decode($request->getParam('policy_number'));

        // Get the customer session
        $customerSession = $this->auth->getStorage()->read();

        // Get the request policy
        $legacyPolicies = new Datasource_Insurance_LegacyQuotes();
        $quote = $legacyPolicies->getByPolicyNumber($quoteNumber);

        if ($quote) {
            // Check the policy customer refno is linked to the customer id through mapping
            $customerMaps = new Datasource_Core_CustomerMaps();
            $customerMap = $customerMaps->getMap(Model_Core_Customer::LEGACY_IDENTIFIER, $quote->refNo);

            // Confirm the policy number belongs to the logged in customer
            if ($customerMap !== false && $customerMap->getIdentifier() == $customerSession->id) {
                // Customer map found and customer is mapped to refno
                $documentHistory = new Datasource_Insurance_DocumentHistory();
                $quoteDocuments = $documentHistory->getDocumentsAll($quoteNumber, array('holder'));
            }
        }

        $this->view->policyNumber = $quoteNumber;
        $this->view->policyDocuments = $quoteDocuments;
        $this->render('view-document-history');
    }

    /**
     * Portal document viewer for quotes
     */
    public function viewQuoteDocumentAction()
    {
        $request = $this->getRequest();
        $quoteNumber_encoded = $request->getParam('policy_number');
        $quoteNumber = base64_decode($quoteNumber_encoded);
        $documentId = $request->getParam('document_id');
        list($documentFilename, $attachments) = $this->_fetchDocument($quoteNumber, $documentId);

        if ($documentFilename == null) {
            // Failed to find quote
            $this->_setBreadcrumbs(array(
                '/' => 'Home',
                '/my-homelet' => 'My HomeLet',
                '/my-homelet/quotes' => 'My Quotes',
                '/my-homelet/quotes/' . $quoteNumber_encoded => $quoteNumber,
            ));

            $this->render('view-document-not-found');
            return;
        }

        $this->_helper->layout()->disableLayout();

        $this->view->documentUrl = $documentFilename;
        $this->view->documentAttachments = $attachments;
        $this->render('view-document');
    }

    /**
     * View policy documents
     *
     * @return void
     */
    public function policiesViewDocumentsAction()
    {
        $policyDocuments = array();
        $this->_setMetaTitle('View Documents');

        $this->view->isAjaxRequest = $this->getRequest()->isXmlHttpRequest();

        if ($this->view->isAjaxRequest) {
            $this->_helper->getHelper('layout')->disableLayout();
        }

        $request = $this->getRequest();
        $policyNumber = base64_decode($request->getParam('policy_number'));

        // Get the customer session
        $customerSession = $this->auth->getStorage()->read();

        // Get the request policy
        $legacyPolicies = new Datasource_Insurance_LegacyPolicies();
        $policy = $legacyPolicies->getByPolicyNumber($policyNumber);

        if ($policy) {
            // Check the policy customer refno is linked to the customer id through mapping
            $customerMaps = new Datasource_Core_CustomerMaps();
            $customerMap = $customerMaps->getMap(Model_Core_Customer::LEGACY_IDENTIFIER, $policy->refNo);

            // Confirm the policy number belongs to the logged in customer
            if ($customerMap !== false && $customerMap->getIdentifier() == $customerSession->id) {
                // Customer map found and customer is mapped to refno
                $documentHistory = new Datasource_Insurance_DocumentHistory();
                $policyDocuments = $documentHistory->getDocumentsAll($policyNumber, array('holder'));
            }
        }

        $this->view->policyNumber = $policyNumber;
        $this->view->policyDocuments = $policyDocuments;
        $this->render('view-document-history');
    }

    /**
     * Portal document viewer for policies
     */
    public function viewPolicyDocumentAction()
    {
        $request = $this->getRequest();
        $policyNumber_encoded = $request->getParam('policy_number');
        $policyNumber = base64_decode($policyNumber_encoded);
        $documentId = $request->getParam('document_id');
        list($documentFilename, $attachments) = $this->_fetchDocument($policyNumber, $documentId);

        if ($documentFilename == null) {
            // Failed to find policy
            $this->_setBreadcrumbs(array(
                '/' => 'Home',
                '/my-homelet' => 'My HomeLet',
                '/my-homelet/policies' => 'My Policies',
                '/my-homelet/policies/' . $policyNumber_encoded => $policyNumber,
            ));

            $this->render('view-document-not-found');
            return;
        }

        $documentHistory = new Datasource_Insurance_DocumentHistory();
        $document = $documentHistory->getDocument($documentId, $policyNumber);

        $documentTemplate = new Datasource_Insurance_Document_InsuranceTemplates();
        $customers_description = $documentTemplate->getCustomersDescription($documentTemplate->getTemplateId($document->template_name));

        $this->_helper->layout()->disableLayout();

        $this->view->documentUrl = $documentFilename;
        $this->view->documentAttachments = $attachments;
        $this->view->document = $document;
        $this->view->customers_template_description = $customers_description;
        $this->render('view-document');
    }

    /**
     * Retrieve a referencing report
     */
    public function viewReferencingReportAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $refNo = $request->getParam('refno');
        $download = $request->getParam('download');
        $reporttype = '';
        $reportkey = $request->getParam('report');

        // Validate the refNo parameter
        preg_match('/([0-9]*\.[0-9]*)/', $refNo, $refNo);

        if (count($refNo) == 2) {
            $refNo = $refNo[1];
        }
        else {
            // Fails validation, return error
            $this->render('view-document-not-found');
            return;
        }

        // Validate direct landlord is the correct owner of the reference
        // Get the customer session
        $customerSession = $this->auth->getStorage()->read();

        // Get list of external reference numbers
        $referenceManager = new Manager_Referencing_Reference();
        $referenceIds = $referenceManager->getAllReferenceIds($customerSession->id);

        if (!in_array($refNo, $referenceIds)) {
            // This reference does not belong to the customer
            $this->render('view-document-not-found');
            return;
        }

        // Get Latest report
        $legacyRefManager = new Manager_ReferencingLegacy_Munt();
        $report = $legacyRefManager->getLatestReport($refNo);

        // Check the $reportkey parameter against the key provided by the report object returned. 
        // If they dont match, display a notice page that the report is out of date.
        if ($reportkey != '' && $report->validationKey != $reportkey) {
            $this->view->download = ($download == 'true' ? 'true' : 'false');
            $this->view->report = $report;
            $this->render('reference-report-outofdate');
            return;
        }

        // Set the report type of that of the report object
        $reporttype = $report->reportType;


        $params = Zend_Registry::get('params');
        $baseRefUrl = $params->baseUrl->referencing;

        $reportUri = $baseRefUrl . 'cgi-bin/refviewreport.pl?refno=' . $refNo .'&repType=' . $reporttype;

//error_log('debug: ' . $reportUri);
        
        $filename = $this->_buildReportAttachementFilename('Report', $refNo);

        // Get the latest report
        $reportDatasource = new Datasource_ReferencingLegacy_ReportHistory();
        $timegenerated = $reportDatasource->getTimeReportGenerated($refNo, $reporttype);

        // Check report file cache
        if (Application_Cache_Referencing_ReportFileCache::getInstance()->has($filename, $timegenerated)) {
            // Return from cache
            $pdfContent = Application_Cache_Referencing_ReportFileCache::getInstance()->get($filename, $timegenerated);
            $this->getResponse()->appendBody($pdfContent);
       }
        else {
            // Request report from legacy
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $reportUri);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_TIMEOUT, 50);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $pdfContent = curl_exec($curl);
            curl_close($curl);

            if (!$pdfContent) {
                $this->render('view-document-not-found');
                return;
            }

            // Cache result
            Application_Cache_Referencing_ReportFileCache::getInstance()->set($filename, $pdfContent, $timegenerated);
            $this->getResponse()->appendBody($pdfContent);
        }

        // Create filename
        // AJD - Why is this being done again? Also - it doesn't follow the new filename schema. Address must not be used.
        /*$referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($refNo);
        $filename = ucfirst(strtolower($reporttype)) . ', ' . $reference->propertyLease->address->addressLine1 . ', ' . $reference->propertyLease->address->addressLine2 . '.pdf';
        $filename = preg_replace('/&|\\//', '', $filename);*/

        // Apply appropriate headers
//        $response->setHeader('Pragma', '');
//        $response->setHeader('Cache-Control', '');

        if ($download == 'true') {
            // Downloading
 
                header('Pragma: '); // Remove pragma
                header('Cache-Control: '); // Remove cache control
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.$filename);


//           $response->setHeader('Content-Description', 'File Transfer');
 //           $response->setHeader('Content-Type', 'application/octet-stream');
 //           $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }
        else {

		header('Pragma: '); // Remove pragma
                header('Cache-Control: '); 
                header('Content-Type: application/pdf');


            // Viewing
//            $response->setHeader('Content-Type', 'text/plain');
        }

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _buildReportAttachementFilename($refType, $refNo)
    {
        return sprintf('%s.pdf', ucfirst($refType) . "-" . str_replace(array('.', '/'), '-', $refNo));
    }

    /**
     * Retrieve the document
     *
     * @param string $policyNumber Policy number
     * @param string $documentId Unique document request hash
     * @return string null or PDF contents
     */
    private function _fetchDocument($policyNumber, $documentId)
    {
        // Get the customer session
        $customerSession = $this->auth->getStorage()->read();

        // Get the request policy
        if ($policyNumber[0] == 'Q') {
            // Quotes
            $legacyPolicies = new Datasource_Insurance_LegacyQuotes();
        }
        else {
            // Policies
            $legacyPolicies = new Datasource_Insurance_LegacyPolicies();
        }

        $policy = $legacyPolicies->getByPolicyNumber($policyNumber);

        if (!$policy) {
            return null;
        }

        // Check the policy customer refno is linked to the customer id through mapping
        $customerMaps = new Datasource_Core_CustomerMaps();
        $customerMap = $customerMaps->getMap(Model_Core_Customer::LEGACY_IDENTIFIER, $policy->refNo);

        // Confirm the policy number belongs to the logged in customer
        if ($customerMap == false || $customerMap->getIdentifier() != $customerSession->id) {
            // Customer map not found or customer is not mapped to refno, render error message
            return null;
        }

        // Get all document details
        $documentHistory = new Datasource_Insurance_DocumentHistory();
        $document = $documentHistory->getDocument($documentId, $policyNumber);

        if (!$document) {
            return null;
        }

        // Retrieve document from store
        $documentFulfillmentService = new Service_Insurance_Document();
        return $documentFulfillmentService->retrieveDocumentFromStore($documentId, $document->template_name, Service_Insurance_Document::DOCUMENT_AND_ATTACHMENTS);
    }

    /**
     * Sets the page meta title with "My HomeLet" prefix
     *
     * @param type $title
     * @return void
     */
    private function _setMetaTitle($title)
    {
        $this->view->pageTitle = sprintf('My HomeLet - %s', $title);
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
