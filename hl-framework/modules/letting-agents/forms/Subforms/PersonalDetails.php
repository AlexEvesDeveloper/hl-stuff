<?php
/**
* Class definition for the form elements in the subform Personal Details
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_PersonalDetails extends Zend_Form_SubForm
{
	public function init(){
		// Type of organisation Element
    	$this->addElement('select', 'organisation_type', array(
            'label'     => 'What sort of organisation are you?',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- Please select ---',
                LettingAgents_Object_CompanyTypes::LimitedCompany => 'Limited Company',
                LettingAgents_Object_CompanyTypes::Partnership => 'Partnership',
                LettingAgents_Object_CompanyTypes::SoleTrader => 'Sole Trader',
                LettingAgents_Object_CompanyTypes::LimitedLiabilityPartnership => 'Limited liability partnership'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'What sort of organisation are you?',
                            'notEmptyInvalid' => 'What sort of organisation are you?'
                        )
                    )
                )
            )
        ));
        
        // date_firm_established Element
        $this->addElement('text', 'date_established', array(
        // I want the id to be different from the name so I can run a standard js validation on the date
        	'id'		 => 'theDate',
            'label'      => 'When were you established?',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'When were you established?',
                            'notEmptyInvalid' => 'You have entered an invalid date for when were you established?'
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
        
        
        // is_associated Element
        $this->addElement('select', 'is_associated', array(
            'label'      => 'Is your company associated with any other letting business?',
            'required'   => true,
	        'multiOptions' => array(
	                '' => '--- Please select ---',
	                '1' => 'Yes',
	                '0' => 'No'
	            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Is your company associated with any other company?',
                            'notEmptyInvalid' => 'Is your company associated with any other company?'
                        )
                    )
                )
            )
        ));        

        // if_yes Element
        $this->addElement('text', 'associated_text', array(
            'label'      => 'If yes please state',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'If yes please state',
                            'notEmptyInvalid' => 'If yes please state'
                        )
                    )
                )
            )
        ));         

        // contact_name Element
        $this->addElement('text', 'contact_name', array(
            'label'      => 'Your contact name',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter a contact name',
                            'notEmptyInvalid' => 'Invalid entry for contact name'
                        )
                    )
                )
            )
        )); 

        // contact_name Element
        $this->addElement('text', 'contact_number', array(
            'label'      => 'Your phone number',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'A uk phone number is required',
                            'notEmptyInvalid' => 'Enter a valid uk phone number'
                        )
                    )
                )
            )
        ));       

        $phoneValidator = new Zend_Validate_TelephoneNumber();
        $phoneValidator->setMessages(
            array(
                Zend_Validate_TelephoneNumber::INVALID    => "Enter a valid uk phone number"
            )
        );
        $this->getElement('contact_number')->addValidator($phoneValidator);        
        
        // General Email Element
        $this->addElement('text', 'general_email', array(
            'label'      => 'Your general email address',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'A general email address is required',
                            'notEmptyInvalid' => 'Enter a valid email address for the general email address'
                        )
                    )
                )
            )
        ));           
        
        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Invalid email address"
            )
        );
        $this->getElement('general_email')->addValidator($emailValidator);
        
        
	    // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/personal-details.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }


    public function isValid($postData) {
    	
    	// If is_associated is 1 then the 'associated_text' text field become required
    	if($postData['is_associated'] == 1){
    		$this->getElement('associated_text')->setRequired(true);
    	}
    	
        return parent::isValid($postData);
        }
    	
}