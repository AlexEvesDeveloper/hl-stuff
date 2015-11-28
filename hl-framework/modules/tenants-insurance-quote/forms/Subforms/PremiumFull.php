<?php

class TenantsInsuranceQuote_Form_Subforms_PremiumFull extends Zend_Form_SubForm
{
    /**
     * Create premium subform
     *
     * @return void
     */
    public function init()
    {
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/premium-full.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}