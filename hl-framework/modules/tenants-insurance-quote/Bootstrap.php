<?php

class TenantsInsuranceQuote_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initActionHelperBrokers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/tenants-insurance-quote/controllers/helpers', 'TenantsInsuranceQuote_Controller_Action_Helper_');
    }
}

