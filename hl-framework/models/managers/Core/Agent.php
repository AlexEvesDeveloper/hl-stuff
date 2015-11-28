<?php

/**
 * Manager class responsible for implementing agent-related business logic,
 * and for binding together the agent domain objects and datasources.
 *
 * @category   Manager
 * @package    Manager_Core
 * @subpackage Agent
*/
class Manager_Core_Agent {

    /**
     * @var Datasource_Core_Agents
     */
    protected $_agentDatasource;

    /**
     * @var Datasource_Core_Agent_Emailaddresses
     */
    protected $_agentEmailAddressDatasource;

    /**
     * @var int
     */
    protected $_agentSchemeNumber;

    /**
     * @var Model_Core_Agent|null
     */
    protected $_agentObject;

    /**
     * @var Application_Core_ImageUtils
     */
    protected $_imageUtils;

    /**
     * @var bool
     */
    protected $_nextCode = false;

    /**
     * Constructor
     *
     * @param null $agentSchemeNumber
     */
    public function __construct($agentSchemeNumber = null)
    {
        // Set up data sources
        $this->_agentDatasource = new Datasource_Core_Agents();
        $this->_agentEmailAddressDatasource = new Datasource_Core_Agent_Emailaddresses();
        $this->_params = Zend_Registry::get('params');
        if (!is_null($agentSchemeNumber)) {
            // Look up initial agent
            $this->getAgent($agentSchemeNumber);
            $this->_imageUtils = new Application_Core_ImageUtils($agentSchemeNumber, $this->_agentDatasource);
        }
    }

	/**
	 * Look up and return an agent's details.
	 * Also sets the current agent properties.
	 *
	 * @param mixed $agentSchemeNumber
	 * @return Model_Core_Agent
     * @throws Zend_Exception
	 */
    public function getAgent($agentSchemeNumber = null)
    {
        if (!is_null($agentSchemeNumber)) {
            $this->_agentSchemeNumber = $agentSchemeNumber;
        }

        if (!is_null($this->_agentSchemeNumber)) {
            // Get basic agent information
            $this->_agentObject = $this->_agentDatasource->getAgent($this->_agentSchemeNumber);
            if (!is_null($this->_agentObject)) {
                // Get agent e-mail addresses
                $this->_agentObject->email = $this->_agentEmailAddressDatasource->getEmailAddresses($this->_agentSchemeNumber);
            }
        }

        if (!is_null($this->_agentObject)) {
            return $this->_agentObject;
        }

        throw new Zend_Exception('Get agent failed');
    }

    /**
     * Public wrapper function to hide some ugliness...
     */
    public function attemptAddToKeyhouse()
    {
        $this->tryAddAsKeyhouseContact();
        $this->tryAddAsKeyhouseClient();
        $this->tryAddAsKeyhouseClientContact();
    }

    /**
     * Attempts to add an agent to the Keyhouse Contacts table. Only adds agent
     * if the agent is not already in the contacts.
     *
     * @param void.
     * @return Boolean success
     */
    protected function tryAddAsKeyhouseContact()
    {
        $kh_con =  new Datasource_Insurance_KeyHouse_Contact();
        // This returns code or false if none exists...
        $this->_nextCode = $kh_con->isDuplicate($this->_agentObject->agentSchemeNumber);
        // Con object is false, so none exists
        if ($this->_nextCode == false) {
            $con_arr = array();
            $con_arr['Code'] = $kh_con->getNextCode();
            $con_arr['Name'] = $this->_agentObject->name;
            $con_arr['Address'] = $this->_agentObject->contact[0]->address->toString(' ');
            // Puts in client name if contact name is empty string
            $con_arr['Salut'] = $this->_agentObject->accountscontactname == '' ?
                $this->_agentObject->name:
                $this->_agentObject->accountscontactname;
            $con_arr['Tel'] = $this->_agentObject->contact[0]->phoneNumbers->telephone1;
            $con_arr['Fax'] = $this->_agentObject->contact[0]->phoneNumbers->fax1;
            $con_arr['email'] = $this->_agentObject->email[0]->emailAddress->emailAddress;
            $con_arr['StartDate'] = Zend_Date::now();
            $con_arr['OtherAddress'] = $this->_agentObject->contact[0]->address->toString(' ');
            $con_arr['OtherRef'] = $this->_agentObject->agentSchemeNumber;
            // Insert into contacts database
            $kh_con->insert($con_arr);
            // Now update nextCode...
            $this->_nextCode = $con_arr['Code'];
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Attempts to add an agent to the Keyhouse clients table. Only adds agent
     * if the agent is not already in the contacts, and we have a valid
     * client code.
     *
     * @param void.
     * @return Boolean success
     */
    protected function tryAddAsKeyhouseClient()
    {
        $kh_cli = new Datasource_Insurance_KeyHouse_Client();
        if ($this->_nextCode != false && !$kh_cli->isDuplicate($this->_nextCode)) {
            $con_arr = array();
            $con_arr['Code'] = $this->_nextCode;
            $con_arr['Name'] = $this->_agentObject->name;
            $con_arr['Address'] = $this->_agentObject->contact[0]->address->toString(' ');
            $con_arr['Tel'] = $this->_agentObject->contact[0]->phoneNumbers->telephone1;
            $con_arr['Fax'] = $this->_agentObject->contact[0]->phoneNumbers->fax1;
            $con_arr['email'] = $this->_agentObject->email[0]->emailAddress->emailAddress;
            $con_arr['doc_folder'] = sprintf(
                '%s\%s',
                $this->_params->connect->sqlserverClientDocPath,
                $con_arr['Code']
            );
            // Insert into clients database
            $kh_cli->insert($con_arr);
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Attempts to add a client into the client contacts table.
     *
     * @param void
     * @return Boolean success
     */
    protected function tryAddAsKeyhouseClientContact()
    {
        $kh_cli_con = new Datasource_Insurance_KeyHouse_ClientContacts();
        if ($this->_nextCode != false && !$kh_cli_con->isDuplicate($this->_nextCode)) {
            $con_arr = array();
            $con_arr['Code'] = $this->_nextCode;
            $con_arr['Name'] = $this->_agentObject->name;
            // Puts in client name if contact name is empty string
            $con_arr['Salut'] = $this->_agentObject->accountscontactname == '' ?
                $this->_agentObject->name:
                $this->_agentObject->accountscontactname;
            // Insert into contacts database
            $kh_cli_con->insert($con_arr);
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Look up and return an agent's email details.
     * Also sets the current agent properties.
     *
     * @param mixed $agentSchemeNumber
     * @return array Array of Model_Core_Agent_EmailMap
     * @throws Zend_Exception
     */
    public function getEmailAddresses($agentSchemeNumber = null)
    {
        if (!is_null($agentSchemeNumber)) {
             $this->getAgent($agentSchemeNumber);
        }

        if (is_null($this->_agentObject)) {
            throw new Zend_Exception('Agent object doesn\'t exist');
        }

        $this->_agentObject->email = $this->_agentEmailAddressDatasource->getEmailAddresses($this->_agentSchemeNumber);

        return $this->_agentObject->email;
    }

    /**
     * Set an agent's email details.
     *
     * @todo: Fix this so it's not messing around with passing arrays to the datasource.
     *
     * @param array $emailMapArray Array of Model_Core_Agent_EmailMap
     * @param mixed $agentSchemeNumber
     * @return bool
     * @throws Zend_Exception
     */
    public function setEmailAddresses($emailMapArray = null, $agentSchemeNumber = null)
    {
        $emailMapArray = (is_null($emailMapArray)) ? $this->_agentObject->email : $emailMapArray;

        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        $emailArray = array();
        foreach ($emailMapArray as $emailMapItem) {
            $emailArray[] = array(
                'emailAddress'  => $emailMapItem->emailAddress->emailAddress,
                'categoryID'    => $emailMapItem->category
            );
        }

        return $this->_agentEmailAddressDatasource->setEmailAddresses($agentSchemeNumber, $emailArray);
    }

    /**
     * Get a single e-mail address by its category ID.
     *
     * @param mixed $categoryId Must be a value from the consts in Model_Core_Agent_EmailMapCategory.
     * @return mixed (string)e-mail address or (bool)false if none set.
     */
    public function getEmailAddressByCategory($categoryId)
    {
        foreach($this->_agentObject->email as $key => $emailMapItem) {
            if ($emailMapItem->category == $categoryId) {
                return $emailMapItem->emailAddress->emailAddress;
            }
        }

        return false;
    }

    /**
     * Set a single e-mail address by its category ID.
     * This uses $this->setEmailAddresses() - if you want to update several at
     * once it's more efficient to use $this->setEmailAddresses() directly than
     * to call this method multiple times.
     *
     * @param string $emailAddress A valid e-mail address or an empty string.
     * @param mixed $categoryId Must be a value from the consts in Model_Core_Agent_EmailMapCategory.
     * @param mixed $agentSchemeNumber
     * @return bool
     * @throws Zend_Exception
     */
    public function setEmailAddressByCategory($emailAddress, $categoryId, $agentSchemeNumber = null)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        // Overwrite the existing e-mail address for the given category ID, if there is one
        $foundEmailCategory = false;
        foreach($this->_agentObject->email as $key => $emailMapItem) {
            if ($emailMapItem->category == $categoryId) {
                $this->_agentObject->email[$key]->emailAddress->emailAddress = $emailAddress;
                $foundEmailCategory = true;
            }
        }
        // If there isn't an e-mail address set for this category ID, add a new one
        if (!$foundEmailCategory) {
            $newEmailMap = new Model_Core_Agent_EmailMap();
            $newEmailMap->emailAddress = new Model_Core_EmailAddress();
            $newEmailMap->emailAddress->emailAddress = $emailAddress;
            $newEmailMap->category = $categoryId;
            $this->_agentObject->email[] = $newEmailMap;
        }

        return $this->setEmailAddresses($this->_agentObject->email, $agentSchemeNumber);
    }

    /**
     * Fetch a physical address for an agent.
     *
     * @param mixed $categoryId A value from Model_Core_Agent_ContactMapCategory.
     * @param mixed $agentSchemeNumber
     * @return Model_Core_Address
     * @throws Zend_Exception
     */
    public function getPhysicalAddressByCategory($categoryId, $agentSchemeNumber = null)
    {
        if (!is_null($agentSchemeNumber)) {
             $this->getAgent($agentSchemeNumber);
        }

        if (is_null($this->_agentObject)) {
            throw new Zend_Exception('Agent object doesn\'t exist');
        }

        // Find correct address
        $returnVal = null;
        foreach ($this->_agentObject->contact as $contact) {
            if ($contact->category == $categoryId) {
                $returnVal = $contact->address;
            }
        }

        return $returnVal;
    }

    /**
     * Fetch a set of contact numbers for an agent.
     *
     * @param mixed $categoryId A value from Model_Core_Agent_ContactMapCategory.
     * @param mixed $agentSchemeNumber
     * @return Model_Core_ContactDetails
     * @throws Zend_Exception
     */
    public function getPhoneNumbersByCategory($categoryId, $agentSchemeNumber = null)
    {
        if (!is_null($agentSchemeNumber)) {
             $this->getAgent($agentSchemeNumber);
        }

        if (is_null($this->_agentObject)) {
            throw new Zend_Exception('Agent object doesn\'t exist');
        }

        // Find correct numbers
        $returnVal = null;
        foreach ($this->_agentObject->contact as $contact) {
            if ($contact->category == $categoryId) {
                $returnVal = $contact->phoneNumbers;
            }
        }

        return $returnVal;
    }

    /**
     * Get an agent's type.
     *
     * @param mixed $agentSchemeNumber
     * @return mixed Const from Model_Core_Agent_Type or null if none found.
     * @throws Zend_Exception
     */
    public function getType($agentSchemeNumber = null)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        return $this->_agentDatasource->getType($agentSchemeNumber);
    }

    /**
     * Set an agent's type.
     *
     * @param mixed Const from Model_Core_Agent_Type.
     * @param mixed $agentSchemeNumber
     * @throws Zend_Exception
     */
    public function setType($type, $agentSchemeNumber = null)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        $this->_agentDatasource->setType($type, $agentSchemeNumber);
    }

    /**
     * Get an agent's Absolute type.
     *
     * @param mixed $agentSchemeNumber
     * @return mixed Const from Model_Core_Agent_AbsoluteType or null if none found.
     * @throws Zend_Exception
     */
    public function getAbsoluteType($agentSchemeNumber = null)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        return $this->_agentDatasource->getAbsoluteType($agentSchemeNumber);
    }

    /**
     * Set an agent's Absolute type.
     *
     * @param mixed Const from Model_Core_Agent_AbsoluteType.
     * @param mixed $agentSchemeNumber
     * @throws Zend_Exception
     */
    public function setAbsoluteType($type, $agentSchemeNumber = null)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        $this->_agentDatasource->setAbsoluteType($type, $agentSchemeNumber);
    }

    /**
     * Get the rateset ID of the agent.
     *
     * @param mixed $agentSchemeNumber
     * @return int
     * @throws Zend_Exception
     */
    public function getRatesetIDByASN($agentSchemeNumber = null)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        return $this->_agentDatasource->getRatesetID($agentSchemeNumber);
    }

    /**
     * Get an agent's FSA status code.
     *
     * @param mixed $agentSchemeNumber Optional agent scheme number.
     *
     * @return string|null FSA status code string on success, or null on lookup
     * failure.
     *
     * @throws Zend_Exception
     */
    public function getFsaStatusCode($agentSchemeNumber = null)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;
        return self::getFsaStatusCodeStatic($agentSchemeNumber);
    }

    /**
     * Get an agent's FSA status code.
     * Note: The original function was declared static but attempted to access $this for the agent scheme number.
     *       As such it has now been split into two separate routines:
     *         getFsaStatusCode (above) which accesses $this->_agentSchemeNumber and now calls
     *         getFsaStatusCodeStatic (below)
     *
     * @param mixed $agentSchemeNumber Optional agent scheme number.
     * @return string|null FSA status code string on success, or null on lookup failure.
     * @throws Zend_Exception
     */
    static public function getFsaStatusCodeStatic($agentSchemeNumber = null)
    {
        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        $params = Zend_Registry::get('params');

		try {
			$client = new Zend_Soap_Client(
	            null,
	            array(
	                'uri' => $params->fsa->soapServerLocation,
	                'location' => $params->fsa->soapServer
	            )
	        );
	        $status = $client->getAgentsFSADetails($agentSchemeNumber);
		} catch (Exception $e) {
			throw new Zend_Exception('Could not connect to FSA Status service');
		}
        $returnVal = (isset($status->FSA_FSA_status_status_abbr)) ? $status->FSA_FSA_status_status_abbr : null;

        return $returnVal;
    }

    /**
     * Check to see whether this agent can offer rent guarantee products.
     *
     * @param mixed $agentSchemeNumber
     * @return bool
     * @throws Zend_Exception
     */
    public function canOfferRGProducts($agentSchemeNumber = null)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        $params = Zend_Registry::get('params');

        $client = new Zend_Soap_Client(
            null,
            array(
                'uri' => $params->fsa->soapServerLocation,
                'location' => $params->fsa->soapServer
            )
        );
        $canOffer = $client->canOffer('Rent Guarantee', $agentSchemeNumber);

        return print_r($canOffer, true);
    }

    /**
     * Add a note to an agent.
     *
     * @param string $note
     * @param int $agentSchemeNumber
     * @param int $csuId
     * @return bool
     * @throws Zend_Exception
     */
    public function addNote($note, $agentSchemeNumber=null, $csuId=0)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        $agentNote = new Datasource_Core_Agent_Notes();
        return $agentNote->addNote($agentSchemeNumber, $note, $csuId);
    }

    /**
     * Add a cleanup note to an agent.
     *
     * @param string $note
     * @param int $agentSchemeNumber
     * @return bool
     * @throws Zend_Exception
    */
    public function addCleanupNote($note, $agentSchemeNumber = null)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        $agentCleanupNote = new Datasource_Core_Agent_CleanupNotes();
        return $agentCleanupNote->addNote($agentSchemeNumber, $note);
    }

    /**
     * Finds agents that match scheme number, which may be partially given.
     *
     * @param string $agentSchemeNumber Agent's scheme number.
     * @return array Simple array of results, primarily for display.
     * @throws Zend_Exception
     */
    public function searchByAsn($agentSchemeNumber)
    {
        return $this->_agentDatasource->searchByAsnOrNameAndAddress($agentSchemeNumber, '', '');
    }

    /**
     * Finds agents that match name and town, each of which may be partially given.
     *
     * @param string $name Agent's name.
     * @param string $town Agent's town.
     * @return array Simple array of results, primarily for display.
     * @throws Zend_Exception
     */
    public function searchByNameAndAddress($name, $town)
    {
        return $this->_agentDatasource->searchByAsnOrNameAndAddress('', $name, $town);
    }

    /**
     * Get agent-level external news visibility.
     *
     * @return bool True for 'on' and false for 'off'.
     * @throws Zend_Exception
     */
    public function getExternalNewsPreference()
    {
        // Check we have ASN set
        if (is_null($this->_agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        // Get agent's news visibility preference
        return $this->_agentDatasource->getAgentEnableExternalNews($this->_agentSchemeNumber);
    }

    /**
     * Set external news preference.
     *
     * @param mixed $newsVisibilityPref
     * @throws Zend_Exception
     */
    public function setExternalNewsPreference($newsVisibilityPref)
    {
        // Check we have ASN set
        if (is_null($this->_agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        // Set agent's news visibility preference
        $this->_agentDatasource->setAgentEnableExternalNews($newsVisibilityPref, $this->_agentSchemeNumber);

        // Keep local agent object in sync
        $this->_agentObject->enableExternalNews = $newsVisibilityPref;
    }

    /**
     * Uploads the logo for displaying in connect
     *
     * @param object $params
     * @param null|int $agentSchemeNumber
     * @return array
     * @throws Zend_Exception
     */
    public function uploadConnectLogo($params, $agentSchemeNumber=null)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        if ( ! $this->_imageUtils) {
            return array(false, array('You must be logged in first'));
        }

        // Validate the logo upload
        if ( ! $this->_imageUtils->validateLogo($params->path, $params->connect)) {
            return array(false, $this->_imageUtils->getErrorMessages());
        }

        // Now delete old logo
        $this->_imageUtils->deleteLogo($params->path);

        // Amend DB to say "no logo"
        $this->_agentDatasource->deleteConnectLogo($agentSchemeNumber);

        if ( ! $this->_imageUtils->uploadLogo($params->connect)) {
            return array(false, $this->_imageUtils->getErrorMessages());
        }

        $this->_agentDatasource->setConnectLogo(
            $this->_imageUtils->getOriginalFileName(),
            $this->_imageUtils->getReSizedFileName(),
            $this->agentSchemeNumber
        );

        return array(true, true);
    }

    /**
     * Uploads the logo for displaying on documents
     *
     * @param object $params
     * @param null|int $agentSchemeNumber
     * @return array
     * @throws Zend_Exception
     */
    public function uploadDocumentLogo($params, $agentSchemeNumber=null)
    {
        $agentSchemeNumber = (is_null($agentSchemeNumber)) ? $this->_agentSchemeNumber : $agentSchemeNumber;

        if (is_null($agentSchemeNumber)) {
            throw new Zend_Exception('ASN not specified');
        }

        if ( ! $this->_imageUtils) {
            return array(false, array('You must be logged in first'));
        }

        // Validate the logo upload
        if ( ! $this->_imageUtils->validateLogo($params->path, $params->document)) {
            return array(false, $this->_imageUtils->getErrorMessages());
        }

        // Now delete old logo
        $this->_imageUtils->deleteLogo($params->path);

        // Amend DB to say "no logo"
        $this->_agentDatasource->deleteDocumentLogo($agentSchemeNumber);

        $now = new DateTime();
        $sftpFileName = $agentSchemeNumber . '.' . $now->format('YmdHis');

        if ( ! $this->_imageUtils->uploadLogo($params->document, $sftpFileName)) {
            return array(false, $this->_imageUtils->getErrorMessages());
        }

        $this->_agentDatasource->setDocumentLogo(
            $this->_imageUtils->getOriginalFileName(),
            $this->_imageUtils->getReSizedFileName(),
            $this->_imageUtils->getSftpFileName(),
            $agentSchemeNumber
        );

        // Additional DPI Warning
        if ($this->_imageUtils->getDpi() < $params->document->minDPI) {
            $message =
                'Thank you for providing your company logo – please note that the version you have provided is of a'
                . ' low resolution which may affect the quality of documentation issued.  We recommend that you provide'
                . ' a higher resolution logo to ensure optimum quality of documentation issued.  If you are able to'
                . ' provide this, please do so – this will then replace the version previously uploaded';
            return array(true, $message);
        }
        else {
            return array(true, true);
        }
    }

    /**
     * Deletes the logo for connect logo file
     *
     * @param object $pathParams
     * @param null|int $agentSchemeNumber
     * @return bool
     * @throws Zend_Exception
     */
    public function deleteConnectLogo($pathParams, $agentSchemeNumber=null)
    {
        $this->_imageUtils->deleteLogo($pathParams);

        // Amend DB to say "no logo"
        $this->_agentDatasource->deleteConnectLogo($this->_agentSchemeNumber);

        return true;
    }

    /**
     * Deletes the logo for document logo file
     *
     * @param object $pathParams
     * @param null|int $agentSchemeNumber
     * @return bool
     */
    public function deleteDocumentLogo($pathParams, $agentSchemeNumber=null)
    {
        $this->_imageUtils->deleteLogo($pathParams);

        // Amend DB to say "no logo"
        $this->_agentDatasource->deleteDocumentLogo($this->_agentSchemeNumber);

        return true;
    }

    /**
     * Policies must not be added to REF only status accounts, or agents
     * with an 'onhold' status. This method checks the ASN against the FSA status
     * and the agent status, and if either are impermissable then the default
     * ASN is returned.
     *
     * @param mixed $asn Agent scheme number to be filtered.
     * @return mixed Filtered agent scheme number.
     */
    static public function filterAsn($asn)
    {

		$params = Zend_Registry::get('params');
        $asn = preg_replace('/\D/', '', $asn);

        //Test FSA status associated with incoming ASN
        if (self::getFsaStatusCodeStatic($asn) == 'REF') {

            $asn = $params->homelet->defaultAgent;
        }
		else {
			
			//Test to ensure that the agent is not onhold.
			$agentManager = new Manager_Core_Agent();
			$agent = $agentManager->getAgent($asn);		
			if($agent->status == Model_Core_Agent_Status::ON_HOLD || $agent->status == Model_Core_Agent_Status::CANCELLED) {
				
				$asn = $params->homelet->defaultAgent;
			}
		}

        return $asn;
    }

    /**
     * Send a notification email to all agents for the required reporting month/year
     *
     * @param int $agentSchemeNumber
     * @param string $email_address
     * @param string $month
     * @param string $year
     * @return bool
     */
    public function sendEmailNotification($agentSchemeNumber, $email_address, $month, $year)
    {
        echo "Send email to agent $agentSchemeNumber - $email_address\n";
        $params = Zend_Registry::get('params');

        $mail = new Application_Core_Mail();
        $mail->setTo($email_address, null);
        $mail->setFrom('noreply@homelet.co.uk', 'HomeLet');
        $mail->setSubject('Your statement is ready for you');

        // Apply template
        $mail->applyTemplate('core/invoice-notification',
            array('asn'       => $agentSchemeNumber,
                  'year'       => $year,
                  'month'      => $month,
                  'heading'    => 'STATEMENT NOTIFICATION',
                  'templateId' => '',
                  'homeletWebsite' => $params->homelet->domain,
                  'connectURL'     => $params->connectUrl->connectRootUrl,
                  'imageBaseUrl' => $params->weblead->mailer->imageBaseUrl),
            false,
            '/email-templates/core/footer.phtml',
            '/email-templates/core/header.phtml');

        // Send email
        if(!$mail->send()) {
            echo 'Message could not be sent to '. $email_address - 'Mailer Error: ' . $mail->ErrorInfo;
        }

        return true;
    }

}
