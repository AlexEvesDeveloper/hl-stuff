<?php

class Form_PortfolioInsuranceQuote_Step1 extends Zend_Form_Multilevel {
    /**
     * Pull in the sub forms that comprise Portfolio Step 1
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new Form_PortfolioInsuranceQuote_Subforms_PersonalDetails(), 'subform_personaldetails');
        $this->addSubForm(new Form_PortfolioInsuranceQuote_Subforms_CorrespondenceAddress(), 'subform_correspondenceaddress');
        $this->addSubForm(new Form_PortfolioInsuranceQuote_Subforms_DataProtection(), 'subform_dataprotection');
        $this->addSubForm(new Form_PortfolioInsuranceQuote_Subforms_IDD(), 'subform_idd');
    }
}
?>