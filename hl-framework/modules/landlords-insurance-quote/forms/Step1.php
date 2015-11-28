<?php

class LandlordsInsuranceQuote_Form_Step1 extends Zend_Form_Multilevel {
    /**
     * Pull in the sub forms that comprise Landlords Step 1
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_PersonalDetails(), 'subform_personaldetails');
    	$this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_DataProtection(), 'subform_dataprotection');
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_CorrespondenceDetails(), 'subform_correspondencedetails');
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_InsuredAddress(), 'subform_insuredaddress');
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_PolicyDetails(), 'subform_policydetails');
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_IDD(), 'subform_idd');
    }
}
?>
