<?php

class TenantsInsuranceQuoteB_Form_Step3 extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Tenants Step 3
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_InsuredAddress(), 'subform_insuredaddress');
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_CorrespondenceDetails(), 'subform_correspondencedetails');
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_PolicyDetails(), 'subform_policydetails');
        // Removed this temporarily as it's VERY confusing
        //$this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_Upsell(), 'subform_upsell');

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

        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_HowHear(), 'subform_howhear');
    }
}
