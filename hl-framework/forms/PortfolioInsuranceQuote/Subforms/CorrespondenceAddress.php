<?php
class Form_PortfolioInsuranceQuote_Subforms_CorrespondenceAddress extends Zend_Form_SubForm
{
    /**
     * Create insured address subform
     *
     * @return void
     */
    public function init()
    {
        // Add house number/name element
        $this->addElement('text', 'cor_house_number_name', array(
            'label'     => 'House number or name',
            'required'  => true,
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
                        'pattern' => '/^[0-9a-z\ \-]{1,}$/i',
                        'messages' => 'House number or name must contain at least one alphanumeric character and only basic punctuation (space and hyphen)'
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
                            'isEmpty' => 'Please enter a postcode',
                            'notEmptyInvalid' => 'Please enter a postcode'
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
            'required'  => false,
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
        
        
       // Add address text boxes
        $this->addElement('text', 'cor_address_line1', array(
            'label'      => '',
            'required'   => false,
            'readonly'     => 'readonly',
            'class'         => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'cor_address_line2', array(
            'label'      => '',
            'required'   => false,
            'readonly'     => 'readonly',
            'class'         => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'cor_address_line3', array(
            'label'      => '',
            'required'   => false,
            'readonly'     => 'readonly',
            'class'         => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'cor_address_postcode', array(
            'label'      => '',
            'required'   => false,
            'readonly'     => 'readonly',
            'class'         => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/subforms/correspondence-address.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Grab view and add the address lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
    }
    
    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
    public function isValid($formData = array())
    {
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
