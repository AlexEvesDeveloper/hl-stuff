<?php

class Form_PortfolioInsuranceQuote_claimsDialog extends Zend_Form_Multilevel {
    /**
     * Pull in the sub forms that comprise Portfolio add property
     *
     * @return void
     */
    public function init()
    {
        $this->addSubForm(new Form_PortfolioInsuranceQuote_Subforms_Claims(), 'subform_previous-claims-form');
        
    }
}
?>