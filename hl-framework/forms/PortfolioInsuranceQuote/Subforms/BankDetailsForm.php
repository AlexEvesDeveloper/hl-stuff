<?php
class Form_PortfolioInsuranceQuote_Subforms_BankDetailsform extends Zend_Form_SubForm
{
    /**
     * Create bank address subform
     *
     * @return void
     */
    public function init(){
        // Property bank interest is applied to
        $this->addElement('select', 'bank_property', array(
            'label'     => 'Property Applied to',
            'required'  => true,
            'multiOptions' => array(
                '' => 'Please Select',
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the property',
                            'notEmptyInvalid' => 'Please select select the property'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        // Add Bank Name
        $this->addElement('text', 'bank_name', array(
            'label'     => 'Bank name',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the name of the bank bank that has interest',
                            'notEmptyInvalid' => 'Please enter the name of the bank bank that has interest'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-]{1,}$/i',
                        'messages' => 'Bank name must contain at least one alphanumeric character and only basic punctuation (space and hyphen)'
                    )
                )
            )
        ));
        
        // Add Bank account number
        $this->addElement('text', 'bank_account_number', array(
            'label'     => 'Bank account number',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a valid bank account number',
                            'notEmptyInvalid' => 'Please enter a valid bank account number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-]{1,}$/i',
                        'messages' => 'Account Number must contain at least one alphanumeric character and only basic punctuation (space and hyphen)'
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        // Add house number/name element
        $this->addElement('text', 'bank_address_line1', array(
            'label'     => 'Bank Address',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the 1st line of the bank address',
                            'notEmptyInvalid' => 'Please enter the 1st line of the bank address'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-]{1,}$/i',
                        'messages' => 'Address must contain at least one alphanumeric character and only basic punctuation (space and hyphen)'
                    )
                )
            )
        ));
        
        // Add house number/name element
        $this->addElement('text', 'bank_address_line2', array(
            'label'     => '',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the 2nd line of the bank address',
                            'notEmptyInvalid' => 'Please enter the 2nd line of the bank address'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-]{1,}$/i',
                        'messages' => 'Address must contain at least one alphanumeric character and only basic punctuation (space and hyphen)'
                    )
                )
            )
        ));
        
        // Add house number/name element
        $this->addElement('text', 'bank_address_line3', array(
            'label'     => '',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the 3rd line of the bank address',
                            'notEmptyInvalid' => 'Please enter the 3rd line of the bank address'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-]{1,}$/i',
                        'messages' => 'Address must contain at least one alphanumeric character and only basic punctuation (space and hyphen)'
                    )
                )
            )
        ));
        
        // Add house number/name element
        $this->addElement('text', 'bank_address_line4', array(
            'label'     => '',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the 4th line of the bank address',
                            'notEmptyInvalid' => 'Please enter the 4th line of the bank address'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-]{1,}$/i',
                        'messages' => 'Addressmust contain at least one alphanumeric character and only basic punctuation (space and hyphen)'
                    )
                )
            )
        ));
        
        // Add postcode element
        $this->addElement('text', 'bank_postcode', array(
            'label'      => 'Postcode',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter an insured address postcode',
                            'notEmptyInvalid' => 'Please enter an insured address postcode'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z]{2,}\ ?[0-9a-z]{2,}$/i', // TODO: temporary regex, needs to use postcode validator once available
                        'messages' => 'Postcode must be in postcode format'
                    )
                )
            )
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/subforms/bank-details-form.phtml'))
        ));
    }
    
    public function isValid($postData) {
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $customerReferenceNumber = $pageSession->CustomerRefNo;
        $propertyManager = new Manager_Insurance_Portfolio_Property();
        $propertyObjects = $propertyManager->fetchAllProperties($customerReferenceNumber);
        $propertyArray = $propertyObjects->toArray();
        $optionList = array('' => '--- please select ---');
        
        foreach($propertyArray as $property){
            $optionList[$property['id']] =
                    //    ($property['houseNumber']) ." ".
                    //    ($property['building'])  ." ".
                        ($property['address1'])  ." ".
                        ($property['address2'])  ." ".
                        ($property['address3'])  ." ".
                    //    ($property['address4'])  ." ".
                    //    ($property['address5'])  ." ".
                        ($property['postcode']);
            }
        
        // Get the subfoem element for property address that the bank may have interest in
        $propertyAddressSelect = $this->getElement('bank_property');
        $propertyAddressSelect->setMultiOptions($optionList);
        
        $validator = new Zend_Validate_InArray(array(
            'haystack' => array_keys($optionList)
        ));
        $validator->setMessages(array(
            Zend_Validate_InArray::NOT_IN_ARRAY => 'Property not in list'
        ));
        $propertyAddressSelect->addValidator($validator, true);
        
        return parent::isValid($postData);
    }
}
?>