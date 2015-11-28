<?php

class LandlordsInsuranceQuote_Form_Step3 extends Zend_Form_Multilevel {
    /**
     * Pull in the sub forms that comprise Landlords Step 3
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_EmergencyAssistance(), 'subform_emergencyassistance');
        // This isn't a product - it's an add-on for emergency assistance
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_BoilerHeating(), 'subform_boilerheating');
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_PrestigeRentGuarantee(), 'subform_prestigerentguarantee');
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_LegalExpenses(), 'subform_legalexpenses');
    }
}
