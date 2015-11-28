<?php
class Cms_View_Helper_PortfolioQuoteSubHeader extends Zend_View_Helper_Abstract
{
    public function portfolioQuoteSubHeader($stepNum) {
    	if ($stepNum == "add") $stepNum = 2;
        $params = array ('stepNum' => $stepNum);
        return $this->view->partial('portfolio-insurance-quote/partials/sub-header.phtml', $params);
    }
}
?>