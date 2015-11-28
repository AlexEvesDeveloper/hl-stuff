<?php

class TenantsInsuranceQuoteB_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initActionHelperBrokers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/tenants-insurance-quote-b/controllers/helpers', 'TenantsInsuranceQuoteB_Controller_Action_Helper_');
    }
}

