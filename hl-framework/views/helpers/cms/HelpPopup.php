<?php

class Cms_View_Helper_HelpPopup extends Zend_View_Helper_Abstract
{
    public function helpPopup($title, $contentKey, $extraOptions = array())
    {
        $extraClasses = '';
        if (isset($extraOptions['extraClasses']) && count($extraOptions['extraClasses']) > 0) {
            $extraClasses = ' ' . implode(' ', $extraOptions['extraClasses']);
        }

        return $this->view->partial(
            'templates/partials/helppopup.phtml',
            'cms',
            array(
                'title' => $title,
                'helpKey' => $contentKey,
                'extraClasses' => $extraClasses
            )
        );
    }
}
?>