<?php
/**
* Class definition for the form elements in the subform SoleTrader
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_SoleTrader extends Zend_Form_SubForm
{
   /**
     * 
     *
     * @return void
     */
    public function init()
    {
        // Add Sole Trader Name  element
        $this->addElement('text', 'contact_name', array(
            'label'      => 'Your Name',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Contact name is required',
                            'notEmptyInvalid' => 'Invalid partner name'
                        )
                    )
                )
            )
        ));
        // Add NI  element
        // Official Regex is
        // ^[A-CEGHJ-PR-TW-Z]{1}[A-CEGHJ-NPR-TW-Z]{1}[0-9]{6}[A-DFM]{0,1}$
        // Simple Format is 
        // ^[A-Z]{2}[0-9]{6}[A-Z]{0,1}$
        $this->addElement('text', 'ni_number', array(
            'label'      => 'Your National Insurance number ',
            'required'   => true,
        // 	Todo: Expectd this to upper case the input, but it didn't
        	'filters' => array('StringToUpper'),
        	'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'National Insurance is required',
                            'notEmptyInvalid' => 'Invalid National Insurance number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[A-Z]{2}[0-9]{6}[A-Z]{0,1}$/i', 
                        'messages' => 'Invalid National Insurance number format'
                    )
                )
            )
        ));     

        // Add Passport number  element
        $this->addElement('text', 'passport_number', array(
            'label'      => 'Your Passport number',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Passport number is required',
                            'notEmptyInvalid' => 'Invalid Passport number'
                        )
                    )
                )
            )
        ));        


        // Add Date of Birth number element
        $this->addElement('text', 'birth_date', array(
            'label'      => 'Date of birth',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your date of birth',
                            'notEmptyInvalid' => 'Invalid date of birth'
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
            array('ViewScript', array('viewScript' => 'subforms/sole-trader.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}
?>