<?php

/**
 * Managers for sending mails.
 */
class Manager_Connect_CompanyApp {    
    
    public function emailCompany($toAddress,$content) {
    	
        $emailer = new Application_Core_Mail();
        $emailer->setTo($toAddress,$toAddress);
        $emailer->setFrom('noreply@homelet.com','noreply@homelet.com');
        $emailer->setSubject('Company Application');
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
    public function emailCompanyWithAttachments($toAddress, $content,$filename) {
        
        $emailer = new Application_Core_Mail();
        $emailer->setTo($toAddress,$toAddress);
        $emailer->setFrom('noreply@homelet.com','noreply@homelet.com');
        $emailer->setSubject('Company Application');
        $emailer->setBodyText($content);
        // Attach all files from detailAttachments
        	$emailer->addAttachment($filename, substr($filename, strrpos($filename, '/') + 1));
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
}

?>