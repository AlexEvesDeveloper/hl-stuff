<?php

class Connect_Form_Subforms_ReferencingCompanyApplication_CompanyDetails extends Zend_Form_SubForm {
    /**
     * Create company details subform
     *
     * @return void
     */
    public function init() {

        // Add company name element
        $this->addElement('text', 'company_name', array(
            'label'      => 'Company name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the company name',
                            'notEmptyInvalid' => 'Please enter the company name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'Company name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add company trading name element
        $this->addElement('text', 'company_tradingname', array(
            'label'      => 'Trading name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the company trading name',
                            'notEmptyInvalid' => 'Please enter the company trading name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'Company trading name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add company registration number element
        $this->addElement('text', 'company_registration', array(
            'label'      => 'Registration number',
            'required'   => true,
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the company registration number',
                            'notEmptyInvalid' => 'Please enter the company registration number'
                        )
                    )
                )
            )
        ));

        // Add company date of incorporation element
        $this->addElement('text', 'company_incorporation', array(
            'label'     => 'Date of incorporation (dd/mm/yyyy)',
            'required'  => true,
            'filters'    => array('StringTrim')
        ));
        $company_incorporation = $this->getElement('company_incorporation');
        $validator = new Zend_Validate_DateCompare();
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 1);
        $validator->setMessages(array(
            'msgMaximum' => 'Date of incorporation cannot be in the future'
        ));
        $company_incorporation->addValidator($validator, true);

        // Add company contact name element
        $this->addElement('text', 'company_contactname', array(
            'label'      => 'Contact name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a company contact name',
                            'notEmptyInvalid' => 'Please enter a company contact name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'Company contact name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add company telephone element
        $this->addElement('text', 'company_phone', array(
            'label'      => 'Telephone (inc STD)',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the company phone number'
                        )
                    )
                ),
                array(
                    'TelephoneNumber'
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'referencing/subforms/company-application-companydetails.phtml'))
        ));
		
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }

}