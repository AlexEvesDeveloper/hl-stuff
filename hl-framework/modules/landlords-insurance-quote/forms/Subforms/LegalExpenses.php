<?php

class LandlordsInsuranceQuote_Form_Subforms_LegalExpenses extends Zend_Form_SubForm
{
    /**
     * Create legal expenses subform
     *
     * @return void
     */
    
    public function init() {
        // Add need contents insurance element
        $this->addElement('radio', 'need_legal_expenses', array(
            'label'     => 'Do you need legal expenses cover?',
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
                            'isEmpty' => 'Please select if you need legal expenses cover or not',
                            'notEmptyInvalid' => 'Please select if you need legal expenses cover or not'
                        )
                    )
                )
            )
        ));
            
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/legal-expenses.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Grab view and add the agent lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
    }
    
    public function isValid($postData) {
    	// If prestige rent guarantee is added to the quote this form isn't even shown because it's included for free
    	// so we need to make the choice not required for validation purposes
    	$pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
		if(isset($pageSession->quoteID)) {
			$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($pageSession->quoteID);
			if ($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::RENT_GUARANTEE) || 
				(isset($postData['need_prestige_rent_guarantee']) && $postData['need_prestige_rent_guarantee'] == 'yes')) {
				$this->getElement('need_legal_expenses')->setRequired(false);
			}
		}
    	
    	return parent::isValid($postData);
    }

    
}
?>