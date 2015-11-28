<?php
class CmsAdmin_TestimonialsController extends Zend_Controller_Action
{
    
    public function init() {
        // Start the zend layout engine and load the cms admin layout
        Zend_Layout::startMvc();
        $this->_helper->layout->setLayout('default');
    }
    
    /**
     * Show a list of testimonials in the admin system
     *
     * @return void
     */
    public function indexAction() {
        $this->view->currentPage = 'testimonials';
        $testimonial = new Datasource_Cms_Testimonials();
        $testimonialsArray = $testimonial->getAll();

        $this->view->testimonialList = $this->view->partialLoop('partials/testimonials-row.phtml', $testimonialsArray);

        $passThrough = $this->_helper->getHelper('FlashMessenger')->getMessages();
        if (count($passThrough)>0) {
            if (isset($passThrough[0]['saved'])) {
                if ($passThrough[0]['saved'] == true) $this->view->saved=true;
            }
            if (isset($passThrough[0]['deleted'])) {
                if ($passThrough[0]['deleted'] == true) $this->view->deleted=true;
            }
            if (isset($passThrough[0]['errorMessage'])) {
                $this->view->errorMessage = $passThrough[0]['errorMessage'];
            }
        }
    }
	
    /**
     * Deletes a testimonial entry
     *
     * @return void
     */
    public function deleteAction() {
        $this->view->currentPage = 'testimonials';
        $testimonialDatasource = new Datasource_Cms_Testimonials();

        $testimonialID = $this->getRequest()->getParam('id');
        $testimonial = $testimonialDatasource->getByID($testimonialID);
        $testimonialDatasource->remove($testimonialID);

		// Record activity
		$auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_admin'));
        $username = $auth->getStorage()->read()->username;
        Application_Core_ActivityLogger::log('CMS Testimonial Deleted', 'complete', 'CMS-Admin', $username, "Testimonial Author: ". $testimonial['person']);
        
        // Changes saved - so send them back with a nice success message
        $this->_helper->getHelper('FlashMessenger')->addMessage(array('deleted' => true));
        $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/testimonials');
    }


    /**
     * Edit an existing testimonial entry
     *
     * @return void
     */
    public function editAction() {
        $this->view->currentPage = 'testimonials';
        $testimonials = new Datasource_Cms_Testimonials();

        if ($this->getRequest()->isPost()) {
            // Save changes
            $this->_saveTestimonial();
        }

        $testimonialID = $this->getRequest()->getParam('id');
        $testimonial = $testimonials->getByID($testimonialID);

        $this->view->person = $testimonial['person'];
        $this->view->quote = $testimonial['quote'];
        $this->view->id = $testimonialID;
        $this->view->tags = $testimonial['tags'];;

        // Load the possible testimonial tags from the database
        $this->view->testimonialTags = $testimonials->getPossibleTags();

        $passThrough = $this->_helper->getHelper('FlashMessenger')->getMessages();
        if (count($passThrough)>0) {
            if (isset($passThrough[0]['saved'])) {
                if ($passThrough[0]['saved'] == true) $this->view->saved=true;
            }
            if (isset($passThrough[0]['errorMessage'])) {
                $this->view->errorMessage = $passThrough[0]['errorMessage'];
            }
        }
    }


    /**
     * Add a new testimonial to the database
     *
     * @return void
     */
    public function addAction() {
        $this->view->currentPage = 'testimonials';

        // Load the possible testimonial tags from the database
        $testimonials = new Datasource_Cms_Testimonials();
        $this->view->testimonialTags = $testimonials->getPossibleTags();

        if ($this->getRequest()->isPost()) {
            // Save changes
            $this->_saveTestimonial();
        }
    }


    /**
     * Save a new testimonial, or changes to an existing testimonial. If it's a new entry the function will return the ID for the record
     *
     * @return int
     */
    protected function _saveTestimonial() {
        $requiredText = new Zend_Validate();
        $requiredText->addValidator(new Zend_Validate_NotEmpty);

        $tagFilter = new Zend_Filter();
        $tagFilter->addFilter(new Zend_Filter_StringTrim);
        $tagFilter->addFilter(new Zend_Filter_StringTrim(','));

        $filters = array(
            'id'                =>  'Digits',
            'person'            =>  'StringTrim',
            'quote'             =>  'StringTrim',
            'tags'              =>  $tagFilter
        );
        $validators = array(
            'id'            =>  array('allowEmpty'  =>  true),
            'person'        =>  $requiredText,
            'quote'         =>  array('allowEmpty'  =>  true),
            'tags'          =>  $requiredText
        );

        $input = new Zend_Filter_Input($filters, $validators, $_POST);
        if ($input->isValid()) {
            $testimonial = new Datasource_Cms_Testimonials();
            // Data is all valid, formatted and sanitized so we can save it in the database

            if (!$input->id) {
                // This is a new testimonial so we need to create a new ID
                $testimonialID = $testimonial->addNew($input->person, $input->quote, $input->tags);
            } else {
                // This is an existing article so we can just update the data
                $testimonial->saveChanges($input->id, $input->person, $input->quote, $input->tags);
                $testimonialID = $input->id;
            }

            // Changes saved - so send them back with a nice success message
            $this->_helper->getHelper('FlashMessenger')->addMessage(array('saved' => true));
            $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/testimonials/edit?id='.$testimonialID);
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