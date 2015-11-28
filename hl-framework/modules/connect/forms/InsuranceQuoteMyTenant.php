<?php

class Connect_Form_InsuranceQuoteMyTenant extends Zend_Form_Multilevel {

    public function init() {   	
    	
    	$this->addSubForm(new Connect_Form_Subforms_Insurance_PersonalDetails(), 'subform_personaldetails');
    	$this->addSubForm(new Connect_Form_Subforms_Insurance_RiskAddress(), 'subform_riskaddress');
    	$this->addSubForm(new Connect_Form_Subforms_Insurance_AdditionalDetails(), 'subform_additionaldetails');
    	// Add submit button
    	$this->addElement('submit', 'submit', array('label' => 'Quote my tenant'));
    	$this->addElement('hidden', 'prospector', array('value' => 'tenant'));
    	// And finally add some CSRF protection
    	$this->addElement('hash', 'csrf', array(
    			'ignore' => true,
    	));
    }

}