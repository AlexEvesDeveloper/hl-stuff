<?php

/**
 * Business rules class which provides underwriting notification services.
 */
class Manager_Core_Notification {
    
    public $_reason="";
    
    /**
	 * Sends a notification email to underwriting with referral details.
	 *
	 * This method provides a convenient way of notifying underwriting when
	 * a customer has failed the underwriting critera during a quote, MTA or
	 * renewal.
	 *
	 * @param string $policyNumber
	 * The quote/policynumber to include in the email.
	 *
	 * @param string $refNo
	 * The customer reference number to include in the email.
	 *
	 * @return boolean
	 * Returns true if the email was successfully sent, false otherwise.
	 */
    public function notifyUnderwriting($policyNumber, $refNo) {
        
        //Get the necessary parameters.
        $params = Zend_Registry::get('params');
        $emailTo = $params->uw->re->to;
        $emailFromName = $params->uw->re->fromname;
        $emailFrom = $params->uw->re->from;
        $emailSubject = $params->uw->re->subject;
        if ('Other' === $this->_reason) {
            $emailBody = $params->uw->re->other_body;
        } else {
            $emailBody = $params->uw->re->body;
        }
        
        
        //Prepare the email.
        $emailer = new Application_Core_Mail();
        $emailer->setTo($emailTo, $emailFromName);
        $emailer->setFrom($emailFrom, $emailFromName);
        
        $emailSubject = preg_replace("/\[--POLNO--\]/", $policyNumber, $emailSubject);
        $emailer->setSubject($emailSubject);
        
        $emailBody = preg_replace("/\[--POLNO--\]/", $policyNumber, $emailBody);
        $emailBody = preg_replace("/\[--REFNO--\]/", $refNo, $emailBody);
        $emailer->setBodyText($emailBody);
        
        $success = $emailer->send();
        if($success) {
            
            //Update the notification log.
            $underwritingEmailLog = new Datasource_Core_UnderwritingEmailLog();
            $underwritingEmailLog->insertNotification(new Zend_Date(), $policyNumber, $emailBody);
        }
        return $success;
    }
}

?>
