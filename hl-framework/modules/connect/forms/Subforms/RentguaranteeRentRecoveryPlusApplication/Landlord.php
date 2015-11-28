<?php

class Connect_Form_Subforms_RentguaranteeRentRecoveryPlusApplication_Landlord extends Zend_Form_SubForm {
    /**
     * Create property subform
     *
     * @return void
     */
    public function init() {

        // Add title element
        $this->addElement('select', 'title', array(            
            'label'     => 'Title',
            'required'  => true,
            'multiOptions' => array(
                'Mr'         => 'Mr',
                'Mrs'        => 'Mrs',
                'Ms'         => 'Ms',
                'Miss'       => 'Miss',
                'Sir'        => 'Sir',
                'Mr and Mrs' => 'Mr and Mrs',
                'Doctor'     => 'Dr',
                'Professor'  => 'Professor',
                'Reverend'   => 'Rev',
                'Other'      => 'Other'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your title',
                            'notEmptyInvalid' => 'Please select landlord title'
                        )
                    )
                )
            )
        ));
        
        // Add first name element
        $this->addElement('text', 'first_name', array(
            'label'      => 'First name',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your first name',
                            'notEmptyInvalid' => 'Please enter landlord first name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'First name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add last name element
        $this->addElement('text', 'last_name', array(
            'label'      => 'Last name',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter landlord last name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'Last name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));
        
        // Add e-mail element
        $this->addElement('text', 'email_address', array(
            'label'      => 'Email address',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter landlord email address'
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
        $this->getElement('email_address')->addValidator($emailValidator);
        
        // Add confirm e-mail element
        $this->addElement('text', 'confirm_email_address', array(
            'label'      => 'Confirm email address',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please confirm landlord email address'
                        )
                    )
                )
            )
        ));        
        
        // Add phone number element
        $this->addElement('text', 'phone_number', array(
            'label'      => 'Phone number',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter landlord phone number'
                        )
                    )
                )
            )
        ));
        
        // Add Question-1 element
        $this->addElement('radio', 'question1', array(
            'label'     => 'Are you aware of any circumstances which may give rise to a claim?',
            'required'  => true,
            'multiOptions' => array(
                'yes' =>  'Yes',
                'no'  => 'No'
            ),
            'separator' => ' ',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select answer to question: Are you aware of any circumstances which may give rise to a claim?',
                            'notEmptyInvalid' => 'Please select answer to question: Are you aware of any circumstances which may give rise to a claim?'
                        )
                    )
                )
            )
        ));        
        
        // Additional claim information element - textarea box
        $this->addElement('textarea', 'claiminfo', array(
            'label'      => 'Additional information',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
            array(
                'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please provide additional claim information.',
                        'notEmptyInvalid' => 'Please enter additional claim information'
                     )
                 )
            )
            )
        ));
        
        // Add Question-2 element
        $this->addElement('radio', 'question2', array(
            'label'     => 'Will only permitted occupiers be living in the property?',
            'required'  => true,
            'multiOptions' => array(
                'yes' =>  'Yes',
                'no'  => 'No'
            ),
            'separator' => ' ',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select answer to question: Will only permitted occupiers be living in the property?',
                            'notEmptyInvalid' => 'Please select answer to question: Will only permitted occupiers be living in the property?'
                        )
                    )
                )
            )
        ));
        
        // Add Question-3 element
        $this->addElement('radio', 'question3', array(
            'label'     => 'Any tenancy disputes, including late payment of rent?',
            'required'  => true,
            'multiOptions' => array(
                'yes' =>  'Yes',
                'no'  => 'No'
            ),
            'separator' => ' ',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select answer to question: Any tenancy disputes, including late payment of rent?',
                            'notEmptyInvalid' => 'Please select answer to question: Any tenancy disputes, including late payment of rent?'
                        )
                    )
                )
            )
        ));
        
        // Add agreement element
        $this->addElement('radio', 'agreement', array(
            'label'     => 'Type of tenancy agreement',
            'required'  => true,
            'multiOptions' => array(
                'AST'      =>  'AST',
                'Company'  => 'Company'
            ),
            'separator' => ' ',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select answer to Type of tenancy agreement',
                            'notEmptyInvalid' => 'Please select answer to Type of tenancy agreement'
                        )
                    )
                )
            )
        ));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguarantee/subforms/rent-recovery-plus-application-landlord.phtml'))
        ));

        $this->setElementFilters(array('StripTags'));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
    
    public function isValid($formData = array()) {         
        // Call original isValid() first before doing further validation checks 
    //    $isValid=parent::isValid($formData);
        
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));
        // Get ASN from auth object
        $agentSchemeNumber = $auth->getStorage()->read()->agentschemeno;                                   
        
        if ($formData['question1'] === 'yes') {
            $this->getElement('claiminfo')->setRequired(true);
        } else {
            $this->getElement('claiminfo')->setRequired(false);
        }

        if ((isset($formData['email_address']) && trim($formData['email_address']) != '')) {
            // The agent manager can throw execptions, for web use we need to treat them as invalid and return false       
            $agentManager = new Manager_Core_Agent();                        
            $agentObj = $agentManager->getAgent($agentSchemeNumber);
            // Get referencing email address (Cat 2)
            $ref_email_address = $agentManager->getEmailAddressByCategory(2);
            // Referencing email address is not allowed
            if ($formData['email_address'] === $ref_email_address){
                $this->getElement('email_address')->addError('Please provide Landlord email address.');                            
                $isValid=false;
            }
            // Confirmed email address filed value should be same as email address field value
            if ((isset($formData['confirm_email_address']) && trim($formData['confirm_email_address']) != '')) {
                if ($formData['email_address'] !== $formData['confirm_email_address']) {
                    $this->getElement('confirm_email_address')->addError('Invalid confirmed email address.');                            
                    $isValid=false;
                }
            }  
        }
        
    //    return $isValid;
        return parent::isValid($formData);
    }
}
