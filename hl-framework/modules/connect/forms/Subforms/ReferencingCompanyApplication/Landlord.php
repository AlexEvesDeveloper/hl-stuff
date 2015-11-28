<?php

class Connect_Form_Subforms_ReferencingCompanyApplication_Landlord extends Zend_Form_SubForm {
    /**
     * Create landlord subform
     *
     * @return void
     */
    public function init() {

        // Add prospective landlord first name element
        $this->addElement('text', 'landlord_firstname', array(
            'label'      => 'First name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the prospective landlord\'s first name',
                            'notEmptyInvalid' => 'Please enter the prospective landlord\'s first name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'Prospective landlord\'s first name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add prospective landlord last name element
        $this->addElement('text', 'landlord_lastname', array(
            'label'      => 'Last name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the prospective landlord\'s last name',
                            'notEmptyInvalid' => 'Please enter the prospective landlord\'s last name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'Prospective landlord\'s last name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add prospective landlord address element
        $this->addElement('text', 'landlord_address', array(
            'label'      => 'Address',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the prospective landlord\'s address',
                            'notEmptyInvalid' => 'Please enter the prospective landlord\'s address'
                        )
                    )
                )
            )
        ));

        // Add prospective landlord postcode element
        $this->addElement('text', 'landlord_postcode', array(
            'label'      => 'Postcode',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the prospective landlord\'s postcode',
                            'notEmptyInvalid' => 'Please enter the prospective landlord\'s postcode'
                        )
                    )
                ),
                array(
                    'Postcode'
                )
            )
        ));

        // Add prospective landlord telephone element
        $this->addElement('text', 'landlord_landlinenumber', array(
            'label'      => 'Telephone',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the prospective landlord\'s landline phone number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^((\+44\s?\(0\)\s?\d{2,4})|(\+44\s?(01|02|03|07|08)\d{2,3})|(\+44\s?(1|2|3|7|8)\d{2,3})|(\(\+44\)\s?\d{3,4})|(\(\d{5}\))|((01|02|03|07|08)\d{2,3})|(\d{5}))(\s|-|.)(((\d{3,4})(\s|-)(\d{3,4}))|((\d{6,7})))$/',
                        'messages' => 'Not a valid landline phone number'
                    )
                )
            )
        ));

        // Add prospective landlord mobile element
        $this->addElement('text', 'landlord_mobilenumber', array(
            'label'      => 'Mobile',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the prospective landlord\'s mobile phone number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^07([\d]{3})[(\D\s)]?[\d]{3}[(\D\s)]?[\d]{3}$/',
                        'messages' => 'Not a valid mobile phone number'
                    )
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'referencing/subforms/company-application-landlord.phtml'))
        ));
        
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

    }

}