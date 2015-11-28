<?php
class Cms_View_Helper_HeaderQuotes extends Zend_View_Helper_Abstract
{
    public function headerQuotes($tags = '', $includeGlobal = true)
    {
        if (!$tags) { $tags = ''; }

        $quotes = new Datasource_Cms_HeaderQuotes();
        $quotesArray = $quotes->getByTags($tags, $includeGlobal);

        return $this->view->partialLoop('templates/partials/header-quote.phtml', $quotesArray);
    }
}
?>