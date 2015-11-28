<?php

class Cms_View_Helper_ViewBreakdown extends Zend_View_Helper_Abstract {

    private $_customerReferenceNumber;
    private $_policyNumber;

    /**
     * Helper function for generating view breakdown HTML fragment
     *
     * @param bool $showAdminFees Optional switch to include admin fees, defaults to false
     *
     * @return string
     */
    public function viewBreakdown($includeAdminFees = true) {

        // Get customer ref no and policy number
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        $this->_customerReferenceNumber = $pageSession->CustomerRefNo;
        $this->_policyNumber = $pageSession->PolicyNumber;

        // Get premium values for quote
        $quoteManager = new Manager_Insurance_TenantsContentsPlus_Quote(null,null,$this->_policyNumber);
        $premiums = $quoteManager->calculatePremiums();
        $this->view->premiums = $premiums;

        // Get cover amounts
        $this->view->contentsCoverAmount = $quoteManager->getCoverAmount(Manager_Insurance_TenantsContentsPlus_Quote::CONTENTS);
        $this->view->bicyclesCoverAmount = $quoteManager->getCoverAmount(Manager_Insurance_TenantsContentsPlus_Quote::PEDALCYCLES);
        $this->view->specifiedPossessionsCoverAmount = $quoteManager->getCoverAmount(Manager_Insurance_TenantsContentsPlus_Quote::SPECIFIEDPOSSESSIONS);
        $this->view->unspecifiedPossessionsCoverAmount = $quoteManager->getCoverAmount(Manager_Insurance_TenantsContentsPlus_Quote::UNSPECIFIEDPOSSESSIONS);

        // Get any sharer occupation details
        $sharersManager = new Manager_Insurance_TenantsContentsPlus_Sharers();
        $sharerData = $sharersManager->getSharers($this->_policyNumber);
        $this->view->sharer1 = (!is_null($sharerData)) ? $sharerData->getSharerOccupation(1) : '';
        $this->view->sharer2 = (!is_null($sharerData)) ? $sharerData->getSharerOccupation(2) : '';

        // Get specified possessions info
        $possession = new Datasource_Insurance_Policy_SpecPossessions($this->_policyNumber);
        $this->view->possessions = $possession->listPossessions();

        // Get bicycle info
        $bike = new Datasource_Insurance_Policy_Cycles($this->_customerReferenceNumber, $this->_policyNumber);
        $this->view->bicycles = $bike->listBikes();

        // Get Current IPT percentage
        $quote = $quoteManager->getQuoteObject();
		$postcode = $quote->propertyPostcode;
		
		$taxDatasource = new Datasource_Core_Tax();
		$tax = $taxDatasource->getTaxbyTypeAndPostcode('ipt', $postcode);
		$ipt = 1 + ($tax['rate']);

        // Get admin fees, if required
        if ($includeAdminFees) {
            $fees = $quoteManager->getFees();
            $this->view->feeMonthly = $fees->tenantspMonthlyFee;
            $this->view->feeAnnual = 0; // TODO: parameterise?  Legacy DB and fee object currently don't handle annual fees
        } else {
            $this->view->feeMonthly = 0;
            $this->view->feeAnnual = 0;
        }
        $this->view->includeAdminFees = $includeAdminFees;

        // Return partial view HTML
        return $this->view->partial('partials/view-breakdown.phtml', $this->view);
    }

}
