<?php
// TODO: Add unit tests
// @codeCoverageIgnoreStart

class CmsAdmin_IndexController extends Zend_Controller_Action
{
    
    public function init() {
        // Start the zend layout engine and load the cms admin layout
        Zend_Layout::startMvc();
        $this->_helper->layout->setLayout('default');
        require_once('gChart.php'); // Include the gChart
    }
    
    /**
     * Generate the dashboard with the google analytics graphs
     *
     * @return void
     */
    public function indexAction() {
        $params = Zend_Registry::get('params');

        $this->view->currentPage = 'dashboard';

        try {

            $email = $params->googleAnalytics->username;
            $password = $params->googleAnalytics->password;
            $profileID = $params->googleAnalytics->homeletProfileID;

            $client = Zend_Gdata_ClientLogin::getHttpClient($email, $password, Zend_Gdata_Analytics::AUTH_SERVICE_NAME);
            $service = new Zend_Gdata_Analytics($client);

            $firstOfMonth = date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
            $today = date("Y-m-d");

            $this->view->startDate = date("d/m/Y", strtotime($firstOfMonth));
            $this->view->today = date("d/m/Y", strtotime($today));

            // LINE CHART for visitors this month
            $query = $service->newDataQuery()
                ->setProfileId($profileID)
                ->addDimension(Zend_Gdata_Analytics_DataQuery::DIMENSION_DAY)
                ->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS)
                ->setStartDate($firstOfMonth)
                ->setEndDate($today)
                ->setSort(Zend_Gdata_Analytics_DataQuery::DIMENSION_DAY, false)
                ->setMaxResults(25);

            $result = $service->getDataFeed($query);
            $chartArray = array();
            foreach($result as $row){
                $chartArray[] = (int)$row->getValue('ga:visits')->getValue();
            }

            $visitorsChart = new gLineChart(800,200);
            $visitorsChart->addDataSet($chartArray);
            $visitorsChart->setLegend(array('Site Visitors'));
            $visitorsChart->setColors(array('FF6D1B'));
            $visitorsChart->setVisibleAxes(array('x','y'));
            $visitorsChart->setDataRange(0,7000);
            $visitorsChart->setLegendPosition('t');

            $visitorsChart->addAxisRange(0,0,count($chartArray),2);
            $visitorsChart->addAxisRange(1,0,7000);

            $visitorsChart->setGridLines(7,1000);
            $this->view->visitorGraphUrl = $visitorsChart->getUrl();

            // PIE CHART for sources
            $query = $service->newDataQuery()
                ->setProfileId($profileID)
                ->addDimension(Zend_Gdata_Analytics_DataQuery::DIMENSION_MEDIUM)
                ->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS)
                ->setStartDate($firstOfMonth)
                ->setEndDate($today)
                ->setSort(Zend_Gdata_Analytics_DataQuery::DIMENSION_MEDIUM, false)
                ->setMaxResults(500);

            $result = $service->getDataFeed($query);

            $chartArray = array();
            $colorArray = array();
            $labelArray = array();
            foreach($result as $row){
                $chartArray[] = (int)$row->getValue('ga:visits')->getValue();
                switch((string)$row->getValue('ga:medium')->getValue()) {
                    case '(none)':
                        $medium = 'Direct';
                        $color = 'FF6F1C';
                        break;
                    case 'email':
                        $medium = 'Email Campaign';
                        $color = 'FDBB30';
                        break;
                    case 'organic':
                        $medium = 'Search Engines';
                        $color = '7AC142';
                        break;
                    case 'referral':
                        $medium = 'Referring Site';
                        $color = 'E60E64';
                        break;
                    default:
                        $medium = (string)$row->getValue('ga:medium')->getValue();
                }
                $labelArray[] = $medium;
                $colorArray[] = $color;
            }

            $sourcesChart = new gPieChart(380,200);
            $sourcesChart->addDataSet($chartArray);
            $sourcesChart->setLegend($labelArray);
            $sourcesChart->setColors($colorArray);

            $this->view->sourcesGraphUrl = $sourcesChart->getUrl();

            // Table of top 20 keywords
            $query = $service->newDataQuery()
                ->setProfileId($profileID)
                ->addDimension(Zend_Gdata_Analytics_DataQuery::DIMENSION_KEYWORD)
                ->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS)
                ->setStartDate($firstOfMonth)
                ->setEndDate($today)
                ->setSort(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS, true)
                ->setMaxResults(20);

            $result = $service->getDataFeed($query);
            $keywordArray = array();
            foreach($result as $row){
                $keyword = (string)$row->getValue('ga:keyword')->getValue();
                if ($keyword=='(not set)') $keyword = '-';
                $keywordArray[$keyword] = (int)$row->getValue('ga:visits')->getValue();
            }

            $this->view->keywordList = $keywordArray;

            // PIE CHART for new/returning visitors
            $query = $service->newDataQuery()
                ->setProfileId($profileID)
                ->addDimension(Zend_Gdata_Analytics_DataQuery::DIMENSION_VISITOR_TYPE)
                ->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS)
                ->setStartDate($firstOfMonth)
                ->setEndDate($today)
                ->setSort(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS, true)
                ->setMaxResults(2);

            $result = $service->getDataFeed($query);

            $chartArray = array();
            $colorArray = array();
            $labelArray = array();
            foreach($result as $row){
                $chartArray[] = (int)$row->getValue('ga:visits')->getValue();
                $labelArray[] = (string)$row->getValue('ga:visitorType')->getValue();
                switch((string)$row->getValue('ga:visitorType')->getValue()) {
                    case 'Returning Visitor':
                        $color = 'FF6F1C';
                        break;
                    case 'New Visitor':
                        $color = '7AC142';
                        break;
                    default:
                }
                $colorArray[] = $color;
            }

            $newReturningChart = new gPieChart(380,200);
            $newReturningChart->addDataSet($chartArray);
            $newReturningChart->setLegend($labelArray);
            $newReturningChart->setColors($colorArray);

            $this->view->newReturningUrl = $newReturningChart->getUrl();

        } catch (Exception $e) {

            $this->view->statsUnavailable = true;

        }
    }
    
    
    /***************************************************************************************/
    /* AUTH FUNCTIONS                                                                      */
    /***************************************************************************************/
    
    /**
     * This function creates a zend form for a login box with the relevant validation
     *
     * @return Zend_Form
     * @munt 8 This shouldn't be in the controller!
     */
    protected function getLoginForm () {
        $form = new Zend_Form();
        $form->setAction('/login')
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
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_admin'));
        
        if ($auth->hasIdentity()) {
            // User is already logged in so just push them into the system
            $this->_redirect('/cms_admin/index');
        }
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            // We have post data from the login form - so attempt a login
            $form = $this->getLoginForm();
            
            if (!$form->isValid($_POST)) {
                // Form is invalid
                $this->view->form = $form;
                //return $this->render();
            }
            else
            {
                // The forms passed validation so we now need to check the identity of the user
                $adapter = Auth_CmsAdmin::getAdapter($form->getValues());
                $result = $auth->authenticate($adapter);
                if (!$result->isValid()) {
                    // Invalid credentials
                    $form->setDescription('Invalid credentials provided');
                    $this->view->form = $form;
                    //return $this->render('login'); // re-render the login form
                } else {
                    // Valid credentials - store the details we need from the database and move the user to the index page
                    $storage = $auth->getStorage();
                    $storage->write($adapter->getResultRowObject(array(
                        'username',
                        'real_name')));
                    
                    // Record activity
                    Application_Core_ActivityLogger::log('CMS Admin Login', 'complete', 'CMS-Admin', $_POST['username']);
                    
                    $this->_redirect('/cms-admin/index');
                }
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
        $this->_redirect('/cms-admin/login');
    }
}
// @codeCoverageIgnoreEnd