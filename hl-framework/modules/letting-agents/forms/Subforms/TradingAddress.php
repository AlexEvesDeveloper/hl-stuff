<?php
class LettingAgents_Form_Subforms_TradingAddress extends Zend_Form_SubForm
{
    /**
     * Create insured address subform
     *
     * @return void
     */
    public function init()
    {
    	// Unique id
    	$this->addElement('hidden', 'trading_uid', array(    		
    		'required'	=> false
    	));
    	    	
        // Add house number/name element
        $this->addElement('text', 'trading_house_number_name', array(
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
            )
        ));

        // Add postcode element
        $this->addElement('text', 'trading_postcode', array(
            'label'      => 'Postcode',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a postcode for Trading Address',
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
            )
        ));

        // Add address select element
        $this->addElement('select', 'trading_address', array(
            'label'     => 'Please select your address',
            'required'  => false,
            'multiOptions' => array(
                '' => '--- please select ---'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your trading address',
                            'notEmptyInvalid' => 'Please select your trading address'
                        )
                    )
                )
            )
        ));


       // Add address text boxes
        $this->addElement('text', 'trading_address_line1', array(
            'label'      => 'Trading Address',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
        ));
        $this->addElement('text', 'trading_address_line2', array(
            'label'      => '',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
        ));
		$this->addElement('text', 'trading_address_line3', array(
            'label'      => '',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
        ));
		$this->addElement('text', 'trading_address_postcode', array(
            'label'      => 'Postcode',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
        ));

        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/trading-address.phtml'))
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
    public function isValid($formData = array()) {
	// If a postcode is present, look it up and populate the allowed values of the associated dropdown
        if ((isset($formData['trading_postcode']) && trim($formData['trading_postcode']) != '')) {
            $postcode = trim($formData['trading_postcode']);
            $postcodeLookup = new Manager_Core_Postcode();
            $addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $postcode));
            $addressList = array('' => '--- please select ---');
            foreach($addresses as $address) {
                $addressList[$address['id']] = $address['singleLineWithoutPostcode'];
            }

            $trading_address = $this->getElement('trading_address');
            $trading_address->setMultiOptions($addressList);
            $validator = new Zend_Validate_InArray(array(
                'haystack' => array_keys($addressList)
            ));
            $validator->setMessages(array(
                Zend_Validate_InArray::NOT_IN_ARRAY => 'Trading address does not match with postcode'
            ));
            $trading_address->addValidator($validator, true);
        }
        
        if (isset($formData['trading_postcode']) && $formData['trading_postcode'] != '') {
	        $this->getElement('trading_house_number_name')->setRequired(false);
        }	
        // Call original isValid()
        return parent::isValid($formData);

    }

}
