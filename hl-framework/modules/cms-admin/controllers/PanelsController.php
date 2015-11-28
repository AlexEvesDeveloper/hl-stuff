<?php
class CmsAdmin_PanelsController extends Zend_Controller_Action {
	
	public function init() {
        // Start the zend layout engine and load the cms admin layout
        Zend_Layout::startMvc();
        $this->_helper->layout->setLayout('default');
    }
    
    /**
     * Show a list of site panels in the admin system
     *
     * @return void
     */
    public function indexAction() {
        $panelsModel = new Datasource_Cms_Panels();
        $panels = $panelsModel->getAll();

        $this->view->panelsList = $this->view->partialLoop('/partials/panels-row.phtml', $panels);
    }

    /**
     * Edit a panel in the admin system
     *
     * @return void
     */
    public function editAction() {
        if ($this->getRequest()->isPost()) {
            // Save changes
            $this->_savePanel();
        } else {
            $panelsModel = new Datasource_Cms_Panels();
            $id = $this->getRequest()->getParam('id');
            $panel = $panelsModel->getByID($id);

            $passThrough = $this->_helper->getHelper('FlashMessenger')->getMessages();
            if (count($passThrough)>0) {
                if (isset($passThrough[0]['saved'])) {
                    if ($passThrough[0]['saved'] == true) $this->view->saved=true;
                }
                if (isset($passThrough[0]['errorMessage'])) {
                    $this->view->errorMessage = $passThrough[0]['errorMessage'];
                }
            }

            $this->view->key = $panel['key'];
            $this->view->description = $panel['description'];
            $this->view->content = $panel['content'];
            $this->view->id = $panel['id'];
        }
    }


    /**
     * Save changes to an existing panel. This can be expanded to allow adding of new Panels in the future.
     *
     * @return void
     */
    protected function _savePanel() {
        // First of all we need to validate and sanitise the input from the form
        $urlFilter = new Zend_Filter();
        $urlFilter->addFilter(new Zend_Filter_StringTrim);
        $urlFilter->addFilter(new Zend_Filter_StringTrim('/'));

        $requiredText = new Zend_Validate();
        $requiredText->addValidator(new Zend_Validate_NotEmpty);

        $filters = array(
            'id'        =>  'Digits'
        );
        $validators = array(
            'id'        =>  array('allowEmpty'  =>  true),
            'content'   =>  array('allowEmpty'  =>  true),
        );

        $input = new Zend_Filter_Input($filters, $validators, $_POST);
        if ($input->isValid()) {
            // Data is all valid, formatted and sanitized so we can save it in the database
            $panel = new Datasource_Cms_Panels();

            if (!$input->id) {
                // This is a new panel so we need to create a new ID

                // NOT IMPLEMENTED - YET
            } else {
                $panel->saveChanges($input->id, $input->getUnescaped('content'));
                $panelID = $input->id;
            }

            // Changes saved - so send them back with a nice success message
            $this->_helper->getHelper('FlashMessenger')->addMessage(array('saved' => true));
            $this->_helper->getHelper('Redirector')->goToUrl('/cms-admin/panels/edit?id='.$panelID);
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