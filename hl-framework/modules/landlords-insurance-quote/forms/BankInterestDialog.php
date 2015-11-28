<?php

class LandlordsInsuranceQuote_Form_BankInterestDialog extends Zend_Form_Multilevel {
    
    public function init()
    {
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_BankInterestDialog(), 'subform_bank_interest_dialog');
        
    }
}
?>