<?php
class Cms_View_Helper_TenantsQuoteSubHeader extends Zend_View_Helper_Abstract
{
    public function tenantsQuoteSubHeader($stepNum) {
        $params = array ('stepNum' => $stepNum);
        return $this->view->partial('partials/sub-header.phtml', $params);
    }
}
?>