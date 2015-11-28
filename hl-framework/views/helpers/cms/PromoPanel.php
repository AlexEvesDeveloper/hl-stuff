<?php

class Cms_View_Helper_PromoPanel extends Zend_View_Helper_Abstract
{
    public function promoPanel($type, $header, $content, $icon = '', $url = '')
    {
        return $this->view->partial(
            "templates/partials/promopanel.phtml",
            array(
                'header' => $header,
                'content' => $content,
                'icon' => $icon,
                'url' => $url
            )
        );
    }
}
?>