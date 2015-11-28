<?php

class Connect_Form_ReferencingResendEmail extends Zend_Form {

    /**
     * Create reference resend e-mail to applicant form.
     *
     * @return void
     */
    public function init() {

        // Add e-mail element
        $this->addElement('text', 'email', array(
            'label'      => 'E-mail address',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter recipient\'s e-mail address'
                        )
                    )
                )
            )
        ));

        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => 'Domain name invalid in e-mail address',
                Zend_Validate_EmailAddress::INVALID_FORMAT      => 'Invalid e-mail address'
            )
        );
        $this->getElement('email')->addValidator($emailValidator);

        // Add replace e-mail address element
        $this->addElement('checkbox', 'replace', array(
            'label'         => 'Replace e-mail address',
            'checkedValue'  => '1'
        ));

        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            array('HtmlTag', array('tag' => 'div')),
        ));

        // Add send button
        $this->addElement('submit', 'send', array('label' => 'Send'));
    }
}