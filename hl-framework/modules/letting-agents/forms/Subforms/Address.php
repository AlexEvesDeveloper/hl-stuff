<?php
/**
* Class definition for the form elements in the subform Campaign
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_Address extends Zend_Form_SubForm
{
   /**
     * 
     *
     * @return void
     */
    public function init()
    {
        // Add postcode element
        $this->addElement('text', 'postcode', array(
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
            	
        // Add address select element
        $this->addElement('select', 'address', array(
            'label'     => 'Please select your address',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- please select ---'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your address',
                            'notEmptyInvalid' => 'Please select your address'
                        )
                    )
                )
            )
        ));
        $this->getElement('address')->setRegisterInArrayValidator(false);  
        // Add address text boxes
        $this->addElement('text', 'address_line1', array(
            'label'      => 'Address',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
        ));
        $this->addElement('text', 'address_line2', array(
            'label'      => '',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
        ));
		$this->addElement('text', 'address_line3', array(
            'label'      => '',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
        ));
		$this->addElement('text', 'address_postcode', array(
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
            array('ViewScript', array('viewScript' => 'subforms/address.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }

    public function isValid($formData = array()) {
		// If a postcode is present, look it up and populate the allowed values of the associated dropdown
        if ((isset($formData['postcode']) && trim($formData['postcode']) != '')) {
            $postcode = trim($formData['postcode']);
            $postcodeLookup = new Manager_Core_Postcode();
            $addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $postcode));
            $addressList = array('' => '--- please select ---');
            foreach($addresses as $address) {
                $addressList[$address['id']] = $address['singleLineWithoutPostcode'];
            }

            $address = $this->getElement('address');
            $address->setMultiOptions($addressList);
            $validator = new Zend_Validate_InArray(array(
                'haystack' => array_keys($addressList)
            ));
            $validator->setMessages(array(
                Zend_Validate_InArray::NOT_IN_ARRAY => 'Address does not match with postcode'
            ));
            $address->addValidator($validator, true);
        }

        // If a value for an address lookup is present, the house name or number is not required
        if (isset($formData['address_postcode']) && $formData['address_postcode'] != '') {
	        $this->getElement('postcode')->setRequired(false);
	        $this->getElement('address')->setRequired(false);
        }	

        // Call original isValid()
        return parent::isValid($formData);

    }
}
?>