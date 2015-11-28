<?php
class CmsAdmin_VacanciesController extends Zend_Controller_Action {
    public function init() {
        // Start the zend layout engine and load the cms admin layout
        Zend_Layout::startMvc();
        $this->_helper->layout->setLayout('default');
    }
	
    /**
     * Show a list of active, expired and future career vacancies in the admin system
     *
     * @return void
     */
    public function indexAction() {
    	$this->view->currentPage = 'jobVacancies';
        $careers = new Datasource_Cms_Careers();
        $activeCareerList = $careers->getActive();
        $expiredCareerList = $careers->getExpired();
        $futureCareerList = $careers->getFuture();

        $this->view->activeCareerList = $this->view->partialLoop('partials/careers-vacancyrow.phtml', $activeCareerList);
        $this->view->expiredCareerList = $this->view->partialLoop('partials/careers-vacancyrow.phtml', $expiredCareerList);
        $this->view->futureCareerList = $this->view->partialLoop('partials/careers-vacancyrow.phtml', $futureCareerList);

        $passThrough = $this->_helper->getHelper('FlashMessenger')->getMessages();
        if (count($passThrough)>0) {
            if (isset($passThrough[0]['saved'])) {
                if ($passThrough[0]['saved'] == true) $this->view->saved=true;
            }
            if (isset($passThrough[0]['errorMessage'])) {
                $this->view->errorMessage = $passThrough[0]['errorMessage'];
            }
            if (isset($passThrough[0]['deleted'])) {
                if ($passThrough[0]['deleted'] == true) $this->view->deleted=true;
            }
        }
    }

    /**
     * Edit an existing vacancy
     *
     * @return void
     */
    public function editAction() {
    	$this->view->currentPage = 'jobVacancies';
        if ($this->getRequest()->isPost()) {
            // Save changes
            $this->_saveVacancy();
        } else {
            $careers = new Datasource_Cms_Careers();
            $vacancyID = $this->getRequest()->getParam('id');
            $vacancy = $careers->getByID($vacancyID);

            $this->view->id = $vacancyID;
            $this->view->jobTitle = $vacancy['title'];
            $this->view->startDate = $vacancy['startDate'];
            $this->view->endDate = $vacancy['endDate'];
            $this->view->jobDescription = $vacancy['description'];
            $this->view->location = $vacancy['location'];
            $this->view->reportingTo = $vacancy['reportingTo'];
        }
    }

    /**
     * Delete an existing vacancy
     *
     * @return void
     */
    public function deleteAction() {
        $vacancyID = $this->getRequest()->getParam('id');
        $careers = new Datasource_Cms_Careers();
        $job = $careers->getByID($vacancyID);
        $careers->remove($vacancyID);

		// Record activity
		$auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_admin'));
        $username = $auth->getStorage()->read()->username;
        Application_Core_ActivityLogger::log('CMS Job Vacancy Deleted', 'complete', 'CMS-Admin', $username, "Job Title: ". $job['title']);
        
        $this->_helper->getHelper('FlashMessenger')->addMessage(array('deleted' => true));
        $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/vacancies');
    }
    
    /**
     * Add a new vacancy
     *
     * @return void
     */
    public function addAction() {
        if ($this->getRequest()->isPost()) {
            // Save changes
            $this->_saveVacancy();
        }
    }
	
    protected function _saveVacancy() {
        // First of all we need to validate and sanitise the input from the form
        $requiredText = new Zend_Validate();
        $requiredText->addValidator(new Zend_Validate_NotEmpty);

        $filters = array(
            'id'                =>  'Digits',
            'jobTitle'          =>  'StringTrim',
            'reportingTo'       =>  'StringTrim',
            'location'          =>  'StringTrim',
            'startDate'         =>  'StringTrim',
            'endDate'           =>  'StringTrim',
            'jobDescription'    =>  'StringTrim'
        );
        $validators = array(
            'id'                =>  array('allowEmpty' => true),
            'jobTitle'          =>  $requiredText,
            'reportingTo'       =>  array('allowEmpty' => true),
            'location'          =>  array('allowEmpty' => true),
            'startDate'         =>  $requiredText,
            'endDate'           =>  $requiredText,
            'jobDescription'       =>  array('allowEmpty' => true)
        );

        $input = new Zend_Filter_Input($filters, $validators, $_POST);
        if ($input->isValid()) {
            // Data is all valid, formatted and sanitized so we can save it in the database
            $careers = new Datasource_Cms_Careers();

            if (!$input->id) {
                // This is a new vacancy so we need to create a new ID
                $vacancyID = $careers->addNew($input->jobTitle, $input->reportingto, $input->location, $input->getUnescaped('startDate'), $input->getUnescaped('endDate'), $input->getUnescaped('jobDescription'));
            } else {
                $careers->saveChanges($input->id, $input->jobTitle, $input->reportingTo, $input->location, $input->getUnescaped('startDate'), $input->getUnescaped('endDate'), $input->getUnescaped('jobDescription'));
                $vacancyID = $input->id;
            }

            // Changes saved - so send them back with a nice success message
            $this->_helper->getHelper('FlashMessenger')->addMessage(array('saved' => true));
            $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/vacancies');
        } else {
            // Invalid data in form
            /*
            print_r($_POST);
            print_r($input->getErrors());
            print_r($input->getInvalid());
            */
        }
    }

}
?>