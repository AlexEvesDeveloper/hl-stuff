<?php

class TenantsInsuranceQuote_Form_Json_Sharers extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub form that comprises sharers
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('get');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_Sharers(), 'subform_sharers');

    }
}