<?php
/**
* Class definition for the form elements in the subform SoleTrader
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_LimitedLiabilityPartnership extends Zend_Form_SubForm
{
   /**
     * 
     *
     * @return void
     */
    public function init()
    {
    	
        
        // Add Name  element
        $this->addElement('text', 'contact_name', array(
            'label'      => 'Your Name ',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Name is required',
                            'notEmptyInvalid' => 'Invalid Name'
                        )
                    )
                )
            )
        ));
         

        // Add Date of Birth number element
        $this->addElement('text', 'birth_date', array(
            'label'      => 'Partner date of birth',
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

		// Append Javascripts
		//Grab view and add the date picker JavaScript files into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        
        $view->headLink()->appendStylesheet(
            '/assets/cms/css/datePicker.css',
            'screen'
        );
                
		$view->headScript()->appendFile(
            '/assets/vendor/js/date.js',
            'text/javascript'
		        )->appendFile(
		            '/assets/cms/js/jquery.datePicker.js',
		            'text/javascript'
		        )->appendFile(
		            '/assets/cms/js/letting-agents/BirthDatePicker.js',
		            'text/javascript'
		);        
        
         // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/limited-liability-partnership.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}
?>