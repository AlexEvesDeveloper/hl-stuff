<?php

/**
 * JSON over REST abstract controller.
 */
abstract class RestAbstractController extends Zend_Rest_Controller
{
    protected $_accessor;
    protected $_params;
    protected $_restAction;
    protected $_requestParameters;

    /**
     * Initialise the response object for JSON output, enforce security, and
     * capture any REST action and request parameters.
     *
     * @return void
     */
    public function init()
    {
        // Load application parameters
        $this->_params = Zend_Registry::get('params');

        // Ensure response object is keyed up to output uncached JSON
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Cache-Control', 'no-cache, must-revalidate')
            ->setHeader('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');

        // Ensure request uses the REST API's username and password
        $this->_enforceSecurity();

        // Get the request parameters from the request URI for individual
        //   controllers' use
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $requestUri = $request->getRequestUri();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        // Chop off everything preceding and including the module and controller
        //   names
        $requestUri = preg_replace("/^.*{$module}\/{$controller}\/?/", '', $requestUri);
        // Get parameters from whatever's left
        $this->_requestParameters = explode('/', $requestUri);

        // Set REST virtual action using the first parameter, if there are any
        //   parameters in the first place (if not, remember this will map to an
        //   INDEX verb request)
        if (count($this->_requestParameters) > 0) {
            $this->_restAction = array_shift($this->_requestParameters);
        }
    }

    /**
     * Secures the API access to only requests that authenticate using the
     * HomeLet REST API realm.  Based on SOAP modules's index controller's
     * security.
     *
     * @return void
     */
    protected function _enforceSecurity()
    {
        $response = Zend_Controller_Front::getInstance()->getResponse();
        $request = Zend_Controller_Front::getInstance()->getRequest();

        // Ensure REST access is allowed
        if (!$this->_params->security->allowRestAccess) {
            $response->clearHeaders();
            $response->setHttpResponseCode(403);
            $response->sendHeaders();
            die('RESTful access disabled');
        }

        $config = array(
            'accept_schemes' => 'basic',
            'realm' => 'HomeLet REST API',
            'nonce_timeout' => 3600
        );

        $adapter = new Zend_Auth_Adapter_Http($config);

        $resolver = new Zend_Auth_Adapter_Http_Resolver_File(APPLICATION_PATH . '/configs/rest_password.txt');
        $adapter->setBasicResolver($resolver);

        $storage = new Zend_Auth_Storage_NonPersistent;
        Zend_Auth::getInstance()->setStorage($storage);

        $adapter->setRequest($request);
        $adapter->setResponse($response);

        $result = Zend_Auth::getInstance()->authenticate($adapter);

        // Ensure we're running over SSL
        if ($request->getScheme() != 'https') {
            $response->clearHeaders();
            $response->setHttpResponseCode(403);
            $response->sendHeaders();
            die('Insecure connection scheme');
        }

        // Bad userame/password, or cancelled password prompt
        if (!$result->isValid()) {
            $response->clearHeaders();
            $response->setHttpResponseCode(401);
            $response->setHeader('WWW-Authenticate', "{$config['accept_schemes']} realm=\"{$config['realm']}\"");
            $response->sendHeaders();
            die('Invalid username/password');
        }

        $response->sendHeaders();
    }
}