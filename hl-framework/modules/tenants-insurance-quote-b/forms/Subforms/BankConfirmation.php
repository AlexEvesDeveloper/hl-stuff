<?php

class TenantsInsuranceQuoteB_Form_Subforms_BankConfirmation extends Zend_Form_SubForm
{
    /**
     * Create direct debit subform
     *
     * @return void
     */
    public function init()
    {
                // Add account owned confirmation element
        $this->addElement('hidden', 'dd_inyourname', array(
        //    'label'     => 'Can you confirm that the bank account is in your name?',
            'required'  => true,
            'multiOptions' => array(
                'yes' => 'Yes',
                'no' => 'No'
            ),
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
        $this->addElement('hidden', 'dd_onlyauthorised', array(
         //   'label'     => 'Can you confirm that only one person is required to authorise debits from this account?',
            'required'  => true,
            'multiOptions' => array(
                'yes' => 'Yes',
                'no' => 'No'
            ),
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
        $this->addElement('hidden', 'dd_accountname', array(
         //   'label'      => 'Account holder name',
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
                'data-ctfilter' => 'yes'
            )
        ));

        // Add bank account number element
        $this->addElement('hidden', 'bank_account_number', array(
         //   'label'      => 'Bank account number',
            'required'   => true,
            'validators' => array(
                array('Digits', true, array(
                        'messages' => array(
                            'notDigits' => 'Bank account number must only contain numbers'
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
                            'inValid' => 'Your bank details are incorrect, please correct them and try again'
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
                'data-ctfilter' => 'yes'
            )
        ));

        // Add bank sort code element
        $this->addElement('hidden', 'bank_sortcode_number', array(
         //   'label'      => 'Bank sort code',
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
                'data-ctfilter' => 'yes'
            )
        ));

        $this->addElement('hidden', 'dd_confirm');
        
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