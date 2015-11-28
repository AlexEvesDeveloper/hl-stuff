<?php
class Connect_Form_RentGuaranteeClaims_Home extends Zend_Form {

    /**
     * Define home form elements
     *
     * @return void
     */
    public function init() {
        $this->setMethod('post');
        
        // Add claim Number element
        $this->addElement(
            'text',
            'claimNumber',
            array(
                'required' => true,
                'validators' => array(
                    array(
                        'NotEmpty',
                        true,
                        array(
                            'messages' => array (
                                'isEmpty' => 'Please enter claim number',
                                'notEmptyInvalid' => 'Please enter valid claim number'
                            )
                        )
                    )
                )
            )
        );

        // Add hidden element for claim reference number
        $this->addElement('hidden', 'ref_num', array(
            'value' => '',
            'class' => 'noborder',
            'label' =>''
        ));
       
        // Add hidden element for mode
        $this->addElement('hidden', 'mode', array(
            'value' => '',
            'class' => 'noborder',
            'label' =>''
        ));     

        // Add search button
        $this->addElement('image', 'search', array(
            'src' => '/assets/connect/images/claims/search-button.jpg',
            'align' => 'top',
            'class' =>  'search',
            'onclick' => 'return validateClaimNumber()'
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