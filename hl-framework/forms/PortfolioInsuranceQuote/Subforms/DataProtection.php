<?php

class Form_PortfolioInsuranceQuote_Subforms_DataProtection extends Zend_Form_SubForm
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
            //'label'         => 'I accept',
            //'required'      => true,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            //'validators' => array(
            //    array(
            //        'NotEmpty', true, array(
            //            'messages' => array(
            //                'isEmpty' => 'You must agree to the data protection statement to continue'
            //            )
            //        )
            //    )
            //)
        ));

        // Add DPA SMS/Email control
        $this->addElement('checkbox', 'dpa_sms_email', array(
            //'label'         => 'I accept',
            //'required'      => true,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            //'validators' => array(
            //    array(
            //        'NotEmpty', true, array(
            //            'messages' => array(
            //                'isEmpty' => 'You must agree to the data protection statement to continue'
            //            )
            //        )
            //    )
            //)
        ));

        // Add DPA data resale control
        $this->addElement('checkbox', 'dpa_resale', array(
            //'label'         => 'I accept',
            //'required'      => true,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            //'validators' => array(
            //    array(
            //        'NotEmpty', true, array(
            //            'messages' => array(
            //                'isEmpty' => 'You must agree to the data protection statement to continue'
            //            )
            //        )
            //    )
            //)
        ));

        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/subforms/data-protection.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}
