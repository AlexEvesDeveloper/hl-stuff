<?php

class Form_PortfolioInsuranceQuote_Step5 extends Zend_Form_Multilevel
{
    /**
     * @return void
     */
    public function init()
    {
        // Grab view and add the sendQuote JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
    }
}
?>