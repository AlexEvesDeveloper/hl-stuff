<?php
class Account_Form_ForgotPassword extends Zend_Form {

    public function init()
    {
        $this->setMethod('post');

        $this->setAttrib('id', 'forgot-password');

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
                Zend_Validate_EmailAddress::INVALID_HOSTNAME => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT => "Invalid email address"
            )
        );
        $this->getElement('email')->addValidator($emailValidator);

        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors',
            array('HtmlTag', array('tag' => 'div')),
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'    => true,
            'label'     => 'Reset Password',
            'class'     => 'button noalt'
        ));

        // Remove the label from the submit button
        $element = $this->getElement('submit');
        $element->removeDecorator('label');

        // Set up the decorator on the form and add in decorators which are removed
        $this->addDecorator('FormElements')
            ->addDecorator(
                'HtmlTag',
                array('tag' => 'div', 'class' => 'form_section one-col')
            )
            ->addDecorator('Form');
    }
}
