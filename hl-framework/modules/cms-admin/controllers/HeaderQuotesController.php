<?php
class Cmsadmin_HeaderQuotesController extends Zend_Controller_Action
{
    
    public function init() {
        // Start the zend layout engine and load the cms admin layout
        Zend_Layout::startMvc();
        $this->_helper->layout->setLayout('default');
        require_once('gChart.php'); // Include the gChart
    }
	
    /**
     * Show a list of header quotes in the admin system
     *
     * @return void
     */
    public function indexAction() {
        $this->view->currentPage = 'headerQuotes';
        $headerQuotes = new Datasource_Cms_HeaderQuotes();
        $headerQuotesArray = $headerQuotes->getAll();

        $this->view->quoteList = $this->view->partialLoop('partials/header-quotes-row.phtml', $headerQuotesArray);

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
     * Deletes a quote entry
     *
     * @return void
     */
    public function deleteAction() {
        $this->view->currentPage = 'headerQuotes';
        $quoteDatasource = new Datasource_Cms_HeaderQuotes();

        $quoteID = $this->getRequest()->getParam('id');
        $quote = $quoteDatasource->getByID($quoteID);
        $quoteDatasource->remove($quoteID);

		// Record activity
		$auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_admin'));
        $username = $auth->getStorage()->read()->username;
        Application_Core_ActivityLogger::log('CMS Header Quote Deleted', 'complete', 'CMS-Admin', $username, "Quote: ". print_r($quote,true));
        
        // Changes saved - so send them back with a nice success message
        $this->_helper->getHelper('FlashMessenger')->addMessage(array('deleted' => true));
        $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/header-quotes');
    }



    /**
     * Edit an existing quote entry
     *
     * @return void
     */
    public function editAction() {
        $this->view->currentPage = 'headerQuotes';
        $quotes = new Datasource_Cms_HeaderQuotes();

        if ($this->getRequest()->isPost()) {
            // Save changes
            $this->_saveQuote();
        }

        $quoteID = $this->getRequest()->getParam('id');
        $quote = $quotes->getByID($quoteID);

        $this->view->title = $quote['title'];
        $this->view->subtitle = $quote['subtitle'];
        $this->view->id = $quoteID;
        $this->view->tags = $quote['tags'];

        // Load the possible testimonial tags from the database
        $this->view->quoteTags = $quotes->getPossibleTags();

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
     * Add a new quote to the database
     *
     * @return void
     */
    public function addAction() {
        $this->view->currentPage = 'headerQuote';

        // Load the possible testimonial tags from the database
        $quotes = new Datasource_Cms_HeaderQuotes();
        $this->view->quoteTags = $quotes->getPossibleTags();

        if ($this->getRequest()->isPost()) {
            // Save changes
            $this->_saveQuote();
        }
    }



    /**
     * Save a new testimonial, or changes to an existing testimonial. If it's a new entry the function will return the ID for the record
     *
     * @return int
     */
    protected function _saveQuote() {
        $requiredText = new Zend_Validate();
        $requiredText->addValidator(new Zend_Validate_NotEmpty);

        $tagFilter = new Zend_Filter();
        $tagFilter->addFilter(new Zend_Filter_StringTrim);
        $tagFilter->addFilter(new Zend_Filter_StringTrim(','));

        $filters = array(
            'id'                =>  'Digits',
            'title'             =>  'StringTrim',
            'subtitle'          =>  'StringTrim',
            'tags'              =>  $tagFilter
        );
        $validators = array(
            'id'            =>  array('allowEmpty'  =>  true),
            'title'         =>  $requiredText,
            'subtitle'      =>  array('allowEmpty'  =>  true),
            'tags'          =>  $requiredText
        );

        $input = new Zend_Filter_Input($filters, $validators, $_POST);
        if ($input->isValid()) {
            $quote = new Datasource_Cms_HeaderQuotes();
            // Data is all valid, formatted and sanitized so we can save it in the database

            if (!$input->id) {
                // This is a new quote so we need to create a new ID
                $quoteID = $quote->addNew($input->getUnescaped('title'), $input->subtitle, $input->tags);
            } else {
                // This is an existing article so we can just update the data
                $quote->saveChanges($input->id, $input->getUnescaped('title'), $input->subtitle, $input->tags);
                $quoteID = $input->id;
            }

            // Changes saved - so send them back with a nice success message
            $this->_helper->getHelper('FlashMessenger')->addMessage(array('saved' => true));
            $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/header-quotes/edit?id='.$quoteID);
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