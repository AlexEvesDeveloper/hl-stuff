<?php

/**
 * Business rules class which provides quote/policy document services.
 */
class Manager_Insurance_Document
{
	protected $_documentHistoryModel;

	/**
	 * Creates and sends a document by post.
	 *
	 * @param string $policyNumber
	 * The quote or policy number against which the document will be built.
	 *
	 * @param Model_Insurance_TenantsContentsPlus_DocumentTypes $documentTypeIdentifier
	 * Specifies the document to create.
	 *
	 * @return void
	 */
    public function createAndPostDocument($policyNumber, $documentTypeIdentifier)
    {
		$curlScriptPath = $this->_buildCurlPath($policyNumber, $documentTypeIdentifier);
		$this->_createAndSend($curlScriptPath);
	}

	/**
	 * Creates and sends a document by email.
	 *
	 * @param string $policyNumber
	 * The quote or policynumber against which the document will be built.
	 *
	 * @param Model_Insurance_TenantsContentsPlus_DocumentTypes $documentTypeIdentifier
	 * Specifies the document to create.
	 *
	 * @param string $emailAddress
	 * The email destination to which the document will be sent.
	 *
	 * @return void
	 */
    public function createAndEmailDocument($policyNumber, $documentTypeIdentifier, $emailAddress)
    {
		$curlScriptPath = $this->_buildCurlPath($policyNumber, $documentTypeIdentifier, $emailAddress);
		$this->_createAndSend($curlScriptPath);
	}


	/**
	 * Curls a remote script which builds and sends the document.
	 *
	 * @param string $curlScriptPath
	 * The cURL path to the remote script.
	 *
	 * @return void
	 */
    protected function _createAndSend($curlScriptPath)
    {
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curlScriptPath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
	}

    /**
     * Builds the cURL path to the Centos4 document logic.
     *
     * @param $policyNumber
     * @param Model_Insurance_TenantsContentsPlus_DocumentTypes $documentTypeIdentifier Specifies the document to create.
     * @param null $emailAddress
     * @throws Zend_Exception
     * @return string Returns the cURL path.
     */
    protected function _buildCurlPath($policyNumber, $documentTypeIdentifier, $emailAddress = null)
    {

		// Fetch the HOMELETUK.COM legacy domain
        $params = Zend_Registry::get('params');
		$domain = $params->homelet->get('legacyDomain');

		$curledScriptPath = "$domain/cgi-bin/UserAdmin/letters/testLetter.pl";

        switch($documentTypeIdentifier)
        {
			case Model_Insurance_TenantsContentsPlus_DocumentTypes::NEW_POLICY_DOCS:
            case Model_Insurance_LandlordsPlus_DocumentTypes::NEW_POLICY_DOCS:
				//Configurations to generate a complete tenants inception letter
				$getString = "policynumber=$policyNumber";
				$getString .= "&";
				$getString .= "emailToSendTo=$emailAddress";
				$getString .= "&";
                $getString .= "lettertype=inception";
				break;

			default:
				throw new Zend_Exception('Document type identifier not recogised.');
		}

		$curledScriptPath = "$curledScriptPath?$getString";
		return $curledScriptPath;
	}


    /**
     * Retrieves all documents for $policyNumber which are of type $documentTypeIdentifier.
     *
     * @param string $policyNumber
     * Specifies the quote or policy number to search against.
     *
     * @param string $documentTypeIdentifier
     * Identifies the documents to retrieve. Use the consts exposed in the DocumentTypes
     * classes to specify arguments here.
     *
     * @param array $addresseeRestrictions
     * @return mixed
     * Returns an array of one or more Model_Insurance_Document objects. If no matching
     * documents are found, then will return null.
     *
     * @todo
     * Encapsulate the docQueue datasource via this method, so that if a document
     * does not exist in the docHistory yet, the docQueue can be checked.
     */
    public function getDocuments($policyNumber, $documentTypeIdentifier, $addresseeRestrictions = array())
    {
        if (!isset($this->_documentHistoryModel)) {
			$this->_documentHistoryModel = new Datasource_Insurance_DocumentHistory();
		}

		if (isset($documentTypeIdentifier)) {
			return $this->_documentHistoryModel->getDocuments($policyNumber, $documentTypeIdentifier);
		}
        else {
			return $this->_documentHistoryModel->getDocumentsAll($policyNumber, $addresseeRestrictions);
		}
	}
}
