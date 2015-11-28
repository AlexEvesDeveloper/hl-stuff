<?php

class LettingAgents_Bootstrap extends Zend_Application_Module_Bootstrap
{
	
	protected function _initAppAutoload()
	{
	    $autoloader = new Zend_Application_Module_Autoloader(array(
	        'namespace' => 'LettingAgents',
	        'basePath' => dirname(__FILE__),
	    	'resourceTypes' => array(
		        'manager' => array(
			            'path'      => 'models/managers/',
			            'namespace' => 'Manager',
			        ),
				'datasource' => array(
			            'path'      => 'models/datasources/',
			            'namespace' => 'Datasource',
			        ),	
			    'object' => array(
			            'path'      => 'models/objects/',
			            'namespace' => 'Object',
			        ),
		     ),
	    ));
	    return $autoloader;
	}
    
    protected function _initActionHelperBrokers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/letting-agents/controllers/helpers', 'LettingAgents_Controller_Action_Helper_');
    }
	
}

