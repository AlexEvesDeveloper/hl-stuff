<?php

class LandlordsInsuranceQuote_Form_ClaimsDialog extends Zend_Form_Multilevel {
    /**
     * Pull in the sub forms that comprise Portfolio add property
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_ClaimsDialog(), 'subform_claims_dialog');
        
    }
}
?>