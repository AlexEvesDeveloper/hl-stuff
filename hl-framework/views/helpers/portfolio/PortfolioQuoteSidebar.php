<?php
class Cms_View_Helper_PortfolioQuoteSidebar extends Zend_View_Helper_Abstract {
    public function PortfolioQuoteSidebar($premiums, $fees, $errorsHtml, $stepNum, $stepMax) {
        // TODO: Determine if customer is logged in
        return $this->view->partial('portfolio-insurance-quote/partials/sidebar.phtml',array(
                                    'premiums' => $premiums,
                                    'fees' => $fees,
                                    'errorsHtml' => $errorsHtml,
                                    'stepNum' => $stepNum,
                                    'stepMax' => $stepMax));
    }
}
?>