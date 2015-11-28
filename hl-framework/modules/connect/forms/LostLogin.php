<?php

class Connect_Form_LostLogin extends Zend_Form {

    /**
     * Create lost login (password retrieval) form.
     *
     * @return void
     */
    public function init() {

        // Invoke the agent user manager
        $agentUserManager = new Manager_Core_Agent_User();

        // Create array of possible security questions
        $securityQuestions  = array('' => '--- please select ---');
        $securityQuestions += $agentUserManager->getUserSecurityAllQuestions();

        $this->setMethod('post');

        // Add agent scheme number element
        $this->addElement('text', 'agentschemeno', array(
            'label'      => 'Agent Scheme Number',
            'required'   => false,
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your Agent Scheme Number',
                            'notEmptyInvalid' => 'Please enter your Letting Agent Scheme Number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d{5,}$/',
                        'messages' => 'Agent Scheme Number must contain at least 5 digits'
                    )
                )
            )
        ));

        // Add username element
        $this->addElement('text', 'username', array(
            'label'      => 'Username',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
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
                        'pattern' => '/^[a-z0-9]{6,64}$/i',
                        'messages' => 'Username must contain between 6 and 64 alphanumeric characters'
                    )
                )
            )
        ));

        // Add password element
        $this->addElement('password', 'password', array(
            'label'      => 'Password',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your password',
                            'notEmptyInvalid' => 'Please enter your password'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^.{6,}$/',
                        'messages' => 'Password must contain at least 6 characters'
                    )
                )
            )
        ));

        // Add real name element
        $this->addElement('text', 'realname', array(
            'label'      => 'First name + last name',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your first name + last name',
                            'notEmptyInvalid' => 'Please enter your first name + last name'
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

        // Add security question element
        $this->addElement('select', 'question', array(
            'label'     => 'Security question',
            'required'  => false,
            'multiOptions' => $securityQuestions,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a security question',
                            'notEmptyInvalid' => 'Please select a security question'
                        )
                    )
                )
            )
        ));

        // Add security answer element
        $this->addElement('text', 'answer', array(
            'label'      => 'Security answer',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your security answer',
                            'notEmptyInvalid' => 'Please enter your security answer'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[\w\ \.\-\'\,]{2,}$/i',
                        'messages' => 'Security answer must contain at least two characters and only basic punctuation (hyphen, apostrophe, comma, full stop and space)'
                    )
                )
            )
        ));

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

        $this->setElementFilters(array('StripTags'));

        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors',
            array('HtmlTag', array('tag' => 'div')),
        ));

        // Add login button
        $this->addElement('submit', 'login', array('label' => 'Login'));
    }

    /**
     * Overridden isValid() method for pre-validation code.
     *
     * @param array $formData data typically from a POST or GET request.
     *
     * @return bool
     */
    public function isValid($formData = array()) {

        // If a password is given, username is mandatory
        if (isset($formData['password']) && trim($formData['password']) != '') {
            $this->getElement('username')->setRequired(true);
        }

        // If a security question is given, security answer is mandatory
        if (isset($formData['question']) && trim($formData['question']) != '') {
            $this->getElement('answer')->setRequired(true);
        }

        // Check what has been supplied is enough detail to find a single agent user with
        $agentUserManager = new Manager_Core_Agent_User();
        $fuzzySearchResult = $agentUserManager->searchByFuzzyCredentials($formData);
        if (is_string($fuzzySearchResult)) {
            // Agent details can't be found; set form-level error
            $this->addError('A problem occurred: ' . $fuzzySearchResult);
        }

        // Call original isValid()
        return parent::isValid($formData);
    }
}