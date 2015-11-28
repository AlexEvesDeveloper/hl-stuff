<?php

class Connect_Form_Subforms_RentGuaranteeClaims_RentalPaymentsDue extends Zend_Form_SubForm {


    /**
     * Create rental payments due subform
     *
     * @return void
     */
    public function init() {

        // Add due date element
        $this->addElement('text', 'date_due', array(
            'required'      => false,
            'validators'    => array(
                array(
                    'NotEmpty',true,array(
                        'messages'    => array(
                            'isEmpty'    => 'Please enter date due'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d\d\/\d\d\/\d{4}$/',
                        'messages' => 'Date due must be in date format'
                    )
                )
            )
        ));

        // Add due amount element
        $this->addElement('text', 'amount_due', array(
            'required'      => false,
            'validators'    => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter amount due'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9\.]*$/',
                        'messages' => 'Amount due must contain only numbers and a decimal point'
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

        // Get rental payment data
        $pageSession = new Zend_Session_Namespace('online_claims');
        $manager = new Manager_Insurance_RentGuaranteeClaim_RentalPayment();
        $paymentData = $manager->getRentalPayments(
            $pageSession->ClaimReferenceNumber
        );

        // If there is not any stored due data, all fields are mandatory so we get at least one entry
        $noPaymentsDueEntered = true;
        if (count($paymentData['data']) > 0) {
            // Run through data present looking for payments due entered
            foreach($paymentData['data'] as $payment) {
                if ($payment['date_due'] != 'N/A') {
                    $noPaymentsDueEntered = false;
                    break 1;
                }
            }
        }
        if ($noPaymentsDueEntered) {
            $this->getElement('date_due')->setRequired(true);
            $this->getElement('amount_due')->setRequired(true);
        }

        // If there is any partly entered data, all fields are mandatory
        if (
            (isset($formData['date_due']) && $formData['date_due'] != '') ||
            (isset($formData['amount_due']) && $formData['amount_due'] != '')
        ) {
            $this->getElement('date_due')->setRequired(true);
            $this->getElement('amount_due')->setRequired(true);
        }

        return parent::isValid($formData);
    }

}