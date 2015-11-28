<?php

class LandlordsInsuranceQuote_Form_Subforms_EmergencyAssistance extends Zend_Form_SubForm
{
    /**
     * Create emergency assistance subform
     *
     * @return void
     */
    
    public function init() {
        // Add need contents insurance element
        $this->addElement('radio', 'need_emergency_assistance', array(
            'label'     => 'Do you need emergency assistance with boiler &amp; heating cover?',
            'required'  => true,
            'multiOptions' => array(
                'yes' => 'Yes',
                'no' => 'No'
            ),
            'separator' => '',
            'label_placement' => 'prepend',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select if you need emergency assistance or not',
                            'notEmptyInvalid' => 'Please select if you need ermergency assistance or not'
                        )
                    )
                )
            )
        ));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/emergency-assistance.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Grab view and add the agent lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
    }
    
    public function isValid($postData) {
    	// If building cover is added to the quote this form isn't even shown because Emergency Assistance is included for free
    	// so we need to make the choice not required for validation purposes
    	$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
		if(isset($pageSession->quoteID)) {
			$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($pageSession->quoteID);
			if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER) ||
			    $quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER)) {
				$this->getElement('need_emergency_assistance')->setRequired(false);
			}
		}
    	
    	return parent::isValid($postData);
    }
}
?>