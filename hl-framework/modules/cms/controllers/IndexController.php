<?php

class Cms_IndexController extends Zend_Controller_Action
{
    /**
     * Initialise the cms controller
     *
     * @return void
     */
    public function init() {
        // Start the zend layout engine and tell it where we put our layouts
        $session = new Zend_Session_Namespace('homelet_global');
        Zend_Layout::startMvc();

        $this->url = $this->getRequest()->getRequestUri();
        // Trim the leading forward slash off
        $this->url = substr($this->url,1);

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayoutPath(APPLICATION_PATH . '/modules/cms/layouts/scripts');
        $layout->setLayout('error');
        $this->view->headLink()->setStylesheet('/assets/cms/css/cms.css');

        // Trim any GET parameters off
        if (($questionMarkPos = strpos($this->url, '?')) !== false) {
            $this->url = substr($this->url, 0, $questionMarkPos);
        }

        if ($this->url == '') $this->url = 'home';
        
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
    
    public function helppopupAction() {
        // Fetch content from CMS using key
        $contentKey = $this->getRequest()->getParam('key');
        
        $panelObj = new Datasource_Cms_Panels();
        $contentArray = $panelObj->getByKey($contentKey);
        
        $this->view->content = $contentArray['content'];
        
        // Disable the layout
        $this->_helper->layout->disableLayout();
    }    
    
    /***************************************************************************************/
    /* VIEW PAGE FUNCTIONS                                                                 */
    /***************************************************************************************/
    
    /**
     * Handles loading and generating a cms controlled page
     *
     * @return void
     */
    public function viewPageAction() {
    	
        $cmsPage = new Datasource_Cms_Pages();
        $page = $cmsPage->getByUrl($this->url);
        
        // Try to work out whether some sub-styling (landlords, tenants, letting-agents) needs to be applied
        $urlSplit = explode('/', $this->url);
        $pageStyling = 'corporate';
        
        switch ($urlSplit[0]) {
        	case 'landlords':
        		$pageStyling = 'landlords';
        		break;
        	case 'tenants':
        		$pageStyling = 'tenants';
        		break;
        	case 'letting-agents':
        		$pageStyling = 'letting-agents';
        		break;
        }
        
        if (count($page)>0) {
            // Valid page in the database so load it's template and pass in the meta and content data
            $content = $this->view->partial('templates/' . $page['template'] . '.phtml', array(
                'meta'      =>  $page['meta'],
                'content'   	=>  $page['content'],
                'pageStyling'	=>	$pageStyling));
            
            // Replace code snippets in the content
            $snippets = new Application_Cms_PageSnippets();
            $content = $snippets->replace($content);
            
            $this->view->content = $content;
            $this->view->pageTitle = $page['title'];
            $this->view->description = $page['description'];
            $this->view->keywords = $page['keywords'];
        } else {
            // Throw a 404 error
	    	throw new Zend_Controller_Action_Exception("This page doesn't exist", 404);
        }
    }
        
    public function landlordLowdownSignupAction() {
		// Throw a 404 error
	    throw new Zend_Controller_Action_Exception("This page doesn't exist", 404);
		die();
		// NOT CURRENTLY IN USE
		// @codeCoverageIgnoreStart	
		
		
        $formInput = array();
        $formInput['email'] = htmlentities($this->getRequest()->getParam('email'));

        // Check e-mail present and valid
        $filters = array(
            'email' => 'StringTrim'
        );
        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => 'Domain name invalid in email address',
                Zend_Validate_EmailAddress::INVALID_FORMAT      => 'Invalid email address'
            )
        );
        $validators = array(
            'email' => $emailValidator
        );
        $validate = new Zend_Filter_Input($filters, $validators, $formInput);
        if ($validate->isValid()) {

            // E-mail address valid, instantiate subscription manager using the e-mail address
            $email = $formInput['email'];
            $subscriptionManager = new Manager_Core_Subscription($email);
            // Create subscription
            try {
                $subscriptionManager->subscribe('landlord-lowdown');
                $this->view->content = "Subscribed to Landlord Lowdown with address <em>{$email}</em>.  Please check your e-mail (including any bulk mail filters) to confirm activation of your subscription.";
            } catch (Exception $e) {
                $this->view->content = 'Sorry, we were unable to set up your subscription for the following reason(s):<br /><br />' . $e->getMessage();
		    }
        } else {

            // E-mail address didn't validate, show flattened errors
            $allErrors = $validate->getMessages();
            $errors = '';
            foreach($allErrors as $key => $val) {
                foreach($val as $subkey => $subval) {
                    $errors .= "{$subval}<br />";
				}
            }
            $this->view->content = 'Sorry, we were unable to set up your subscription for the following reason(s):<br /><br />' . $errors;
        }

        // Disable the layout
        $this->_helper->layout->disableLayout();
        
        // @codeCoverageIgnoreEnd
    }

}
