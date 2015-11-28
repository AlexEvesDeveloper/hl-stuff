<?php

class Cms_View_Helper_TenantsQuoteSidebar extends Zend_View_Helper_Abstract {

    public function TenantsQuoteSidebar($premiums, $fees, $errorsHtml, $stepNum, $stepMax) {
        
        // TODO: Determine if customer is logged in
        
        return $this->view->partial('partials/sidebar.phtml',array(
                                    'premiums' => $premiums,
                                    'fees' => $fees,
                                    'errorsHtml' => $errorsHtml,
                                    'stepNum' => $stepNum,
                                    'stepMax' => $stepMax));
    }
}
?>