<?php

class CmsAdmin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initActionHelperBrokers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/cms-admin/controllers/helpers', 'CmsAdmin_Controller_Action_Helper_');
    }
}

