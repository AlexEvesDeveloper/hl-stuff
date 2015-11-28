<?php

final class Service_Insurance_Document
{
    /**
     * Controls for document attachments retrieval criteria
     */
    const DOCUMENT_ONLY = 1;
    const ATTACHMENTS_ONLY = 2;
    const DOCUMENT_AND_ATTACHMENTS = 3;

    private $httpclient = null;
    private $sessionid = null;
    private $customercode = null;

    /**
     * Add a request to the queue for creating a new document. For use when a document is not required immediately (e.g. CRON jobs).
     *
     * @param string $policynumber Document policy number
     * @param string $documentname Document type name, or template name
     * @param int $csuid Csu ID number
     * @param string $deliverymethod Delivery method required, print, email, fax or none
     * @param string $deliverytarget Target for delivery on email and fax based delivery methods
     * @param string $bucketname Bucket for determining the print bucket override
     * @param string $emailCatTarget Email category target
     * @param string $emailCat Email category
     * @param $documentDelivery
     * @param array $referencefields List of reference fields
     * @param array $properties List of properties
     * @param array $variables List of variables
     * @param array $policycovers List of policy covers
     * @param array $endorsements List of endorsements
     * @param array $pedalcycles List of pedalcycles
     * @param null $specpossessions
     * @param array $underwritingquestions List of underwriting questions
     * @param array $propertydetails List of property details
     * @param array $inserts List of inserts
     *
     * @return int
     */
    public function queueDocument($policynumber, $documentname, $csuid, $deliverymethod, $deliverytarget, $bucketname, $emailCatTarget, $emailCat, $documentDelivery, $referencefields,
                                  $properties = null, $variables = null, $policycovers = null, $endorsements = null, $pedalcycles = null,
                                  $specpossessions = null, $underwritingquestions = null, $propertydetails = null, $inserts = null)
    {
        list($xml, $uniquerequestid) = $this->_createAndStoreRequest
        (
            $policynumber,
            $documentname,
            $csuid,
            $deliverymethod,
            $deliverytarget,
            $bucketname,
            $emailCatTarget,
            $emailCat,
            $documentDelivery,
            $referencefields,
            $properties,
            $variables,
            $policycovers,
            $endorsements,
            $pedalcycles,
            $specpossessions,
            $underwritingquestions,
            $propertydetails,
            $inserts
        );
        
        $status = new stdClass();
        $status->status = 'QUEUED';
        $status->uniqueRequestID = $uniquerequestid;
        
        return $status;
    }

    /**
     * Send a document request for a new document immediately. For use when a document is required immediately (e.g. web front end)
     *
     * @param string $policynumber Policy number for request
     * @param string $documentname Document type name, or template name
     * @param int $csuid Csu ID number
     * @param string $deliverymethod Delivery method required, print, email, fax or none
     * @param string $deliverytarget Target for delivery on email and fax based delivery methods
     * @param string $bucketname name for determining the print bucket override
     * @param string $emailCatTarget Email category target
     * @param string $emailCat Email category
     * @param array $referencefields List of reference fields
     * @param array $properties List of properties
     * @param array $variables List of variables
     * @param array $policycovers List of policy covers
     * @param array $endorsements List of endorsements
     * @param array $pedalcycles List of pedalcycles
     * @param null $specpossessions
     * @param array $underwritingquestions List of underwriting questions
     * @param array $propertydetails List of property details
     * @param array $inserts List of inserts
     *
     * @throws Application_Soap_Fault
     * @return int
     */
    public function createDocument($policynumber, $documentname, $csuid, $deliverymethod, $deliverytarget, $bucketname, $emailCatTarget, $emailCat, $documentDelivery, $referencefields,
                                  $properties = null, $variables = null, $policycovers = null, $endorsements = null, $pedalcycles = null,
                                  $specpossessions = null, $underwritingquestions = null, $propertydetails = null, $inserts = null)
    {
        list($xml, $uniquerequestid) = $this->_createAndStoreRequest
        (
            $policynumber,
            $documentname,
            $csuid,
            $deliverymethod,
            $deliverytarget,
            $bucketname,
            $emailCatTarget,
            $emailCat,
            $documentDelivery,
            $referencefields,
            $properties,
            $variables,
            $policycovers,
            $endorsements,
            $pedalcycles,
            $specpossessions,
            $underwritingquestions,
            $propertydetails,
            $inserts
        );
        
        // Authenticate with document production server
        $this->_remoteServerAuthentication();
        
        $this->httpclient->setParameterPost
        (
            array
            (
                'PHPSESSID'     => $this->sessionid,
                'action'        => 'ComposeDoc',
                'CustomerCode'  => $this->customercode,
                'DocTypeName'   => $documentname,
                'VarDocData'    => base64_encode($xml),
            )
        );
        
        try
        {
            $response = $this->httpclient->request('POST');
        }
        catch(Zend_Http_Client_Exception $ex)
        {
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('Failure calling DMS server', 'Server');
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
            error_log(__FILE__ . ':' . __LINE__ . ':DMS server returned unknown response');
            throw new Application_Soap_Fault('DMS server returned unknown response', 'Server');
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
            throw new Application_Soap_Fault('DMS server returned invalid response', 'Server');
        }
        
        // Check login response
        if ($createresponse == null)
        {
            // Problem requesting document generation with DMS server
            error_log(__FILE__ . ':' . __LINE__ . ':Failed to request document generation from DMS server for request');
            throw new Application_Soap_Fault('Failed to request document generation from DMS server for request', 'Server');
        }
        
        // Check create response
        if ($createresponse['Status'] != 'DocComp_OK' && $createresponse['Status'] != 'DocComp_WRN')
        {
            // Problem creating document. Report failure but keep the queue record
            error_log(__FILE__ . ':' . __LINE__ . ':Failed to request document generation from DMS server for request: ' .
                      $createresponse['MsgID'] . ':' . $createresponse['MsgText']);
            
            $status = new stdClass();
            $status->status = 'QUEUED';
            $status->uniqueRequestID = $uniquerequestid;
            
            return $status;
        }
        
        // Move the queue record from the queue table into the history table
        try
        {
            // Retrieve queue record
            $xmlstore = new Datasource_Insurance_Document_InsuranceRequest();
            $queuerecord = $xmlstore->retrieveRequest($uniquerequestid);
            
            // Insert into history
            $historystore = new Datasource_Insurance_Document_InsuranceRequestHistory();
            $historystore->storeQueueRecord($queuerecord);
            
            // Delete previous request
            $xmlstore->deleteRequest($uniquerequestid);
        }
        catch(Zend_Db_Exception $ex)
        {
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('Failed to move document request to history', 'Server');
        }
        
        $status = new stdClass();
        $status->status = 'CREATED';
        $status->uniqueRequestID = $uniquerequestid;
        
        return $status;
    }

    /**
     * Fetch a generated document
     *
     * @param string $requesthash Request hash of document
     * @param sting $documentname Document name to search on
     *
     * @return string Url of document
     * @throws Application_Soap_Fault
     */
    public function fetchDocument($requesthash, $documentname)
    {
        $config = Zend_Registry::get('params');
        $homeletDomain = null;

        // Check the homelet domain parameter is set
        if (isset($config->homelet) && isset($config->homelet->domain))
            $homeletDomain = $config->homelet->domain;

        if ($homeletDomain == null)
        {
            // No homelet domain set
            error_log(__FILE__ . ':' . __LINE__ . ':HomeLet domain parameter not set');
            throw new Application_Soap_Fault('HomeLet domain parameter not set', 'Server');
        }

        list ($documentUrl, $attachments) = $this->retrieveDocumentFromStore($requesthash, $documentname);
        return $documentUrl;
    }

    /**
     * Retrieve a document from the document store and place in to local storage
     *
     * @param string $requesthash Unique request hash
     * @param string $documentname Document template name
     * @param int $associatedDocsInclusion Include associated documents. Defaults to the main document only if not given.
     * @return string Local file path to retrieved document
     * @throws Application_Soap_Fault
     */
    public function retrieveDocumentFromStore($requesthash, $documentname, $associatedDocsInclusion = 1)
    {
        $documentUrl = null;
        $attachmentCounter = 1;
        $documentAttachments = array();
        $requesthash = preg_replace('/\//', '', $requesthash); // Remove all slashes, this prevents directory traversals

        // Authenticate with document production server
        $this->_remoteServerAuthentication();

        // Get customer id and document type id
        $customerid = $this->getCustomerId($this->customercode);
        $documentid = $this->getDocTypeId($customerid, $documentname);
        $keyrefid = $this->getDocKeyReferenceId($customerid, $documentid, 'Unique Key');

        // Search for PDF from server
        try
        {
            $searchcriteria = Zend_Json::encode(array(array($keyrefid), array($requesthash)));
        }
        catch(Exception $ex)
        {
            // Problem encoding request
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('Json encoding error when forwarding request', 'Server');
        }

        // Request document
        $this->httpclient->setParameterGet
            (
                array
                (
                    'PHPSESSID'     => $this->sessionid,
                    'action'        => 'OpenDocument',
                    'CustomerID'    => $customerid,
                    'DocTypeID'     => $documentid,
                    'jsparam'       => $searchcriteria,
                    'dd_t1'         => 1,
                    'dd_t2'         => 1,
                    'dd_t3'         => 1,
                    'OnlyPDF'       => 1,
                    'ReturnInBody'  => 1,// Only return the raw PDF, don't return the side bar
                    'asct_doc_mode' => $associatedDocsInclusion
                )
            );

        try
        {
            $response = $this->httpclient->request('GET');
        }
        catch(Zend_Http_Client_Exception $ex)
        {
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('Failure calling DMS server', 'Server');
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
            error_log(__FILE__ . ':' . __LINE__ . ':DMS server returned unknown response');
            throw new Application_Soap_Fault('DMS server returned unknown response', 'Server');
        }

        // Check response
        if ($response == null)
        {
            // Problem requesting document from DMS server
            error_log(__FILE__ . ':' . __LINE__ . ':Failed to request document from DMS server for request');
            throw new Application_Soap_Fault('Failed to request document from DMS server for request', 'Server');
        }

        // Perform check for 202 response with application/pdf mime type
        if ($response->isSuccessful() !== true || $response->getBody() == '')
        {
            // Unsuccessful request or not a pdf
            error_log(__FILE__ . ':' . __LINE__ . ':Failed to fetch request');
            throw new Application_Soap_Fault('Failed to fetch request', 'Server');
        }

        try
        {
            $response = $response->getBody();
            $openresponse = Zend_Json::decode($response);
        }
        catch(Exception $ex)
        {
            // Problem requesting service, report the problem back
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('DMS server returned invalid response', 'Server');
        }

        // Retrieve primary document url
        $documentUrl = $this->_relativeToAbsolteUrl($openresponse['DocUrl']);
        unset ($openresponse['DocUrl']);

        // Retrieve the url for each attachment

        foreach ($openresponse as $attachment) {
            if (preg_match('/^SI_/', $attachment['ID'])) {
                // Only display inserts, nothing more
                $documentAttachments[] = array('name' => $attachment['Name'], 'url' => $this->_relativeToAbsolteUrl($attachment['Url']));
            }
        }
        
        return array($documentUrl, $documentAttachments);
    }

    /**
     * Converts a relative Aurora url to an absolute Aurora url.
     *
     * @param string $url Relative url
     * @return string Absolute url
     */
    private function _relativeToAbsolteUrl($url) 
    {
        $currenturi = $this->httpclient->getUri(true);
        $currenturi = preg_replace('/(.*\/)\?{0,1}.*/', '\\1', $currenturi);
        return $currenturi . $url;
    }


    /**
     * Build the XML structure to be sent to DMS solution
     *
     * @param string $policynumber Document policy number
     * @param $documentname
     * @param $csuid
     * @param string $deliverymethod Delivery method required, print, email, fax or none
     * @param string $deliverytarget Target for delivery on email and fax based delivery methods
     * @param string $bucketname Bucket for determining the print bucket override
     * @param string $emailCatTarget Email category target
     * @param string $emailCat Email category
     * @param $documentDelivery
     * @param array $referencefields List of reference fields
     * @param array $properties List of properties
     * @param array $variables List of variables
     * @param null $policycovers
     * @param null $endorsements
     * @param null $pedalcycles
     * @param null $specpossessions
     * @param null $underwritingquestions
     * @param null $propertydetails
     * @param null $inserts
     * @throws Application_Soap_Fault
     * @internal param string $documenttype Document type name, or template name
     * @return array XML string, generated unique reference number
     */
    private function _createAndStoreRequest($policynumber, $documentname, $csuid, $deliverymethod, $deliverytarget, $bucketname, $emailCatTarget, $emailCat, $documentDelivery, $referencefields,
                                            $properties = null, $variables = null, $policycovers = null, $endorsements = null, $pedalcycles = null,
                                            $specpossessions = null, $underwritingquestions = null, $propertydetails = null, $inserts = null)
    {
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->preserveWhiteSpace = true;
        $doc->formatOutput = true;

        // Create root element
        $root = $doc->createElementNS('urn:HomeLetDMSTypes', 'hl:documentRequest');
        $doc->appendChild($root);
        
        // Create basic data
        $uniquerequestnode = $doc->createElement('hl:uniqueRequestID');
        
        $root->appendChild($doc->createElement('hl:documentType', $this->_encodechars($documentname)));
        $root->appendChild($uniquerequestnode);
        $root->appendChild($doc->createElement('hl:deliveryMethod', $this->_encodechars($deliverymethod)));
        $root->appendChild($doc->createElement('hl:deliveryTarget', $this->_encodechars($deliverytarget)));
        $root->appendChild($doc->createElement('hl:BucketOverride', $this->_encodechars($bucketname)));
        $root->appendChild($doc->createElement('hl:policyNumber', $this->_encodechars($policynumber)));
        
        $root->appendChild($doc->createElement('hl:requestTimestamp', gmdate("l dS \of F Y h:i:s.u A"))); // Adds some entropy to the message
        
        // Add reference field agentSchemeNumber
        if ($referencefields !== null && is_a($referencefields, 'stdClass') &&
            isset($referencefields->agentSchemeNumber) && $referencefields->agentSchemeNumber !== null)
        {
            # Add properties if found
            $root->appendChild($doc->createElement('hl:agentSchemeNumber', $this->_encodechars($referencefields->agentSchemeNumber)));
        }
        else
        {
            # Missing reference fields, fail with fault
            error_log(__FILE__ . ':' . __LINE__ . ':Missing reference field agentSchemeNumber in SOAP request; cannot proceed');
            throw new Application_Soap_Fault('Missing reference field agentSchemeNumber', 'Server');
        }
        
        // Add reference field targetPostcode
        if ($referencefields !== null && is_a($referencefields, 'stdClass') &&
            isset($referencefields->targetPostcode) && $referencefields->targetPostcode !== null)
        {
            # Add properties if found
            $root->appendChild($doc->createElement('hl:targetPostcode', $this->_encodechars(strtoupper($referencefields->targetPostcode))));
        }
        else
        {
            # Missing reference fields, fail with fault
            error_log(__FILE__ . ':' . __LINE__ . ':Missing reference field targetPostcode in SOAP request; cannot proceed');
            throw new Application_Soap_Fault('Missing reference field targetPostcode', 'Server');
        }

        // Add cat target & cat name
        $root->appendChild($doc->createElement('hl:emailCategoryTarget', $this->_encodechars($emailCatTarget)));
        $root->appendChild($doc->createElement('hl:emailCategory', $this->_encodechars($emailCat)));
        $root->appendChild($doc->createElement('hl:documentDelivery', $this->_encodechars($documentDelivery)));
        
        // Add list of properties
        $proplistnode = $root->appendChild($doc->createElement('hl:properties'));
        if ($properties !== null && is_a($properties, 'stdClass') && isset($properties->property) && $properties->property !== null)
        {
            foreach ($properties->property as $property)
            {
                $propnode = $proplistnode->appendChild($doc->createElement('hl:property'));
                $propnode->appendChild($doc->createElement('hl:name', $this->_encodechars($property->name)));
                $propnode->appendChild($doc->createElement('hl:value', $this->_encodechars($property->value)));
            }
        }
        
        // Add list of variables
        $varlistnode = $root->appendChild($doc->createElement('hl:variables'));
        if ($variables !== null && is_a($variables, 'stdClass') && isset($variables->variable) && $variables->variable !== null)
        {
            foreach ($variables->variable as $variable)
            {
                $varnode = $varlistnode->appendChild($doc->createElement('hl:variable'));
                $varnode->appendChild($doc->createElement('hl:name', $this->_encodechars($variable->name)));
                $varnode->appendChild($doc->createElement('hl:value', $this->_encodechars($variable->value)));
            }

            // Add specific document retrieval url
            if ($emailCatTarget == 'AGENT') {
                $config = Zend_Registry::get('params');
                $connectUrl = $config->connectUrl->connectRootUrl . '/insurance/show-policy?policyno=' . $policynumber;

                $varnode = $varlistnode->appendChild($doc->createElement('hl:variable'));
                $varnode->appendChild($doc->createElement('hl:name', $this->_encodechars('customer-portal-url')));
                $varnode->appendChild($doc->createElement('hl:value', $this->_encodechars($connectUrl)));
            } 
            elseif ($emailCatTarget == 'LANDLORD' || $emailCatTarget == 'TENANT') {
                $config = Zend_Registry::get('params');

                if (preg_match('/^Q/', $policynumber)) {
                    $portalUrl = $config->homelet->domain . '/my-homelet/quotes?id=' . $policynumber;
                }
                else {
                    $portalUrl = $config->homelet->domain . '/my-homelet/policies?id=' . $policynumber;
                }

                $varnode = $varlistnode->appendChild($doc->createElement('hl:variable'));
                $varnode->appendChild($doc->createElement('hl:name', $this->_encodechars('customer-portal-url')));
                $varnode->appendChild($doc->createElement('hl:value', $this->_encodechars($portalUrl)));
            }
        }
        
        // policy covers
        $coverlistnode = $root->appendChild($doc->createElement('hl:policycovers'));
        if ($policycovers !== null && is_a($policycovers, 'stdClass') && isset($policycovers->policycover) && $policycovers->policycover !== null)
        {
            foreach ($policycovers->policycover as $policycover)
            {
                $covernode = $coverlistnode->appendChild($doc->createElement('hl:policycover'));
                $covernode->appendChild($doc->createElement('hl:cover', $this->_encodechars($policycover->cover)));
                $covernode->appendChild($doc->createElement('hl:suminsured', $this->_encodechars($policycover->suminsured)));
                $covernode->appendChild($doc->createElement('hl:excess', $this->_encodechars($policycover->excess)));
                $covernode->appendChild($doc->createElement('hl:monthlypremium', $this->_encodechars($policycover->monthlypremium)));
                $covernode->appendChild($doc->createElement('hl:annualpremium', $this->_encodechars($policycover->annualpremium)));
                $covernode->appendChild($doc->createElement('hl:presentation', $this->_encodechars($policycover->presentation)));
                $covernode->appendChild($doc->createElement('hl:bulleted', $this->_encodechars($policycover->bulleted)));
            }
        }
        
        // endorsements
        $endorsementlistnode = $root->appendChild($doc->createElement('hl:endorsements'));
        if ($endorsements !== null && is_a($endorsements, 'stdClass') && isset($endorsements->endorsement) && $endorsements->endorsement !== null)
        {
            foreach ($endorsements->endorsement as $endorsement)
            {
                $endorsementnode = $endorsementlistnode->appendChild($doc->createElement('hl:endorsement'));
                $endorsementnode->appendChild($doc->createElement('hl:name', 'Endorsement ' . $this->_encodechars($endorsement->endorsement)));
                
                if ($endorsement->effectivedate !== '0000-00-00') // dont send if no effective date
                    $endorsementnode->appendChild($doc->createElement('hl:effective-date', $this->_encodechars($endorsement->effectivedate)));
                
                $endorsementnode->appendChild($doc->createElement('hl:excess', $this->_encodechars($endorsement->excess)));
            }
        }
        
        // pedal cycles
        $pedalcyclelistnode = $root->appendChild($doc->createElement('hl:pedalcycles'));
        if ($pedalcycles !== null && is_a($pedalcycles, 'stdClass') && isset($pedalcycles->pedalcycle) && $pedalcycles->pedalcycle !== null)
        {
            foreach ($pedalcycles->pedalcycle as $pedalcycle)
            {
                $pedalcyclenode = $pedalcyclelistnode->appendChild($doc->createElement('hl:pedalcycle'));
                $pedalcyclenode->appendChild($doc->createElement('hl:make', $this->_encodechars($pedalcycle->make)));
                $pedalcyclenode->appendChild($doc->createElement('hl:model', $this->_encodechars($pedalcycle->model)));
                $pedalcyclenode->appendChild($doc->createElement('hl:serialno', $this->_encodechars($pedalcycle->serialno)));
                $pedalcyclenode->appendChild($doc->createElement('hl:value', $this->_encodechars($pedalcycle->value)));
            }
        }
        
        // specified possessions
        $specpossessionslistnode = $root->appendChild($doc->createElement('hl:specpossessions'));
        if ($specpossessions !== null && is_a($specpossessions, 'stdClass') && isset($specpossessions->specpossession) && $specpossessions->specpossession !== null)
        {
            foreach ($specpossessions->specpossession as $specpossession)
            {
                $specpossessionsnode = $specpossessionslistnode->appendChild($doc->createElement('hl:specpossession'));
                $specpossessionsnode->appendChild($doc->createElement('hl:description', $this->_encodechars($specpossession->description)));
                $specpossessionsnode->appendChild($doc->createElement('hl:value', $this->_encodechars($specpossession->value)));
                $specpossessionsnode->appendChild($doc->createElement('hl:confirmed', $this->_encodechars($specpossession->confirmed)));
            }
        }
        
        // underwriting questions
        $underwritingquestionlistnode = $root->appendChild($doc->createElement('hl:underwritingquestions'));
        if ($underwritingquestions !== null && is_a($underwritingquestions, 'stdClass') &&
            isset($underwritingquestions->underwritingquestion) && $underwritingquestions->underwritingquestion !== null)
        {
            foreach ($underwritingquestions->underwritingquestion as $underwritingquestion)
            {
                $underwritingquestionnode = $underwritingquestionlistnode->appendChild($doc->createElement('hl:underwritingquestion'));
                $underwritingquestionnode->appendChild($doc->createElement('hl:question', $this->_encodechars($underwritingquestion->question)));
                $underwritingquestionnode->appendChild($doc->createElement('hl:answer', $this->_encodechars($underwritingquestion->answer)));
            }
        }
        
        // property details
        $propertydetailslistnode = $root->appendChild($doc->createElement('hl:propertydetails'));
        if ($propertydetails !== null && is_a($propertydetails, 'stdClass') &&
            isset($propertydetails->propertydetail) && $propertydetails->propertydetail !== null)
        {
            foreach ($propertydetails->propertydetail as $propertydetail)
            {
                $propertydetailnode = $propertydetailslistnode->appendChild($doc->createElement('hl:propertydetail'));
                $propertydetailnode->appendChild($doc->createElement('hl:detail', $this->_encodechars($propertydetail->detail)));
                $propertydetailnode->appendChild($doc->createElement('hl:value', $this->_encodechars($propertydetail->value)));
            }
        }
        
        // Add list of inserts
        $insertlistnode = $root->appendChild($doc->createElement('hl:inserts'));
        if ($inserts !== null && is_a($inserts, 'stdClass') && isset($inserts->insert) && $inserts->insert !== null)
        {
            foreach ($inserts->insert as $insert)
            {
                $insertnode = $insertlistnode->appendChild($doc->createElement('hl:insert'));
                $insertnode->setAttribute('name', $this->_encodechars($insert->name));
                $insertnode->setAttribute('burst', ($insert->burst === 1 ? 1 : 0));
            }
        }
        
        // Generate unique request hash and insert into XML
        $uniquerequestid = strtoupper(hash('sha256', $doc->saveXML())); // force upper case for Fastant's benefit
        $uniquerequestnode->appendChild(new DOMText($uniquerequestid));
        $xml = $doc->saveXML();
        
        // Get request method id
        $reqmethod = new Datasource_Insurance_Document_InsuranceRequestMethods();
        
        try
        {
            $deliverymethodid = $reqmethod->getRequestMethodId($deliverymethod);
        }
        catch(Zend_Db_Exception $ex)
        {
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('Failed to get request method id', 'Server');
        }
        
        // Get Print Bucket override id
        $bucket = new Datasource_Insurance_Document_InsurancePrintBuckets();
        
       try
        {
            if ($bucketname) {
                $bucketid = $bucket->getBucketId($bucketname);
            } else {
                $bucketid = 0;
            }
        }
        catch(Zend_Db_Exception $ex)
        {
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('Failed to get print bucket id', 'Server');
        }

        // Fetch the document template id
        $templates = new Datasource_Insurance_Document_InsuranceTemplates();
        
        try
        {
            $templateid = $templates->getTemplateId($documentname);
            
            if (!isset($templateid))
            {
                // Failed to fetch template id
                throw new Application_Soap_Fault('Failed to fetch template Id', 'Server');
            }
        }
        catch(Zend_Db_Exception $ex)
        {
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('Failed to fetch template Id', 'Server');
        }
        
        // Store the request record
        $xmlstore = new Datasource_Insurance_Document_InsuranceRequest();
        
        try
        {
            if ($xmlstore->storeRequest($policynumber, $templateid, $csuid, $deliverymethodid, $deliverytarget, $uniquerequestid, $xml) === false)
            {
                // Failed to store request, return failure to client
                throw new Application_Soap_Fault('Failed to start document request record', 'Server');
            }
        }
        catch(Zend_Db_Exception $ex)
        {
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('Failed to start document request record', 'Server');
        }
        
        return array($xml, $uniquerequestid);
    }

    /**
     * Authenticate with the remote document production server
     * Sets the property httpclient to an instance of Zend_Http_Client
     * for further interaction with the document production server.
     *
     * @return bool
     * @throws Application_Soap_Fault
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
            throw new Application_Soap_Fault('DMS service host not set', 'Server');
        }
        
        if (!isset($authk) || $authk == '')
        {
            error_log(__FILE__ . ':' . __LINE__ . ':DMS service auth token not set');
            throw new Application_Soap_Fault('DMS service auth token not set', 'Server');
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
            throw new Application_Soap_Fault('Failure calling DMS server', 'Server');
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
            error_log(__FILE__ . ':' . __LINE__ . ':DMS server returned unknown response');
            throw new Application_Soap_Fault('DMS server returned unknown response', 'Server');
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
            throw new Application_Soap_Fault('DMS server returned invalid response', 'Server');
        }
        $client->resetParameters();
        
        // Check login response
        if ($loginresponse == null)
        {
            // Problem authenticating with DMS server
            error_log(__FILE__ . ':' . __LINE__ . ':Failed to auth DMS server for request');
            throw new Application_Soap_Fault('Failed to auth DMS server for request', 'Server');
        }
        
        // Make request for creation
        $client->setUri($host); // Remove get parameters from original login uri
        
        $this->httpclient = $client;
        $this->sessionid = $loginresponse['SessionId'];
        $this->customercode = $customercode;
        return true;
    }

    /**
     * Retrieve the Customer id number from the remote
     * document production server
     *
     * @param $customercode
     * @return int
     * @throws Application_Soap_Fault
     */
    private function getCustomerId($customercode)
    {
        $customerid = null;
        
        // Discover customer id from customer name
        $this->httpclient->setParameterGet
        (
            array
            (
                'PHPSESSID'     => $this->sessionid,
                'action'        => 'GetCustomersList',
            )
        );
        
        try
        {
            $response = $this->httpclient->request('GET');
        }
        catch(Zend_Http_Client_Exception $ex)
        {
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('Failure calling DMS server', 'Server');
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
            error_log(__FILE__ . ':' . __LINE__ . ':DMS server returned unknown response');
            throw new Application_Soap_Fault('DMS server returned unknown response', 'Server');
        }
        
        try
        {
            $response = $response->getBody();
            $customerlistresponse = Zend_Json::decode($response);
        }
        catch(Exception $ex)
        {
            // Problem requesting service, report the problem back but keep the queue record
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('DMS server returned invalid response', 'Server');
        }
        $this->httpclient->resetParameters();
        
        array_shift($customerlistresponse); // Remove the first element, this is a header
        foreach ($customerlistresponse as $customer)
        {
            // Seach for the customer id that matches the customer code
            //if ($customer[1] == $customercode)
            //{
                // Assume the first customer, HomeLet should be the only customer anyway.
                $customerid = $customer[0];
                break;
            //}
        }
        
        if ($customerid == null)
        {
            // customer id not found
            error_log(__FILE__ . ':' . __LINE__ . ':Customer Id not found on DMS server');
            throw new Application_Soap_Fault('Customer Id not found on DMS server', 'Server');
        }
        
        return $customerid;
    }

    /**
     * Retrieve the Document type id number from the remote
     * document production server
     *
     * @param $customerid
     * @param string $documentname Document name
     * @throws Application_Soap_Fault
     * @return int
     */
    private function getDocTypeId($customerid, $documentname)
    {
        $documentid = null;
        
        // Discover customer id from customer name
        $this->httpclient->setParameterGet
        (
            array
            (
                'PHPSESSID'     => $this->sessionid,
                'action'        => 'GetDocTypesList',
                'CustomerID'    => $customerid,
            )
        );
        
        try
        {
            $response = $this->httpclient->request('GET');
        }
        catch(Zend_Http_Client_Exception $ex)
        {
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('Failure calling DMS server', 'Server');
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
            error_log(__FILE__ . ':' . __LINE__ . ':DMS server returned unknown response');
            throw new Application_Soap_Fault('DMS server returned unknown response', 'Server');
        }
        
        try
        {
            $response = $response->getBody();
            $doclistresponse = Zend_Json::decode($response);
        }
        catch(Exception $ex)
        {
            // Problem requesting service, report the problem back but keep the queue record
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('DMS server returned invalid response', 'Server');
        }
        $this->httpclient->resetParameters();
        
        array_shift($doclistresponse); // Remove the first element, this is a header
        foreach ($doclistresponse as $document)
        {
            // Seach for the customer id that matches the customer code
            if ($document[1] == $documentname)
            {
                $documentid = $document[0];
                break;
            }
        }
        
        if ($documentid == null)
        {
            // customer id not found
            error_log(__FILE__ . ':' . __LINE__ . ':Document Type Id not found on DMS server');
            throw new Application_Soap_Fault('Document Type Id not found on DMS server', 'Server');
        }
        
        return $documentid;
    }

    /**
     * Retrieve the Document key reference id number from the remote
     * document production server
     *
     * @param int $customerid Customer Id number
     * @param $documentid
     * @param string $keyrefname Key reference name
     * @throws Application_Soap_Fault
     * @internal param \documentid $int Document Id number
     * @return int
     */
    private function getDocKeyReferenceId($customerid, $documentid, $keyrefname)
    {
        $keyrefid = null;
        
        // Discover customer id from customer name
        $this->httpclient->setParameterGet
        (
            array
            (
                'PHPSESSID'     => $this->sessionid,
                'action'        => 'GetKeyRefsList',
                'CustomerID'    => $customerid,
                'DocTypeID'     => $documentid,
            )
        );
        
        try
        {
            $response = $this->httpclient->request('GET');
        }
        catch(Zend_Http_Client_Exception $ex)
        {
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('Failure calling DMS server', 'Server');
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
            error_log(__FILE__ . ':' . __LINE__ . ':DMS server returned unknown response');
            throw new Application_Soap_Fault('DMS server returned unknown response', 'Server');
        }
        
        try
        {
            $response = $response->getBody();
            $keyreflistresponse = Zend_Json::decode($response);
        }
        catch(Exception $ex)
        {
            // Problem requesting service, report the problem back but keep the queue record
            error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
            throw new Application_Soap_Fault('DMS server returned invalid response', 'Server');
        }
        $this->httpclient->resetParameters();
        
        array_shift($keyreflistresponse); // Remove the first element, this is a header
        foreach ($keyreflistresponse as $keyreference)
        {
            // Seach for the customer id that matches the customer code
            if ($keyreference[2] == $keyrefname)
            {
                $keyrefid = $keyreference[0];
                break;
            }
        }
        
        if ($keyrefid == null)
        {
            // customer id not found
            error_log(__FILE__ . ':' . __LINE__ . ':Document Key reference Id not found on DMS server');
            throw new Application_Soap_Fault('Document Key reference Id not found on DMS server', 'Server');
        }
        
        return $keyrefid;
    }

    /**
     * Generate the mac key name. Must be the same function as used in the InsuranceFunctions.php
     *
     * @param string $requesthash Request hash of request
     * @return string Auth key
     * @throws Exception
     */
    private function _generateAuthKey($requesthash)
    {
        $config = Zend_Registry::get('params');
        $secret = null;
        
        // Capture HMAC secret key
        if (isset($config->dms) && isset($config->dms->localcache) && isset($config->dms->localcache->hmacsecret))
            $secret = $config->dms->localcache->hmacsecret;
        
        if ($secret == null)
            throw new Exception('hmac secret not set');
        
        return strtoupper(Zend_Crypt_Hmac::compute($secret, 'sha256', $requesthash));
    }

    /**
     * Debug helper function to log the last request/response pair against the DMS server
     *
     * @param Zend_Http_Client $client Zend Http client object
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
    
    private function _encodechars($value)
    {
        return htmlspecialchars($value, ENT_COMPAT, 'UTF-8', false);
    }
}
