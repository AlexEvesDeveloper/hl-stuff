<?php

class TenantsInsuranceQuote_Form_Subforms_InsuredAddress extends Zend_Form_SubForm
{
    /**
     * Create insured address subform
     *
     * @return void
     */
    public function init()
    {
        // Add house number/name element
        $this->addElement('hidden', 'ins_house_number_name', array(
            'label'     => '',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a house number or name',
                            'notEmptyInvalid' => 'Please enter a house number or name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-\/]{1,}$/i',
                        'messages' => 'House number or name must contain at least one alphanumeric character and only basic punctuation (space, hyphen and forward slash)'
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));

        // Add postcode element
        $this->addElement('text', 'ins_postcode', array(
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
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'postcode',
                'class' => 'form-control',
            )
        ));

        // Add address select element
        $this->addElement('select', 'ins_address', array(
            'label'     => 'Please select your address',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- please select ---'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your insured address',
                            'notEmptyInvalid' => 'Please select your insured address'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));

        // Add address text boxes
        $this->addElement('text', 'ins_address_line1', array(
            'label'      => '',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'ins_address_line2', array(
            'label'      => '',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'ins_address_line3', array(
            'label'      => '',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'ins_address_postcode', array(
            'label'      => '',
            'required'   => true,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
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
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/insured-address.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Grab view and add the address lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/common/js/addressLookup.js',
            'text/javascript'
        );
        $view->headScript()->appendFile(
            '/assets/tenants-insurance-quote/js/addressLookupTenants.js',
            'text/javascript'
        );
    }

    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
    public function isValid($formData = array()) {

        // If a postcode is or was present, look it up and populate the allowed values of the associated dropdown
        if ((isset($formData['ins_postcode']) && trim($formData['ins_postcode']) != '')) {
            $postcode = trim($formData['ins_postcode']);
            $postcodeLookup = new Manager_Core_Postcode();
            $addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $postcode));
            $addressList = array('' => '--- please select ---');
            foreach($addresses as $address) {
                $addressList[$address['id']] = $address['singleLineWithoutPostcode'];
            }

            $ins_address = $this->getElement('ins_address');
            $ins_address->setMultiOptions($addressList);
            $validator = new Zend_Validate_InArray(array(
                'haystack' => array_keys($addressList)
            ));
            $validator->setMessages(array(
                Zend_Validate_InArray::NOT_IN_ARRAY => 'Insured address does not match with postcode'
            ));
            $ins_address->addValidator($validator, true);
        }
        
        // If a value for an address lookup is present, the house name or number is not required
        if (isset($formData['ins_postcode'])) {
            $this->getElement('ins_house_number_name')->setRequired(false);
        }
        
        // Call original isValid()
        return parent::isValid($formData);

    }
}