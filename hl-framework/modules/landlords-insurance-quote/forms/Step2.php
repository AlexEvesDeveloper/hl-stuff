<?php

class LandlordsInsuranceQuote_Form_Step2 extends Zend_Form_Multilevel {
    /**
     * Pull in the sub forms that comprise Landlords Step 2
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_BuildingsInsurance(), 'subform_buildinginsurance');
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_ContentsInsurance(), 'subform_contentsinsurance');
    }
}
?>