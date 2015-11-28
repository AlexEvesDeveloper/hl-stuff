<?php
/**
 * Action Helper for generating panel content
 *
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Connect_Controller_Action_Helper_Panelcontent extends Zend_Controller_Action_Helper_Abstract {

    public function fetch($panelKey) {

        // Check for content panel to go into panel and pass into view
        $panelShow = false;
        $panelContent = '';
        $panelObj = new Datasource_Cms_Panels();
        $panel = $panelObj->getByKey($panelKey);

        if (!is_null($panel)) {

            $panelContent = $panel['content'];

            if (!is_null($panelContent) && trim($panelContent) != '') {

                $panelShow = true;
            }
        }

        $this->getActionController()->view->panelShow = $panelShow;
        $this->getActionController()->view->panelContent = $panelContent;
    }
}
