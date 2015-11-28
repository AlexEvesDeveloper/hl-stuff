<?php

class TenantsInsuranceQuote_Form_Step4 extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Tenants Step 4
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_PaymentSelection(), 'subform_paymentselection');
    }
}
?>