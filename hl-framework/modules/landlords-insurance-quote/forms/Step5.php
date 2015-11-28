<?php

/**
 * Class LandlordsInsuranceQuote_Form_Step5
 */
class LandlordsInsuranceQuote_Form_Step5 extends Zend_Form_Multilevel
{
    /**
     * Pull in the sub forms that comprise Landlords Step 5
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new LandlordsInsuranceQuote_Form_Subforms_PaymentSelection(), 'subform_paymentselection');
    }
}