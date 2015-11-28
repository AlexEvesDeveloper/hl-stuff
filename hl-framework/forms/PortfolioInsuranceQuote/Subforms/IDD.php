<?php

class Form_PortfolioInsuranceQuote_Subforms_IDD extends Zend_Form_SubForm
{
    /**
     * Create IDD subform
     *
     * @return void
     */
    public function init()
    {
        // Add IDD agree element
        $this->addElement('checkbox', 'idd', array(
            'required'      => true,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'You must agree to the initial disclosure document to continue'
                        )
                    )
                )
            )
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/subforms/idd.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}