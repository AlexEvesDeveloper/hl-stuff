<?php

class Connect_View_Helper_RentGuaranteeClaimsRentalPayments extends Zend_View_Helper_Abstract
{

    /**
     * Helper function for generating rental payments summary HTML fragment
     *
     * @return string
     */
    public function rentGuaranteeClaimsRentalPayments() {

        $pageSession = new Zend_Session_Namespace('online_claims');
        $manager = new Manager_Insurance_RentGuaranteeClaim_RentalPayment();

        $paymentData = $manager->getRentalPayments(
            $pageSession->ClaimReferenceNumber
        );

        return $this->view->partial('partials/rent-guarantee-payment-list.phtml', array('paymentData' => $paymentData['data']));
    }
}