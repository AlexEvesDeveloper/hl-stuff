<?php

class Connect_Form_Login extends Zend_Form {

    /**
     * Create login form.
     *
     * @return void
     */
    public function init() {

        $this->setMethod('post');

        // Add agent scheme number element
        $this->addElement('text', 'agentschemeno', array(
            'label'      => 'Agent Scheme Number',
            'required'   => true,
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
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your username',
                            'notEmptyInvalid' => 'Please enter your username'
                        )
                    )
                )
            )
        ));

        // Add password element
        $this->addElement('password', 'password', array(
            'label'      => 'Password',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your password',
                            'notEmptyInvalid' => 'Please enter your password'
                        )
                    )
                )
            )
        ));

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
}