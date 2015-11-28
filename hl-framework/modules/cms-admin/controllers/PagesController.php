<?php
class CmsAdmin_PagesController extends Zend_Controller_Action {

	public function init() {
        // Start the zend layout engine and load the cms admin layout
        Zend_Layout::startMvc();
        $this->_helper->layout->setLayout('default');
    }
    
    /**
     * Show a list of site pages in the admin system
     *
     * @return void
     */
    public function indexAction() {
        $this->view->currentPage = 'pages';

        $pages = new Datasource_Cms_Pages();
        $pagesArray = $pages->getPageList();

        $pageList = $this->view->partialLoop('partials/pages-row.phtml', $pagesArray);
        $this->view->pageList = $pageList;

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
     * Save changes to an existing page, or create a new page. If creating a new page the function will return the ID
     *
     * @return int
     */
    protected function _savePage($siteID) {
        // First of all we need to validate and sanitise the input from the form
        $urlFilter = new Zend_Filter();
        $urlFilter->addFilter(new Zend_Filter_StringTrim);
        $urlFilter->addFilter(new Zend_Filter_StringTrim('/'));

        $requiredText = new Zend_Validate();
        $requiredText->addValidator(new Zend_Validate_NotEmpty);

        $filters = array(
            'id'                =>  'Digits',
            'pageTitle'         =>  'StringTrim',
            'pageURL'           =>  $urlFilter,
            'metaKeywords'      =>  'StringTrim',
            'metaDescription'   =>  'StringTrim',
            'template'          =>  'Digits'
        );
        $validators = array(
            'id'                =>  array('allowEmpty'  =>  true),
            'pageTitle'         =>  $requiredText,
            'pageURL'           =>  'NotEmpty',
            'metaKeywords'      =>  array('allowEmpty'  =>  true),
            'metaDescription'   =>  array('allowEmpty'  =>  true),
            'pageContent'       =>  array('allowEmpty'  =>  true),
            'template'          =>  'NotEmpty'
        );

        $input = new Zend_Filter_Input($filters, $validators, $_POST);
        if ($input->isValid()) {
            // Data is all valid, formatted and sanitized so we can save it in the database
            $page = new Datasource_Cms_Pages();
			
			$auth = Zend_Auth::getInstance();
	        $auth->setStorage(new Zend_Auth_Storage_Session('hl_admin'));
	        $username = $auth->getStorage()->read()->username;
			
            if (!$input->id) {
                // This is a new page so we need to create a new ID
                $pageID = $page->addNew($siteID, $input->pageTitle, $input->pageURL, $input->getUnescaped('pageContent'), $input->metaKeywords, $input->metaDescription, $input->template);
                // Record activity
		        Application_Core_ActivityLogger::log('CMS Page Added', 'complete', 'CMS-Admin', $username, "Page URL: /". $input->pageURL);
            } else {
                // This is an existing page

                // If the URL is not editable, use the original saved version
                $pageEdit = $page->getByID($input->id);
                if ($pageEdit['urlEditable'] == 0) {
                    $input->pageURL = $pageEdit['url'];
                }
                
                $page->saveChanges($input->id, $input->pageTitle, $input->pageURL, $input->getUnescaped('pageContent'), $input->metaKeywords, $input->metaDescription, $input->template);
                $pageID = $input->id;
                Application_Core_ActivityLogger::log('CMS Page Edited ', 'complete', 'CMS-Admin', $username, "Page URL: /". $input->pageURL);
            }

            // Now we need to save any meta data associated with this page
            $metaFields = $page->getMetaFields($pageID);

            // We now have an array that tells us what meta fields are expected and what type they are - so first
            //  we need to make a filter and validator array for them.
            // This could be expanded in the future to allow us to require certain meta data to be entered
            //  if a 'required' flag is set in the meta database.
            $metaFilters = array();
            $metaValidators = array();
            foreach ($metaFields as $metaField) {
                if ($metaField['metaType'] == 'string' || $metaField['metaType'] == 'html' || $metaField['metaType'] == 'icon') {
                    array_push($metaFilters,array(
                        $metaField['metaName']   =>  'StringTrim'
                    ));
                    array_push($metaValidators,array(
                        $metaField['metaName']  =>  array('allowEmpty'  =>  true)
                    ));
                }
            }

            $metaInput = new Zend_Filter_Input($metaFilters, $metaValidators, $_POST);
            $metaDataArray = array();
            foreach ($metaFields as $metaField) {
                if ($metaField['metaType'] == 'html') {
                    $metaDataArray[$metaField['metaName']] = $metaInput->getUnescaped($metaField['metaName']);
                } else {
                    $metaDataArray[$metaField['metaName']] = $metaInput->getEscaped($metaField['metaName']);
                }
            }

            $page->saveMeta($pageID, $metaDataArray);


            // Changes saved - so send them back with a nice success message
            $this->_helper->getHelper('FlashMessenger')->addMessage(array('saved' => true));
            $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/pages/edit?id='.$pageID);
        } else {
            // Invalid data in form
            /*
            print_r($_POST);
            print_r($input->getErrors());
            print_r($input->getInvalid());
            */
        }
    }


    /**
     * Edit an existing site page
     *
     * @return void
     */
    public function editAction() {
        $this->view->currentPage = 'pages';
		$siteID = $this->getRequest()->getParam('site');
		if ($siteID == '') { $siteID = 1; }
			
        if ($this->getRequest()->isPost()) {
            // Save changes
            $this->_savePage($siteID);
        } else {
            // Edit page
            $pageID = $this->getRequest()->getParam('id');
			
            $page = new Datasource_Cms_Pages();
            $pageEdit = $page->getByID($pageID);

            $passThrough = $this->_helper->getHelper('FlashMessenger')->getMessages();
            if (count($passThrough)>0) {
                if (isset($passThrough[0]['saved'])) {
                    if ($passThrough[0]['saved'] == true) $this->view->saved=true;
                }
                if (isset($passThrough[0]['errorMessage'])) {
                    $this->view->errorMessage = $passThrough[0]['errorMessage'];
                }
            }
            $this->view->pageContent = $pageEdit['pageContent'];
            $this->view->pageTitle = $pageEdit['pageTitle'];
            $this->view->pageURL = $pageEdit['url'];
            $this->view->pageID = $pageID;
            $this->view->metaKeywords = $pageEdit['keywords'];
            $this->view->metaDescription = $pageEdit['description'];
            $this->view->urlEditable = $pageEdit['urlEditable']==1?true:false;

            $meta = $page->getMeta($pageID);
            $metaFieldData = $page->getMetaFields($pageID);
            foreach ($metaFieldData as &$metaRow) {
                if (isset($meta[$metaRow['metaName']])) $metaRow['value'] = $meta[$metaRow['metaName']];
            }

            $metaFieldList = $this->view->partialLoop('partials/edit-page-metafield.phtml',$metaFieldData);
            $this->view->metaFields = $metaFieldList;

            // Load the possible testimonial tags from the database
            $testimonials = new Datasource_Cms_Testimonials();
            $this->view->testimonialTags = $testimonials->getPossibleTags();

            // Load the possible header quote tags from the database
            $quotes = new Datasource_Cms_HeaderQuotes();
            $this->view->quoteTags = $quotes->getPossibleTags();

            $templates = new Datasource_Cms_Page_Template();
            $templateArray = $templates->getAll($siteID);
            foreach ($templateArray as &$template) {
                $template['current'] = $template['id'] == $pageEdit['layoutID']?true:false;
            }
            $templateList = $this->view->partialLoop('partials/edit-page-templatefield.phtml',$templateArray);
            $this->view->templateFields = $templateList;
        }
    }


    /**
     * Add a new page to the site
     *
     * @return void
     */
    public function addAction() {
        $this->view->currentPage = 'pages';
        $siteID = $this->getRequest()->getParam('site');
		if ($siteID == '') { $siteID = 1; }
        $templates = new Datasource_Cms_Page_Template();
            $templateArray = $templates->getAll($siteID);
            $templateList = $this->view->partialLoop('partials/edit-page-templatefield.phtml',$templateArray);
            $this->view->templateFields = $templateList;
        $page = new Datasource_Cms_Pages();
        if ($this->getRequest()->isPost()) {
            $this->_savePage($siteID);
        }
    }


    /**
     * Delete an existing site page
     *
     * @return void
     */
    public function deleteAction() {
        $pageID = $this->getRequest()->getParam('id');

        $this->view->currentPage = 'pages';
        $pageDatasource = new Datasource_Cms_Pages();
        $page = $pageDatasource->getByID($pageID);
        $pageDatasource->remove($pageID);

		// Record activity
		$auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_admin'));
        $username = $auth->getStorage()->read()->username;
        Application_Core_ActivityLogger::log('CMS Page Deleted', 'complete', 'CMS-Admin', $username, "Page URL: /". $page['url']);
        
        // Changes saved - so send them back with a nice success message
        $this->_helper->getHelper('FlashMessenger')->addMessage(array('deleted' => true));
        $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/pages');
    }

}

?>