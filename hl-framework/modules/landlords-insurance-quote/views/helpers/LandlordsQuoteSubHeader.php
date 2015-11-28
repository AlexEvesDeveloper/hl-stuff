<?php
class Cms_View_Helper_LandlordsQuoteSubHeader extends Zend_View_Helper_Abstract
{
    public function landlordsQuoteSubHeader($stepNum) {
        $params = array ('stepNum' => $stepNum);
        return $this->view->partial('partials/sub-header.phtml', $params);
    }
}
?>