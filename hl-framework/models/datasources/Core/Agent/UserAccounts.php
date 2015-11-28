<?php
/**
 * Model definition for the agent user accounts table (agentid)
 *
 * @category   Datasource
 * @package    Datasource_Core
 * @subpackage Agent
 */

class Datasource_Core_Agent_UserAccounts extends Zend_Db_Table_Multidb {

    protected $_name = 'agentid';
    protected $_primary = 'agentid';
    protected $_multidb = 'db_legacy_homelet';

    /**
     * Fetch the details for a single agent user account.
     *
     * @param int $userId
     * @param string $userName
     * @param mixed $agentSchemeNumber
     *
     * @return Model_Core_Agent_User
     */
    public function getUser($userId = null, $userName = null, $agentSchemeNumber = null) {

        if (
            !is_null($userId) ||
            (!is_null($userName) && !is_null($agentSchemeNumber))
        ) {
            // We have enough info to do a look up
        } else {
            throw new Zend_Exception('Get user doesn\'t have enough details');
        }

        $select = $this->select();
        if (!is_null($userId)) {
            $select->where('agentid = ?', $userId);
        }
        if (!is_null($userName)) {
            $select->where('username = ?', $userName);
        }
        if (!is_null($agentSchemeNumber)) {
            $select->where('agentschemeno = ?', $agentSchemeNumber);
        }
        $userRow = $this->fetchRow($select);

        $returnVal = $this->_populateUserObject($userRow);

        return $returnVal;
    }

    /**
     * Store the details for a single agent user account.  If the given unique
     * ID is null, a new user is written to the DB and its ID is returned.
     *
     * @param Model_Core_Agent_User $userObj The user object to store.
     *
     * @return mixed Boolean true on update existing agent user success or
     * integer Model_Core_Agent_User->id on insert new agent user success
     */
    public function setUser(Model_Core_Agent_User $userObj) {

        // TODO: Add some error checking to ensure incoming object is complete.

        // Build mapping array to insert or update into DB
        $userArray = array(
            'agentschemeno'                     => $userObj->agentSchemeNumber,
            'realname'                          => $userObj->name,
            'username'                          => $userObj->username,
            'agentSecurityQuestionID'           => $userObj->securityQuestionId,
            'agentSecurityQuestionAnswer'       => $userObj->securityAnswer,
            'agentSecurityQuestionAnswerSimple' => strtolower(preg_replace('/\W/', '', $userObj->securityAnswer)),
            'email'                             => $userObj->email->emailAddress,
            'copymailto'                        => $userObj->copyMailTo->emailAddress,
            'level'                             => $this->_translateRoleToLegacyDb($userObj->role),
            'status'                            => $this->_translateStatusToLegacyDb($userObj->status),
            'enableExternalNews'                => ($userObj->enableExternalNews) ? 'yes' : 'no',
            // TODO: Deeper objects?
            //'newsCategoryPreferences'       => ?,
        );


        //If the current *stored* state of the user is DEACTIVATED, and the $userObj
        //is requesting that the user be ACTIVATED, then also reset the lastlogin
        //field.
        // Don't do this check if the user is being newly added (eg, has no ID).
        if (!is_null($userObj->id)) {
            $currentStoredUser = $this->getUser($userObj->id);
            if($currentStoredUser->status == Model_Core_Agent_UserStatus::DEACTIVATED) {

                if($userObj->status == Model_Core_Agent_UserStatus::ACTIVATED) {

                    //Update the lastlogin date to today.
                    $currentDate = Zend_Date::now();
                    $userArray['LASTLOGINDATE'] = '0000-00-00';
                }
            }
        }


        // Set resources
        $userArray += $this->_translateResourcesToLegacyDb($userObj->resources);
        // Is a new password being set?
        // TODO: Add salt and one-way encryption support
        if (!is_null($userObj->password)) {
            $userArray['password'] = $userObj->password;
        }

        if (!is_null($userObj->id)) {

            // User ID exists - do an UPDATE
            try {
                $where = $this->getAdapter()->quoteInto('agentid = ?', $userObj->id);
                $update = $this->update($userArray, $where);
            } catch (Exception $e) {
                throw new Zend_Exception('Couldn\'t update agent user.');
            }

            return true;

        } else {

            // New user - do an INSERT
            try {
                $query = $this->insert($userArray);
            } catch (Exception $e) {
                throw new Zend_Exception('Couldn\'t insert new agent user.');
            }

            return $query;

        }

    }

    /**
     * Look up and return all agent user details for a particular agency.
     *
     * @param mixed $agentSchemeNumber
     *
     * @return array Array of Model_Core_Agent_User.
     */
    public function getAllUsers($agentSchemeNumber) {

        $select = $this->select();
        $select->where('agentschemeno = ?', $agentSchemeNumber);
        $select->order('STATUS ASC');
        $select->order('realname ASC');
        $select->order('username ASC');
        $allUsers = $this->fetchAll($select);
        $returnVal = array();
        foreach ($allUsers as $userRow) {
            $returnVal[] = $this->_populateUserObject($userRow);
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

        $select = $this->select();
        $select->where('agentschemeno = ?', $agentSchemeNumber);
        $select->where('STATUS = ?', $userStatus);
        $select->order('STATUS ASC');
        $select->order('realname ASC');
        $select->order('username ASC');
        $allUsers = $this->fetchAll($select);
        $returnVal = array();
        foreach ($allUsers as $userRow) {
            $returnVal[] = $this->_populateUserObject($userRow);
        }

        return $returnVal;
    }

    /**
     * Look up and return all agent master user details for a particular agency.
     *
     * @param mixed $agentSchemeNumber
     * @param int $limit Optional limit, if set then a random selection of master users up to this maximum will be returned.
     *
     * @return array Array of Model_Core_Agent_User.
     */
    public function getMasterUsers($agentSchemeNumber, $limit = null) {

        $select = $this->select();
        $select->where('agentschemeno = ?', $agentSchemeNumber);
        $select->where('level = ?', 3);
        if (is_numeric($limit)) {
            $select->order('RAND()');
            $select->limit($limit);
        } else {
            $select->order('username ASC');
        }
        $allMasterUsers = $this->fetchAll($select);
        $returnVal = array();
        foreach ($allMasterUsers as $masterUserRow) {
            $returnVal[] = $this->_populateUserObject($masterUserRow);
        }

        return $returnVal;
    }

    /**
     * Look up an agent user's status.
     *
     * @param int $userId
     *
     * @return mixed Null or a const from Model_Core_Agent_UserStatus.
     */
    public function getUserStatus($userId) {
        /*
         * SELECT a.status, s.*
         *   FROM agentid AS a JOIN AGENTUSERSTATUS AS s
         *   ON a.status = s.ID
         *   WHERE a.agentid = $userId;
         */
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('a' => $this->_name),
                array('status')
            )
            ->join(
                array('s' => 'AGENTUSERSTATUS'),
                'a.status = s.ID'
            )
            ->where('a.agentid = ?', $userId);

        $statusRow = $this->fetchRow($select);

        $returnVal = null;
        switch ($statusRow->LABEL) {
            case 'activated':
                $returnVal = Model_Core_Agent_UserStatus::ACTIVATED;
                break;
            case 'deactivated':
                $returnVal = Model_Core_Agent_UserStatus::DEACTIVATED;
                break;
        }

        return $returnVal;
    }

    /**
     * Set an agent user's status.
     *
     * @param mixed $status A const from Model_Core_Agent_UserStatus.
     * @param int $userId
     *
     * @return void
     */
    public function setUserStatus($status, $userId) {

        $where = $this->getAdapter()->quoteInto('agentid = ?', $userId);
        $update = $this->update(
            array('status' => $status),
            $where
        );
    }

    /**
     * Get the role of a particular agent user.
     *
     * @param int $userId
     *
     * @return mixed A const from Model_Core_Agent_UserRole or null on failure.
     */
    public function getUserRole($userId) {

        $select = $this->select()
            ->from(
                array('a' => $this->_name),
                array('level')
            )
            ->where('a.agentid = ?', $userId);

        $roleRow = $this->fetchRow($select);

        return $this->_translateRoleFromLegacyDb($roleRow->level);

    }

    /**
     * Set the role for an agent user.
     *
     * @param mixed $role A const from Model_Core_Agent_UserRole.
     * @param int $userId
     *
     * @return void
     */
    public function setUserRole($role, $userId) {

        $where = $this->getAdapter()->quoteInto('agentid = ?', $userId);
        $update = $this->update(
            array('level' => $this->_translateRoleFromLegacyDb($role)),
            $where
        );
    }

    /**
     * Set the last login date
     *
     * @param Zend_Date $date Last login date required, or null if current date
     * @param int $userId
     * @return void
     */
    public function setLastLoginDate($date = null, $userId)
    {
        if ($date == null)
            $date = new Zend_Date();

        $where = $this->getAdapter()->quoteInto('agentid = ?', $userId);

        $update = $this->update
        (
            array('LASTLOGINDATE' => $date->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY)),
            $where
        );
    }

    /**
     * Get the resource list available for an agent user.
     *
     * @param int $userId
     *
     * @return array An array of constants from Model_Core_Agent_UserResources.
     */
    public function getUserResources($userId) {

        $select = $this->select()
            ->from(
                array('a' => $this->_name),
                array('level', 'reports', 'newRef', 'refSuite', 'modCover', 'accounts')
            )
            ->where('a.agentid = ?', $userId);

        $roleRow = $this->fetchRow($select);

        return $this->_translateResourcesFromLegacyDb($roleRow);
    }

    /**
     * Sets the list of resources available to an agent user.
     *
     * @param array $resources An array of constants from Model_Core_Agent_UserResources.
     * @param int $userId
     *
     * @return void
     */
    public function setUserResources($resources, $userId) {
        // Hardcoded translation from object to DB data

        $resourceTranslated = $this->_translateResourcesToLegacyDb($resources);

        $where = $this->getAdapter()->quoteInto('agentid = ?', $userId);
        $update = $this->update($resourceTranslated, $where);
    }

    /**
     * Fetch all available security questions that can be posed to agent users.
     *
     * @return array Array of (int)question ID => (string)question tuples.
     */
    public function getUserSecurityAllQuestions() {

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('q' => 'AGENTSECURITYQUESTION')
            )
            ->order('q.ordinal ASC');
        $allQuestions = $this->fetchAll($select);
        $returnVal = array();
        foreach ($allQuestions as $questionRow) {
            $returnVal[$questionRow->agentSecurityQuestionID] = $questionRow->question;
        }

        return $returnVal;
    }

    /**
     * Get the security question and answer details for a particular agent user.
     *
     * @param int $userId
     *
     * @return mixed Associative array of details containing values for keys 'questionID', 'question' and 'answer', or null on failure.
     */
    public function getUserSecurityDetails($userId) {

        /*
         * SELECT a.agentSecurityQuestionAnswer, q.*
         *   FROM agentid AS a JOIN AGENTSECURITYQUESTION AS q
         *   ON a.agentSecurityQuestionID = q.agentSecurityQuestionID
         *   WHERE a.agentid = $userId;
         */
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('a' => $this->_name),
                array('agentSecurityQuestionAnswer')
            )
            ->join(
                array('q' => 'AGENTSECURITYQUESTION'),
                'a.agentSecurityQuestionID = q.agentSecurityQuestionID'
            )
            ->where('a.agentid = ?', $userId);

        $qaRow = $this->fetchRow($select);

        $returnVal = null;
        if (!empty($qaRow)) {
            $returnVal = array(
                'questionID' => $qaRow->agentSecurityQuestionID,
                'question' => $qaRow->question,
                'answer' => $qaRow->agentSecurityQuestionAnswer
            );
        }

        return $returnVal;
    }

    /**
     * Set the security details for an agent user.
     *
     * @param array $securityQA Associative array containing key => value pairs for keys 'questionID' and 'answer'.
     * @param int $userId
     *
     * @return void
     */
    public function setUserSecurityDetails($securityQA, $userId) {

        $where = $this->getAdapter()->quoteInto('agentid = ?', $userId);
        $update = $this->update(
            array(
                'agentSecurityQuestionID' => $securityQA['questionID'],
                'agentSecurityQuestionAnswer' => $securityQA['answer'],
            ),
            $where
        );
    }

    /**
     * Set an agent user's external news visibility preference.
     *
     * @param bool $switch True for visible or false for invisible.
     * @param int $userId
     *
     * @return void
     */
    public function setUserEnableExternalNews($switch, $userId) {

        $switch = ($switch === false) ? 'no' : 'yes';
        $where = $this->getAdapter()->quoteInto('agentid = ?', $userId);
        $update = $this->update(
            array(
                'enableExternalNews' => $switch
            ),
            $where
        );
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

        // Built SQL query from supplied data
        $select = $this->select();
        foreach(array('agentschemeno', 'username', 'realname', 'email') as $checkField) {
            if (!is_null($details[$checkField]) && $details[$checkField] != '') {
                $select->where("{$checkField} LIKE ?", $details[$checkField]);
            }
        }
        // Search based on password is dependent on username also being provided
        if (
            !is_null($details['password']) && $details['password'] != '' &&
            !is_null($details['username']) && $details['username'] != ''
        ) {
            $select->where('password LIKE ?', $details['password']);
        }
        // Search based on security question is dependent on security answer also being provided
        if (
            !is_null($details['question']) && $details['question'] != '' &&
            !is_null($details['answer']) && $details['answer'] != ''
        ) {
            $t_answer = strtolower(preg_replace('/\W/', '', $details['answer']));
            $select->where('agentSecurityQuestionID = ?', $details['question']);
            $select->where("(agentSecurityQuestionAnswer = ? OR agentSecurityQuestionAnswerSimple = '{$t_answer}')", $details['answer']);
        }

        // Limit the results returned, we only need to know if there are 0, 1 or multiple matches
        $select->limit(2);

        $users = $this->fetchAll($select);

        if (count($users) == 0) {
            // No results found
            return 'We were unable to locate your details from the information given, please try filling in a different combination of items.';
        } elseif (count($users) > 1) {
            // Multiple matches found
            return 'We were unable to locate your details from the information given, please try filling in more items.';
        } else {
            // Success, a single match was found, return a populated agent user object
            return $this->_populateUserObject($users[0]);
        }
    }

    /**
     * Set a password reset token, and a time limit of 12 hours to use it.
     *
     * @param int $userId
     * @param string $token
     *
     * @return void
     */
    public function setPasswordResetToken($userId, $token) {

        $where = $this->getAdapter()->quoteInto('agentid = ?', $userId);
        $update = $this->update(
            array(
                'agentSecurityPasswordResetToken' => $token,
                'agentSecurityPasswordResetTokenValidUntil' => new Zend_Db_Expr('DATE_ADD(NOW(), INTERVAL 12 HOUR)')
            ),
            $where
        );
    }

    /**
     * Find user records based on a valid password reset token.
     *
     * @param string $code
     *
     * @return array Array of Model_Core_Agent_User.
     */
    public function getUserByPasswordResetCode($code) {

        $code = substr(preg_replace('/[^0-9a-f]/', '', $code), 0, 8);

        $select = $this->select();
        $select->where('agentSecurityPasswordResetToken = ?', $code);
        $select->where('agentSecurityPasswordResetTokenValidUntil > ?', new Zend_Db_Expr('NOW()'));
        $select->limit(2);
        $allUsers = $this->fetchAll($select);
        $returnVal = array();
        foreach ($allUsers as $userRow) {
            $returnVal[] = $this->_populateUserObject($userRow);
        }

        return $returnVal;
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
    public function resetPassword($password, $userId) {

        $where = $this->getAdapter()->quoteInto('agentid = ?', $userId);
        $update = $this->update(
            array(
                'password' => $password,
                'agentSecurityPasswordResetToken' => '',
                'agentSecurityPasswordResetTokenValidUntil' => '0000-00-00 00:00:00'
            ),
            $where
        );
    }

    /**
     * Attempts to put a row of agent user data from the DB into a
     * Model_Core_Agent_User and returns it.
     *
     * @param Zend_Db_Table_Row $userData
     *
     * @return mixed Model_Core_Agent_User or null on failure.
     */
    protected function _populateUserObject($userData) {

        if (!empty($userData)) {
            $user = new Model_Core_Agent_User();

            $email = new Model_Core_EmailAddress();
            $email->emailAddress = $userData->email;

            $copyMailTo = new Model_Core_EmailAddress();
            $copyMailTo->emailAddress = $userData->copymailto;

            $user->name =                       $userData->realname;
            $user->id =                         $userData->agentid;
            $user->agentSchemeNumber =          $userData->agentschemeno;
            $user->username =                   $userData->username;
            $user->password =                   $userData->password;
            // TODO: Shouldn't be hardcoded nor '' (none) in future:
            $user->passwordEncryptionScheme =   '';
            $user->passwordEncryptionSalt =     '';
            $user->email =                      $email;
            $user->copyMailTo =                 $copyMailTo;
            $user->lastLoginDate =              ($userData->LASTLOGINDATE != '0000-00-00') ? new Zend_Date($userData->LASTLOGINDATE) : new Zend_Date();
            $user->role =                       $this->_translateRoleFromLegacyDb($userData->level);
            $user->resources =                  $this->_translateResourcesFromLegacyDb($userData);
            $user->enableExternalNews =         ($userData->enableExternalNews == 'yes') ? true : false;
            $user->securityQuestionId =         $userData->agentSecurityQuestionID;
            $user->securityAnswer =             $userData->agentSecurityQuestionAnswer;
            $user->status =                     $this->_translateStatusFromLegacyDb($userData->STATUS);

            $returnVal = $user;
        } else {
            $returnVal = null;
        }

        return $returnVal;
    }

    private function _translateRoleFromLegacyDb($legacyRole) {

        $returnVal = null;

        switch ($legacyRole) {
            case '1':
                $returnVal = Model_Core_Agent_UserRole::BASIC;
                break;
            case '3':
                $returnVal = Model_Core_Agent_UserRole::MASTER;
                break;
        }

        return $returnVal;
    }

    private function _translateRoleToLegacyDb($role) {

        $returnVal = null;

        switch ($role) {
            case Model_Core_Agent_UserRole::BASIC:
                $returnVal = '1';
                break;
            case Model_Core_Agent_UserRole::MASTER:
                $returnVal = '3';
                break;
        }

        return $returnVal;
    }

    private function _translateResourcesFromLegacyDb($legacyResources) {

        $returnVal = array();

        if ($legacyResources->level == '3') {
            $returnVal[] = Model_Core_Agent_UserResources::ADD_USER;
        }
        if ($legacyResources->reports == 'on') {
            $returnVal[] = Model_Core_Agent_UserResources::REPORTS;
        }
        if ($legacyResources->newRef == 'on') {
            $returnVal[] = Model_Core_Agent_UserResources::NEW_REFERENCE;
        }
        if ($legacyResources->refSuite == 'on') {
            $returnVal[] = Model_Core_Agent_UserResources::REFERENCE_SUITE;
        }
        if ($legacyResources->modCover == 'on') {
            $returnVal[] = Model_Core_Agent_UserResources::MODIFY_COVER;
        }
        if ($legacyResources->accounts == 'on') {
            $returnVal[] = Model_Core_Agent_UserResources::ACCOUNTS;
        }

        return $returnVal;
    }

    private function _translateResourcesToLegacyDb($resources) {

        $returnVal = array(
            'level' => 1,
            'reports' => 'off',
            'newRef' => 'off',
            'refSuite' => 'off',
            'modCover' => 'off',
            'accounts' => 'off'
        );
        foreach ($resources as $resource) {
            switch($resource) {
                case Model_Core_Agent_UserResources::ADD_USER:
                    $returnVal['level'] = 3;
                    break;
                case Model_Core_Agent_UserResources::REPORTS:
                    $returnVal['reports'] = 'on';
                    break;
                case Model_Core_Agent_UserResources::NEW_REFERENCE:
                    $returnVal['newRef'] = 'on';
                    break;
                case Model_Core_Agent_UserResources::REFERENCE_SUITE:
                    $returnVal['refSuite'] = 'on';
                    break;
                case Model_Core_Agent_UserResources::MODIFY_COVER:
                    $returnVal['modCover'] = 'on';
                    break;
                case Model_Core_Agent_UserResources::ACCOUNTS:
                    $returnVal['accounts'] = 'on';
                    break;
            }
        }

        return $returnVal;
    }

    private function _translateStatusFromLegacyDb($legacyStatus) {

        $returnVal = null;

        switch ($legacyStatus) {
            case '1':
                $returnVal = Model_Core_Agent_UserStatus::ACTIVATED;
                break;
            case '2':
                $returnVal = Model_Core_Agent_UserStatus::DEACTIVATED;
                break;
        }

        return $returnVal;
    }

    private function _translateStatusToLegacyDb($status) {

        $returnVal = null;

        switch ($status) {
            case Model_Core_Agent_UserStatus::ACTIVATED:
                $returnVal = '1';
                break;
            case Model_Core_Agent_UserStatus::DEACTIVATED:
                $returnVal = '2';
                break;
        }

        return $returnVal;
    }

    /**
     * Deprecated, don't use, try $this->getUser() instead.
     */
    public function getByAgentSchemeNumber($schemeNumber) {
        $select = $this->select();
        $select->where('agentschemeno = ?', $schemeNumber);
        $select->order('STATUS desc');
        $select->order('username asc');
        $results = $this->fetchAll($select);
        $return = array();
        foreach($results as $userAccount) {
            $return[] = array(
                'id'                =>  $userAccount->agentid,
                'agentSchemeNumber' =>  $userAccount->agentschemeno,
                'realname'          =>  $userAccount->realname,
                'username'          =>  $userAccount->username,
                'password'          =>  $userAccount->password,
                'email'             =>  $userAccount->email,
                'copyMailTo'        =>  $userAccount->copymailto,
                'level'             =>  $userAccount->level,
                'reports'           =>  $userAccount->reports,
                'status'            =>  $userAccount->STATUS
            );
        }
    }

}
?>