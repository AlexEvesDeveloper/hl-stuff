<?php

class LandlordsInsuranceQuote_Form_Subforms_BoilerHeating extends Zend_Form_SubForm
{
    /**
     * Create boiling & heating subform
     *
     * @return void
     */
    
    public function init() {
        // Add need contents insurance element
        $this->addElement('radio', 'need_boiler_heating', array(
            'label'     => 'Do you need boiler & heating cover?',
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
                            'isEmpty' => 'Please select if you need boiler & heating cover or not',
                            'notEmptyInvalid' => 'Please select if you need boiler & heating cover or not'
                        )
                    )
                )
            )
        ));
            
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/boiler-heating.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Grab view and add the agent lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
    }
    
    public function isValid($postData) {
    	// If Emergency Assistance is included for free
    	// so we need to make the choice not required for validation purposes
    	$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
		if(isset($pageSession->quoteID)) {
			$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($pageSession->quoteID);
			if (!$quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::BUILDING_COVER) && !$quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::CONTENTS_COVER)) {
				$this->getElement('need_boiler_heating')->setRequired(false);
			}
		}
    	
    	return parent::isValid($postData);
    }

}
?>