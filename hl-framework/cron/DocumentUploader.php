<?php

final class Cron_DocumentUploader
{
    private $httpclient = null;
    private $sessionid = null;
    
    public function run()
    {
        $offset = 0;
        $queue = new Datasource_Insurance_Document_InsuranceRequest();
        $history = new Datasource_Insurance_Document_InsuranceRequestHistory();
        $templates = new Datasource_Insurance_Document_InsuranceTemplates();
        
        // Authenticate with document production server
        $this->_remoteServerAuthentication();
        
        while ($request = $queue->retrieveNextRequest($offset))
        {
            //error_log('request id: ' . $request['request_id']);
            
            $this->httpclient->resetParameters();
            $this->httpclient->setParameterPost
            (
                array
                (
                    'PHPSESSID'     => $this->sessionid,
                    'action'        => 'ComposeDoc',
                    'CustomerCode'  => $this->customercode,
                    'DocTypeName'   => $templates->getTemplateName($request['template_id']),
                    'VarDocData'    => base64_encode($request['request_xml']),
                )
            );
            
            try
            {
                $response = $this->httpclient->request('POST');
            }
            catch(Zend_Http_Client_Exception $ex)
            {
                error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
                $offset++;
                continue;
            }
            
            $this->_debugRequest($this->httpclient);
            
            if (is_string($response))
            {
                $response = Zend_Http_Response::fromString($response);
            }
            else if (!$response instanceof Zend_Http_Response)
            {
                // Some other response returned, don't know how to process.
                // The request is queued, so return a fault.
                error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
                $offset++;
                continue;
            }
            
            try
            {
                $response = $response->getBody();
                $createresponse = Zend_Json::decode($response);
            }
            catch(Exception $ex)
            {
                // Problem requesting service, report the problem back but keep the queue record
                error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
                $offset++;
                continue;
            }
            
            // Check login response
            if ($createresponse == null)
            {
                // Problem requesting document generation with DMS server
                error_log(__FILE__ . ':' . __LINE__ . ':Failed to request document generation from DMS server for request');
                $offset++;
                continue;
            }
            
            // Check create response
            if ($createresponse['Status'] != 'DocComp_OK' && $createresponse['Status'] != 'DocComp_WRN')
            {
                // Problem creating document. Report failure but keep the queue record
                error_log(__FILE__ . ':' . __LINE__ . ':Failed to request document generation from DMS server for request: ' .
                          $createresponse['MsgID'] . ':' . $createresponse['MsgText']);
                
                // Failed to generate document, skip to next docment
                $offset++;
                continue;
            }
            
            // Move the queue record from the queue table into the history table
            try
            {
                // Insert into history
                $history->storeQueueRecord($request);
                
                // Delete previous request
                $queue->deleteRequest($request['request_hash']);
            }
            catch(Zend_Db_Exception $ex)
            {
                error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
                throw new Exception('Failed to move document request to history');
            }
        }
    }
    
    /**
     * Authenticate with the remote document production server
     * Sets the property httpclient to an instance of Zend_Http_Client
     * for further interaction with the document production server.
     *
     * @return bool
     */
    private function _remoteServerAuthentication()
    {
        $config = Zend_Registry::get('params');
        
        $host = '';
        $authk = '';
        $requesttimeout = '';
        
        // Capture DMS service parameters
        if (isset($config->dms) && isset($config->dms->requestHost))
            $host = $config->dms->requestHost;
        
        if (isset($config->dms) && isset($config->dms->authToken))
            $authk = $config->dms->authToken;
        
        if (isset($config->dms) && isset($config->dms->requestTimeout))
            $requesttimeout = $config->dms->requestTimeout;
            
        if (isset($config->dms) && isset($config->dms->customercode))
            $customercode = $config->dms->customercode;
            
        // Validate parameters are ok
        if (!isset($host) || $host == '')
        {
            error_log(__FILE__ . ':' . __LINE__ . ':DMS service host not set');
            throw new Exception('DMS service host not set');
        }
        
        if (!isset($authk) || $authk == '')
        {
            error_log(__FILE__ . ':' . __LINE__ . ':DMS service auth token not set');
            throw new Exception('DMS service auth token not set');
        }
        
        if (!isset($requesttimeout) || $requesttimeout == '')
        {
            // Default to 15 seconds
            $requesttimeout = 15;
        }
        
        $client = new Zend_Http_Client
        (
            $host . '?action=UserAuth&language=gb&cc=' . $customercode,
            array
            (
                'maxredirects' => 0,
                'timeout'      => $requesttimeout, 
                'keepalive'    => true,
            )
        );
        
        // Set the adapter
        $client->setAdapter(new Zend_Http_Client_Adapter_Curl());
        
        // Disable SSL certificate verification, fails in testing
        $client->getAdapter()->setCurlOption(CURLOPT_SSL_VERIFYHOST, 0);
        $client->getAdapter()->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
        
        // Login to DMS service
        $client->setParameterPost
        (
            array
            (
                'authmethod'    => '1',
                'AuthK'         => $authk,
                'login'         => '', // Must be set to blank, using authk but have to be passed
                'password'      => '', // Must be set to blank, using authk but have to be passed
            )
        );
        
        try
        {
            $response = $client->request('POST');
        }
        catch(Zend_Http_Client_Exception $ex)
        {
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Exception('Failure calling DMS server');
        }
        
        $this->_debugRequest($client);
        
        if (is_string($response))
        {
            $response = Zend_Http_Response::fromString($response);
        }
        else if (!$response instanceof Zend_Http_Response)
        {
            // Some other response returned, don't know how to process.
            // The request is queued, so return a fault.
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Exception('DMS server returned unknown response');
        }
        
        try
        {
            $response = $response->getBody();
            $loginresponse = Zend_Json::decode($response);
        }
        catch(Exception $ex)
        {
            // Problem requesting service, report the problem back but keep the queue record
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Exception('DMS server returned invalid response');
        }
        $client->resetParameters();
        
        // Check login response
        if ($loginresponse == null)
        {
            // Problem authenticating with DMS server
            error_log(__FILE__ . ':' . __LINE__ . ':Failed to auth DMS server for request');
            throw new Exception('Failed to auth DMS server for request');
        }
        
        // Make request for creation
        $client->setUri($host); // Remove get parameters from original login uri
        
        $this->httpclient = $client;
        $this->sessionid = $loginresponse['SessionId'];
        $this->customercode = $customercode;
        
        return true;
    }
    
    /**
     * Debug helper function to log the last request/response pair against the DMS server
     *
     * @parma Zend_Http_Client $client Zend Http client object
     * @return void
     */
    private function _debugRequest(Zend_Http_Client $client)
    {
        $config = Zend_Registry::get('params');
        $logfile = '';
        
        // Capture DMS service parameters
        if (isset($config->dms) && isset($config->dms->logfile))
            $logfile = $config->dms->logfile;
        
        if ($logfile != null && APPLICATION_ENV != 'production')
        {
            $request = $client->getLastRequest();
            $response = $client->getLastResponse();
            
            $fh = fopen($logfile, 'a+');
            fwrite($fh, $request);
            fwrite($fh, "\n\n");
            fwrite($fh, $response);
            fwrite($fh, "\n\n\n\n");
            fclose($fh);
        }
    }
}
