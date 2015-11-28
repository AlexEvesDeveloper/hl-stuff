<?php

class Connect_Form_ResetPassword extends Zend_Form {

    /**
     * Create reset password form.
     *
     * @return void
     */
    public function init() {

        $this->setMethod('post');

        // Add password1 element
        $this->addElement('password', 'password1', array(
            'label'      => 'New password',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your new password',
                            'notEmptyInvalid' => 'Please enter your new password'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^.{6,}$/',
                        'messages' => 'New password must contain at least 6 characters'
                    )
                )
            )
        ));

        // Add password2 element
        $this->addElement('password', 'password2', array(
            'label'      => 'New password again',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your new password again',
                            'notEmptyInvalid' => 'Please enter your new password again'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^.{6,}$/',
                        'messages' => 'New password again must contain at least 6 characters'
                    )
                )
            )
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

        // Check passwords are strong enough and match each other
        $filter= '[^\w\,\.\-\=\+\(\)\@\;\:\£\$\*\{\}\[\]\~\<\>\?\!]';
        $t_pass1 = preg_replace("/{$filter}/", '', $formData['password1']);
        $t_pass2 = preg_replace("/{$filter}/", '', $formData['password2']);
        if ($t_pass1 != $formData['password1']) {
            $this->addError('Password includes invalid characters, please use alphanumeric and basic punctuation only.');
        }
        if ($t_pass1 != $t_pass2) {
            $this->addError('Passwords don\'t match.');
        }
        if (strlen($t_pass1) < 6) {
            $this->addError('Password too short, must be at least 6 characters.');
        }
        if (strlen($t_pass1) > 20) {
            $this->addError('Password too long, must be less than 20 characters.');
        }
        if ((preg_match('/[a-z]/', $t_pass1) == 0) || (preg_match('/[A-Z]/', $t_pass1) == 0) || (preg_match('/\d/', $t_pass1) == 0)) {
            $this->addError('Password too weak, must contain a mix of lower case, upper case and numeric characters.');
        }

        // Call original isValid()
        return parent::isValid($formData);
    }
}