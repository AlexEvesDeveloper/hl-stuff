<?php

class Connect_Form_Subforms_RentGuaranteeClaims_RentalPaymentsReceived extends Zend_Form_SubForm {


    /**
     * Create rental payments paid subform
     *
     * @return void
     */
    public function init() {

        // Add date paid element
        $this->addElement('hidden', 'date_received', array(
            'required'      => false,
            'validators'    => array(
                array(
                    'NotEmpty',true,array(
                        'messages'    => array(
                            'isEmpty'    => 'Please enter date received'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d\d\/\d\d\/\d{4}$/i',
                        'messages' => 'Date received must be in date format'
                    )
                )
            )
        ));

        // Add paid amount element
        $this->addElement('text', 'amount_received', array(
            'required'      => false,
            'validators'    => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter amount received'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9\.]*$/',
                        'messages' => 'Amount received must contain only numbers and a decimal point'
                    )
                )
            )
        ));

    }

    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
    public function isValid($formData = array()) {

        // If there is any partly entered data, all fields are mandatory
        if (
            (isset($formData['date_received']) && $formData['date_received'] != '') ||
            (isset($formData['amount_received']) && $formData['amount_received'] != '')
        ) {
            $this->getElement('date_received')->setRequired(true);
            $this->getElement('amount_received')->setRequired(true);
        }

        return parent::isValid($formData);
    }

}