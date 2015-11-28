<?php
class Cms_View_Helper_SubHeader extends Zend_View_Helper_Abstract
{
    public function subHeader($tags, $linkTypes) {
        $params['quotes'] = $this->view->headerQuotes( $tags );
        $params['linkTypes'] = $linkTypes;
        return $this->view->partial('templates/partials/sub-header.phtml', $params);
    }
}
?>