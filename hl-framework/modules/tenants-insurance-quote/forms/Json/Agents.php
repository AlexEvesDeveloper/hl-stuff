<?php

class TenantsInsuranceQuote_Form_Json_Agents extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub form that comprises letting agent
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');
        
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_LettingAgent(), 'subform_lettingagent');
    }
}