<?php
/**
* Class definition for the form elements in the subform Personal Details
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_MultipleEmails extends Zend_Form_SubForm
{
	public function init(){

        // General Email Element
        $this->addElement('text', 'general_email_address', array(
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
        
        // Add a new validator
        $generalEmailValidator = new Zend_Validate_EmailAddress();
        $generalEmailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Enter a valid email address for the general email address"
            )
        );		
        $this->getElement('general_email_address')->addValidator($generalEmailValidator);  
        
        
        
        // Referencing Email Element
        $this->addElement('text', 'email_for_referencing', array(
            'label'      => 'Your referencing email address',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'A referencing email address is required',
                            'notEmptyInvalid' => 'Enter a valid email address for the referencing email address'
                        )
                    )
                )
            )
        )); 

        //Add a new validator
        $referencingEmailValidator = new Zend_Validate_EmailAddress();
        $referencingEmailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Enter a valid email address for the referencing email address"
            )
        );	        
        $this->getElement('email_for_referencing')->addValidator($referencingEmailValidator); 
        
        
        
        // Insurance Email Element
        $this->addElement('text', 'email_for_insurance', array(
            'label'      => 'Your insurance email address',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'An insurance email address is required',
                            'notEmptyInvalid' => 'Enter a valid email address for the insurance email address'
                        )
                    )
                )
            )
        )); 

        // Add a new validator
        $insuranceEmailValidator = new Zend_Validate_EmailAddress();
        $insuranceEmailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Enter a valid email address for the insurance email address"
            )
        );	        
        $this->getElement('email_for_insurance')->addValidator($insuranceEmailValidator);         
        
        
        
        // RG Renwals Email Element
        $this->addElement('text', 'email_for_rg_renewals', array(
            'label'      => 'Your rent guarantee email address',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'A rent guarantee address is required',
                            'notEmptyInvalid' => 'Enter a valid email address for the rent guarantee email address'
                        )
                    )
                )
            )
        )); 

       // Add a new validator
       $rgEmailValidator = new Zend_Validate_EmailAddress();        
       $rgEmailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Enter a valid email address for the rent guarantee email address"
            )
        );	        
        $this->getElement('email_for_rg_renewals')->addValidator($rgEmailValidator);

        
        
        
        // Invoicing Email Element
        $this->addElement('text', 'email_for_invoicing', array(
            'label'      => 'Your invoicing email address',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'A invoicing email address is required',
                            'notEmptyInvalid' => 'Enter a valid email address for the invoicing email address'
                        )
                    )
                )
            )
        )); 

        // Add a new validator
        $invoicingEmailValidator = new Zend_Validate_EmailAddress();
        $invoicingEmailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Enter a valid email address for the invoicing email address"
            )
        );	        
        $this->getElement('email_for_invoicing')->addValidator($invoicingEmailValidator);
                
        
        
        // Marketing Email Element
        $this->addElement('text', 'email_for_marketing', array(
            'label'      => 'Your HomeLet updates email address',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'A HomeLet update email address is required',
                            'notEmptyInvalid' => 'Enter a valid email address for the HomeLet updates email address'
                        )
                    )
                )
            )
        )); 
        
        // Add a new validator
        $updatesEmailValidator = new Zend_Validate_EmailAddress();
        $updatesEmailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Enter a valid email address for the HomeLet updates email address"
            )
        );	        
        $this->getElement('email_for_marketing')->addValidator($updatesEmailValidator);   
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/multiple-emails.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}