<?php

class Connect_Form_RentGuaranteeClaims_Step3 extends Zend_Form {

    /**
     * Define the OC step3 form elements
     *
     * @return void
     */
    public function init() {

        $this->setMethod('post');

        // Set up the 'not applicable' signifier
        $this->addElement('text', 'notApplicable');
        $this->getElement('notApplicable')->setValue(
            Datasource_Insurance_RentGuaranteeClaim_RentalPayment::DATE_PAID_NOT_APPLICABLE
        );

        // Add additional Information element
        $this->addElement('textarea', 'additional_information', array(
            'label'     => '',
            'required'  => false,
            'class'     => 'additionalinfo fullwidth',
            'rows'      => '5',
            'cols'      => '77'
        ));

        // Bank account details
        $this->addElement('text', 'dd_accountname', array(
            'label'     => 'Account name',
            'required'  => true,
            'filters' 	=> array('StringTrim'),
            'maxlength' => '45',
            'validators' => array(
                array(
                    'NotEmpty',
                    true,
                    array(
                        'messages' => array (
                            'isEmpty' => 'Please enter bank account name',
                            'notEmptyInvalid' => 'Please enter bank account name'
                        )
                    )
                )
            )
        ));
        $this->addElement('text', 'bank_account_number', array(
            'label'     => 'Account number',
            'required'  => true,
            'filters' 	=> array('StringTrim'),
            'maxlength' => '45',
            'validators' => array(
                array('Digits', true, array(
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
                    'regex', true, array(
                        'pattern' => '/^\d{8,}$/',
                            'messages' => 'Bank account number must contain at least eight digits'
                    )
                )
            )
        ));

        $this->addElement('text', 'bank_sortcode_number', array(
            'label'     => 'Sort code',
            'required'  => true,
            'filters' 	=> array('StringTrim'),
            'maxlength' => '45',
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
            )
        ));

        $this->addSubForm(new Connect_Form_Subforms_RentGuaranteeClaims_RentalPaymentsDue(), 'subform_rentalpaymentsdue');
        $this->addSubForm(new Connect_Form_Subforms_RentGuaranteeClaims_RentalPaymentsReceived(), 'subform_rentalpaymentsreceived');

        // Set decorators
        $this->clearDecorators();
        $this->setDecorators(array('Form'));
        $this->setElementDecorators(array ('ViewHelper', 'Label', 'Errors'));

        // Add the back button
        $this->addElement('button', 'back', array(
            'type'     => 'submit',
            'ignore'   => true,
            'label'    => 'Back'
        ));

        // Add the next button
        $this->addElement('button', 'next', array(
            'type'     => 'submit',
            'ignore'   => true,
            'label'    => 'Continue to Step 4'
        ));

        // Add the save & exit button
        $this->addElement('button', 'save_exit', array(
            'ignore'   => true,
            'label'    => 'Save & Exit         ',
            'onclick'  => "window.location = '/rentguaranteeclaims/saveclaim';"
        ));

        $next = $this->getElement('next');
        $next->clearDecorators();
        $next->setDecorators(array('ViewHelper'));

        $back = $this->getElement('back');
        $back->clearDecorators();
        $back->setDecorators(array('ViewHelper'));

		$saveExit = $this->getElement('save_exit');
        $saveExit->clearDecorators();
        $saveExit->setDecorators(array('ViewHelper'));

        $this->setDecorators(array(
            array('ViewScript',
                array('viewScript' =>
                    'rentguaranteeclaims/subforms/additional-information-and-rental-payments.phtml'
                )
            )
        ));

        Application_Core_FormUtils::removeFormErrors($this);
    }

    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
    public function isValid($formData = array()) {

        return parent::isValid($formData);
     }
}
?>
