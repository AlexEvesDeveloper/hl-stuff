<?php

class Cms_Bootstrap extends Zend_Application_Module_Bootstrap
{
	protected function _initActionHelperBrokers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/cms/controllers/helpers', 'Cms_Controller_Action_Helper_');
    }
}

