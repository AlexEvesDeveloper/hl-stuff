<?php
/**
* Class definition for the form elements in the subform Campaign
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_Common extends Zend_Form_SubForm
{
   /**
     * 
     *
     * @return void
     */
    public function init()
    {

    	// Unique id
    	$this->addElement('hidden', 'uid', array(    		
    		'required'	=> false
    	));
    	
    	$this->addElement('button', 'add_contact', array(
        		'label'		=> 'Add Director'
        ));
    	
        
         // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/common.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        $this->getElement('add_contact')->removeDecorator('Label');

    }
}
?>