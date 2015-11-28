<?php

class Form_TenantsReferencingTracker_CallMe extends Zend_Form {
    /**
     * Define the call-me-back form elements
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');

        // Add mobile number element
        $this->addElement('text', 'mobile_number', array(
            'label'      => 'Mobile number',
            'required'   => true,
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

        // Add landline number element
        $this->addElement('text', 'landline_number', array(
            'label'      => 'Landline number',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your landline number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^.{1,100}$/',
                        'messages' => 'Landline number must not be more than 100 characters'
                    )
                )
            )
        ));

        // Add additional info element
        $this->addElement('textarea', 'additional_info', array(
            'label'     => 'Any additional information',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter details for declaration question 1',
                        )
                    )
                )
            )
        ));

        // Add best time to call element
        $this->addElement('select', 'time_to_call', array(
            'label'     => 'When is the best time to call',
            'required'  => true,
            'multiOptions' => array(
                'Anytime' => 'Anytime',
                'Morning' => 'Morning',
                'Afternoon' => 'Afternoon',
                'Evening' => 'Evening'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the best time for us to call you',
                            'notEmptyInvalid' => 'Please select the best time for us to call you'
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
            array('ViewScript', array('viewScript' => 'tenants-referencing-tracker/subforms/callme.phtml'))
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