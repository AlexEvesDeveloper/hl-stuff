<?php

/**
 * Class LandlordsInsuranceQuote_Form_Subforms_PaymentSelection
 */
class LandlordsInsuranceQuote_Form_Subforms_PaymentSelection extends Zend_Form_SubForm
{
    /**
     * Create payment selection and direct debit subform
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

        // Add account owned confirmation element
        $this->addElement('radio', 'dd_inyourname', array(
            'label'     => 'Can you confirm that the bank account is in your name?',
            'required'  => true,
            'multiOptions' => array(
                'yes' => 'Yes',
                'no' => 'No'
            ),
            'separator' => '',
            'label_placement' => 'prepend',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select whether the bank account is in your name',
                            'notEmptyInvalid' => 'Please select whether the bank account is in your name'
                        )
                    )
                ),
                array(
                    'identical', true, array(
                        'token' => 'yes',
                        'messages' => array(
                            'notSame' => 'The bank account must be in your name'
                        )
                    )
                )
            )
        ));

        // Add account owned confirmation element
        $this->addElement('radio', 'dd_onlyauthorised', array(
            'label'     => 'Can you confirm that only one person is required to authorise debits from this account?',
            'required'  => true,
            'multiOptions' => array(
                'yes' => 'Yes',
                'no' => 'No'
            ),
            'separator' => '',
            'label_placement' => 'prepend',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select whether only one person is required to authorise debits from this account',
                            'notEmptyInvalid' => 'Please select whether only one person is required to authorise debits from this account'
                        )
                    )
                ),
                array(
                    'identical', true, array(
                        'token' => 'yes',
                        'messages' => array(
                            'notSame' => 'Only one person must be required to authorise debits from this account'
                        )
                    )
                )
            )
        ));

        // Add account holder's name element
        $this->addElement('text', 'dd_accountname', array(
            'label'      => 'Account holder name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the account holder\'s name',
                            'notEmptyInvalid' => 'Please enter the account holder\'s name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\ \-\']{3,}$/i',
                        'messages' => 'Account holder\'s name must contain at least three alphabetic characters and only basic punctuation (space, hyphen and single quote)'
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-ctfilter' => 'yes',
                'class' => 'declarationAnswer form-control',
            )
        ));

        // Add bank account number element
        $this->addElement('text', 'bank_account_number', array(
            'label'      => 'Bank account number',
            'required'   => true,
            'validators' => array(
                array(
                    'Digits', true, array(
                        'messages' => array(
                            'notDigits' => 'Bank account number must only contain numbers',
                            'digitsStringEmpty' => 'Please enter your bank account number'
                        )
                    )
                ),
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your bank account number'
                        )
                    )
                ),
                array(
                    'Bank', true, array(
                        'messages' => array(
                            'inValid' => 'Your bank details are incorrect, please correct them and try again',
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d{8,}$/',
                        'messages' => 'Bank account number must contain at least eight digits'
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-ctfilter' => 'yes',
                'class' => 'declarationAnswer form-control',
            )
        ));

        // Add bank sort code element
        $this->addElement('text', 'bank_sortcode_number', array(
            'label'      => 'Bank sort code',
            'required'   => true,
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9\-]*$/',
                        'messages' => 'Sort code must contain only numbers and hyphens'
                    )
                ),
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your bank sort code'
                        )
                    )
                ),
                array(
                    'SortCode', true, array(
                        'messages' => array(
                            'inValid' => 'Unable to confirm the bank Sortcode'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-ctfilter' => 'yes',
                'class' => 'declarationAnswer form-control',
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

    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
    public function isValid($formData = array())
    {
        // Check if user is making a CC purchase, if so ignore DD fields for validation by making them non-mandatory
        if (isset($formData['payment_method']) && 'cc' == $formData['payment_method']) {
            $this->getElement('dd_inyourname')->setRequired(false);
            $this->getElement('dd_onlyauthorised')->setRequired(false);
            $this->getElement('dd_accountname')->setRequired(false);
            $this->getElement('bank_account_number')->setRequired(false);
            $this->getElement('bank_sortcode_number')->setRequired(false);
        }

        // Call original isValid()
        return parent::isValid($formData);
    }
}