<?php

require_once('ConnectAbstractController.php');
class Connect_ReportsController extends ConnectAbstractController
{
    public function init()
    {
        parent::init();

        // Get name of static page from URI
        $action = trim($this->getRequest()->getRequestUri(), '/');
        $action = str_replace('/', '-', $action);
        $action = strtolower($action);

        // Set panel content, if any
        $this->_helper->panelcontent->fetch('connect-brochureware-' . $action);
    }

    public function indexAction()
    {
        $this->render('index'); // no logic, just show the page
    }

    public function referencingAction()
    {
        $request = $this->getRequest();

        $refreport = new Connect_Form_ReferencingReport();
        $slareport = new Connect_Form_ReferencingServiceLevelReport();
        $overnightreport = new Connect_Form_ReferencingOvernightReport();
        $this->view->refreport = $refreport;
        $this->view->slareport = $slareport;
        $this->view->overnightreport = $overnightreport;

        if ($request->isPost())
        {
            $formdata = $request->getPost();
            $refreport->populate($formdata);
            $slareport->populate($formdata);
            $overnightreport->populate($formdata);

            if ($refreport->isValid($formdata) && isset($formdata['referencingreport_producereport']))
            {
                $startdate = new Zend_Date
                (
                    $refreport->getElement('referencingreport_start')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $enddate = new Zend_Date
                (
                    $refreport->getElement('referencingreport_end')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $report = new Datasource_Connect_Mi_RefSales();
                $this->view->reportdata                 = $report->refSalesForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);
                $this->view->productoverview_reportdata = $report->refSalesOverviewByProductForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);
                $this->view->appltype_reportdata        = $report->refSalesOverviewByApplicantTypeForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);
                $this->view->subtype_reportdata         = $report->refSalesOverviewBySubmissionTypeForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);

                $this->view->headLink()->appendStylesheet('/assets/connect/css/print.css', 'print');
                $this->render('referencing_report');
            }
            else if ($refreport->isValid($formdata) && isset($formdata['referencingreport_exporttoexcel']))
            {
                $startdate = new Zend_Date
                (
                    $refreport->getElement('referencingreport_start')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $enddate = new Zend_Date
                (
                    $refreport->getElement('referencingreport_end')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $this->_sendCsvHeaders('referencing-report.csv');

                $report = new Datasource_Connect_Mi_RefSales();
                $this->view->reportdata                 = $report->refSalesForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);
                $this->view->productoverview_reportdata = $report->refSalesOverviewByProductForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);
                $this->view->appltype_reportdata        = $report->refSalesOverviewByApplicantTypeForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);
                $this->view->subtype_reportdata         = $report->refSalesOverviewBySubmissionTypeForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);

                $this->_helper->getHelper('layout')->disableLayout();
                $this->render('referencing_csv');
            }
            else if ($slareport->isValid($formdata) && isset($formdata['slareport_producereport']))
            {
                $report = new Datasource_Connect_Mi_RefSales();
                $this->view->reportdata = $report->refSlaForMonthYear
                (
                    $this->_agentSchemeNumber,
                    $formdata['slareport_month'],
                    $formdata['slareport_year']
                );

                $this->view->headLink()->appendStylesheet('/assets/connect/css/print.css', 'print');
                $this->render('referencingsla_report');
            }
            else if ($slareport->isValid($formdata) && isset($formdata['slareport_exporttoexcel']))
            {
                $this->_sendCsvHeaders('referencing-sla-report.csv');

                $report = new Datasource_Connect_Mi_RefSales();
                $this->view->reportdata = $report->refSlaForMonthYear
                (
                    $this->_agentSchemeNumber,
                    $formdata['slareport_month'],
                    $formdata['slareport_year']
                );

                $this->_helper->getHelper('layout')->disableLayout();
                $this->render('referencingsla_csv');
            }else{

                $auth = Zend_Auth::getInstance();
                $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));

            //	Zend_Debug::dump($overnightreport->isValid($formdata));
            	$report = new Datasource_Connect_Mi_RefDailyReport();
                $this->view->isAgentInIris = $auth->getStorage()->read()->isInIris;
            	$this->view->reportlivedata = $report->fetchLiveByASN($this->_agentSchemeNumber);
            	$this->view->reporttemporarydata = $report->fetchTemporaryByASN($this->_agentSchemeNumber);
            	$this->view->reportcompleteddata = $report->fetchCompleteByASN($this->_agentSchemeNumber);
            	$this->render('referencing_overnight_report');
            //	die("HELLO");
            }
        }
    }

    public function rentGuaranteeAction()
    {
        $request = $this->getRequest();

        $livergreport = new Connect_Form_LiveRGPoliciesReport();
        $lapsedrgreport = new Connect_Form_LapsedRGPoliciesReport();
        $this->view->livergreport = $livergreport;
        $this->view->lapsedrgreport = $lapsedrgreport;

        if ($request->isPost())
        {
            $formdata = $request->getPost();
            $livergreport->populate($formdata);
            $lapsedrgreport->populate($formdata);

            if ($livergreport->isValid($formdata) && isset($formdata['livergreport_producereport']))
            {
                $startdate = new Zend_Date
                (
                    $livergreport->getElement('livergreport_start')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $enddate = new Zend_Date
                (
                    $livergreport->getElement('livergreport_end')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $report = new Datasource_Connect_Mi_InsuranceSales();
                $this->view->reportdata = $report->liveRGPolicyForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);

                $this->view->headLink()->appendStylesheet('/assets/connect/css/print.css', 'print');
                $this->render('livergpolicies_report');
            }
            else if ($livergreport->isValid($formdata) && isset($formdata['livergreport_exporttoexcel']))
            {
                $startdate = new Zend_Date
                (
                    $livergreport->getElement('livergreport_start')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $enddate = new Zend_Date
                (
                    $livergreport->getElement('livergreport_end')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $this->_sendCsvHeaders('live-rg-policies-report.csv');

                $report = new Datasource_Connect_Mi_InsuranceSales();
                $this->view->reportdata = $report->liveRGPolicyForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);

                $this->_helper->getHelper('layout')->disableLayout();
                $this->render('livergpolicies_csv');
            }
            else if ($lapsedrgreport->isValid($formdata) && isset($formdata['lapsedrgreport_producereport']))
            {
                $startdate = new Zend_Date
                (
                    $lapsedrgreport->getElement('lapsedrgreport_start')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $enddate = new Zend_Date
                (
                    $lapsedrgreport->getElement('lapsedrgreport_end')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $report = new Datasource_Connect_Mi_InsuranceSales();
                $this->view->reportdata = $report->lapsedRGPolicyForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);

                $this->view->headLink()->appendStylesheet('/assets/connect/css/print.css', 'print');
                $this->render('lapsedrgpolicies_report');
            }
            else if ($lapsedrgreport->isValid($formdata) && isset($formdata['lapsedrgreport_exporttoexcel']))
            {
                $startdate = new Zend_Date
                (
                    $lapsedrgreport->getElement('lapsedrgreport_start')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $enddate = new Zend_Date
                (
                    $lapsedrgreport->getElement('lapsedrgreport_end')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $this->_sendCsvHeaders('lapsed-rg-policies-report.csv');

                $report = new Datasource_Connect_Mi_InsuranceSales();
                $this->view->reportdata = $report->lapsedRGPolicyForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);

                $this->_helper->getHelper('layout')->disableLayout();
                $this->render('lapsedrgpolicies_csv');
            }
        }
    }

    public function insuranceAction()
    {
        $request = $this->getRequest();

        $landlordssalesreport = new Connect_Form_LandlordsInsuranceSalesReport();
        $tenantssalesreport = new Connect_Form_TenantsInsuranceSalesReport();
        $this->view->tenantssalesreport = $tenantssalesreport;
        $this->view->landlordssalesreport = $landlordssalesreport;

        if ($request->isPost())
        {
            $formdata = $request->getPost();
            $landlordssalesreport->populate($formdata);
            $tenantssalesreport->populate($formdata);

            if ($landlordssalesreport->isValid($formdata) && isset($formdata['landlordsinsurancesalesreport_producereport']))
            {
                $startdate = new Zend_Date
                (
                    $landlordssalesreport->getElement('landlordsinsurancesalesreport_start')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $enddate = new Zend_Date
                (
                    $landlordssalesreport->getElement('landlordsinsurancesalesreport_end')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $report = new Datasource_Connect_Mi_InsuranceSales();
                $this->view->reportdata = $report->landlordsInsuranceSalesForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);

                $this->view->headLink()->appendStylesheet('/assets/connect/css/print.css', 'print');
                $this->render('landlordsinsurancesales_report');
            }
            else if ($landlordssalesreport->isValid($formdata) && isset($formdata['landlordsinsurancesalesreport_exporttoexcel']))
            {
                $startdate = new Zend_Date
                (
                    $landlordssalesreport->getElement('landlordsinsurancesalesreport_start')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $enddate = new Zend_Date
                (
                    $landlordssalesreport->getElement('landlordsinsurancesalesreport_end')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $this->_sendCsvHeaders('landlords-sales-report.csv');

                $report = new Datasource_Connect_Mi_InsuranceSales();
                $this->view->reportdata = $report->landlordsInsuranceSalesForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);

                $this->_helper->getHelper('layout')->disableLayout();
                $this->render('landlordsinsurancesales_csv');
            }
            else if ($tenantssalesreport->isValid($formdata) && isset($formdata['tenantsinsurancesalesreport_producereport']))
            {
                $startdate = new Zend_Date
                (
                    $tenantssalesreport->getElement('tenantsinsurancesalesreport_start')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $enddate = new Zend_Date
                (
                    $tenantssalesreport->getElement('tenantsinsurancesalesreport_end')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $report = new Datasource_Connect_Mi_InsuranceSales();
                $this->view->reportdata = $report->tenantsInsuranceSalesForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);

                $this->view->headLink()->appendStylesheet('/assets/connect/css/print.css', 'print');
                $this->render('tenantsinsurancesales_report');
            }
            else if ($tenantssalesreport->isValid($formdata) && isset($formdata['tenantsinsurancesalesreport_exporttoexcel']))
            {
                $startdate = new Zend_Date
                (
                    $tenantssalesreport->getElement('tenantsinsurancesalesreport_start')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $enddate = new Zend_Date
                (
                    $tenantssalesreport->getElement('tenantsinsurancesalesreport_end')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $this->_sendCsvHeaders('tenants-sales-report.csv');

                $report = new Datasource_Connect_Mi_InsuranceSales();
                $this->view->reportdata = $report->tenantsInsuranceSalesForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);

                $this->_helper->getHelper('layout')->disableLayout();
                $this->render('tenantsinsurancesales_csv');
            }
        }
    }

    public function invoicesAction()
    {
        $request = $this->getRequest();

        $invoiceselection = new Connect_Form_Invoices();
        $this->view->invoiceselection = $invoiceselection;

        if ($request->isPost())
        {
            $formdata = $request->getPost();
            $invoiceselection->populate($formdata);

            if ($invoiceselection->isValid($formdata))
            {
                /*
                 1. Get filename from invoicing table
                 2. Make request for url from fileserver using Zend_XmlRpc_Client
                 3. Redirect client to url
                 */

                $invoices = new Datasource_Core_Agent_Invoice();
                $filename = $invoices->getInvoiceFilename
                (
                    $this->_agentSchemeNumber,
                    $invoiceselection->getElement('invoice_month')->getValue(),
                    $invoiceselection->getElement('invoice_year')->getValue()
                );

                $rpcclient = new Zend_XmlRpc_Client($this->_params->fileserver->requestHost);
                $retrieveurl = null;

                try
                {
                    $retrieveurl = $rpcclient->call('authenticate', array($filename, 0));
                    $digest = hash('sha256', $filename . 0);
                }
                catch(Zend_XmlRpc_Client_FaultException $ex)
                {
                    $retrieveurl = null; // Ensure url is null to skip next section
                }

                if (isset($retrieveurl)) {
                    $invoiceStatus = new Datasource_Core_Agent_InvoiceViewStatus();
                    $invoiceStatus->insertInvoiceViewStatus($this->_agentSchemeNumber, $this->_agentId, $this->_agentrealname, $invoiceselection->getElement('invoice_month')->getValue(), $invoiceselection->getElement('invoice_year')->getValue());
                    $this->_redirect($this->_params->fileserver->request . $retrieveurl . '?filehash=' . $digest);
                }
                // Some kind of error, report error page
                $this->render('invoices_report_error');
            }
        }
    }

    /**
     * This action can be triggered within email sent to the agent
     * by clicking the link to view the invoice
     */
    public function viewInvoiceAction()
    {

        $invoice = new Datasource_Core_Agent_Invoice();
        $invoiceStatus = new Datasource_Core_Agent_InvoiceViewStatus();

        /*
            1. Get invoice filename from invoicing table - homeletuk_com.invoiceHashLookup
            2. Make request for url from fileserver using Zend_XmlRpc_Client
            3. Redirect client to url
        */
        $filename = $invoice->getInvoiceFilename
            (
                $this->_agentSchemeNumber,
                $_GET['month'],
                $_GET['year']
            );

        $rpcclient = new Zend_XmlRpc_Client($this->_params->fileserver->requestHost);
        $retrieveurl = null;

        try
        {
            $retrieveurl = $rpcclient->call('authenticate', array($filename, 0));
            $digest = hash('sha256', $filename . 0);
        }
        catch(Zend_XmlRpc_Client_FaultException $ex)
        {
            $retrieveurl = null; // Ensure url is null to skip next section
        }

        if (isset($retrieveurl)) {
            // Insert agents invoiceView flag
            $invoiceStatus->insertInvoiceViewStatus($this->_agentSchemeNumber, $this->_agentId, $this->_agentrealname, $_GET['month'], $_GET['year']);

            $this->_redirect($this->_params->fileserver->request . $retrieveurl . '?filehash=' . $digest);
        }
        // Some kind of error, report error page
        $this->render('invoices_report_error');
    }

    public function commissionAction()
    {
        $request = $this->getRequest();

        $commissionselection = new Connect_Form_Commission();
        $this->view->commissionselection = $commissionselection;

        if ($request->isPost())
        {
            $formdata = $request->getPost();
            $commissionselection->populate($formdata);

            if ($commissionselection->isValid($formdata))
            {
                $startdate = new Zend_Date
                (
                    '01/' .
                    $commissionselection->getElement('commission_startmonth')->getValue() . '/' .
                    $commissionselection->getElement('commission_startyear')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                $enddate = new Zend_Date
                (
                    '01/' .
                    $commissionselection->getElement('commission_endmonth')->getValue() . '/' .
                    $commissionselection->getElement('commission_endyear')->getValue(),
                    Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR
                );

                // Move end date forward one month and back one day to get to the last day of the month
                $enddate->add(1, Zend_Date::MONTH);
                $enddate->sub(1, Zend_Date::DAY);

                $report = new Datasource_Connect_Mi_InsuranceSales();
                $this->view->reportdata = $report->commissionForMonthYear($this->_agentSchemeNumber, $startdate, $enddate);

                $this->view->headLink()->appendStylesheet('/assets/connect/css/print.css', 'print');
                $this->render('commission_report');
            }
        }
    }

    public function visualAction()
    {
        // Add necessary view files required for the visual reports page

        // Charting jquery plugin
        $this->view->headScript()->appendFile('/assets/vendor/jqPlot/js/excanvas.js', 'text/javascript', array('conditional' => 'lt IE 9'));
        $this->view->headScript()->appendFile('/assets/vendor/jqPlot/js/jquery.jqplot.min.js', 'text/javascript');

        // Charting plugin extensions
        $this->view->headScript()->appendFile('/assets/vendor/jqPlot/js/plugins/jqplot.json2.min.js', 'text/javascript');
        $this->view->headScript()->appendFile('/assets/vendor/jqPlot/js/plugins/jqplot.highlighter.min.js', 'text/javascript');
        $this->view->headScript()->appendFile('/assets/vendor/jqPlot/js/plugins/jqplot.cursor.min.js', 'text/javascript');
        $this->view->headScript()->appendFile('/assets/vendor/jqPlot/js/plugins/jqplot.dateAxisRenderer.min.js', 'text/javascript');
        $this->view->headScript()->appendFile('/assets/vendor/jqPlot/js/plugins/jqplot.canvasTextRenderer.min.js', 'text/javascript');
        $this->view->headScript()->appendFile('/assets/vendor/jqPlot/js/plugins/jqplot.canvasAxisLabelRenderer.min.js', 'text/javascript');

        // Charting plugin stylesheets
        $this->view->headLink()->appendStylesheet('/assets/vendor/jqPlot/css/jquery.jqplot.min.css');

        // Print css, for printing the charts
        $this->view->headLink()->appendStylesheet('/assets/connect/css/print.css', 'print');
    }

    /**
     * Private function to send IE-friendly headers for CSV file output over
     * HTTPS.
     *
     * @param string filename Name of CSV file being output.
     */
    private function _sendCsvHeaders($filename)
    {
        /* // This is the original code, but over HTTPS IE doesn't like it.
        $this->getResponse()->setHeader('Content-type', 'text/csv');
        $this->getResponse()->setHeader('Content-disposition', 'attachment; filename=referencing-report.csv');
        $this->getResponse()->setHeader('Pragma', 'public');
        */
        header('Pragma: public'); // required
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for certain browsers
        header("Content-Disposition: attachment; filename={$filename}");
        header('Content-type: text/csv');
    }
        
    /**
     * Retrieve the AgentID for the reference and display the report.
     *
     * @todo: push parameters into the view
     *
     * @return void
     */
    public function retrieveAction() {

        $baseRefUrl = $this->_params->connect->baseUrl->referencing;

        $request = $this->getRequest();              
        $refno=$request->getParam('refno');
        $reptype=$request->getParam('repType');

        // Requested time generation for the report        
        $timegenerated1=$request->getParam('generated');
        if (!$timegenerated1) {
            $timegenerated1=0;
        }
        
        // Latest time generation of the report
        $reportDatasource = new Datasource_ReferencingLegacy_ReportHistory();        
        $timegenerated2 = $reportDatasource->getTimeReportGenerated($refno, $reptype);
        //$timegenerated2=strtotime($timegenerated);
              
        // If refno belongs to the agent then push params into view                                                                                                       
        if ($this->_isReferenceOwnedBy($refno, $this->_agentSchemeNumber)) {
            // Create Filters for params
            $filters = array('*' => array('StringTrim','HtmlEntities','StripTags')); 
            //Create Validators
            $validators = array('*' => array('allowEmpty' => false));
            // Check values
            $requestFilter = new Zend_Filter_Input($filters, $validators, array('refno' => $refno, 'reptype'=>$reptype));
            if ($requestFilter->isValid()) {            
                $this->view->refNo=$requestFilter->getEscaped('refno');
                $this->view->repType=$requestFilter->getEscaped('reptype');
                
                $this->view->timegenerated1=$timegenerated1;
                $this->view->timegenerated2=$timegenerated2;        
                
//              $this->_helper->layout()->disableLayout();           
//              $page=$this->_helper->redirector->gotoUrlAndExit($baseRefUrl . '/cgi-bin/refviewreport.pl?refno=' . $refno.'&repType=interim');
//              $this->_redirect($page);
            }
        } else {                        
            $this->render('report_error');
        }
    }
   
    /**
     * Check this reference against an ASN to check for ownership
     * 
     * @param int $ownerAgentId Agent ID of the agent we're checking for ownership
     * @param string $refno Reference number of the reference to check
     * @return boolean
     */
    private function _isReferenceOwnedBy($refno, $ownerAgentId) {
	
        // Find the ASN of the reference                            
        $enquiryDatasource = new Datasource_ReferencingLegacy_Enquiry();
        $agentID = $enquiryDatasource->getReferenceAgentID($refno);

        return ($agentID == $ownerAgentId);	
    }
 
    /**
     * To Download/View the Final/Intereim report.
     *
     * @todo: The legacy URL should be parameterised.
     *
     * @return void
     */
    public function viewReportPdfAction()
    {
        if (!$this->_isReferenceOwnedBy($this->getRequest()->getParam('refno'), $this->_agentSchemeNumber)) {
            throw new Exception("Agent does not own this reference");
        }

        $baseRefUrl = $this->_params->connect->baseUrl->referencing;
        
        $reportUri = $baseRefUrl . 'cgi-bin/refviewreport.pl?refno=' .
                                        $this->getRequest()->getParam('refno') .'&repType=' .
                                        $this->getRequest()->getParam('repType');

        $filename = $this->_buildReportAttachementFilename($this->getRequest()->getParam('repType') ?: 'Report',
            $this->getRequest()->getParam('refno'));

        switch ($this->getRequest()->getParam('contentDisposition')) {

            // View in-line
            default:
            case "view":
                header('Pragma: '); // Remove pragma
                header('Cache-Control: '); 
                header('Content-Type: application/pdf');
                break;
            
            // Download
            case "attachment":
                header('Pragma: '); // Remove pragma
                header('Cache-Control: '); // Remove cache control
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.$filename);
                break;
        }

        // Get the latest report
        $reportDatasource = new Datasource_ReferencingLegacy_ReportHistory();
        $report = $reportDatasource->getLatestReport($this->getRequest()->getParam('refno'));
        $timegenerated = null;
        if ($report && isset($report->generationTime)) {
            $timegenerated = strtotime($report->generationTime);
        }

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
            if (!$pdfContent) {
                error_log('Critical Error: ' . curl_error($curl));
                exit('Critical Error: Please contact us');
            }

            curl_close($curl);

            // Cache result
            Application_Cache_Referencing_ReportFileCache::getInstance()->set($filename, $pdfContent, $timegenerated);

            $this->getResponse()->appendBody($pdfContent);
        }

        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    private function _buildReportAttachementFilename($refType, $refNo)
    {
        return sprintf('%s.pdf', ucfirst($refType) . "-" . str_replace(array('.', '/'), '-', $refNo));
    }
}


