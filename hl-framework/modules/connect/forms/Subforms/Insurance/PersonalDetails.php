<?php
/**
 * 
 * @author johnburrin
 * Zend Form for quote my Tenant request
 */
class Connect_Form_Subforms_Insurance_PersonalDetails extends Zend_Form_SubForm {
	/**
	 * Static array of title options
	 *
	 * @var array
	 */
	public static $titles = array(
			'Mr' => 'Mr',
			'Mrs' => 'Mrs',
			'Ms' => 'Ms',
			'Miss' => 'Miss',
			'Sir' => 'Sir',
			'Mr and Mrs' => 'Mr and Mrs',
			'Dr' => 'Dr',
			'Professor' => 'Professor',
			'Rev' => 'Rev',
			'Other' => 'Other'
	);

	/**
	 * Create personal details subform
	 *
	 * @return void
	*/
	public function init()
	{
		// Add title element
		$this->addElement('select', 'title', array(
				'label'     => 'Title',
				'required'  => true,
				'multiOptions' => self::$titles,
				'validators' => array(
						array(
								'NotEmpty', true, array(
										'messages' => array(
												'isEmpty' => 'Please select your title',
												'notEmptyInvalid' => 'Please select your title'
										)
								)
						)
				)
		));

		// Add other title element
		$this->addElement('text', 'other_title', array(
				'label' => 'Other title',
				'required' => false,
				'filters'    => array('StringTrim'),
				'validators' => array(
						array(
								'NotEmpty', true, array(
										'messages' => array(
												'isEmpty' => 'Please enter your title',
												'notEmptyInvalid' => 'Please enter your title'
										)
								)
						)
				)
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
				)
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
				)
		));

		// Add phone number element
		$this->addElement('text', 'phone_number', array(
				'label'      => 'Phone number',
				'required'   => true,
				'validators' => array(
						array(
								'NotEmpty', true, array(
										'messages' => array(
												'isEmpty' => 'Please enter your phone number'
										)
								)
						)
				)
		));

		// Add mobile number element
		$this->addElement('text', 'mobile_number', array(
				'label'      => 'Mobile number',
				'required'   => true,
				'validators' => array(
						array(
								'NotEmpty', true, array(
										'messages' => array(
												'isEmpty' => 'Please enter your mobile number'
										)
								)
						)
				)
		));
/*
		// Add e-mail element
		$this->addElement('text', 'email_address', array(
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

		
		$emailValidator = new Zend_Validate_EmailAddress();
		$emailValidator->setMessages(
				array(
						Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
						Zend_Validate_EmailAddress::INVALID_FORMAT      => "Invalid email address"
				)
		);
		$this->getElement('email_address')->addValidator($emailValidator);
*/
		// Set custom subform decorator
		$this->setDecorators(array(
				array('ViewScript', array('viewScript' => 'insurance/subforms/quote-my-personaldetail.phtml'))
		));

		// Strip all tags to prevent XSS errors
		$this->setElementFilters(array('StripTags'));


		$this->setElementDecorators(array(
				array('ViewHelper', array('escape' => false)),
				array('Label', array('escape' => false))
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
		
		// If a landline phone number is given, mobile is not mandatory
		if (isset($formData['phone_number']) && trim($formData['phone_number']) != '') {
			$this->getElement('mobile_number')->setRequired(false);
		}
		
		// If a mobile phone number is given, landline is not mandatory
		if (isset($formData['mobile_number']) && trim($formData['mobile_number']) != '') {
			$this->getElement('phone_number')->setRequired(false);
		}		
		// Call original isValid()
		return parent::isValid($formData);
	}
}