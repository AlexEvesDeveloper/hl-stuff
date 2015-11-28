<?php

class TenantsInsuranceQuote_Form_Json_Claims extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub form that comprises important information (contains claims)
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');

        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_ImportantInformation(), 'subform_importantinformation');
    }
}