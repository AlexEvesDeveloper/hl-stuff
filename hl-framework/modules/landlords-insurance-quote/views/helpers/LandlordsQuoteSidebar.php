<?php

class Cms_View_Helper_LandlordsQuoteSidebar extends Zend_View_Helper_Abstract {

    public function landlordsQuoteSidebar($premiums, $fees, $errorsHtml, $stepNum, $stepMax) {
        
        return $this->view->partial('partials/sidebar.phtml',array(
                                    'premiums' => $premiums,
                                    'fees' => $fees,
                                    'errorsHtml' => $errorsHtml,
                                    'stepNum' => $stepNum,
                                    'stepMax' => $stepMax));
    }
}
?>