<?php

class Account_Form_Subforms_Login extends Zend_Form_SubForm {

    public function init()
    {
        // Set request method
        $this->setMethod('post');

        // Email entry
        $this->addElement('text', 'email', array(
            'label'      => 'Email address',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter your email address'
                    )
                )
                )
            )
        ));

        // Modify email error messages & add validator
        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Invalid email address"
            )
        );
        $this->getElement('email')->addValidator($emailValidator);


        // Password entry
        $this->addElement('password', 'password', array(
            'required'  => true,
            'label'     => 'Password',
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter your password'
                    )
                )
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/login.phtml'))
        ));

        // Set element decorators
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}
