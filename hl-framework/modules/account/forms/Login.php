<?php
class Account_Form_Login extends Zend_Form {

    public function init()
    {
        $this->setMethod('post');

        // Hidden referrer url
        $urlFilter = new Zend_Filter_PregReplace(); // Filter to remove any host names
        $urlFilter->setMatchPattern('/[a-zA-Z0-9]*:\/\/.*\//');
        $urlFilter->setReplacement('/');

        $this->addElement('hidden', 'referrerUrl', array(
            'required' => false,
            'filters'  => array(
                $urlFilter
            ),
        ));
        
        // Email entry
        $this->addElement('text', 'email', array(
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
        
        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors'
        ));
                
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'    => true,
            'label'     => 'Retrieve My Quotes',
            'class'     => 'btn btn-primary pull-left'
        ));

        // Add a resend validation link button
        $this->addElement('submit', 'resendValidation', array(
            'ignore'    => true,
            'label'     => 'Resend Account Validation',
            'class'     => 'btn btn-primary'
        ));

        // Add a forgotten password button
        $this->addElement('submit', 'forgottenPassword', array(
            'ignore'    => true,
            'label'     => 'Reset Password',
            'class'     => 'btn btn-primary'
        ));
        
        // Remove the label from the submit buttons
        $element = $this->getElement('submit');
        $element->removeDecorator('label');

        $element = $this->getElement('resendValidation');
        $element->removeDecorator('label');

        $element = $this->getElement('forgottenPassword');
        $element->removeDecorator('label');

        // Set up the decorator on the form and add in decorators which are removed
        /*$this->addDecorator('FormElements')
            ->addDecorator(
                'HtmlTag', 
                array('tag' => 'div', 'class' => 'form_section one-col')
                )
            ->addDecorator('Form');*/

        $this->setDecorators(array(
                'FormElements',
                array('HtmlTag', array('tag' => 'div')),
                'Form'
            ));
    }
}
