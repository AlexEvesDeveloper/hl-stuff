<?php

class TenantsInsuranceQuote_Form_Subforms_DataProtection extends Zend_Form_SubForm
{
    /**
     * Create marketing Qs / data protection subform
     *
     * @return void
     */
    public function init()
    {
        // Add DPA Phone/Post control
        $this->addElement('checkbox', 'dpa_phone_post', array(
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
        ));
        
        // Add DPA SMS/Email control
        $this->addElement('checkbox', 'dpa_sms_email', array(
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
        ));
        
        // Add DPA data resale control
        $this->addElement('checkbox', 'dpa_resale', array(
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
        ));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/data-protection.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}