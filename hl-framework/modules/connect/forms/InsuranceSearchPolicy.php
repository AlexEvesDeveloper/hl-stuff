<?php

class Connect_Form_InsuranceSearchPolicy extends Zend_Form {

    /**
     * Create insurance policy search form.
     *
     * @return void
     */
    public function init() {

        $this->setMethod('get');

        // Add reference number element
        $this->addElement('text', 'campRefNo', array(
            'label'      => 'Reference Number',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^\w+\/?\w+$/',
                        'messages' => 'Reference Number must contain alphanumeric characters and an optional forward slash'
                    )
                )
            )
        ));

        // Add policy number element
        $this->addElement('text', 'policyNo', array(
            'label'      => 'Policy Number',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^\w+\/?\w+$/',
                        'messages' => 'Policy Number must contain alphanumeric characters and an optional forward slash'
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

        // Add line 3 property address element
        $this->addElement('text', 'address3', array(
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

        // Add payment ref element
        $this->addElement('text', 'paymentRef', array(
            'label'      => 'Payment Ref',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^\w+\/?\w+$/',
                        'messages' => 'Payment Ref must contain alphanumeric characters and an optional forward slash'
                    )
                )
            )
        ));

        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            array('HtmlTag', array('tag' => 'div')),
        ));

        // Add search button
        $this->addElement('submit', 'search', array('label' => 'Search'));
    }
}