<?php
/**
* Class definition for the form elements in the subform Personal Details
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_CompanyFax extends Zend_Form_SubForm
{
	public function init(){
       
        // General Email Element
        $this->addElement('text', 'fax_number', array(
            'label'      => 'Fax number',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter you business fax number',
                            'notEmptyInvalid' => 'Fax number is not a valid UK telephone number'
                        )
                    )
                )
            )
        ));

        $faxValidator = new Zend_Validate_TelephoneNumber();
        $faxValidator->setMessages(
            array(
                Zend_Validate_TelephoneNumber::INVALID    => "Fax number is not a valid UK telephone number"
            )
        );
        $this->getElement('fax_number')->addValidator($faxValidator);           
        
        // Referencing Email Element
        $this->addElement('text', 'company_website_address', array(
            'label'      => 'Your company website',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter your business website address',
                            'notEmptyInvalid' => 'Website address is invalid'
                        )
                    )
                )
            )
        )); 

      
	    // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/company-fax.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}