<?php

class TenantsInsuranceQuote_Form_Step1 extends Zend_Form_Multilevel {
    /**
     * Pull in the sub forms that comprise Tenants Step 1
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_PersonalDetails(), 'subform_personaldetails');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_InsuredAddress(), 'subform_insuredaddress');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_CorrespondenceDetails(), 'subform_correspondencedetails');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_DataProtection(), 'subform_dataprotection');
    }
}
?>