<?php

class Cms_View_Helper_ShowEmailHistory extends Zend_View_Helper_Abstract {

    /**
     * Helper function for generating sent email history HTML fragment
     *
     * @return string
     */
    public function showEmailHistory() {
        
        // Fetch e-mails
        $pageSession = new Zend_Session_Namespace('tenants_referencing_tracker');

        if (isset($pageSession->enquiryId)) {

            $tatManager = new Manager_Referencing_Tat($pageSession->enquiryId);
            $tat = $tatManager->getTat();
            
            $tatNotificationArray = $tat->tatNotifications;
            $outputData = array();
            if(!empty($tatNotificationArray)) {
                
                foreach($tatNotificationArray as $currentNotification) {
                    $outputData[] = array('date' => $currentNotification->sendDate->toString(), 'content' => $currentNotification->content);
                }
            }
            
            if (count($outputData) > 0) {
                
                // Return partial view HTML for when e-mails have been sent
                return $this->view->partial('tenants-referencing-tracker/partials/show-email-history.phtml', array('emails' => $outputData));
            }
            else {
                
                // Return partial view HTML for when no e-mails have been sent
                return $this->view->partial('tenants-referencing-tracker/partials/show-email-history-empty.phtml');
            }
        }
    }

}
