<?php

// This class can't really be included in code coverage
// @codeCoverageIgnoreStart

/*
 Current username and password for soap service is :-
 HomeLet_API
 uPyu9J8AK5HmDuhx
*/

class Soap_IndexController extends Zend_Controller_Action
{
    public function init() {

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
	}

    /**
     * Provides basic httpd security for the soap calls
     *
     * @return void
     *
     * This function forces basic http security. I wish we could do this site wide with digest domains but it doesn't seem
     * to work properly in Zend at the moment. So this function is called by the actions we want to protect. Dirty :(
     */
    protected function enforceSecurity($passwdFilePath = '/configs/soap_password.txt') {
        $config = array(
            'accept_schemes' => 'basic',
            'realm' => 'HomeLet API',
            'nonce_timeout' => 3600
        );

        // Don't request authentication if this is the KeyHouse server
        // TEMPORARY FIX - SECURITY ISSUE
        // TODO: Remove me!

        if ($_SERVER['REMOTE_ADDR'] != '82.44.126.148') {
            $adapter = new Zend_Auth_Adapter_Http($config);

            $resolver = new Zend_Auth_Adapter_Http_Resolver_File(APPLICATION_PATH . $passwdFilePath);
            $adapter->setBasicResolver($resolver);

            $storage = new Zend_Auth_Storage_NonPersistent;
            Zend_Auth::getInstance()->setStorage($storage);
            $response = Zend_Controller_Front::getInstance()->getResponse();


            $response = new Zend_Controller_Response_Http;
            $request = new Zend_Controller_Request_Http;

            $adapter->setRequest($request);
            $adapter->setResponse($response);

            $result = Zend_Auth::getInstance()->authenticate($adapter);
            $response->sendHeaders();

            if (!$result->isValid()) {
                // Bad userame/password, or cancelled password prompt
                die ('Invalid username/password');
            }
        }
    }

	/**
	 * Returns the FSA status for a given agent (built for FastAnt)
	 * 
	 * @return void
	 */
	public function fsaStatusAction() {
		$params = Zend_Registry::get('params');
        $this->enforceSecurity('/configs/fastant_password.txt');
		if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/fsa-status');
            $autoDiscover->setClass('Service_Referencing_FsaStatus');
            $autoDiscover->handle();
		} else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
            array('uri' => $params->homelet->domain.'/soap/fsa-status'));
			
			// register php exceptions
			$server->registerFaultException(array('Exception'));

            // set SOAP service class
            $server->setClass('Service_Referencing_FsaStatus');

            // handle request
            $server->handle();
		}
	}
	
	/**
     * Defines the referencing TAT (tenancy application tracker) web service
     *
     * @return void
     */
	public function tatNotifyAction() {

		$params = Zend_Registry::get('params');
        $this->enforceSecurity();
        if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/tat-notify');
            $autoDiscover->setClass('Service_Referencing_TatAccessor');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
            array('uri' => $params->homelet->domain.'/soap/tat-notify'));

            // set SOAP service class
            $server->setClass('Service_Referencing_TatAccessor');

            // handle request
            $server->handle();
        }
	}

	/**
     * Defines the referencing insert notice web service.
     *
     * @return void
     */
	public function insertNoticeAction() {

		$params = Zend_Registry::get('params');
        $this->enforceSecurity();
        if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/insert-notice');
            $autoDiscover->setClass('Service_Referencing_TatAccessor');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
            array('uri' => $params->homelet->domain.'/soap/insert-notice'));

            // set SOAP service class
            $server->setClass('Service_Referencing_TatAccessor');

            // handle request
            $server->handle();
        }
	}

    /**
     * Defines the MOTD web service
     *
     * @return void
     */
    public function motdAction()
    {
        $params = Zend_Registry::get('params');

        $this->enforceSecurity();
        if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/motd');
            $autoDiscover->setClass('Service_Connect_MotdAccessor');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
            array('uri' => $params->homelet->domain.'/soap/motd'));

            // set SOAP service class
            $server->setClass('Service_Connect_MotdAccessor');

            // handle request
            $server->handle();
        }
    }

    /**
     * Defines the blog web service
     *
     * @return void
     */
    public function blogAction()
    {
        $params = Zend_Registry::get('params');

        $this->enforceSecurity();
        if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/blog');
            $autoDiscover->setClass('Service_Connect_BlogAccessor');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
            array('uri' => $params->homelet->domain.'/soap/blog'));

            // set SOAP service class
            $server->setClass('Service_Connect_BlogAccessor');

            // handle request
            $server->handle();
        }
    }

    /**
     * Defines the referencing web service
     *
     * @return void
     */
    public function referencingAction()
    {
        $params = Zend_Registry::get('params');
        $this->enforceSecurity();
        if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/referencing');
            $autoDiscover->setClass('Application_Referencing_Functions');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
            array('uri' => $params->homelet->domain.'/soap/referencing'));

            // set SOAP service class
            $server->setClass('Application_Referencing_Functions');

            // handle request
            $server->handle();
        }
    }

    /**
     * Defines the agent web service
     *
     * @return void
     */
    public function agentAction ()
    {
        $params = Zend_Registry::get('params');

        $this->enforceSecurity();
        if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/agent');
            $autoDiscover->setClass('Application_Agent_Functions');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
            array('uri' => $params->homelet->domain.'/soap/agent'));

            // set SOAP service class
            $server->setClass('Application_Agent_Functions');

            // handle request
            $server->handle();
        }
    }


    /**
     * Defines the agent web service
     *
     * @return void
     */
    public function tenantAction ()
    {
        $params = Zend_Registry::get('params');

        $this->enforceSecurity();
        if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/tenant');
            $autoDiscover->setClass('Application_Tenant_InsuranceQuote');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
            array('uri' => $params->homelet->domain.'/soap/tenant'));

            // set SOAP service class
            $server->setClass('Application_Tenant_InsuranceQuote');

            // handle request
            $server->handle();
        }
    }

    /**
     * Defines the document production services
     *
     * @return void
     */
    public function insuranceDocumentAction ()
    {
        $params = Zend_Registry::get('params');

        if(isset($_GET['wsdl']))
        {
            $fh = fopen(APPLICATION_PATH . '/models/services/Wsdls/Document/insurance-document.wsdl', 'r');
            $wsdldata = fread($fh, filesize(APPLICATION_PATH . '/models/services/Wsdls/Document/insurance-document.wsdl'));
            fclose($fh);

            echo $wsdldata;
        }
        else
        {
            $this->enforceSecurity(); // Only done when talking to the soap service
            $server = new Zend_Soap_Server
            (
                APPLICATION_PATH . '/models/services/Wsdls/Document/insurance-document.wsdl',
                array
                (
                    'uri' => $params->homelet->domain.'/soap/insurance-document',
                    'features' => SOAP_SINGLE_ELEMENT_ARRAYS
                )
            );

            // Use the Soap fault exception to raise soap faults back to client.
            $server->registerFaultException('Application_Soap_Fault');

            // set SOAP service class
            $server->setClass('Service_Insurance_Document');

            // handle request
            $server->handle();
        }
    }

    /**
     * For the rent guarantee claims suite
     */
    public function rentguaranteeclaimAction()
    {
        $params = Zend_Registry::get('params');
        $this->enforceSecurity();
        if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/rentguaranteeclaim');
            $autoDiscover->setClass('Service_Connect_RGCSAccessor');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
            array('uri' => $params->homelet->domain.'/soap/rentguaranteeclaim'));

            // set SOAP service class
            $server->setClass('Service_Connect_RGCSAccessor');

            // handle request
            $server->handle();
        }
    }
    
    /**
     * For Bank Validation
     */
    public function bankValidateAction()
    {
        $params = Zend_Registry::get('params');
        $this->enforceSecurity();
        if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/bank-validate');
            $autoDiscover->setClass('Service_Core_BankAccessor');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
            array('uri' => $params->homelet->domain.'/soap/bank-validate'));

            // set SOAP service class
            $server->setClass('Service_Core_BankAccessor');

            // handle request
            $server->handle();
        }
    }

    /**
    * Fetches all the products for RG
    */
    public function rgProductOfferedAction()
    {
        $params = Zend_Registry::get('params');
        //$this->enforceSecurity();
        if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/rg-product-offered');
            $autoDiscover->setClass('Service_Core_RGProductOfferedAccessor');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
            array('uri' => $params->homelet->domain.'/soap/rg-product-offered'));

            // set SOAP service class
            $server->setClass('Service_Core_RGProductOfferedAccessor');
            $server->setObject(new Manager_Core_RGProductOffered());
            // handle request
            $server->handle();
        }
    }
    /**
     * For Bank Validation
     */
    public function partialRegistrationAction()
    {
        $params = Zend_Registry::get('params');
        $this->enforceSecurity();
        if(isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/partial-registration');
            $autoDiscover->setClass('Service_Core_PartialRegistration');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
                array('uri' => $params->homelet->domain.'/soap/partial-registration'));

            // set SOAP service class
            $server->setClass('Service_Core_PartialRegistration');

            // handle request
            $server->handle();
        }
    }

    public function updatePersonalDetailsAction()
    {
        $this->enforceSecurity();
        $params = Zend_Registry::get('params');

        if (isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/update-personal-details');
            $autoDiscover->setClass('Service_Core_UpdatePersonalDetails');
            $autoDiscover->handle();
        }
        else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
                array('uri' => $params->homelet->domain.'/soap/update-personal-details'));

            // set SOAP service class
            $server->setClass('Service_Core_UpdatePersonalDetails');

            // handle request
            $server->handle();
        }
    }
    
    public function channelAction()
    {
        $this->enforceSecurity();
        $params = Zend_Registry::get('params');

        if (isset($_GET['wsdl'])) {
            $autoDiscover = new Zend_Soap_AutoDiscover();
            $autoDiscover->setUri($params->homelet->domain.'/soap/channel');
            $autoDiscover->setClass('Service_Core_ChannelAccessor');
            $autoDiscover->handle();
        } else {
            // Disable all layouts
            $this->getHelper('viewRenderer')->setNoRender(true);

            // initialize server and set URI
            $server = new Zend_Soap_Server(null,
                array('uri' => $params->homelet->domain.'/soap/channel'));

            // set SOAP service class
            $server->setClass('Service_Core_ChannelAccessor');

            // handle request
            $server->handle();
        }
    }
}
        
// @codeCoverageIgnoreEnd
