<?php
class Connect_Form_Subforms_Insurance_AdditionalDetails extends Zend_Form_SubForm {
	/**
	 * Create personal details subform
	 *
	 * @return void
	 */
	public function init(){
		//Additional information.
		$this->addElement('textarea', 'additional_information', array(
				'label'     => 'Additional information',
				'required'  => false,
				'filters'    => array('StringTrim'),
				'validators' => array(
						array(
								'NotEmpty', true, array(
										'messages' => array(
												'isEmpty' => 'Please enter additional information',
										)
								)
						)
				)
		));

		// Add declaration statement agree element
		$this->addElement('checkbox', 'confirmation_statement', array(
				'label'         => '',
				'required'      => true,
				'checkedValue'  => '1',
				'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
				'validators' => array(
						array(
								'NotEmpty', true, array(
										'messages' => array(
												'isEmpty' => 'You must agree to confirmation statement to continue'
										)
								)
						)
				)
		));

		// Set custom subform decorator
		$this->setDecorators(array(
				array('ViewScript', array('viewScript' => 'insurance/subforms/quote-my-additionaldetail.phtml'))
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
		// Call original isValid()

		return parent::isValid($formData);
	}
}