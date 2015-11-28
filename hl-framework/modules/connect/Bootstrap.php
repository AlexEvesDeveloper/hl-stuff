<?php

class Connect_Bootstrap extends Zend_Application_Module_Bootstrap
{    
    protected function _initActionHelperBrokers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/connect/controllers/helpers', 'Connect_Controller_Action_Helper_');
    }
}

