<?php

class TenantsInsuranceQuoteB_Form_Step1 extends Zend_Form_Multilevel {
    /**
     * Pull in the sub forms that comprise Tenants Step 1
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_PersonalDetails(), 'subform_personaldetails');
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_DataProtection(), 'subform_dataprotection');
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_ContentsInsurance(), 'subform_contentsinsurance');
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_Sharers(), 'subform_sharers');
    }
}
?>