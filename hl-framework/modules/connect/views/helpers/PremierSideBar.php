<?php
class Connect_View_Helper_PremierSideBar extends Zend_View_Helper_Abstract {

    public function premierSideBar($product = '') {

        return $this->view->partial(
            'partials/premier-product-sidebar.phtml',
            array('product' => $product)
        );
    }
}