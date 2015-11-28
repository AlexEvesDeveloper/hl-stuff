<?php
/**
 * Standard zend error controller to capture and handle errors
 *
 */
class Error_ErrorController extends Zend_Controller_Action
{
    private $_logger;
    private $_extendedMessage;
    
    public function errorAction()
    {
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
        
        $errors = $this->_getParam('error_handler');
		
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                $this->_extendedMessage = $errors->exception->getMessage();
                $this->render('error-404');
                break;
                
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                $this->_extendedMessage = $errors->exception->getMessage();
                $this->render('error-404');
                break;
                
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                $this->_extendedMessage = "";
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $trace = $errors->exception->getTrace();
            foreach ($trace as &$traceItem) {
                unset($traceItem['args']);
                unset($traceItem['type']);
            }
            $log->setEventItem('extendedMessage', $errors->exception->getMessage());
            $log->setEventItem('ipAddress', $this->getRequest()->getServer('REMOTE_ADDR'));
            $log->setEventItem('requestURL', $errors->request->getRequestUri());
            $log->setEventItem('trace', print_r($trace,true));
            $log->setEventItem('file', $errors->exception->getFile());
            $log->setEventItem('line', $errors->exception->getLine());
            $log->crit($this->view->message, $errors->exception);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request = $errors->request;
        
        if (APPLICATION_ENV != 'development') {
        	// Show friendly error page as we're likely in a production environment
        	$this->render('error-friendly');
    }
    }

    public function getLog()
    {
        $this->_logger = Zend_Registry::get('logger');
        if (!$this->_logger) {
            return false;
        }
        return $this->_logger;
    }
}

