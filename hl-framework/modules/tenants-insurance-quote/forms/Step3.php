<?php

class TenantsInsuranceQuote_Form_Step3 extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Tenants Step 3
     *
     * @return void
     */
    public function init()
    {
        // Grab view and add the sendQuote JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');

        // JavaScript that shows or hides promo panels, depending on values in subform
        $view->headScript()->appendFile('/assets/tenants-insurance-quote/js/sendQuote.js', 'text/javascript');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_ImportantInformation(), 'subform_importantinformation');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_DisclosureAndDeclaration(), 'subform_disclosureanddeclaration');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_IDD(), 'subform_idd');
        $this->addSubForm(new TenantsInsuranceQuote_Form_Subforms_HowHear(), 'subform_howhear');
    }
}
