<?php

class TenantsInsuranceQuoteB_Form_Subforms_CorrespondenceDetails extends Zend_Form_SubForm
{
    /**
     * Create correspondence details subform
     *
     * @return void
     */
    public function init()
    {
        // Add same as insured element
        $this->addElement('radio', 'cor_same_address', array(
            'label'     => 'My address is the same as the insured address',
            'required'  => true,
            'multiOptions' => array(
                'yes' => 'Yes',
                'no' => 'No'
            ),
            'separator' => '',
            'label_placement' => 'prepend',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select if your address is the same as the insured address',
                            'notEmptyInvalid' => 'Please select if your address is the same as the insured address'
                        )
                    )
                )
            )
        ));

        // Add house number/name element
        $this->addElement('hidden', 'cor_house_number_name', array(
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
        $this->addElement('text', 'cor_postcode', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a correspondence address postcode',
                            'notEmptyInvalid' => 'Please enter a correspondence address postcode'
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

        // Add address select element
        $this->addElement('select', 'cor_address', array(
            'label'     => 'Please select your address',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- please select ---'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your correspondence address',
                            'notEmptyInvalid' => 'Please select your correspondence address'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/correspondence-details.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        

        // Grab view and add the address lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/tenants-insurance-quote-b/js/addressLookup.js',
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
        
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        
        // If correspondence address is set to be the same as insured address, copy the address values across
        if (isset($formData['cor_same_address']) && $formData['cor_same_address'] == 'yes') {
            // NOTE: THIS BIT OF CODE MEANS BOTH ADDRESS SUBFORMS MUST APPEAR ON THE SAME PAGE
            $formData['cor_house_number_name'] = $formData['ins_house_number_name'];
            $formData['cor_postcode'] = $formData['ins_postcode'];
            $formData['cor_address'] = $formData['ins_address'];
            
            $findPostcode = $formData['ins_postcode'];
        }
        
        // If a postcode is present, look it up and populate the allowed values of the associated dropdown
        if ((isset($formData['cor_postcode']) && trim($formData['cor_postcode']) != '')) {
            $postcode = trim($formData['cor_postcode']);
            $postcodeLookup = new Manager_Core_Postcode();
            $addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $postcode));
            $addressList = array('' => '--- please select ---');
            foreach($addresses as $address) {
                $addressList[$address['id']] = $address['singleLineWithoutPostcode'];
            }
            
            $cor_address = $this->getElement('cor_address');
            $cor_address->setMultiOptions($addressList);
            $validator = new Zend_Validate_InArray(array(
                'haystack' => array_keys($addressList)
            ));
            $validator->setMessages(array(
                Zend_Validate_InArray::NOT_IN_ARRAY => 'Correspondence address does not match with postcode'
            ));
            $cor_address->addValidator($validator, true);
        }
        
        // If a value for an address lookup is present, the house name or number is not required
        if (isset($formData['cor_postcode'])) {
            $this->getElement('cor_house_number_name')->setRequired(false);
        }
        
        // Call original isValid()
        return parent::isValid($formData);
        
    }
}