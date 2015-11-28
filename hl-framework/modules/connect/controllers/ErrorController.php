<?php
/**
 * Standard zend error controller to capture and handle errors
 *
 */
require_once('ConnectAbstractController.php');
class Connect_ErrorController extends ConnectAbstractController
{
    private $_logger;
    private $_extendedMessage;

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- route, controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                $this->_extendedMessage = $errors->exception->getMessage();
                $this->render('error-404');
                break;
            // TODO: Doesn't work:
            case 403:
                // 403 error -- forbidden
                $this->getResponse()->setHttpResponseCode(403);
                $this->view->message = 'Access forbidden';
                $this->_extendedMessage = $errors->exception->getMessage();
                $this->render('error-403');
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

