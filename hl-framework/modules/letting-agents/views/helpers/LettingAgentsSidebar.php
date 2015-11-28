<?php
class Cms_View_Helper_LettingAgentsSidebar extends Zend_View_Helper_Abstract {
    public function lettingAgentsSidebar($errorsHtml, $stepNum, $stepMax) {
        // TODO: Determine if customer is logged in
        return $this->view->partial('partials/sidebar.phtml',array(
                                    'errorsHtml' => $errorsHtml,
                                    'stepNum' => $stepNum,
                                    'stepMax' => $stepMax));
    }
}
?>