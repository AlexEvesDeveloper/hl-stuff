<?php

class Form_PortfolioInsuranceQuote_Step4 extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Portfolio Step 4 (UW Questions)
     *
     * @return void
     */
    public function init()
    {
        // Grab view and add the sendQuote JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');

        $this->addSubForm(new Form_PortfolioInsuranceQuote_Subforms_ImportantInformation(), 'subform_importantinformation');
        $this->addSubForm(new Form_PortfolioInsuranceQuote_Subforms_DisclosureAndDeclaration(), 'subform_disclosureanddeclaration');
    }
}
?>