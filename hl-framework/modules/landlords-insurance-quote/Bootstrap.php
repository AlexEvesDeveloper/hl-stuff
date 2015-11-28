<?php

class LandlordsInsuranceQuote_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initActionHelperBrokers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/landlords-insurance-quote/controllers/helpers', 'LandlordsInsuranceQuote_Controller_Action_Helper_');
    }
}

