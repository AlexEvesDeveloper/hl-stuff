<?php
class Connect_Form_RentGuaranteeClaims_SubmitClaim extends Zend_Form {

    /**
     * Define submit claim form elements
     *
     * @return void
     */
    public function init() {
        $this->setMethod('post');
        
        // Add hidden element for claim number
        $this->addElement('hidden', 'ref_num', array(
            'value' => '',
            'class' => 'noborder'
        ));

         // Add hidden element to pass mode
        $this->addElement('hidden', 'mode', array(
            'value' => 'print',
            'class' => 'noborder'
        ));
    
        // Add the exit button
        $this->addElement('button', 'claim_exit', array(
            'ignore' => true,
            'label' => 'Exit',
            'value' => 'Exit',
            'onclick' => 'window.location="/rentguaranteeclaims/home"'
        ));

        // Add the fax header button
        $this->addElement('button', 'print_fax_header', array(
            'ignore' => true,
            'value' => 'Print fax header',
            'label' => 'Print fax header',
            'onclick' => 'javascript:submitForm("/rentguaranteeclaims/print-fax-header")'
        ));

        // Add the print claim button
        $this->addElement('button', 'print_claim', array(
            'ignore' => true,
            'value' => 'Print claim',
            'label' => 'Print claim',
            'onclick' => 'javascript:submitForm("/rentguaranteeclaims/print-claim")'
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