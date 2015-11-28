<?php

class Cms_CareersController extends Zend_Controller_Action
{

	public function init() {
        // Start the zend layout engine and tell it where we put our layouts
        $session = new Zend_Session_Namespace('homelet_global');
        Zend_Layout::startMvc();
		// Use the CMS layout
		Zend_Layout::getMvcInstance()->setLayoutPath( APPLICATION_PATH . '/modules/cms/layouts/scripts/' );
        $this->url = $this->getRequest()->getRequestUri();
        // Trim the leading forward slash off
        $this->url = substr($this->url,1);
        
        // Check to see if we have a referrer code - if we do store it in a session variable
        if ($this->getRequest()->getParam('referrer')!='') {
            $session->referrer = $this->getRequest()->getParam('referrer');
        }
        
        // Check to see if we have an agent scheme number - if we do store it in a session variable
        if ($this->getRequest()->getParam('asn')!='') {
            $session->agentSchemeNumber = $this->getRequest()->getParam('asn');
        }
        
        // Populate the menus into the layout
        $menuData = array();
        // This could be quite yucky - I'm just trying to work out how to get the menu structure to work
        if (strpos($this->url,'/')>0) {
            $urlSplit = explode('/',$this->url);
            $menuData['selected'] = $urlSplit[0];
        }
        $menuData['url'] = $this->url;
        
        $params = Zend_Registry::get('params');
        $urlArray = array();
        foreach ($params->url as $key => $url)
        {
            $urlArray[$key] = $url;
        }
        $menuData['linkUrls'] = $urlArray;
        
        $mainMenu = $this->view->partial('partials/homelet-mainmenu.phtml', $menuData);
        $subMenu = $this->view->partial('partials/homelet-submenu.phtml', $menuData);
        $layout = Zend_Layout::getMvcInstance();
        $layout->getView()->mainMenu = $mainMenu;
        $layout->getView()->subMenu = $subMenu;
        $layout->getView()->linkUrls = $urlArray;
        if (isset($menuData['selected'])) { $layout->getView()->styling = $menuData['selected']; }
        
        // Load the site link urls from the parameters and push them into the layout
        $params = Zend_Registry::get('params');
        $layout->getView()->urls = $params->url->toArray();
    }
	
 	/**
     * Load a list of currently active job vacancies and show them in the correct templaet
     *
     * @return void
     */
    public function indexAction() {
        $careers = new Datasource_Cms_Careers();
        
        $careerList = $careers->getActive();
        $this->view->content = $this->view->partialLoop('partials/career-vacancy.phtml', $careerList);
        $this->view->pageTitle = 'Careers';
    }
    
    public function applyAction() {
        $this->view->pageTitle = 'Careers';
        
        if ($this->getRequest()->isPost()) {
            // Handle the cv file and form data
            $filters = array(
                'name'      =>  'StringTrim',
                'tel'       =>  'StringTrim',
                'email'     =>  'StringTrim',
                'enquiry'   =>  'StringTrim');
            
            $validators = array (
                'name'      =>  array('NotEmpty','messages' => 'Please enter your name'),
                'tel'       =>  array('NotEmpty','messages' => 'Please enter your telephone number'),
                'email'     =>  array('NotEmpty','messages' => 'Please enter your email address'),
                'enquiry'   =>  array('NotEmpty','messages' => 'Please tell us why this position interests you'));
            
            $input = new Zend_Filter_Input($filters, $validators, $_POST);
            
            if ($input->isValid()) {
                $upload = new Zend_File_Transfer();
                
                // Make sure the file is actually a document
                $upload->clearValidators();
                $upload->setOptions(array('ignoreNoFile' => true));
                //$upload->addValidator('MimeType', false, array('application/msword', 'application/pdf', 'application/rtf', 'text/plain'));

                if ($upload->isValid()) {
                    $params = Zend_Registry::get('params');
                    $uploadPath = $params->cms->fileUploadPath;
                    $upload->setDestination($uploadPath);
                    $upload->receive();
                    
                    $fileInfo = $upload->getFileInfo();
                    
                    $emailer = new Application_Core_Mail();
                    $emailer->setTo($params->email->careers, 'HomeLet');
                    $emailer->setFrom($input->email, $input->name);
                    $emailer->setSubject('HomeLet - Job Application (' . $input->position . ')');
                    $bodyHtml  = 'Position : ' . $input->position . '<br />';
                    $bodyHtml .= 'Name : ' . $input->name . '<br />';
                    $bodyHtml .= 'Email : ' . $input->email . '<br />';
                    $bodyHtml .= 'Tel : ' . $input->tel . '<br />';
                    $bodyHtml .= 'Enquiry : <pre>' . $input->enquiry . '</pre><br />';
                    if ($fileInfo['cv_file']['type']!== null) { $emailer->addAttachment($fileInfo['cv_file']['destination'].'/'.$fileInfo['cv_file']['name'],$fileInfo['cv_file']['name']); }
                    $emailer->setBodyHtml($bodyHtml);
                    
                    if ($emailer->send()) {
                        $this->_helper->redirector('thanks','careers');
                    } else {
                    }
                    
                } else {
                    // Invalid file type
                    $this->view->errors = array('cv_file' => 'Invalid file type');
                    $this->view->name = $input->name;
                    $this->view->tel = $input->tel;
                    $this->view->email = $input->email;
                    $this->view->enquiry = $input->enquiry;
                }
            } else {
                // Invalid form data
                $this->view->errors = $input->getMessages();
                $this->view->name = $input->name;
                $this->view->tel = $input->tel;
                $this->view->email = $input->email;
                $this->view->enquiry = $input->enquiry;
            }
        }
        
        $careerUrl = $this->getRequest()->getParam('careerID');
        $careerID = substr($careerUrl,0,strpos($careerUrl,'-'));

        $careers = new Datasource_Cms_Careers();
        $career = $careers->getById($careerID);
                
        $this->view->title = $career['title'];
        $this->view->id = $career['id'];
        
    }
    
    public function thanksAction()
    {
    }

}
?>
