<?php

/**
 * Bootstrap for the REST module.
 */
class Rest_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /**
     * Initialise the route used by the REST module.
     *
     * @return void
     */
    protected function _initRoute()
    {
/*
        $bootstrap = $this->getApplication();
        $bootstrap->bootstrap('frontcontroller');
        $front = $bootstrap->getResource('frontcontroller');

        // TODO: This needs to go in a routing .ini file using
        //   "routes.toplevel_domain.chains.rest.type = Zend_Rest_Route", but as
        //   PB and I have found out this doesn't work, at least in this version
        //   of ZF
        // Set up REST routing - solution from http://mwop.net/blog/228-Building-RESTful-Services-with-Zend-Framework
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        // Specifying the "rest" module only as RESTful:
        $restRoute = new Zend_Rest_Route(
            $front,
            array(),
            array(
                'rest',
            )
        );
        $router->addRoute('rest', $restRoute);
*/
    }
}