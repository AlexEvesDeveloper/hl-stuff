<?php

class TenantsInsuranceQuote_Form_BankConfirmation extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Tenants Direct Debit
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_BankConfirmation(), 'subform_bankconfirmation');
    }
}

?>
