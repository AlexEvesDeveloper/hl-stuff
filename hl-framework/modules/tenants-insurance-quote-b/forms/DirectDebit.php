<?php

class TenantsInsuranceQuoteB_Form_DirectDebit extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Tenants Direct Debit
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_DirectDebit(), 'subform_directdebit');
    }
}

?>