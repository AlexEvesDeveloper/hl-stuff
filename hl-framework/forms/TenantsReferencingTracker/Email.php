<?php

class Form_TenantsReferencingTracker_Email extends Zend_Form {
    
    /**
     * Define the e-mail form elements
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');

        // Add name element
        $this->addElement('text', 'name', array(
            'label'      => 'Please enter your name (optional)',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your name',
                            'notEmptyInvalid' => 'Please enter your name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\.\-\ \']{2,}$/i',
                        'messages' => 'Name must contain at least two alphabetic characters and only basic punctuation (full stop, hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add contact number or e-mail address element
        $this->addElement('text', 'contact_info', array(
            'label'      => 'Please enter a contact number or email address (optional)',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your mobile number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^.{1,100}$/',
                        'messages' => 'Mobile number must not be more than 100 characters'
                    )
                )
            )
        ));

        // Add message element
        $this->addElement('textarea', 'message', array(
            'label'     => 'Please enter the message for the assessor',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a message',
                        )
                    )
                )
            )
        ));

        // Set decorators
        $this->clearDecorators();
        $this->setDecorators(array('Form'));
        $this->setElementDecorators(array ('ViewHelper', 'Label', 'Errors'));

        // Add the back button
        $this->addElement('submit', 'back', array(
            'ignore'   => true,
            'label'    => 'Back',
        ));
        $next = $this->getElement('back');
        $next->clearDecorators();
        $next->setDecorators(array('ViewHelper'));

        // Add the next button
        $this->addElement('submit', 'next', array(
            'ignore'   => true,
            'label'    => 'Submit',
        ));
        $next = $this->getElement('next');
        $next->clearDecorators();
        $next->setDecorators(array('ViewHelper'));

        /*
        // Add some CSRF protection ***** read up and check this works
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
        */

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'tenants-referencing-tracker/subforms/email.phtml'))
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

        // If a mobile phone number is given, landline is not mandatory
        if (isset($formData['mobile_number']) && trim($formData['mobile_number']) != '') {
            $this->getElement('landline_number')->setRequired(false);
        }

        // If a landline phone number is given, mobile is not mandatory
        if (isset($formData['landline_number']) && trim($formData['landline_number']) != '') {
            $this->getElement('mobile_number')->setRequired(false);
        }
        
        // Call original isValid()
        return parent::isValid($formData);
    }
}
?>