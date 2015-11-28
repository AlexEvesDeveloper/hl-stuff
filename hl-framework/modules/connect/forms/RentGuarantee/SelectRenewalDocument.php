<?php

class Connect_Form_Rentguarantee_SelectRenewalDocument extends Zend_Form
{
    /**
     * Initialise the form
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');
        
        $this->addElement('hidden', 'policynumber');
        
        $documenttype = $this->addelement('select', 'documenttype', array('label' => 'Landlords document type to produce'))->getElement('documenttype');
        $documenttype->addMultiOption('landlordinvite', 'Invite');
        $documenttype->addMultiOption('landlordreminder', 'Reminder');
        
        $this->addElement('checkbox', 'agentinvite', array('label' => 'Resend me a copy of my renewal invitation', 'value' => 'true'));
        
        // Continue and Back
        $this->addElement('submit', 'formsubmit_back', array('name' => 'formsubmit_back', 'label' => 'Back'));
        $this->addElement('submit', 'formsubmit_continue', array('name' => 'formsubmit_continue', 'label' => 'Continue'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguarantee/subforms/rentguarantee-select-renewal-document.phtml'))
        ));
        
		$this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        $back = $this->getElement('formsubmit_back');
        $back->setDecorators(array(
            array('ViewHelper', array('escape' => false))
        ));
        
        $next = $this->getElement('formsubmit_continue');
        $next->setDecorators(array(
            array('ViewHelper', array('escape' => false))
        ));
    }
}
