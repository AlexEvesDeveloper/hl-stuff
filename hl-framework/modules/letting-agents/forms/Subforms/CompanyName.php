<?php
/**
* Class definition for the form elements in the subform CompanyName
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_CompanyName extends Zend_Form_SubForm
{
   /**
     * 
     *
     * @return void
     */
    public function init()
    {
        // Add legal_name_of_company element
        $this->addElement('text', 'legal_name', array(
            'label'      => 'Your company’s legal name',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Legal name of company',
                            'notEmptyInvalid' => 'Legal name of company'
                        )
                    )
                )
            )
        )); 
        
        // Add trading_name_of_company element
        $this->addElement('text', 'trading_name', array(
            'label'      => 'Your company’s trading name',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Your company’s trading name',
                            'notEmptyInvalid' => 'Your company’s trading name'
                        )
                    )
                )
            )
        ));    	
    	
         // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/company-name.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}
?>