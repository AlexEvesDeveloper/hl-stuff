<?php

/**
 * MUNT-Ugly Nastiness Translator.
 *
 * Insurance Munt Manager class providing conversion services between the new
 * datasources and the legacy, munting datasources.
 */
class Manager_Insurance_Munt {

    public function searchLegacyPolicies($agentschemeno, $criteria = array(), $sort = '') {

        // Instantiate legacy insurance search datasource
        $insuranceLegacySearchDatasource = new Datasource_Insurance_Legacy_PolicySearch();

        // Run search directly through datasource
        return $insuranceLegacySearchDatasource->getPolicies($agentschemeno, $criteria, $sort);
    }

    public function getCustomers($agentschemeno, $criteria = array()) {

        // Instantiate legacy insurance search datasource
        $insuranceLegacySearchDatasource = new Datasource_Insurance_Legacy_CustomerSearch();

        // Run search directly through datasource
        return $insuranceLegacySearchDatasource->getCustomers($agentschemeno, $criteria);
    }
    
    public function getPolicy($polno) {
     // Instantiate legacy insurance search datasource
        $insuranceLegacySearchDatasource = new Datasource_Insurance_Legacy_PolicySearch();

        // Run search directly through datasource
        return $insuranceLegacySearchDatasource->getPolicy($polno);
    }
    
    public function getCover($polno) {
    	// Instantiate legacy insurance search datasource
        $insuranceLegacySearchDatasource = new Datasource_Insurance_Legacy_CoverSearch();

        // Run search directly through datasource
        return $insuranceLegacySearchDatasource->searchCovers($polno);
    	
    }
    
    public function getCustomer($refno) {
        $insLegacyCustomerManager = new Manager_Core_Customer();
        $customer = $insLegacyCustomerManager->getCustomer(Model_Core_Customer::LEGACY_IDENTIFIER,$refno);
    	return $customer;
    }
    
}