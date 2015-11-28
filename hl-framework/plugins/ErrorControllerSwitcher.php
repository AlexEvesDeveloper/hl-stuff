<?php
class Plugin_ErrorControllerSwitcher extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown (Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        // Check to make sure we have an error handler - the default one is defined in bootstrap.php
        if (!($front->getPlugin('Zend_Controller_Plugin_ErrorHandler') instanceof Zend_Controller_Plugin_ErrorHandler)) {
            return;
        }
        $error = $front->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        
        // Create a test http request for the module's error handler
        $testRequest = new Zend_Controller_Request_Http();
        $testRequest->setModuleName($request->getModuleName())
                    ->setControllerName($error->getErrorHandlerController())
                    ->setActionName($error->getErrorHandlerAction());
        
        // If the module does have an error handler that can deal with the request
        // we will use it - otherwise we leave the default error handler in place
        if ($front->getDispatcher()->isDispatchable($testRequest)) {
            $error->setErrorHandlerModule($request->getModuleName());
        }
    }
}
