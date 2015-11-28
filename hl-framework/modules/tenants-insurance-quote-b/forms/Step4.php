<?php

class TenantsInsuranceQuoteB_Form_Step4 extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Tenants Step 4
     *
     * @return void
     */
    public function init()
    {
        // Grab view and add the sendQuote JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');

        // JavaScript that shows or hides promo panels, depending on values in subform
        $view->headScript()->appendFile('/assets/tenants-insurance-quote-b/js/sendQuote.js', 'text/javascript');
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_ImportantInformation(), 'subform_importantinformation');
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_DisclosureAndDeclaration(), 'subform_disclosureanddeclaration');
        $this->addSubForm(new TenantsInsuranceQuoteB_Form_Subforms_IDD(), 'subform_idd');
    }
}
?>