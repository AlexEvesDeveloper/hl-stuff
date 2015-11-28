<?php

/**
 * Tenancy application tracker class providing TAT services.
 */
class Manager_Referencing_TatMail {
	
	protected $_reference;
	
	/**
     * Indicates if the initial TAT invitation to the reference subject has been sent.
     *
     * Can be used by calling code to prevent multiple invites being sent.
     *
     * @param Model_Referencing_Reference $reference
     * Encapsulates details of the reference.
     *
     * @return boolean
     * True if the TAT invitation has been sent, false otherwise.
     */
	public function __construct($reference){
		
		if(is_null($reference)) {
			
			throw new Zend_Exception("Reference is null");
		}

		$this->_reference = $reference;
	}
	
    public function getIsTatInvitationSent() {
                        
        $tatInvitationDatasource = new Datasource_Referencing_TatInvitation();
        return $tatInvitationDatasource->getIsTatInvitationSent($this->_reference->externalId);
    }

    
    /**
     * Sends an initial notification to the reference subject (tenant or guarantor).
     * 
     * The initital notification sent to the reference subject advises them that their
     * reference is on the system and they can track it.
     *
     * @param mixed $enquiryId
     * The unique Enquiry identifier (internal or external). May be integer or string.
     *
     * @return boolean
     * Returns true on succesful sending of the notification, false otherwise.
     */
    public function sendTatInvitation() {
        
        //Get the general parameters.
        $params = Zend_Registry::get('params');
        $metaData = array();
        
        $emailFromName = $params->tat->email->fromname;
        $emailFrom = $params->tat->email->from;
        $emailSubject = $params->tat->email->subject;
        $metaData['tatUrl'] = $params->url->tenantsTrackApplicationFromEmail;
        $metaData['homeLetUrl'] = $params->homelet->domain . '/';
        
        // Ensure enquiry ID given out is an internal one
        $metaData['tenantIrn'] = $this->_reference->internalId;
        
                        
        //Set the destination email address.
        $referenceSubjectContact = $this->_reference->referenceSubject->contactDetails;
        if(!empty($referenceSubjectContact->email1)) {
            
            $emailTo = $referenceSubjectContact->email1;
        }
        else if(!empty($referenceSubjectContact->email2)) {
            
            $emailTo = $referenceSubjectContact->email2;
        }
        else {
            
        	return false;
        }
        
        
        //Set the reference subject name into the email body.
        $referenceSubjectName = $this->_reference->referenceSubject->name;
        $referenceSubjectNameString = $referenceSubjectName->firstName . ' ' . $referenceSubjectName->lastName;

        $metaData['tenantName'] = $referenceSubjectNameString;
        
        
        //Set the property address into the email body.
        $propertyAddress = $this->_reference->propertyLease->address;
        $propertyAddressString = '';
        $propertyAddressString .= ($propertyAddress->addressLine1 != '') ? "{$propertyAddress->addressLine1}\n" : '';
        $propertyAddressString .= ($propertyAddress->addressLine2 != '') ? "{$propertyAddress->addressLine2}\n" : '';
        $propertyAddressString .= ($propertyAddress->town != '') ? "{$propertyAddress->town}\n" : '';
        $propertyAddressString .= ($propertyAddress->postCode != '') ? "{$propertyAddress->postCode}\n" : '';

        $metaData['propertyAddress'] = nl2br(substr($propertyAddressString, 0, -1));
        $metaData['propertyAddressTxt'] = $propertyAddressString;
        
        
        //Put the letting agent name and ASN into the email body.
		if($this->_reference->customer->customerType == Model_Referencing_CustomerTypes::LANDLORD) {

			$agentId = $params->homelet->defaultAgent;
		}
		else {
		
			$agentId = $this->_reference->customer->customerId;
		}
		
		$agentDatasource = new Datasource_Core_Agents();
		$lettingAgent = $agentDatasource->getAgent($agentId);
        $lettingAgentNameString = $lettingAgent->name;

        $metaData['lettingAgentName'] = $lettingAgentNameString;
        $metaData['lettingAgentAsn'] = $lettingAgent->agentSchemeNumber;
        
        
        //Prepare the email.
        $emailer = new Application_Core_Mail();
        $emailer->setTo($emailTo, $emailFromName);
        $emailer->setFrom($emailFrom, $emailFromName);
        $emailer->setSubject($emailSubject);
        $emailer->applyTemplate('tenants_tatInvitation', $metaData, false, '/email-branding/homelet/generic-with-signature-footer.phtml');
        $emailer->applyTextTemplate('tenants_tatInvitationTxt', $metaData, false, '/email-branding/homelet/generic-with-signature-footer-txt.phtml');
        
        
        //Send, update and return
        $success = $emailer->send();
        if($success) {
            
            //Update the notification log.
            $tatInvitationDatasource = new Datasource_Referencing_TatInvitation();
            $tatInvitationDatasource->insertInvitation($this->_reference->externalId);
            $returnVal = true;
        }
        else {
            
            $returnVal = false;
        }
        
        return $returnVal;
    }
    
    
    /**
     * Sends an email to 'The Assessor' who responsible for processing details provided by the reference subject.
     *
     * Does not provide support for attachments, so should be used where the reference
     * subject (tenant or guarantor) has not provided one.
     * 
     * @param mixed $enquiryId
     * The unique Enquiry identifier (internal or external). May be integer or string.
     *
     * @param string $content
     * The content of the message sent by the reference subject.
     *
     * @return boolean
     * Returns true on successful send, false otherwise.
     */
    public function notifyAssessor($content) {
    	
        $emailer = new Application_Core_Mail();
        $emailer->setTo('applicant.enquiries@homelet.co.uk', 'Applicant Enquiries');
        $emailer->setFrom('applicant.enquiries@homelet.co.uk', 'Tenant Tracker');
        $emailer->setSubject('Tenant Application Tracker');
        $emailer->setBodyText($content);
        //Send and return
        $success = $emailer->send();
        if($success) {
              $returnVal = true;
        }
        else {          
            $returnVal = false;
        }
        return $returnVal;
    }
    
    
    /**
     * Sends an email to 'The Assessor' who responsible for processing details provided by the reference subject.
     *
     * Provides support for attachments, automatically including any uploaded by the
     * reference subject.
     * 
     * @param mixed $enquiryId
     * The unique Enquiry identifier (internal or external). May be integer or string.
     *
     * @param string $content
     * The content of the message sent by the reference subject.
     *
     * @return boolean
     * Returns true on successful send, false otherwise.
     */
    public function notifyAssessorWithAttachments($content) {
        
        $emailer = new Application_Core_Mail();
        $emailer->setTo('applicant.enquiries@homelet.co.uk', 'Applicant Enquiries');
        $emailer->setFrom('applicant.enquiries@homelet.co.uk', 'Tenant Tracker');
        $emailer->setSubject('Tenant Application Tracker');
        $emailer->setBodyText($content);
        // Attach all files from detailAttachments
        foreach (array_keys($this->detailAttachments($this->_reference->internalId)) as $filename) {
        	$emailer->addAttachment($filename, substr($filename, strrpos($filename, '/') + 1));
        }
        // Send and set returnval
        $success = $emailer->send();
        if($success) {
              $returnVal = true;
        }
        else {          
            $returnVal = false;
        }
        return $returnVal;
    }
    
    
    /**
     * Uploads all attachments provided by the reference subject.
     *
     * @param mixed $enquiryId
     * The unique Enquiry identifier (internal or external). May be integer or string.
     *
     * @return boolean
     * Returns true on successful upload, false otherwise.
     */
    public function addAttachments() {
        
        $params = Zend_Registry::get('params');
        
        
        //Create a temporary folder for this Enquiry, if one has not already been
        //created. The folder name will incorporate the IRN.
        $temporaryAttachmentFolder = "{$params->tat->attachmentUploadPath}{$this->_reference->internalId}";
        if(!file_exists($temporaryAttachmentFolder)) {
            
            // Parameterised octal number's type not being correctly read in from params
            mkdir($temporaryAttachmentFolder, octdec('0' . $params->tat->attachmentFileMode));
        }
        
        //Upload the attachment(s).
        $upload = new Zend_File_Transfer_Adapter_Http();
        $upload->setDestination($temporaryAttachmentFolder);
        $upload->addValidator('Extension', false, $params->tat->attachmentAllowedTypes);
        $upload->addValidator('Count', false, $params->tat->attachmentMaxNumberAllowed);
        $upload->addValidator('Size', false, $params->tat->attachmentMaxUploadSize);
        
        
        //Call isUploaded(), which will return true if the user has uploaded an attachment.
        $returnVal = false;
        if($upload->isUploaded()) {
            
            if ($upload->receive()) {
                
                //The attachment has been uploaded succesfully.
                $returnVal = true;
            }
        }
        return $returnVal;
    }
    
    
    /**
     * Gives basic details of all attachments provided by the reference subject.
     *
     * @param mixed $enquiryId The unique Enquiry identifier (internal or external). May be integer or string.
     *
     * @return array List of filename => filesize (in bytes) pairs
     */
    public function detailAttachments() {
        
        $returnVal = array();
        $params = Zend_Registry::get('params');
        
        //Locate the temporary folder for this Enquiry, if one exists. Note that
        //the temporary folder incorporates the IRN in its name.
        $temporaryAttachmentFolder = "{$params->tat->attachmentUploadPath}{$this->_reference->internalId}";
        if (file_exists($temporaryAttachmentFolder)) {
			
            // Read file names and sizes
            if ($handle = opendir($temporaryAttachmentFolder)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        $returnVal["{$temporaryAttachmentFolder}/{$file}"] = filesize("{$temporaryAttachmentFolder}/{$file}");
                    }
                }
                closedir($handle);
            }
        }
        
        return $returnVal;
    }
    
    
    /**
     * Deletes all attachments previously provided by the reference subject, by deleting
     * the relevant temporary attachment folder.
     *
     * @param mixed $enquiryId
     * The unique Enquiry identifier (internal or external). May be integer or string.
     *
     * @return boolean
     * Returns true on successful deletion, false otherwise.
     */
    public function deleteAttachments() {
        
        $params = Zend_Registry::get('params');
        
        
        //Locate the temporary folder for this Enquiry, if one exists. Note that
        //the temporary folder incorporates the IRN in its name.
       
        $temporaryAttachmentFolder = "{$params->tat->attachmentUploadPath}{$this->_reference->internalId}";
        if (file_exists($temporaryAttachmentFolder)) {
            // Empty contents of directory
            if ($handle = opendir($temporaryAttachmentFolder)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        unlink("{$temporaryAttachmentFolder}/{$file}");
                    }
                }
                closedir($handle);
            }
            
            // Delete directory
            $returnVal = rmdir($temporaryAttachmentFolder);
        }
        else {
        
            //No files to delete. As there are no attachments for the current TAT on
            //the server, return true to put the client code at ease.
            $returnVal = true;
        }
        
        return $returnVal;
    }
    
    
    /**
     * Sends an email to 'The Campaign Team' who are responsible for calling back the reference subject.
     *
     * Does not provide support for attachments, so should be used where the reference
     * subject (tenant or guarantor) has not provided one.
     * 
     * @param mixed $enquiryId
     * The unique Enquiry identifier (internal or external). May be integer or string.
     *
     * @param string $content
     * The content of the message sent by the reference subject.
     *
     * @return boolean
     * Returns true on successful send, false otherwise.
     */
    public function notifyCampaignTeam($content) {
    	
        $emailer = new Application_Core_Mail();
        $emailer->setTo('campaign.team@homelet.co.uk', 'Campaign Team');
        $emailer->setFrom('campaign.team@homelet.co.uk', 'Tenant Tracker');
        $emailer->setSubject('Tenant Tracker Quote');
        $emailer->setBodyText($content);
        //Send and return
        $success = $emailer->send();
        if($success) {
              $returnVal = true;
        }
        else {          
            $returnVal = false;
        }
        return $returnVal;
    }
	
	
	
}

?>
