<?php
class Cms_View_Helper_LettingAgentsSubHeader extends Zend_View_Helper_Abstract
{
    public function LettingAgentsSubHeader($stepNum) {
        $params = array ('stepNum' => $stepNum);
        return $this->view->partial('partials/sub-header.phtml', $params);
    }
}
?>