<?php

/**
 * Edit customer account form
 * 
 * @package Account_Form_EditAccount 
 */
class Account_Form_EditAccount extends Zend_Form
{
    protected $_currentPassword = null;

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
        $this->addElement('text', 'title', array(
            'label'     => 'Title',
        ));

        // Add first name element
        $this->addElement('text', 'first_name', array(
            'label'      => 'First name'
        ));

        // Add last name element
        $this->addElement('text', 'last_name', array(
            'label'      => 'Last name'
        ));

        $this->addElement('password', 'existing_password', array(
            'required'  => true,
            'filters'    => array('StringTrim'),
            'class' => 'form-control',
        ));

        // Email element
        $this->addElement('text', 'email', array(
            'label'      => 'Email Address'
        ));

        //The password element.
		$passwordElement = new Zend_Form_Element_Password('password');
		$passwordElement->setRequired(false); // New password is not required to update options
		$passwordElement->addValidator(new Zend_Validate_PasswordStrength());
        $passwordElement->setAttribs(array('class' => 'form-control'));
		
		$validator = new Zend_Validate_Identical();
		$validator->setToken('confirm_password');
		$validator->setMessage('Passwords are not the same', Zend_Validate_Identical::NOT_SAME);
		$passwordElement->addValidator($validator);
		$this->addElement($passwordElement);
		
		//The confirm password element.
		$confirmPasswordElement = new Zend_Form_Element_Password('confirm_password');
		$confirmPasswordElement->setRequired(false); // New password is not required to update options
        $confirmPasswordElement->setAttribs(array('class' => 'form-control'));
		
		$validator = new Zend_Validate_NotEmpty();
		$validator->setMessage('Please confirm your password');
		$confirmPasswordElement->addValidator($validator);
		$this->addElement($confirmPasswordElement);
        
        // Security question & answer
        $this->addElement('select', 'security_question', array(
            'required'  => true,
            'multiOptions' => array(0 => 'Please select'),
            'decorators' => array (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            ),
            'class' => 'form-control',
        ));
        
        $this->addElement('text', 'security_answer', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'class' => 'form-control',
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'    => true,
            'class'     => 'btn btn-primary pull-right',
            'label'     => 'Save',
        ));
        
        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors',
            array('HtmlTag', array('tag' => 'div')),
        ));

        // Set up the decorator on the form and add in decorators which are removed
        $this->addDecorator('FormElements')
            ->addDecorator(
                'HtmlTag',
                array('tag' => 'div', 'class' => 'form_section one-col')
                )
            ->addDecorator('Form');

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/edit-account.phtml'))
        ));

        // Remove the label from the submit button
        $element = $this->getElement('submit');
        $element->removeDecorator('label');
    }

    /**
     * Set the users current password for performing password validation.
     * Done in this way to reduce coupling.
     *
     * @param $currentPassword The users current password
     */
    public function setCurrentPassword($currentPassword)
    {
        $this->_currentPassword = $currentPassword;
    }


    /**
     * Perform form validation. Overrides to perform current password
     * validation checks.
     *
     * @param $data Form data
     * @return bool Validation status
     */
    public function isValid($data)
    {
        $customermgr = new Manager_Core_Customer();

        // Perform the overridden form validation
        $isValid = parent::isValid($data);

        if ($this->existing_password->getValue() != '' && $this->_currentPassword != $this->existing_password->getValue()) {
            // Invalid password, override the error status of the form
            // If the password has not been supplied, rely on validation from form
            $this->getElement('existing_password')
                ->addErrorMessage('Current password is incorrect')
                ->markAsError();
            return false;
        }

        // Additional checks are valid, return standard form validation result
        return $isValid;
    }
}
