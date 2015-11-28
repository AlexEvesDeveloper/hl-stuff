<?php

/**
 * Manager class responsible for implementing agent user-related business logic,
 * and for binding together the agent user domain objects and datasources.
 *
 * @category   Manager
 * @package    Manager_Core
 * @subpackage Agent
 */
class Manager_Core_Agent_User {

    /**#@+
     * References to common aspects of the same agent user stored in the datasources.
     */
    protected $_userDatasource;
    protected $_agentSchemeNumber;
    protected $_userId;
    protected $_userObject;
    /**#@-*/

    /**
     * Create Manager_Core_Agent_User object.
     *
     * @param int $userId
     * @param string $userName
     * @param mixed $agentSchemeNumber
     *
     * @return void
     */
    public function __construct($userId = null, $userName = null, $agentSchemeNumber = null) {

        // Set up data sources
        $this->_userDatasource = new Datasource_Core_Agent_UserAccounts();

        if (!is_null($userId) || !is_null($userName) || !is_null($agentSchemeNumber)) {
            // Look up initial user
            $this->getUser($userId, $userName, $agentSchemeNumber);
        }
    }

    /**
     * Look up and return an agent user's details.
     *
     * @param int $userId
     * @param string $userName
     * @param mixed $agentSchemeNumber
     *
     * @return Model_Core_Agent_User
     */
    public function getUser($userId = null, $userName = null, $agentSchemeNumber = null) {

        if (!is_null($userId)) {
            $this->_userId = $userId;
        }

        if (!is_null($agentSchemeNumber)) {
            $this->_agentSchemeNumber = $agentSchemeNumber;
        }

        if (!is_null($this->_userId) || !is_null($userName)) {
            // Get basic user information
            $this->_userObject = $this->_userDatasource->getUser($this->_userId, $userName, $this->_agentSchemeNumber);
        }

        if (!is_null($this->_userObject)) {
            $this->_userId = $this->_userObject->id;
            $this->_agentSchemeNumber = $this->_userObject->agentSchemeNumber;
            return $this->_userObject;
        }

        throw new Zend_Exception('Get agent user failed');
    }

    /**
     * Store the details for a single agent user account.  If the given unique
     * ID is null, a new user is written to the DB and its ID is returned.
     *
     * @param Model_Core_Agent_User $userObj The user object to store.
     *
     * @return mixed
     */
    public function setUser(Model_Core_Agent_User $userObj) {

        return $this->_userDatasource->setUser($userObj);
    }

    /**
     * Look up and return all agent user details for a particular agency.
     *
     * @param mixed $agentSchemeNumber
     *
     * @return array Array of Model_Core_Agent_User.
     */
    public function getAllUsers($agentSchemeNumber = null) {

        if (!is_null($agentSchemeNumber)) {
            $this->_agentSchemeNumber = $agentSchemeNumber;
        }

        if (!is_null($this->_agentSchemeNumber)) {
            $returnVal = $this->_userDatasource->getAllUsers($this->_agentSchemeNumber);
        } else {
            throw new Zend_Exception('ASN not specified');
        }

        return $returnVal;
    }

    /**
     * Look up and return all agent user details for a particular agency with matching status.
     *
     * @param mixed $agentSchemeNumber
     * The agent scheme number
     *
     * @param integer $userStatus
     * Must correspond to one of the consts exposed by the Model_Core_Agent_UserStatus
     * class, indicating if the user is activated or deactivated.
     *
     * @return array Array of Model_Core_Agent_User.
     */
    public function getUsersByStatus($agentSchemeNumber, $userStatus) {

        return $this->_userDatasource->getUsersByStatus($agentSchemeNumber, $userStatus);
    }

    /**
     * Look up an agent user's status.
     *
     * @param int $userId
     *
     * @return mixed Null or a const from Model_Core_Agent_UserStatus.
     */
    public function getUserStatus($userId = null) {

        $userId = (!is_null($userId)) ? $userId : $this->_userId;

        return $this->_userDatasource->getUserStatus($userId);
    }

    /**
     * Set an agent user's status.
     *
     * @param mixed $status A const from Model_Core_Agent_UserStatus.
     * @param int $userId
     *
     * @return void
     */
    public function setUserStatus($status, $userId = null) {

        $userId = (!is_null($userId)) ? $userId : $this->_userId;

        return $this->_userDatasource->setUserStatus($status, $userId);
    }

    /**
     * Get the role of a particular agent user.
     *
     * @param int $userId
     *
     * @return mixed A const from Model_Core_Agent_UserRole or null on failure.
     */
    public function getUserRole($userId = null) {

        $userId = (!is_null($userId)) ? $userId : $this->_userId;

        return $this->_userDatasource->getUserRole($userId);
    }

    /**
     * Set the role for an agent user.
     *
     * @param mixed $role A const from Model_Core_Agent_UserRole.
     * @param int $userId
     *
     * @return void
     */
    public function setUserRole($role, $userId = null) {

        $userId = (!is_null($userId)) ? $userId : $this->_userId;

        return $this->_userDatasource->setUserRole($role, $userId);
    }

    /**
     * Get the resource list available for an agent user.
     *
     * @param int $userId
     *
     * @return array An array of constants from Model_Core_Agent_UserResources.
     */
    public function getUserResources($userId = null) {

        $userId = (!is_null($userId)) ? $userId : $this->_userId;

        return $this->_userDatasource->getUserResources($userId);
    }

    /**
     * Check if a particular resource is available to a user agent.
     *
     * @param mixed $resourceType A const from Model_Core_Agent_UserResources.
     * @param int $userId
     *
     * @return bool True if the resource is available, false otherwise.
     */
    public function getUserResourceByType($resourceType, $userId = null) {

        $userResources = $this->getUserResources($userId);

        foreach ($userResources as $userResource) {
            if ($userResource == $resourceType) return true;
        }

        return false;
    }

    /**
     * Sets the list of resources available to an agent user.
     *
     * @param array $resources An array of constants from Model_Core_Agent_UserResources.
     * @param int $userId
     *
     * @return void
     */
    public function setUserResources($resources, $userId = null) {

        $userId = (!is_null($userId)) ? $userId : $this->_userId;

        return $this->_userDatasource->setUserResources($resources, $userId);
    }

    /**
     * Fetch all available security questions.
     *
     * @return array Array of (int)question ID => (string)question tuples.
     */
    public function getUserSecurityAllQuestions() {

        return $this->_userDatasource->getUserSecurityAllQuestions();
    }

    /**
     * Get the security question and answer details for a particular agent user.
     *
     * @param int $userId
     *
     * @return mixed Associative array of details containing values for keys 'questionID', 'question' and 'answer', or null on failure.
     */
    public function getUserSecurityDetails($userId = null) {

        $userId = (!is_null($userId)) ? $userId : $this->_userId;

        return $this->_userDatasource->getUserSecurityDetails($userId);
    }

    /**
     * Set the security details for an agent user.
     *
     * @param array $securityQA Associative array containing key => value pairs for keys 'questionID' and 'answer'.
     * @param int $userId
     *
     * @return void
     */
    public function setUserSecurityDetails($securityQA, $userId = null) {

        $userId = (!is_null($userId)) ? $userId : $this->_userId;

        return $this->_userDatasource->setUserSecurityDetails($securityQA, $userId);
    }

    /**
     * Fetch the list of external news categories an agent is subscribing to.
     *
     * @param bool $filter Turn on/off filter by whether the agent wants to see
     * news, and whether the agency-level setting allows the viewing of news
     * (which takes precedence).
     *
     * @return array Array of news visibility (true for visible or false for
     * invisible), and news category ID => news category name tuples.
     */
    public function getUserExternalNewsPreferences($filter = true) {

        // Check we have an ASN and user ID set
        if (is_null($this->_agentSchemeNumber) || is_null($this->_userId)) {
            throw new Zend_Exception('ASN and/or user ID not specified');
        }

        // Fetch an agent user's details if need be
        if (is_null($this->_userObject)) {
            $this->getUser();
        }

        $returnVal = array();

        // Does this user want to see external news?
        if ($this->_userObject->enableExternalNews || !$filter) {
            // Invoke the agent manager
            $agentManager = new Manager_Core_Agent($this->_agentSchemeNumber);
            $agent = $agentManager->getAgent();
            // Does this agency allow seeing external news?
            if ($agent->enableExternalNews || !$filter) {
                // Use the external news category datasource to get the news category list for this agent user
                $externalNewsCategoryDatasource = new Datasource_Cms_ExternalNews_CategoriesAgentsMap();
                $returnVal = $externalNewsCategoryDatasource->getNewsPreferences($this->_userId);
            }
        }

        return array($this->_userObject->enableExternalNews, $returnVal);
    }

    /**
     * Set the external news preferences for an agent user.
     *
     * @param bool $newsVisibilityPref True for visible or false for invisible.
     * @param mixed $newsCategoryPrefs Array of news category IDs or null to leave as-is.
     *
     * @return void
     */
    public function setUserExternalNewsPreferences($newsVisibilityPref, $newsCategoryPrefs = null) {

        // Check we have a user ID set
        if (is_null($this->_userId)) {
            throw new Zend_Exception('User ID not specified');
        }

        // Set user's news visibility preference
        $this->_userDatasource->setUserEnableExternalNews($newsVisibilityPref, $this->_userId);

        // Keep local user object in sync
        $this->_userObject->enableExternalNews = $newsVisibilityPref;

        // Set user's news category preferences, if given
        if (!is_null($newsCategoryPrefs)) {
            $externalNewsCategoryDatasource = new Datasource_Cms_ExternalNews_CategoriesAgentsMap();

            return $externalNewsCategoryDatasource->setNewsPreferences($newsCategoryPrefs, $this->_userId);
        }
    }

    /**
     * Takes an array of partial user details and tries to find a single
     * matching record.
     *
     * @param $details array Associative array of user details.
     *
     * @return Model_Core_Agent_User|string Returns a single matching record, or
     * a string containing an error message if none or multiple records found.
     */
    public function searchByFuzzyCredentials($details) {

        $fuzzySearchParams = array();
        $fieldsNonEmpty = 0;
        foreach(array('agentschemeno', 'username', 'password', 'realname', 'question', 'answer', 'email') as $field) {
            if (isset($details[$field]) && trim($details[$field]) != '') {
                $fuzzySearchParams[$field] = trim($details[$field]);
                $fieldsNonEmpty++;
            } else {
                $fuzzySearchParams[$field] = '';
            }
        }

        // Ensure search data isn't empty and at least two items are populated
        if ($fieldsNonEmpty == 0) {
            return 'Please provide some information we can use to find your details with.';
        } elseif ($fieldsNonEmpty < 2) {
            return 'Please provide at least two of the following items of information.';
        }

        // Pass data to datasource to run fuzzy search
        return $this->_userDatasource->searchByFuzzyCredentials($fuzzySearchParams);
    }

    /**
     * Generates and sends a password reset link to a specific agent user, or if
     * the user has no e-mail address on file, a random selection of up to 5 of
     * his/her master users.
     *
     * @param Model_Core_Agent_User $user An agent user object.
     *
     * @return void
     */
    public function sendPasswordResetLink($user) {

        // Generate new password reset token
        $secToken = substr(md5(microtime() . '-' . print_r($user, true) . ' a little bit of r4NDóm|5h text junk ' . mt_rand(0, 1000000)), 0, 8);
        // Store password reset token and set a time limit
        $this->_userDatasource->setPasswordResetToken($user->id, $secToken);

        // Does this user have an e-mail address or copymailto address?
        if ($user->email->emailAddress != '' || $user->copyMailTo->emailAddress != '') {
            // Yes - send e-mail(s)
            if ($user->email->emailAddress != '') {
                $this->_emailPasswordReset($user->email->emailAddress, $user->name, $user->email->emailAddress, $user->name, $user->agentSchemeNumber, $user->username, $secToken);
            }
            if ($user->copyMailTo->emailAddress != '') {
                $this->_emailPasswordReset($user->copyMailTo->emailAddress, $user->name, $user->email->emailAddress, $user->name, $user->agentSchemeNumber, $user->username, $secToken);
            }
            // Success!
            return true;
        } else {
            // No - try to locate master user(s) to send e-mail to
            // Only e-mail up to a random selection of 5 max - hopefully one of them will receive it!
            $masterUsers = $this->_userDatasource->getMasterUsers($user->agentSchemeNumber, 5);
            if (count($masterUsers) > 0) {
                // Found master user(s) - send it/them email(s)
                foreach($masterUsers as $masterUser) {
                    if ($masterUser->email->emailAddress != '') {
                        $this->_emailPasswordReset($masterUser->email->emailAddress, $user->name, '', $user->name, $user->agentSchemeNumber, $user->username, $secToken);
                    }
                    if ($masterUser->copyMailTo->emailAddress != '') {
                        $this->_emailPasswordReset($masterUser->copyMailTo->emailAddress, $user->name, '', $user->name, $user->agentSchemeNumber, $user->username, $secToken);
                    }
                }
                // Success!
                return true;
            } else {
                // Failed to send the info anywhere
                $params = Zend_Registry::get('params');
                $phoneTechnicalSupport = $params->homelet->phone->technicalsupport;
                return "We located your details but not your or your Connect master user\'s e-mail address to send them to. Please speak to the Connect master user within your office, or if you are the master user please call {$phoneTechnicalSupport}.";
            }
        }
    }

    /**
     * Check to see if a password reset code is valid.  Sets current user to
     * found user if check is successful.
     *
     * @param string $code An input code to test.
     *
     * @return bool|string If valid returns true, else a reason string.
     */
    public function checkPasswordResetCodeValid($code) {

        $user = $this->_userDatasource->getUserByPasswordResetCode($code);

        if (count($user) == 1) {
            $this->_agentSchemeNumber = $user[0]->agentSchemeNumber;
            $this->_userId = $user[0]->id;
            $this->_userObject = $user[0];
            return true;
        }

        return 'The password reset link you followed is either invalid, been used or has expired.';
    }

    /**
     * (Re)set the password for an agent user.  Also sets any reset token
     * validity to null.
     *
     * @param string $password
     * @param int $userId
     *
     * @return void
     */
    public function resetPassword($password, $userId = null) {

        $userId = (!is_null($userId)) ? $userId : $this->_userId;

        return $this->_userDatasource->resetPassword($password, $userId);
    }

    /**
     * Send e-mail to agent or agent's master user.
     *
     * @param $toEmail
     * @param $toName
     * @param $agentEmail
     * @param $agentFullname
     * @param $agentSchemeNo
     * @param $agentUsername
     * @param $secToken
     *
     * @return bool
     */
    protected function _emailPasswordReset($toEmail, $toName, $agentEmail = '', $agentFullname, $agentSchemeNo, $agentUsername, $secToken) {

        // Get parameters.
        $params = Zend_Registry::get('params');

        // Instantiate and set up an e-mailer object.
        $emailer = new Application_Core_Mail();
        $emailer->setTo($toEmail, $toName);
        $emailer->setFrom($params->connect->lostlogin->emailFromAddress, $params->connect->lostlogin->emailFromName);
        $emailer->setSubject($params->connect->lostlogin->emailSubject);

        $metaData = array(
            'agentFullname' => $agentFullname,
            'agentSchemeNo' => $agentSchemeNo,
            'agentUsername' => $agentUsername,
            'resetLink'     => "{$params->url->connectLogin}reset-password?code={$secToken}"
        );

        $emailer->applyTemplate('connect_lostLogin', $metaData, false, '/email-branding/homelet/generic-with-signature-footer.phtml');

        return $emailer->send();
    }
}
