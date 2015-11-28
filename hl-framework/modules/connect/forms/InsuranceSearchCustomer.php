<?php

class Connect_Form_InsuranceSearchCustomer extends Zend_Form {

    /**
     * Create insurance customer search form.
     *
     * @return void
     */
    public function init() {

        $this->setMethod('get');

        // Add first name element
        $this->addElement('text', 'firstName', array(
            'label'      => 'Customer First Name',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']+$/i',
                        'messages' => 'Customer First Name must contain alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add last name element
        $this->addElement('text', 'lastName', array(
            'label'      => 'Customer Last Name',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']+$/i',
                        'messages' => 'Customer Last Name must contain alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add line 1 property address element
        $this->addElement('text', 'address1', array(
            'label'      => 'Street Address',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z0-9\-\ \'\,\.]+$/i',
                        'messages' => 'Street Address must contain alphanumeric characters and only basic punctuation (hyphen, space, single quote, comma and full stop)'
                    )
                )
            )
        ));

        // Add line 2 property address element
        $this->addElement('text', 'address2', array(
            'label'      => 'Town/City',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z0-9\-\ \'\,\.]+$/i',
                        'messages' => 'Town/City must contain alphanumeric characters and only basic punctuation (hyphen, space, single quote, comma and full stop)'
                    )
                )
            )
        ));

        // Add postcode element
        $this->addElement('text', 'postcode', array(
            'label'      => 'Post Code',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z0-9\ ]+$/i',
                        'messages' => 'Post Code must only contain alphanumeric characters and spaces'
                    )
                )
            )
        ));

        // Add telephone element
        $this->addElement('text', 'telephone', array(
            'label'      => 'Telephone',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^((\+44\s?\(0\)\s?\d{2,4})|(\+44\s?(01|02|03|07|08)\d{2,3})|(\+44\s?(1|2|3|7|8)\d{2,3})|(\(\+44\)\s?\d{3,4})|(\(\d{5}\))|((01|02|03|07|08)\d{2,3})|(\d{5}))(\s|-|.)(((\d{3,4})(\s|-)(\d{3,4}))|((\d{6,7})))$/',
                        'messages' => 'Not a valid phone number'
                    )
                )
            )
        ));

        // Add e-mail element
        $this->addElement('text', 'email', array(
            'label'      => 'Email',
            'required'   => false,
            'filters'    => array('StringTrim')
        ));

        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => 'Domain name invalid in email address',
                Zend_Validate_EmailAddress::INVALID_FORMAT      => 'Invalid email address'
            )
        );
        $this->getElement('email')->addValidator($emailValidator);

        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            array('HtmlTag', array('tag' => 'div')),
        ));

        // Add search button
        $this->addElement('submit', 'search', array('label' => 'Search'));
    }
}