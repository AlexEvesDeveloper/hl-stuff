<?php
/**
* Class definition for the form elements in the subform Campaign
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_LimitedCompanyContacts extends Zend_Form_SubForm
{
   /**
     * 
     *
     * @return void
     */
    public function init()
    {
    	    
        // Add Directors name  element
        $this->addElement('text', 'contact_name', array(
            'label'      => 'Director Name ',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Directors name is required',
                            'notEmptyInvalid' => 'Invalid director name'
                        )
                    )
                )
            )
        )); 
     
        
         // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/limited-company-contacts.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        

    }
}
?>