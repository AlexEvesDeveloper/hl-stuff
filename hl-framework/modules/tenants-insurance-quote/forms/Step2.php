<?php

class TenantsInsuranceQuote_Form_Step2 extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Tenants Step 2
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_PolicyDetails(), 'subform_policydetails');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_ContentsInsurance(), 'subform_contentsinsurance');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_Sharers(), 'subform_sharers');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_Possessions(), 'subform_possessions');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_Bicycle(), 'subform_bicycle');
    }
}
?>