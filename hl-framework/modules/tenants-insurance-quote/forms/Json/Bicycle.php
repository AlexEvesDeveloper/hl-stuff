<?php

class TenantsInsuranceQuote_Form_Json_Bicycle extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub form that comprises bicycle
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');

        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_Bicycle(), 'subform_bicycle');

    }
}