<?php

class TenantsInsuranceQuoteB_Form_Subforms_PaymentSelection extends Zend_Form_SubForm
{
    /**
     * Create premium subform
     *
     * @return void
     */
    public function init()
    {
        // Add payment method selection element
        $this->addElement('radio', 'payment_method', array(
            'label'     => 'Please choose your payment method',
            'required'  => true,
            'multiOptions' => array(
                'cc' => 'Credit/Debit Card',
                'dd' => 'Direct Debit'
            ),
            'separator' => '',
            //'label_placement' => 'prepend',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a payment method',
                            'notEmptyInvalid' => 'Please select a payment method'
                        )
                    )
                )
            )
        ));

        // Add payment frequency selection element
        $this->addElement('radio', 'payment_frequency', array(
            'label'     => 'How often would you like to pay?',
            'required'  => true,
            'multiOptions' => array(
                'Monthly' => 'Monthly',
                'Annually' => 'Annually'
            ),
            'separator' => '',
            //'label_placement' => 'prepend',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select how often you\'d like to pay',
                            'notEmptyInvalid' => 'Please select how often you\'d like to pay'
                        )
                    )
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/payment-selection.phtml'))
        ));

        // Strip all tags to prevent XSS errors - done iteratively so not to overwrite any existing filters
        foreach($this->getElements() as $element) {
            $element->addFilter('StripTags');
        }

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

    }
}