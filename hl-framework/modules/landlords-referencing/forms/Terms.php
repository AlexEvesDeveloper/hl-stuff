<?php
class LandlordsReferencing_Form_Terms extends Zend_Form {

    protected $_clearValidators = false;
	
	public function init() {
		
		$checkedValidator = new Zend_Validate_GreaterThan(array('min' => 0));
		$checkedValidator->setMessage(
			'We are unable to process your application if you do not agree to the referencing terms',
			Zend_Validate_GreaterThan::NOT_GREATER);
		
		$consentInfoStored = new Zend_Form_Element_Checkbox('consent_information_stored');
		$consentInfoStored
			->setRequired(true)
			->addValidator($checkedValidator);
		$this->addElement($consentInfoStored);

		$consentReferee = new Zend_Form_Element_Checkbox('consent_referee');
		$consentReferee
			->setRequired(true)
			->addValidator($checkedValidator);
		$this->addElement($consentReferee);

		$this->addElement('checkbox', 'consent_nondigital_marketing', array(
            'required'  => false
        ));
		
		$this->addElement('checkbox', 'consent_digital_marketing', array(
            'required'  => false
        ));
        
        $this->addElement('select', 'terms_agreed', array(
            'label'     => 'I agree:',
            'required'  => true,
            'multiOptions' => array(
				'No' => 'No',
        		'Yes' => 'Yes'
        	)
        ));
        
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'landlords-referencing/terms.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
	
	public function setClearValidators($clearValiators) {
		
		$this->_clearValidators = $clearValiators;
	}
	
	public function isValid($data) {
		
		if($this->_clearValidators) {
			
			$this->consent_information_stored->clearValidators();
			$this->consent_referee->clearValidators();
		}
		return parent::isValid($data);
	}
	
	public function getMessagesFlattened() {
		
		return $this->getMessages();
	}
}
?>