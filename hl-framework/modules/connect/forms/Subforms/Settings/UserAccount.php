<?php

class Connect_Form_Subforms_Settings_UserAccount extends Zend_Form_SubForm {

    protected $_role;

    public function __construct($role = Model_Core_Agent_UserRole::BASIC) {

        $this->_role = $role;
        return parent::__construct();
    }

    /**
     * Create user details form (single user).
     *
     * @return void
     */
    public function init() {

        // Invoke the agent user manager
        $agentUserManager = new Manager_Core_Agent_User();

        // Create array of possible security questions
        $securityQuestions  = array('' => '--- please select ---');
        $securityQuestions += $agentUserManager->getUserSecurityAllQuestions();

        // Add real name element
        $this->addElement('text', 'realname', array(
            'label'      => 'Full name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your full name',
                            'notEmptyInvalid' => 'Please enter your full name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'Name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add username element
        $this->addElement('text', 'username', array(
            'label'         => 'Username',
            'required'      => false,
            'filters'       => array('StringTrim'),
            'validators'    => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your username',
                            'notEmptyInvalid' => 'Please enter your username'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z0-9]{1,64}$/i',
                        'messages' => 'Username must contain between 1 and 64 alphanumeric characters'
                    )
                )
            )
        ));
        if ($this->_role == Model_Core_Agent_UserRole::MASTER) {
            $this->getElement('username')->setRequired(true);
        } else {
            $this->getElement('username')->setAttrib('disabled', 'disabled');
        }


        // Add password1 element
        $passwordElement1 = new Zend_Form_Element_Password('password1');
        $passwordElement1->setRequired(false);
        $passwordElement1->setLabel('New password:');
        $passwordElement1->addValidator(new Zend_Validate_PasswordStrength());
        $this->addElement($passwordElement1);

        $validator = new Zend_Validate_Identical();
        $validator->setToken('password2');
        $validator->setMessage('Passwords are not the same', Zend_Validate_Identical::NOT_SAME);
        $passwordElement1->addValidator($validator);

        $passwordElement2 = new Zend_Form_Element_Password('password2');
        $passwordElement2->setRequired(false);
        $passwordElement2->setLabel('New password (again)');
        $this->addElement($passwordElement2);


        // Add e-mail element
        $this->addElement('text', 'email', array(
            'label'      => 'E-mail address',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your e-mail address'
                        )
                    )
                )
            )
        ));
        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => 'Domain name invalid in e-mail address',
                Zend_Validate_EmailAddress::INVALID_FORMAT      => 'Invalid e-mail address'
            )
        );
        $this->getElement('email')->addValidator($emailValidator);
        if ($this->_role == Model_Core_Agent_UserRole::MASTER) {
            $this->getElement('email')->setRequired(true);
        } else {
            $this->getElement('email')->setAttrib('disabled', 'disabled');
        }

        // Add e-mail element
        $this->addElement('text', 'emailcopyto', array(
            'label'      => 'Copy e-mail to',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a copy-to e-mail address'
                        )
                    )
                )
            )
        ));
        $emailCopyToValidator = new Zend_Validate_EmailAddress();
        $emailCopyToValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => 'Domain name invalid in copy-to e-mail address',
                Zend_Validate_EmailAddress::INVALID_FORMAT      => 'Invalid copy-to e-mail address'
            )
        );
        $this->getElement('emailcopyto')->addValidator($emailCopyToValidator);

        // Add security question element
        $this->addElement('select', 'question', array(
            'label'     => 'Security question',
            'required'  => false,
            'multiOptions' => $securityQuestions
        ));

        // Add security answer element
        $this->addElement('text', 'answer', array(
            'label'      => 'Security answer',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[\w\ \.\-\'\,]{2,}$/i',
                        'messages' => 'Security answer must contain at least two characters and only basic punctuation (hyphen, apostrophe, comma, full stop and space)'
                    )
                )
            )
        ));

        // Add master user element
        $this->addElement('checkbox', 'master', array(
            'label'         => 'Master user',
            'required'      => false,
            'checkedValue'  => '1',
            'uncheckedValue' => '0'
        ));

        // Add agent reports element
        $this->addElement('checkbox', 'reports', array(
            'label'         => 'Agent reports',
            'required'      => false,
            'checkedValue'  => '1',
            'uncheckedValue' => '0'
        ));

        // Add status element
        $this->addElement('checkbox', 'status', array(
            'label'         => 'Active',
            'required'      => false,
            'checkedValue'  => '1',
            'uncheckedValue' => '0'
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'settings/subforms/useraccount.phtml',
                    'role' => $this->_role
                )
            )
        ));

        $this->setElementFilters(array('StripTags'));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}