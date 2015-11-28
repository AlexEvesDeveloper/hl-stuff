<?php

/**
 * register customer account form
 * 
 * @package Account_Form_Register 
 */
class Account_Form_Register extends Zend_Form
{
    /**
     * Initialise the form
     * 
     * @todo Validation
     * @return void 
     */
    public function init()
    {
        // Set request method
        $this->setMethod('POST');


        // Add title element
        $this->addElement('select', 'title', array(
            'label'     => 'Title',
            'required'  => true,
            'multiOptions' => TenantsInsuranceQuote_Form_Subforms_PersonalDetails::$titles,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please select your title',
                        'notEmptyInvalid' => 'Please select your title'
                    )
                )
                )
            ),
            'class' => 'form-control',
        ));

        // Add first name element
        $this->addElement('text', 'first_name', array(
            'label'      => 'First name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter your first name',
                        'notEmptyInvalid' => 'Please enter your first name'
                    )
                )
                ),
                array(
                    'regex', true, array(
                    'pattern' => '/^[a-z\-\ \']{2,}$/i',
                    'messages' => 'First name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                )
                )
            ),
            'class' => 'form-control',
        ));

        // Add last name element
        $this->addElement('text', 'last_name', array(
            'label'      => 'Last name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter your last name'
                    )
                )
                ),
                array(
                    'regex', true, array(
                    'pattern' => '/^[a-z\-\ \']{2,}$/i',
                    'messages' => 'Last name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                )
                )
            ),
            'class' => 'form-control',
        ));

        // Email element
        $this->addElement('text', 'email', array(
            'label'      => 'Email Address',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Email address is required'
                        )
                    )
                )
            ),
            'class' => 'form-control',
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
		$passwordElement = new Zend_Form_Element_Password('password', array(
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Password is required'
                        )
                    )
                )
            ),
            'class' => 'form-control',
        ));
		$passwordElement->setRequired(true);
		$passwordElement->setLabel('Password');
		$passwordElement->setAttribs(array('class' => 'form-control'));
		$passwordElement->addValidator(new Zend_Validate_PasswordStrength());
		
		$validator = new Zend_Validate_Identical();
		$validator->setToken('confirm_password');
		$validator->setMessage('Passwords are not the same', Zend_Validate_Identical::NOT_SAME);
		$passwordElement->addValidator($validator);
		$this->addElement($passwordElement);

		//The confirm password element.
		$confirmPasswordElement = new Zend_Form_Element_Password('confirm_password');
		$confirmPasswordElement->setRequired(true);
		$confirmPasswordElement->setLabel('Confirm Password');
        $confirmPasswordElement->setAttribs(array('class' => 'form-control'));
		$validator = new Zend_Validate_NotEmpty();
		$validator->setMessage('Please confirm your password');
		$confirmPasswordElement->addValidator($validator);
		$this->addElement($confirmPasswordElement);
        
        // Security question & answer
        $this->addElement('select', 'security_question', array(
            'label'     => 'Security Question',
            'required'  => true,
            'multiOptions' => array('' => 'Please select'),
            'decorators' => array (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Security question is required'
                        )
                    )
                )
            ),
            'class' => 'form-control',
        ));
        
        $this->addElement('text', 'security_answer', array(
            'label'      => 'Answer',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Security answer is required'
                        )
                    )
                )
            ),
            'class' => 'form-control',
        ));
        
        $this->addElement('hidden','refno', array('required'  => false, 'class' => 'noalt'));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'    => true,
            'label'     => 'Register',
            'class'     => 'btn btn-primary pull-right'
        ));

        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors',
        ));

        // Set up the decorator on the form and add in decorators which are removed
        $this->addDecorator('FormElements')
            ->addDecorator(
                'HtmlTag',
                array('tag' => 'div', 'class' => 'form_section')
            )
            ->addDecorator('Form');

        // Remove the label from the submit button
        $element = $this->getElement('submit');
        $element->removeDecorator('label');

        $this->getElement('refno')->removeDecorator('HtmlTag');
    }
}
