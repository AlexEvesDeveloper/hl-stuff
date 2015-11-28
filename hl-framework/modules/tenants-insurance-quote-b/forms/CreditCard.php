<?php

class TenantsInsuranceQuoteB_Form_CreditCard extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Tenants Credit Card
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_CreditCard(), 'subform_creditcard');
    }
}

?>