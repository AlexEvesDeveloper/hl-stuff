<?php

class LandlordsInsuranceQuote_Form_Subforms_InsuredAddress extends Zend_Form_SubForm
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
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z]{2,}\ ?[0-9a-z]{2,}$/i', // TODO: temporary regex, needs to use postcode validator once available
                        'messages' => ''
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        // Add owned time select element
        $this->addElement('select', 'owned_for', array(
            'label'     => 'How long have you owned this property?',
            'required'  => true,
            'multiOptions' => array(
                ''  => '--- please select ---',
                '1' => 'less than 1 year',
                '2' => '1 year',
                '3' => '2 years',
                '4' => '3 years',
                '5'    => 'more than 3 years'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tell us how long you\'ve owned your property',
                            'notEmptyInvalid' => 'Please tell us how long you\'ve owned your property'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
        
        // Add no claims select element
        $this->addElement('select', 'no_claims', array(
            'label'     => 'How many years no claims do you have on this property?',
            'required'  => true,
            'multiOptions' => array(
                ''  => '--- please select ---',
                '1' => 'less than 1 year',
                '2' => '1 year',
                '3' => '2 years',
                '4' => '3 years',
                '5'    => 'more than 3 years'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tell us how many years no claims you have on this property',
                            'notEmptyInvalid' => 'Please tell us how many years no claims you have on this property'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
        
        // Add tenants type select element
        $this->addElement('select', 'tenants_type', array(
            'label'     => 'What type of tenants are living in your property?',
            'required'  => true,
            'multiOptions' => array(
                ''                  => '--- please select ---',
                '1'          => 'Employed',
                '2'     => 'Self-employed',
                '3'           => 'Student',
                '4'           => 'Retired',
                '5'        => 'Unemployed',
                '6' => 'Tenant\'s claiming benefit',
                '7' => 'Property let to housing authority',		//DO NOT CHANGE THIS VALUE - B.V.
                '8'           => 'Unknown'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tell us what type of tenants are living in your property',
                            'notEmptyInvalid' => 'Please tell us what type of tenants are living in your property'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        // Add use (managed through) letting agent select element
        $this->addElement('radio', 'through_letting_agent', array(
            'label'     => 'Is your property managed through a letting agent?',
            'required'  => true,
            'multiOptions' => array(
                'yes'   => 'Yes',
                'no'    => 'No'
            ),
            'separator' => '',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tell us if a letting agent manages the property to be insured',
                            'notEmptyInvalid' => 'Please tell us if a letting agent manages the property to be insured'
                        )
                    )
                )
            )
        ));

        // Add exclude flood cover element
        $this->addElement('select', 'exclude_flood_cover', array(
            'label'     => 'Your property is in a flood risk area, would you like flood cover?',
            'required'  => true,
            'multiOptions' => array(
                ''      => '--- please select ---',
                'yes'   => 'Yes',
                'no'    => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tell us if you require flood cover or not',
                            'notEmptyInvalid' => 'Please tell us if you require flood cover or not'
                        )
                    )
                )
            )
        ));


        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/insured-address.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Strip all tags to prevent XSS errors - done iteratively so not to overwrite any existing filters
        foreach($this->getElements() as $element) {
            $element->addFilter('StripTags');
        }

        // Grab view and add the address lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/common/js/addressLookup.js',
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
    /*public function isValid($formData = array()) {

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

            $landlordsRiskAreas = new Datasource_Insurance_LandlordsPlus_RiskAreas();
            $landlordsRiskAreas = $landlordsRiskAreas->getByPostcode($formData['ins_address_postcode']);

            // Flood risk validation
            if ($landlordsRiskAreas['floodArea']!='600') {
                $this->getElement('exclude_flood_cover')->setRequired(false);
            } else {
                $this->getElement('exclude_flood_cover')->setRequired(false);
            }
        }

        if (isset($formData['ins_postcode'])) {
            $this->getElement('ins_address')->setRequired(false);
            $formData['ins_address'] = null;
        }

        // Call original isValid()
        return parent::isValid($formData);

    }*/

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

            $landlordsRiskAreas = new Datasource_Insurance_LandlordsPlus_RiskAreas();
            try {
                $landlordsRiskAreas = $landlordsRiskAreas->getByPostcode($formData['ins_postcode']);
            }
            catch (Datasource_Exception_PostcodeNotFoundException $e) {
                // Catch postcode not found exception and rethrow chained risk area not found exception
                throw new LandlordsInsuranceQuote_Form_Exception_RiskAreaNotFoundException(
                    $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }

            if ($landlordsRiskAreas['floodArea']!='600') {
                $this->getElement('exclude_flood_cover')->setRequired(false);
            }
        } else {
            $this->getElement('exclude_flood_cover')->setRequired(false);
        }

        if (isset($formData['ins_postcode'])) {
            $this->getElement('ins_address')->setRequired(false);
            $formData['ins_address'] = null;
        }

        // Call original isValid()
        return parent::isValid($formData);

    }

}
