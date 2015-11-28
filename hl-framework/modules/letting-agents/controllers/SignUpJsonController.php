<?php
/**
* Letting Agents
* @author John Burrin
* @since 1.5 
*
*/
class LettingAgents_SignUpJsonController extends Zend_Controller_Action
{
		public function init() {
		
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		header('Content-type: application/json');
		// Add some Xdebug out params
		if (APPLICATION_ENV == 'development') {
    		ini_set('html_errors', 'Off'); // Html Output form errors
		}
		
	}

	/**
	 * 
	 * Remove an existing contact
	 */
	public function removeContactAction(){
		$return = array();
		$pageSession = new Zend_Session_Namespace('letting_agents_application');
		$contactManager = new LettingAgents_Manager_Contacts();
		
		$postData = $this->getRequest()->getParams();
		$filters = array('uid' => 'StringTrim', 'uid' => 'StripTags');
		$validators = array('uid' => 'Alnum');
		$input = new Zend_Filter_Input($filters, $validators, $postData);
		if ($input->isValid()) {
			// Valid input
			$contactManager->deleteByUid($input->uid);
		} else {
		    // false
		    $return['errorHtml'] = 'Invalid Contact';
		}
		
		$agent = new LettingAgents_Manager_AgentApplication();
		$agentData = new LettingAgents_Object_AgentApplication();
		$agentData = $agent->fetchByUid($pageSession->agentUniqueId);
		$organisation_type = $agentData->get_organisation_type();
		switch($organisation_type){
			case LettingAgents_Object_CompanyTypes::LimitedCompany :
				$partialFile = "limited-company-list.phtml";
				break;
			case LettingAgents_Object_CompanyTypes::LimitedLiabilityPartnership :
			case LettingAgents_Object_CompanyTypes::Partnership :
				$partialFile ="partnership-list.phtml";
				break;
		}
		$return['contactHtml'] = $this->view->partialLoop("partials/$partialFile",$contactManager->fetchByAgencyUid($pageSession->agentUniqueId));
		echo Zend_Json::encode($return);
	}
	
	/**
	 * 
	 * Add a new contact...
	 */
	public function addContactAction(){
		$return = array();
		$pageSession = new Zend_Session_Namespace('letting_agents_application');
		$agent = new LettingAgents_Manager_AgentApplication();
		$agentData = new LettingAgents_Object_AgentApplication();
		$agentData = $agent->fetchByUid($pageSession->agentUniqueId);
		$agencyid = $agentData->get_id();
		$postData = $this->getRequest()->getParams();
		$contactManager = new LettingAgents_Manager_Contacts();
		
		
		$agent = new LettingAgents_Manager_AgentApplication();
		$agentData = new LettingAgents_Object_AgentApplication();
		$agentData = $agent->fetchByUid($pageSession->agentUniqueId);
		$organisation_type = $agentData->get_organisation_type();
		switch($organisation_type){
			case LettingAgents_Object_CompanyTypes::LimitedCompany :
				$data = new LettingAgents_Object_Contact();
				$partialFile = "limited-company-list.phtml";
				break;
			case LettingAgents_Object_CompanyTypes::LimitedLiabilityPartnership :
			case LettingAgents_Object_CompanyTypes::Partnership :
				$data = new LettingAgents_Object_PartnershipContact();
				$partialFile ="partnership-list.phtml";
				break;
		}
		
		$data->set_agency_id($agencyid);		
		if(isset($postData['contact_name']))  	$data->set_contact_name( $postData['contact_name']);
		if(isset($postData['address1'])) 		$data->set_address1( $postData['address1']);
		if(isset($postData['address2'])) 		$data->set_address2( $postData['address2']);
		if(isset($postData['address3'])) 		$data->set_address3( $postData['address3']);
		if(isset($postData['birth_date'])) 		$data->set_birth_date( $postData['birth_date']);
		if(isset($postData['ni_number'])) 		$data->set_ni_number( $postData['ni_number']);
		if(isset($postData['passport_number'])) $data->set_passport_number( $postData['passport_number']);
		if(isset($postData['postcode'])) 		$data->set_postcode( $postData['postcode']);
		$lastid = $contactManager->save($data);
		
		# $contactManager->fetchByAgencyUid($pageSession->agentUniqueId);
		$return['contactHtml'] = $this->view->partialLoop("partials/$partialFile",$contactManager->fetchByAgencyUid($pageSession->agentUniqueId));
		echo Zend_Json::encode($return);
	}
	
	public function validatePageAction() {

		$return = array();

		$pageSession = new Zend_Session_Namespace('letting_agents_application');

        $postData = $this->getRequest()->getParams();
		$page = $postData['page'];

		switch($page) {
			case '1':
				$ajaxForm = new LettingAgents_Form_Step1();
				break;
				
			case '2':
				$pageSession = new Zend_Session_Namespace('letting_agents_application');
				$agent = new LettingAgents_Manager_AgentApplication();
				$agentData = new LettingAgents_Object_AgentApplication();
				$agentData = $agent->fetchByUid($pageSession->agentUniqueId);
		
				/*
				 *  What we need to present as stage 2 is dependant on the selection of organisation type
				 *  step 2 will need to load different subforms
				 * so pass organisation_type to the form   
				 */
				$organisation_type = $agentData->get_organisation_type();
				$ajaxForm = new LettingAgents_Form_Step2(array(
					'organisationId' => $organisation_type
					));
					
				switch ($organisation_type ){
					case LettingAgents_Object_CompanyTypes::LimitedCompany:
						break;
					case LettingAgents_Object_CompanyTypes::LimitedLiabilityPartnership:
						break;
					case LettingAgents_Object_CompanyTypes::Partnership:
						break;
					case LettingAgents_Object_CompanyTypes::SoleTrader:
						break;						
					
				}				
				break;
					
			case '3':
				$ajaxForm = new LettingAgents_Form_Step3();
				break;		

			case '4':
				$ajaxForm = new LettingAgents_Form_Step4();
				break;				
		}
		 $valid = $ajaxForm->isValid($postData);

		if (!$valid) {
			$errorMessages = $ajaxForm->getMessagesFlattened();
			// We need to strip out some complex messages that the end user won't care about
			if (isset($errorMessages['email_address'])) {
				if (isset($errorMessages['email_address']['hostnameUnknownTld'])) {
					unset($errorMessages['email_address']['hostnameUnknownTld']);
				};
				if (isset($errorMessages['email_address']['hostnameLocalNameNotAllowed'])) {
					unset($errorMessages['email_address']['hostnameLocalNameNotAllowed']);
				};
			}

			$return['errorJs'] = $errorMessages;
			$return['errorCount'] = count($errorMessages);
			// don't push errors into the parent page
			$return['errorHtml'] =  $this->view->partial('partials/error-list.phtml', array('errors' => $errorMessages));
			
		} else {
			$return['errorJs'] = '';
			$return['errorCount'] = '';
			$return['errorHtml'] = '';
		}

		echo Zend_Json::encode($return);
	}
	
}
?>