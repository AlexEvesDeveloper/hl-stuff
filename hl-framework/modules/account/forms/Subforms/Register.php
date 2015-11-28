<?php

class Account_Form_Subforms_Register extends Zend_Form_SubForm {

    public function init()
    {
        // Set request method
        $this->setMethod('post');

        // Email entry
        $this->addElement('span', 'email', array(
            'label'      => 'Email address',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'class'      => 'formvalue',
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


        //The password element.
        $passwordElement = new Zend_Form_Element_Password('password');
        $passwordElement->setRequired(true);
        $passwordElement->setLabel('Create your password');
        $passwordElement->setOptions(array('data-noAjaxValidate' => '1'));

        $passwordElement->addValidator(new Zend_Validate_PasswordStrength());

        $validator = new Zend_Validate_Identical();
        $validator->setToken('confirm_password');
        $validator->setMessage('Passwords are not the same', Zend_Validate_Identical::NOT_SAME);
        $passwordElement->addValidator($validator);
        $this->addElement($passwordElement);

        //The confirm password element.
        $confirmPasswordElement = new Zend_Form_Element_Password('confirm_password');
        $confirmPasswordElement->setRequired(true);
        $confirmPasswordElement->setLabel('Re-enter password');
        $confirmPasswordElement->setOptions(array('data-noAjaxValidate' => '1'));

        $validator = new Zend_Validate_NotEmpty();
        $validator->setMessage('Please confirm your password');
        $confirmPasswordElement->addValidator($validator);
        $this->addElement($confirmPasswordElement);

        // Security question & answer
        $securityQuestionModel = new Datasource_Core_SecurityQuestion();
        $securityQuestionOptions = array(0 => '- Please Select -');

        foreach ($securityQuestionModel->getOptions() as $option) {
            $securityQuestionOptions[$option['id']] = $option['question'];
        }

        $this->addElement('select', 'security_question', array(
            'label'     => 'Security Question',
            //'required'  => true, // Value no longer mandatory, Redmine #11873
            'required'  => false,
            'multiOptions' => $securityQuestionOptions,
            'decorators' => array (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ));
/* Value no longer mandatory, Redmine #11873
        $questionElement = $this->getElement('security_question');
        $validator = new Zend_Validate_GreaterThan(array('min'=> 0));
        $validator->setMessage('You must select a security question');
        $questionElement->addValidator($validator);
*/

        $this->addElement('text', 'security_answer', array(
            'label'     => 'Answer',
            //'required'  => true, // Value no longer mandatory
            'required'  => false,
            'filters'   => array('StringTrim'),
        ));

        // Set custom subform decorator - this is the default and gets overridden by view scripts in the tenants' and landlords' Q&Bs
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/register.phtml'))
        ));

        // Set element decorators
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Grab view and add the client-side password validation JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $view->headScript()->appendFile(
            '/assets/common/js/passwordValidation.js',
            'text/javascript'
        );
    }
}
