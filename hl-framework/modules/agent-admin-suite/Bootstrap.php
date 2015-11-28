<?php

class AgentAdminSuite_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initActionHelperBrokers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/agent-admin-suite/controllers/helpers', 'AgentAdminSuite_Controller_Action_Helper_');
    }
}

