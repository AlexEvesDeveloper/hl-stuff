<?php
/**
* Class definition for the form elements in the subform Company Detail
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_CompanyDetail extends Zend_Form_SubForm
{
   /**
     * 
     *
     * @return void
     */
    public function init()
    {     
        // Add element
        $this->addElement('text', 'current_referencing_supplier', array(
            'label'      => 'Current referencing supplier',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tell us who your Current referencing supplier is',
                            'notEmptyInvalid' => 'Invalid value for Current referencing supplier'
                        )
                    )
                )
            )
        ));    	

        // Add element
        $this->addElement('text', 'no_of_branches', array(
            'label'      => 'How many branches have you got?',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please let us know how many branches you have',
                            'notEmptyInvalid' => 'Invalid value for number of branches'
                        )
                    )
                )
            )
        ));    	
        
        // Add element
        $this->addElement('text', 'no_of_staff', array(
            'label'      => 'How many people work for you? ',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the number of staff you employ',
                            'notEmptyInvalid' => 'Invalid value for the number of staff'
                        )
                    )
                )
            )
        ));    	

        // Add element
        $this->addElement('text', 'no_of_properties_managed', array(
            'label'      => 'On average, how many properties do you let per month?',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'How many properties do you let per month?',
                            'notEmptyInvalid' => 'Invalid value for how many properties do you let per month?'
                        )
                    )
                )
            )
        ));    	
        
        // Add element
        $this->addElement('text', 'no_of_landlords', array(
            'label'      => 'How many landlords do you look after?',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter how many landlords your look after',
                            'notEmptyInvalid' => 'Invalid valu for the number of landlords'
                        )
                    )
                )
            )
        ));    	
        
        
         // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/company-detail.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}
?>