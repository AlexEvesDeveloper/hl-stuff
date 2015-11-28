<?php
class Connect_View_Helper_NewsTicker extends Zend_View_Helper_Abstract
{

    public function newsTicker()
    {
        $newsTicker = new Datasource_Connect_NewsTicker();
        $newsArray = $newsTicker->getAll();

        return $this->view->partialLoop('partials/news-ticker-item.phtml', $newsArray);
    }

}