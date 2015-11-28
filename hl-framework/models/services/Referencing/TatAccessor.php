<?php

/**
 * Class for remotely triggering the TAT notification
 */
class Service_Referencing_TatAccessor {
	
	/**
	 * Sends the TAT invitation email to the reference subject.
	 *
	 * @param string $enquiryId
	 * Identifies the reference. Can be either the IRN or the ERN.
	 *
	 * @return boolean
	 * Returns true on success, false otherwise.
	 */
	public function sendTatInvitation($enquiryId) {
	   
		$returnVal = false;
	
		$tatManager = new Manager_Referencing_Tat($enquiryId);
		
		if($tatManager->isTatApplicable()) {
			 
			$tatMailManager = new Manager_Referencing_TatMail($tatManager->_reference);
			if(!$tatMailManager->getIsTatInvitationSent()) {
			
				if($tatMailManager->sendTatInvitation()) {
					
					$returnVal = true;
				}
			}
		}
		
		return $returnVal;
	}
	
	
	/**
	 * Inserts a user notice into the datasource.
	 * 
	 * The user notice is displayed to the user after payment has been made for
	 * a referencing service.
	 * 
	 * @param integer $noticeId
	 * The unique user notice identifier.
	 * 
	 * @param integer $referenceId
	 * The unique Reference (Enquiry) identifer.
	 * 
	 * @return boolean
	 * True on success, false otherwise.
	 */
	public function insertNotice($noticeId, $referenceId) {
	
		$noticeManager = new Manager_Referencing_UserNotices();
        return $noticeManager->insertNoticeMap($noticeId, $referenceId);
	}
	
	
	/**
	 * Resends the TAT invitation email to the reference subject.
	 *
	 * @param string $enquiryId
	 * Identifies the reference. Can be either the IRN or the ERN.
	 *
	 * @return boolean
	 * Returns true on success, false otherwise.
	 */
	public function resendTatInvitation($enquiryId) {
		
		$returnVal = false;
	
		$tatManager = new Manager_Referencing_Tat($enquiryId);
		if($tatManager->isTatApplicable()) {
			
			//Does not check if initial invitation has been sent.
			$tatMailManager = new Manager_Referencing_TatMail($tatManager->_reference);
			if($tatMailManager->sendTatInvitation()) {
				
				$returnVal = true;
			}
		}
		
		return $returnVal;
	}
	
	
	/**
	 * Records a TAT notification email in the datasource.
	 *
	 * Will not record a TAT notification if a TAT is not applicable for
	 * the reference.
	 *
	 * @param string $enquiryId
	 * Identifies the reference. Can be either the IRN or the ERN.
	 *
	 * @param string $content
	 * The email content that was sent to the reference subject.
	 *
	 * @return void
	 */
	public function logTatNotification($enquiryId, $content) {
		
		//Ensure no logging of non-applicable references.
		$tatManager = new Manager_Referencing_Tat($enquiryId);
		if($tatManager->isTatApplicable()) {
								
			$tatNotification = new Model_Referencing_TatNotification();
			$tatNotification->enquiryId = $tatManager->_reference->externalId;
			$tatNotification->sendDate = Zend_Date::now();
			$tatNotification->content = $content;
			
			$tatNotifications = new Datasource_Referencing_TatNotification();
			$tatNotifications->insertNotification($tatNotification);
		}
	}
	
	
	/**
	 * Returns whether the agent is opted in or out of the TAT service.
	 *
	 * @param string $agentSchemeNumber
	 * The unique agent identifier.
	 *
	 * @return string
	 * Returns a string corresponding to one of the consts exposed by
	 * the Model_Referencing_TatOptedStates class.
	 */
	public function getAgentOptedStatus($agentSchemeNumber) {
		
		$datasource = new Datasource_Referencing_TatOptedStatus();
		return $datasource->getOptedStatus($agentSchemeNumber);
	}
	
	
	/**
	 * Sets whether the agent is opted in or out of the TAT service.
	 *
	 * @param string $agentSchemeNumber
	 * The unique agent identifier.
	 *
	 * @param string $optedStatus
	 * The opted status to set. Must correspond to one of the consts
	 * exposed by the Model_Referencing_TatOptedStates class.
	 *
	 * @return void
	 */
	public function setAgentOptedStatus($agentSchemeNumber, $optedStatus) {
		
		$datasource = new Datasource_Referencing_TatOptedStatus();
		if($optedStatus == Model_Referencing_TatOptedStates::OPTED_OUT) {
			
			$datasource->setOptedOut($agentSchemeNumber);
		}
		else {
			
			$datasource->setOptedIn($agentSchemeNumber);
		}
	}
}

?>