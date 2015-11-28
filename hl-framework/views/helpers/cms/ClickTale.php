<?php

/**
 * Class Cms_View_Helper_ClickTale
 */
class Cms_View_Helper_ClickTale extends Zend_View_Helper_Abstract
{
    /**
     * @var array Array of modules where ClickTale is active.
     * @todo Not used while Portfolio can't be selected by module, $this->allowedPaths is used instead.
     */
    //private $activeModules;

    /**
     * @var string The currently executing module's name.
     * @todo Not used while Portfolio can't be selected by module.
     */
    //private $module;

    /**
     * @var array Array of allowed URL paths for ClickTale tracking to be active.
     * @todo Alternative method in place of $this->activeModules - to be deprecated.
     */
    private $allowedPaths;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Modules to track configuration
        // todo: Not used while Portfolio can't be selected by module.
        //$this->activeModules = array(
        //    'landlords-insurance-quote',
        //    'landlords-referencing',
        //    'tenants-insurance-quote',
        //    'default', // for Landlords' Portfolio
        //);

        // URL paths to track
        // todo: Used only while Portfolio can't be selected by module.
        $this->allowedPaths = array(
            '^/landlords/insurance-quote/',
            '^/landlords/referencing/',
            '^/portfolio/insurance-quote/',
            '^/tenants/insurance-quote/',
        );
    }

    /**
     * Check to see whether ClickTale may be used in the current module context.
     *
     * @return bool
     */
    private function allow()
    {
        // Look up to see if there's a match with module list
        // todo: Not used while Portfolio can't be selected by module.
        //if (isset($this->activeModules[$this->module])) {
        //    return true;
        //}

        // Get request URI
        // todo: Used only while Portfolio can't be selected by module.
        $req = $_SERVER['REQUEST_URI'];

        // Look for matches between request and allowed paths
        // todo: Used only while Portfolio can't be selected by module.
        foreach ($this->allowedPaths as $pathPattern) {
            if (preg_match("|{$pathPattern}|", $req)) {
                return true;
            }
        }

        return false;
    }

    /**
     * ClickTale view helper to insert ClickTale tracking code into a layout.
     *
     * @param string $location Set to "top" or "bottom" to server the top or bottom script.
     * @return null|string
     */
    public function clickTale($location = 'bottom'/*, Zend_Controller_Request_Http $request*/)
    {
        // Get module name from request
        // todo: Not used while Portfolio can't be selected by module.
        //$this->module = $request->getModuleName();

        if ($this->allow()) {

            // Pass in to the partial view to display
            if ('top' == $location) {
                return $this->view->partial('partials/clicktale-tracking-top.phtml');
            }
            else {
                return $this->view->partial('partials/clicktale-tracking-bottom.phtml');
            }

        }
        else {

            // No match, no tracking
            return null;

        }
    }
}