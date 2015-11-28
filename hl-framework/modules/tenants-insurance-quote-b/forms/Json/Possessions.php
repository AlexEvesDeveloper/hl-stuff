<?php

class TenantsInsuranceQuoteB_Form_Json_Possessions extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub form that comprises possessions
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_Possessions(), 'subform_possessions');
    }
}