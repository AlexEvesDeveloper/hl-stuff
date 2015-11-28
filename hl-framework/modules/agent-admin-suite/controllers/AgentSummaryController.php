<?php

class AgentAdminSuite_AgentSummaryController extends Zend_Controller_Action
{

    public function indexAction() {
        $agentSchemeNumber = $this->getRequest()->getParam('asn');
        $agents = new Manager_Core_Agent();

        $agent = $agents->getAgent($agentSchemeNumber);

        $this->view->tradingName = $agent->name;
        $this->view->schemeNumber = $agent->agentSchemeNumber;
        $this->view->commissionRate = $agent->commissionRate*100;
        $this->view->newBusinessCommissionRate = $agent->newBusinessCommissionRate*100;

        Zend_Debug::dump($agent->contact);
        foreach($agent->contact as $contactDetails) {
            if ($contactDetails->category == Model_Core_Agent_ContactMapCategory::OFFICE)
            {
            // @todo Commented out to allow tests to run - repair
            /*
                $this->view->contactAddress1 = '';
                if ($contactDetails->flatNumber) $this->view->contactAddressLine1 .= $contactDetails->flatNumber . ' ';
                if ($contactDetails->houseNumber) $this->view->contactAddressLine1 .= $contactDetails->houseNumber . ' ';
                if ($contactDetails->houseName) $this->view->contactAddressLine1 .= $contactDetails->houseName . ' ';
                if ($contactDetails->addressLine1)
                {
                    $this->view->contactAddressLine1 .= $contactDetails->addressLine1 . ' ';
                    $this->view->contactAddressLine2 = $contactDetails->addressLine2;
                } else {
                    $this->view->contactAddressLine1 .= $contactDetails->addressLine2 . ' ';
                }
                */

                $this->view->contactTown = $contactDetails->address->town;
                $this->view->contactPostcode = $contactDetails->address->postCode;
            }
        }
        Zend_Debug::dump($agent);
    }

}
?>