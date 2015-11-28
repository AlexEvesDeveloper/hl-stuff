<?php

/**
 * Class Cms_View_Helper_GoogleRemarketing
 */
class Cms_View_Helper_GoogleRemarketing extends Zend_View_Helper_Abstract
{
    /**
     * Google remarketing view helper to insert Google remarketing tag code into a layout.
     *
     * @return string
     */
    public function googleRemarketing()
    {
        return $this->view->partial('partials/google-remarketing.phtml');
    }
}