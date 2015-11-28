<?php

class LandlordsReferencing_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initActionHelperBrokers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/landlords-referencing/controllers/helpers', 'LandordsReferencing_Controller_Action_Helper_');
    }
}

