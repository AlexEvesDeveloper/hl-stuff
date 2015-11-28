<?php

class Account_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initActionHelperBrokers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/account/controllers/helpers', 'Account_Controller_Action_Helper_');
    }
	
}

