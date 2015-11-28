<?php

class LandlordsInsuranceQuote_Form_MyHomeLetRegistration extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise My HomeLet registration
     *
     * @return void
     */
    public function init()
    {
        // Switch my homelet subform
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        if ($auth->hasIdentity()) {

            // We have an identified, logged-in customer
            $this->addSubForm(new Account_Form_Subforms_LoggedIn($auth), 'subform_logged_in');
        }
        else {

            // Grab the legacy customer from the session
            $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
            $customerManager = new Manager_Core_Customer();

            $legacyCustomer = $customerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER, $pageSession->customerRefNo);

            // Do we have a new customer?
            $newCustomer = $customerManager->getCustomerByEmailAddress($legacyCustomer->getEmailAddress());

            if ($newCustomer instanceof Model_Core_Customer) {
                $this->addSubForm(new Account_Form_Subforms_Login(), 'subform_login');
            }
            else {
                $this->addSubForm(new Account_Form_Subforms_Register(), 'subform_register');
            }
        }
    }

    /**
     * Extra account validation beyond parent::isValid()
     *
     * @param $data Array of form data
     * @return bool
     */
    public function isValid($data)
    {
        $validationResult = parent::isValid($data);

        // Perform login validation
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        $loginSubForm = $this->getSubForm('subform_login');
        if (isset($loginSubForm) && $data['email'] != '' && $data['password'] != '') {
            // Process login
            $customerManager = new Manager_Core_Customer();
            $adapter = $customerManager->getAuthAdapter(array('email' => $data['email'], 'password' => $data['password']));
            $result = $auth->authenticate($adapter);

            if ($result->isValid()) {
                $email = $loginSubForm->getElement('email');
                $newCustomer = $customerManager->getCustomerByEmailAddress($email->getValue());
                if ($newCustomer->getEmailValidated() !== true) {
                    $auth->clearIdentity();
                    $email->addError("Unfortunately you haven't validated your email address yet. We've sent you an email which includes a link to validate your My HomeLet account. You'll need to validate your account to continue. If you've not received your validation email or if you're unable to access your account, please call us on 0845 117 6000.");

                    return false;
                }

                $storage = $auth->getStorage();
                $storage->write($adapter->getResultRowObject(array(
                    'title',
                    'first_name',
                    'last_name',
                    'email_address',
                    'id'
                )));
            }
            else {
                $password = $loginSubForm->getElement('password');
                $password->addError('Your password is incorrect, please try again');
                return false;
            }
        }

        // All valid above, return parents validation result
        return $validationResult;
    }
}
