<?php

class LandlordsInsuranceQuote_Form_Subforms_BankConfirmation extends Zend_Form_SubForm
{
    /**
     * Create bank confirmation subform
     *
     * @return void
     */
    public function init()
    {
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/bank-confirmation.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}