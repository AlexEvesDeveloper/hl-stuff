<?php
class LettingAgents_Form_Step2 extends Zend_Form_Multilevel{

	/*
	 * Organisation type
	 */
	private $_organisationId;
	private $_agent_uid;
	
	/* 
	 * Not sure if this is best practice, but it allows for parameters to be passed when the form is 
	 * created by overriding the parent constructor 
	 * $pageForm = new LettingAgents_Form_Step2(array('occupationId' => '1'));
	 */
	
	public function __construct(array $params = array())
	{
		$this->_organisationId = $params['organisationId'];
		
		// Now call parent contructor
		parent::__construct(); 
	    $pageSession = new Zend_Session_Namespace('letting_agents_application');
	    $this->_agent_uid = $pageSession->agentUniqueId;
	}
	
	
    public function init()
    {	
    	/*
    	 * Depending on the value of the $_organisationId we get a different form
    	 */
    	switch($this->_organisationId){
    		case LettingAgents_Object_CompanyTypes::LimitedCompany:   			
				$this->addSubForm(new LettingAgents_Form_Subforms_LimitedCompanyRegistration(), 'subform_limited-company-registration');
				$this->addSubForm(new LettingAgents_Form_Subforms_LimitedCompanyContacts(), 'subform_limited-company-contacts');
				$this->addSubForm(new LettingAgents_Form_Subforms_Common(), 'subform_common');
				$this->addSubForm(new LettingAgents_Form_Subforms_ContactList(), 'subform_contact-list');
				// Alter the Buttons label
				$this->getSubForm('subform_common')->getElement('add_contact')->setLabel('Add Director');
        	break;
    		
    		case LettingAgents_Object_CompanyTypes::Partnership:   			
    			$this->addSubForm(new LettingAgents_Form_Subforms_Partnership(), 'subform_partnership');
        		$this->addSubForm(new LettingAgents_Form_Subforms_Address(), 'subform_address');
        		$this->addSubForm(new LettingAgents_Form_Subforms_Common(), 'subform_common');
        		$this->addSubForm(new LettingAgents_Form_Subforms_ContactList(), 'subform_contact-list');
        		// Alter the Buttons label
        		$this->getSubForm('subform_common')->getElement('add_contact')->setLabel('Add Partner');
    		break;
    		
    		case LettingAgents_Object_CompanyTypes::SoleTrader:    			
    			$this->addSubForm(new LettingAgents_Form_Subforms_SoleTrader(), 'subform_sole-trader');
    			$this->addSubForm(new LettingAgents_Form_Subforms_Address(), 'subform_address');
    			$this->addSubForm(new LettingAgents_Form_Subforms_Common(), 'subform_common');
    			// Remove the Button to add multi Partners/Directors
    			$this->getSubForm('subform_common')->removeElement('add_contact');
    		break;
    		
    		case LettingAgents_Object_CompanyTypes::LimitedLiabilityPartnership:  			
    			$this->addSubForm(new LettingAgents_Form_Subforms_LimitedLiabilityPartnership(), 'subform_limited-liability-partnership');
    			$this->addSubForm(new LettingAgents_Form_Subforms_Address(), 'subform_address');
    			$this->addSubForm(new LettingAgents_Form_Subforms_Common(), 'subform_common');
    			$this->addSubForm(new LettingAgents_Form_Subforms_ContactList(), 'subform_contact-list');
    			// Alter the Buttons label
    			$this->getSubForm('subform_common')->getElement('add_contact')->setLabel('Add Partner');
    		break;
    		
    		default:
    	}
    }
    
     public function isValid($postData) {
     	$contacts = new LettingAgents_Manager_Contacts();
    	$pageSession = new Zend_Session_Namespace('letting_agents_application');

    	if($contacts->fetchByAgencyUid($this->_agent_uid)){
    		switch($this->_organisationId){
    			case LettingAgents_Object_CompanyTypes::LimitedCompany:
    				$this->getSubForm('subform_limited-company-contacts')->getElement('contact_name')->setRequired(false);
    				
    				break;
    			case LettingAgents_Object_CompanyTypes::LimitedLiabilityPartnership:
		    		if($postData['postcode'] != ""){
    					$this->getSubForm('subform_limited-liability-partnership')->getElement('contact_name')->setRequired(true);
    				}elseif($postData['contact_name'] != ""){
    					$this->getSubForm('subform_address')->getElement('postcode')->setRequired(true);
    				}else{
						$this->getSubForm('subform_limited-liability-partnership')->getElement('contact_name')->setRequired(false);
						$this->getSubForm('subform_address')->getElement('postcode')->setRequired(false);
						$this->getSubForm('subform_address')->getElement('address')->setRequired(false);
						$this->getSubForm('subform_address')->getElement('address')->setRegisterInArrayValidator(false);
    				}    				
    				break;
    			case LettingAgents_Object_CompanyTypes::Partnership:
    				if($postData['postcode'] != "" || $postData['contact_name'] != ""){
    					$this->getSubForm('subform_partnership')->getElement('contact_name')->setRequired(true);
    				}else{
						$this->getSubForm('subform_partnership')->getElement('contact_name')->setRequired(false);
						$this->getSubForm('subform_address')->getElement('postcode')->setRequired(false);
						$this->getSubForm('subform_address')->getElement('address')->setRequired(false);
						$this->getSubForm('subform_address')->getElement('address')->setRegisterInArrayValidator(false);
    				}
					break;
    		}
    	}
    	elseif ($postData['contact_name'] != ''){
    		$agent_id   = $this->_agent_uid;
    		$db = Zend_Registry::get('db_letting_agents');
			//die($clause);
			$validator = new Zend_Validate_Db_RecordExists(
			    array(
			        'table' => 'contact',
		            'field' => 'agency_id',
		            'value' => $agent_id,
		    		'adapter' => $db
					   )
					);
            $validator->setMessages(array(
                Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Please Click Add Director'
            ));		
            
            switch($this->_organisationId){
            	case LettingAgents_Object_CompanyTypes::LimitedCompany:
            		$this->getSubForm('subform_limited-company-contacts')->getElement('contact_name')
		    		->addValidator($validator);
		    	     $validator->setMessages(array(
                		Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Please Click Add Director'
            			));	
            		break;
            	case LettingAgents_Object_CompanyTypes::LimitedLiabilityPartnership:
            		$this->getSubForm('subform_limited-liability-partnership')->getElement('contact_name')
		    		->addValidator($validator);
		    		$validator->setMessages(array(
                		Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Please Click Add Partner'
            			));
            		break;
            	case LettingAgents_Object_CompanyTypes::Partnership:
            		$this->getSubForm('subform_partnership')->getElement('contact_name')
		    		->addValidator($validator);
		    		$validator->setMessages(array(
                		Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Please Click Add Partner'
            			));
            		break;
            }			
		    
    	}
    	
    return parent::isValid($postData);
     }
}