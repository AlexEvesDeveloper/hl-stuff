<?php
/**
* Class definition for the form elements in the subform Personal Details
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_HeardAbout extends Zend_Form_SubForm
{
	public function init(){
    	$this->addElement('multiCheckbox', 'heard_about', array(
            'label'     => 'Where did you hear about us?',
            'required'  => false,
            'multiOptions' => array(
                'Magazine' => 'Magazine',
                'Search Engine' => 'Search Engine',
                'Literature' => 'Literature',
                'Recommendation' => 'Recommendation',
                'Exhibition' => 'Exhibition',
                'Online' => 'Online',
    			'Email' => 'Email',
    			'HomeLet Website' => 'HomeLet Website',
    			'Mailer-Campaign' => 'Mailer / Campaign',
                'Other' => 'Other'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'What products are you interested in',
                            'notEmptyInvalid' => 'Invalid selection for interested products'
                        )
                    )
                )
            )
        ));         
             

       
	    // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/heard-about.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}