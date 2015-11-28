<?php
class Connect_Form_RentGuaranteeClaims_ViewClaims extends Zend_Form {

    /**
     * Define view claim form elements
     *
     * @return void
     */
    public function init() {
        $this->setMethod('post');

       // Add hidden element for claim number
        $this->addElement('hidden', 'claimNumber', array(
            'value' => '',
            'class' => 'noborder',
            'label' =>''
        ));
    
        // Add the back button
        $this->addElement('button', 'back', array(
            'ignore' => true,
            'label' => 'Back',
            'value' => 'Back',
            'onclick' => 'window.location="/rentguaranteeclaims/home"'
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