<?php
class Form_PortfolioInsuranceQuote_Subforms_Edit extends Zend_Form_SubForm
{
    /**
    * TODO: Document this
    * @param
    * @return
    * @author John Burrin
    */
    public function init()
    {
        // Add Comprehensive Buildings Insurance element
        $this->addElement('hidden', 'propertyid', array(
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('Digits', true, array(
                        'messages' => array(
                            'notDigits' => 'Invalid Property ID'
                        )
                    )
                ),
            )
        ));
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/subforms/edit.phtml'))
        ));

    }
}
?>