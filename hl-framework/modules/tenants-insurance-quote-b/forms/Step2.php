<?php

class TenantsInsuranceQuoteB_Form_Step2 extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Tenants Step 2
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_Possessions(), 'subform_possessions');
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_Bicycle(), 'subform_bicycle');
    }
}
?>