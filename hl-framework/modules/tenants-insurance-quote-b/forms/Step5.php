<?php

class TenantsInsuranceQuoteB_Form_Step5 extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Tenants Step 5
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_PaymentSelection(), 'subform_paymentselection');
    }
}
?>