<?php
/**
* Class definition for the form elements in the subform Personal Details
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_AgentType extends Zend_Form_SubForm
{
	public function init(){
       
		$this->addElement('hidden', 'agent_type', array(
			'value'		=> 'standard',
            'required'  => true,
	        'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'You must select an agent type',
                            'notEmptyInvalid' => 'Invalid agent type'
                        )
                    )
                )
            )
        ));
        
		        
		$this->addElement('select', 'pi_cert', array(
            'label'     => 'Have you got professional indemnity insurance?',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- Please select ---',
                'yes' => 'Yes',
                'no' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Do you hold professional Indemnity insurance?',
                            'notEmptyInvalid' => 'Do you hold professional Indemnity insurance?'
                        )
                    )
                )
            )
        ));

        
		$this->addElement('select', 'ico_select', array(
            'label'     => 'Are you registered with the Information Commissioner\'s Office?',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- Please select ---',
                'yes' => 'Yes',
                'no' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Are you registered with the Information Commissioner\'s Office?',
                            'notEmptyInvalid' => 'Are you registered with the Information Commissioner\'s Office?'
                        )
                    )
                )
            )
        ));        

        // retroactive_date Element
        $this->addElement('text', 'ico_renewal_date', array(
        // I want the id to be different from the name so I can run a standard js validation on the date
        	'id'	     => 'theDate',
            'label'      => 'Date of renewal',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter the ICO renewal date',
                            'notEmptyInvalid' => 'Invaid ICO renewal date'
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
		            '/assets/cms/js/letting-agents/DatePicker.js',
		            'text/javascript'
		);        
        
	    // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/agent-type.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }

   public function isValid($formData = array()) {
	
		//Zend_Debug::dump($formData);
		if(isset($formData['ico_select']) && $formData['ico_select'] != 'yes'){
			// un require some fields
			$this->getElement('ico_renewal_date')->setRequired(false);
		}else{
			// Turn em back on
			$this->getElement('ico_renewal_date')->setRequired(true);
		}
        // Call original isValid()
        return parent::isValid($formData);

    }      
}