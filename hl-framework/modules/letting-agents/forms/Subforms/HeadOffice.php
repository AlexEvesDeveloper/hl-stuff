<?php
/**
* Class definition for the form elements in the subform ead Office details
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_HeadOffice extends Zend_Form_SubForm
{
	public function init(){
       
        // contactnameElement
        $this->addElement('text', 'contactname', array(
            'label'      => 'Please give us a head office contact name',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please give us a head office contact name',
                            'notEmptyInvalid' => 'Invalid contact name'
                        )
                    )
                )
            )
        ));

        // telephone_numberl Element
        $this->addElement('text', 'telephone_number', array(
            'label'      => 'Head office phone number',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter your Head Office telephone number',
                            'notEmptyInvalid' => 'Invalid UK telephone number for Head Office'
                        )
                    )
                )
            )
        )); 

        $phoneValidator = new Zend_Validate_TelephoneNumber();
        $phoneValidator->setMessages(
            array(
                Zend_Validate_TelephoneNumber::INVALID    => "Invalid UK telephone number for Head Office"
            )
        );
        $this->getElement('telephone_number')->addValidator($phoneValidator);          
               
        // head_office_email_address Email Element
        $this->addElement('text', 'head_office_email_address', array(
            'label'      => 'Head office email address',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter your head office email address',
                            'notEmptyInvalid' => 'Head office email address is invalid'
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
        $this->getElement('head_office_email_address')->addValidator($emailValidator);        
        
        // head_office_fax_number Element
        $this->addElement('text', 'head_office_fax_number', array(
            'label'      => 'Head office fax number',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter your Head Office fax number',
                            'notEmptyInvalid' => 'Head Office Fax number is not a valid UK telephone number'
                        )
                    )
                )
            )
        ));

        $faxValidator = new Zend_Validate_TelephoneNumber();
        $faxValidator->setMessages(
            array(
                Zend_Validate_TelephoneNumber::INVALID    => "Head Office Fax number is not a valid UK telephone number"
            )
        );
        $this->getElement('head_office_fax_number')->addValidator($faxValidator);  
        
	    // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/head-office.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}