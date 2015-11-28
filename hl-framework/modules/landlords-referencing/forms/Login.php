<?php

class LandlordsReferencing_Form_Login extends Zend_Form
{
    public function init()
    {
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
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
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
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
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
            'label'     => 'Login',
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
    
    /**
     * Attempts to log the user in.
     * 
     * @param string $emailAddress The login name.
     * @param string $password The password.
     * @return bool
     */
    public function login($emailAddress, $password)
    {
        // There may be more than one customer record with the same email address.
        $loginManager = new Manager_Referencing_Login();

        // Attempt to log in using the current customer details.
        if ($loginManager->logUserIn($emailAddress, $password)) {
            // User successfully logged in. Set some session variables
            // and return success.
            $customerManager = new Manager_Referencing_Customer();
            $customer = $customerManager->getByEmailAddress($emailAddress);

            $session = new Zend_Session_Namespace('referencing_global');
            $session->customerId = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);

            return true;
        }

        return false;
    }
	
	public function getMessagesFlattened()
    {
		return $this->getMessages();
	}
}
