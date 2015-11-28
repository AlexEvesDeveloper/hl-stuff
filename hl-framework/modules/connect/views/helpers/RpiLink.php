<?php

class Connect_View_Helper_RpiLink extends Zend_View_Helper_Abstract
{

    public function rpiLink() {

        // TODO: Set this to use a URL driven by the CMS, and have the CMS allow PDF uploads
        // Alternatively set this to a generic path and use a symbolic link to the latest PDF?
        $data = array(
			'url' => '/assets/connect/pdf/rpi/HL_HRI.pdf'
        );

        return $this->view->partial('partials/rpi.phtml', $data);
    }

}
