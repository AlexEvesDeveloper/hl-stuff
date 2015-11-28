<?php

class Connect_View_Helper_InfoPanel extends Zend_View_Helper_Abstract {

    public function infoPanel($content) {

        return $this->view->partial(
            'partials/infopanel.phtml',
            array(
                'panelContent' => $content
            )
        );
    }

}