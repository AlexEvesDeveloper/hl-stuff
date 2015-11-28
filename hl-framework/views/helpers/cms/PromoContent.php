<?php
class Cms_View_Helper_PromoContent extends Zend_View_Helper_Abstract
{
    public function promoContent($key)
    {
        // Get promo panel content
        $panelObj = new Datasource_Cms_Panels();
        $panelContent = $panelObj->getByKey($key);
        if (!is_null($panelContent)) {
            return $panelContent['content'];
        } else {
            return '';
        }
    }
}
?>