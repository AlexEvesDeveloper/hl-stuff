<?php
class Connect_Form_RentGuaranteeClaims_ClaimDetails extends Zend_Form {

    /**
     * Define claim details form elements
     *
     * @return void
     */
    public function init() {
        $this->setMethod('post');

       // Add hidden element for claim number
        $this->addElement('hidden', 'claimNumber', array(
            'value' => '',
            'class' => 'noborder'
        ));

        // Add the back button
        $this->addElement('submit', 'back', array(
            'ignore' => true,
            'label' => 'Back',
            'value' => 'Back',
            'onclick' => 'window.location.href="/rentguaranteeclaims/view-claims"'
        ));

        // Add the email handler button
        $this->addElement('submit', 'email_claims_handler', array(
            'ignore' => true,
            'value' => 'Email Claims Handler',
            'label' => 'Email Claims Handler',
            'class' => 'btn_orange',
            'onclick' => 'javascript:submitForm("/rentguaranteeclaims/send-message")'
        ));

        // Add the print claim button
        $this->addElement('submit', 'print_claim_report', array(
            'ignore' => true,
            'value' => 'Print Status Report',
            'class' => 'btn_orange',
            'label' => 'Print Status Report',
            'target' => '_blank',
            'onclick' => 'javascript:submitForm("/rentguaranteeclaims/claim-status")'
        ));

        // Set decorators
        $this->clearDecorators();
        $this->setDecorators(array('Form'));
        $this->setElementDecorators(array ('ViewHelper', 'Errors'));
    }

    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     * @return bool
     */
    public function isValid($formData = array()) {
        // Call original isValid()
        return parent::isValid($formData);
    }
}
?>
