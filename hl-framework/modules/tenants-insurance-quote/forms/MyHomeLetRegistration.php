<?php

class TenantsInsuranceQuote_Form_MyHomeLetRegistration extends Zend_Form_Multilevel
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
            // Get new customer from session
            $this->addSubForm(new Account_Form_Subforms_Register(), 'subform_register');
        }
    }
}
