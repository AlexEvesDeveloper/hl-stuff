<?php
class LandlordsInsuranceQuote_Form_Save extends Zend_Form_Multilevel {

    public function init()
    {
        $this->setMethod('post');
        //The password element.
		$passwordElement = new Zend_Form_Element_Password('password');
		$passwordElement->setRequired(true);
		$passwordElement->setLabel('New Password:');
		
		$passwordElement->addValidator(new Zend_Validate_PasswordStrength());
		
		$validator = new Zend_Validate_Identical();
		$validator->setToken('confirm_password');
		$validator->setMessage('Passwords are not the same', Zend_Validate_Identical::NOT_SAME);
		$passwordElement->addValidator($validator);
		$this->addElement($passwordElement);
		
		//The confirm password element.
		$confirmPasswordElement = new Zend_Form_Element_Password('confirm_password');
		$confirmPasswordElement->setRequired(true);
		$confirmPasswordElement->setLabel('Confirm New Password:');
		
		$validator = new Zend_Validate_NotEmpty();
		$validator->setMessage('Please confirm your password');
		$confirmPasswordElement->addValidator($validator);
		$this->addElement($confirmPasswordElement);
        
        // Security question & answer
        $this->addElement('select', 'security_question', array(
            'label'     => 'Security Question',
            'required'  => true,
            'multiOptions' => array(0 => 'Please select'),
            'decorators' => array (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ));
        
        $this->addElement('text', 'security_answer', array(
            'label'      => 'Answer',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));

        $this->addElement('hidden','instruction', array('required'  => false ));
        $this->addElement('submit', 'submit', array(
            'ignore'    => true,
            'label'     => 'Save',
            'class'     => 'button'
        ));
        
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

        $element = $this->getElement('submit');
        $element->removeDecorator('label');
        
    }
}
?>
