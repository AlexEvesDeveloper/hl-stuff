<?php
/*
 * 
 * <<Applicant Name>>, <<First line of Property to Let address>>, <<Second Line of Address>>, <<'View Report' text link>>'
 *
 */
class Connect_View_Helper_NotificationHistoryPanel extends Zend_View_Helper_Abstract {

    public function NotificationHistoryPanel($agentSchemeNumber) {

    	$params = Zend_Registry::get('params');
    	$baseReferencingUrl = $params->connect->baseUrl->referencing;
    	// Instantiate security manager for generating MAC
    	$securityManager = new Application_Core_Security($params->connect->ref->security->securityString->agent);
    	$agentId = $this->view->agentId;
    	$macToken = $securityManager->generate(
    			array(
    					$agentSchemeNumber,
    					$agentId
    			)
    	);   
      
   //	Zend_Debug::dump($this->view->agentId);die();
	$rs = new Datasource_Referencing_NotificationHistory();
	$dataToDisplay = $rs->getHistoryByASN($agentSchemeNumber);
	$enquiry = new Datasource_ReferencingLegacy_Enquiry();
	//Zend_Debug::dump($enquiry);
	$partialArray = array();
	$x = 0;
	foreach ($dataToDisplay as $d){
		//Zend_Debug::dump($d);
		$refno = $d['refno'];
		$ds = $enquiry->getEnquiry($refno);

		$retrieveReportString = "/reports/view-report-pdf?refno=$refno&repType=&contentDisposition=attachment";
		$partialArray[$x]['refno'] = $refno;
        $partialArray[$x]['line'] = '';
		$partialArray[$x]['viewReportURL'] = $retrieveReportString;

        $name = trim("{$ds->referenceSubject->name->firstName} {$ds->referenceSubject->name->lastName}");
        if ($name != '') {
            $partialArray[$x]['line'] .= "{$name}, ";
        }
		if(isset($ds->propertyLease->address->addressLine1)) {
			$partialArray[$x]['line'] .= $ds->propertyLease->address->addressLine1 . ', ';
		}
		/*if(isset($ds->propertyLease->address->addressLine2) && $ds->propertyLease->address->addressLine2 != ""){
			$partialArray[$x]['line'] .= $ds->propertyLease->address->addressLine2 . ', ';
		}
		if(isset($ds->propertyLease->address->postCode)){
			$partialArray[$x]['line'] .= $ds->propertyLease->address->postCode . ', ';
		}*/
        $partialArray[$x]['line'] = preg_replace('/, $/', ' ', $partialArray[$x]['line']);
        //$partialArray[$x]['line'] .= "<em>(Reference number: {$refno})</em>";
		$x++;	
	}
	
	//Zend_Debug::dump($partialArray);die();
         return array($this->view->partialLoop('partials/notificationhistorypanel.phtml',$partialArray),$x);
    }

}

//123702119