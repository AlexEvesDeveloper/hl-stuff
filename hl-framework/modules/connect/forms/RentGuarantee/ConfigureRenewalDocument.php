<?php

class Connect_Form_Rentguarantee_ConfigureRenewalDocument extends Zend_Form
{
    /**
     * Initialise the form
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');
        
        $this->addElement
        (
            'text',
            'landlords_name',
            array
            (
                'required'  => true,
                'validators' => array
                (
                    array
                    (
                        'NotEmpty', true, array
                        (
                            'messages' => array
                            (
                                'isEmpty' => 'Please provide the landlords full name',
                            )
                        )
                    )
                ),
            )
        );
        
        $this->addElement
        (
            'text',
            'landlords_email',
            array
            (
                'required'  => true,
                'validators' => array
                (
                    array
                    (
                        'NotEmpty', true, array
                        (
                            'messages' => array
                            (
                                'isEmpty' => 'Please provide the landlords email address',
                            )
                        )
                    )
                ),
            )
        );
        
        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Invalid email address"
            )
        );
        $this->getElement('landlords_email')->addValidator($emailValidator);

        
        $this->addElement
        (
            'text',
            'agent_job_title',
            array
            (
                'required'  => true,
                'validators' => array
                (
                    array
                    (
                        'NotEmpty', true, array
                        (
                            'messages' => array
                            (
                                'isEmpty' => 'Please provide your job title',
                            )
                        )
                    )
                ),
            )
        );
        
        
        $this->addElement
        (
            'text',
            'agent_contact_number',
            array
            (
                'required'  => true,
                'validators' => array
                (
                    array
                    (
                        'NotEmpty', true, array
                        (
                            'messages' => array
                            (
                                'isEmpty' => 'Please provide your contact telephone number',
                            )
                        )
                    )
                ),
            )
        );
        
        $this->addElement
        (
            'text',
            'policy_premium',
            array
            (
                'required'  => true,
                'validators' => array
                (
                    array
                    (
                        'NotEmpty', true, array
                        (
                            'messages' => array
                            (
                                'isEmpty' => 'Please provide the policy premium',
                            )
                        )
                    )
                ),
            )
        );
        
        $this->addElement
        (
            'text',
            'policy_term',
            array
            (
                'required'  => true,
                'validators' => array
                (
                    array
                    (
                        'NotEmpty', true, array
                        (
                            'messages' => array
                            (
                                'isEmpty' => 'Please provide the policy term',
                            )
                        )
                    )
                ),
            )
        );
        
        // Submit buttons
        $this->addElement('submit', 'formsubmit_refresh', array('name' => 'formsubmit_refresh', 'label' => 'Back'));
        $this->addElement('submit', 'formsubmit_back', array('name' => 'formsubmit_back', 'label' => 'Back'));
        $this->addElement('submit', 'formsubmit_email', array('name' => 'formsubmit_email', 'label' => 'Email'));
        $this->addElement('submit', 'formsubmit_post', array('name' => 'formsubmit_post', 'label' => 'Post'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguarantee/subforms/rentguarantee-configure-renewal-document.phtml'))
        ));
    }
}
