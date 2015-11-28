<?php
/**
* Class definition for the form elements in the subform 
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_LimitedCompanyRegistration extends Zend_Form_SubForm
{
   /**
     * 
     *
     * @return void
     */
    public function init()
    {

    	// Add Company registration number element
        $this->addElement('text', 'registration_number', array(
            'label'      => 'Company registration number ',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your company registration number',
                            'notEmptyInvalid' => 'Company registration number in invalid'
                        )
                    )
                )
            )
        )); 
        
        
         // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/limited-company-registration.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

    }
}
?>