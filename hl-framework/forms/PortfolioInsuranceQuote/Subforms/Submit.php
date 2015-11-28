<?php
class Form_PortfolioInsuranceQuote_Subforms_Submit extends Zend_Form_SubForm
{
    /**
    * TODO: Document this
    * @param
    * @return
    * @author John Burrin
    */
    public function init()
    {
       
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/subforms/submit.phtml'))
        ));

    }
}
?>