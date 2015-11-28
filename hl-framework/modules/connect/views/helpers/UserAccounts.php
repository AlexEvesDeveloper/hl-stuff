<?php

class Connect_View_Helper_UserAccounts extends Zend_View_Helper_Abstract
{
    protected $_agentSchemeNumber;

    public function userAccounts($agentSchemeNumber) {

        $this->_agentSchemeNumber = $agentSchemeNumber;
        return $this;
    }

    /**
     * Generate user accounts HTML.
     *
     * @param integer $userStatus
     * Must correspond to one of the consts exposed by the Model_Core_Agent_UserStatus class.
     *
     * @param null|int|array $filterOutUserIds Optional filter, can be empty
     * (null) for no filtering, have one ID (int), or an array of IDs (array of
     * int)
     *
     * @return string
     * Returns the HTML as a string.
     */
    public function listUsers($userStatus, $filterOutUserIds = null) {

        $returnVal = '';

        $userManager = new Manager_Core_Agent_User();
        $users = $userManager->getUsersByStatus($this->_agentSchemeNumber, $userStatus);

        if($userStatus == Model_Core_Agent_UserStatus::ACTIVATED) {

            $userStatusClass = 'activated';
        }
        else {

            $userStatusClass = 'deactivated';
        }

        // Set up to do any on-the-fly filtering
        if (is_numeric($filterOutUserIds)) {

            // Convert numeric to single element array
            $filterOutUserIds = array($filterOutUserIds);
        }

        if (!is_array($filterOutUserIds)) {
            // Convert null to empty array
            $filterOutUserIds = array();
        }

        // Instantiate form - a single instantiation is recycled to save CPU
        // time and mem footprint!
        $userForm = new Connect_Form_SettingsUserAccount(Model_Core_Agent_UserRole::MASTER);

        $firstRow = true;
        $userManager = new Manager_Core_Agent_User();
        foreach ($users as $userObj) {

            // Filter out?
            if (!in_array($userObj->id, $filterOutUserIds)) {

                // Populate form with user object stuffs
                $userForm->subform_useraccount->getElement('realname')->setValue($userObj->name);
                $userForm->subform_useraccount->getElement('username')->setValue($userObj->username);
                $userForm->subform_useraccount->getElement('email')->setValue($userObj->email->emailAddress);
                $userForm->subform_useraccount->getElement('emailcopyto')->setValue($userObj->copyMailTo->emailAddress);
                $userSecurity = $userManager->getUserSecurityDetails($userObj->id);
                $userForm->subform_useraccount->getElement('question')->setValue($userSecurity['questionID']);
                $userForm->subform_useraccount->getElement('answer')->setValue($userSecurity['answer']);
                $userRole = ($userObj->role == Model_Core_Agent_UserRole::MASTER) ? '1' : '0';
                $userForm->subform_useraccount->getElement('master')->setValue($userRole);
                $userResourcesReports = (in_array(Model_Core_Agent_UserResources::REPORTS, $userObj->resources)) ? '1' : '0';
                $userForm->subform_useraccount->getElement('reports')->setValue($userResourcesReports);
                $userStatus = ($userObj->status == Model_Core_Agent_UserStatus::ACTIVATED) ? '1' : '0';
                $userForm->subform_useraccount->getElement('status')->setValue($userStatus);

                // Generate HTML using partial view
                $returnVal .= $this->view->partial(
                    'partials/edit-user-account.phtml',
                    array(
                        'form' =>       $userForm,
                        'userId' =>     $userObj->id,
                        'firstRow' =>   $firstRow,
                        'userStatusClass' => $userStatusClass
                    )
                );

                $firstRow = false;
            }
        }

        return $returnVal;
    }

    /**
     * Generate new user entry HTML.
     *
     * @return string
     * Returns the HTML as a string.
     */
    public function newUser() {

        $returnVal = '';

        // Add blank row for adding a user
        $userForm = new Connect_Form_SettingsUserAccount(Model_Core_Agent_UserRole::MASTER);

        // Default user to "active"
        $userForm->subform_useraccount->getElement('status')->setValue(1);

        // Generate HTML using partial view
        $returnVal .= $this->view->partial(
            'partials/add-user-account.phtml',
            array(
                'form' =>       $userForm,
                'userId' =>     'new'
            )
        );

        return $returnVal;
    }
}