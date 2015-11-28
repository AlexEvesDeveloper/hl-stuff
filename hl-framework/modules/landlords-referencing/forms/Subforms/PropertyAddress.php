<?php
class LandlordsReferencing_Form_Subforms_PropertyAddress extends Zend_Form_SubForm
{
    public function init()
    {
        // Add house number/name element
        $this->addElement('hidden', 'property_number_name', array(
            'required'  => false,
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        // Add postcode element
        $this->addElement('text', 'ins_property_postcode', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a postcode',
                            'notEmptyInvalid' => 'Please enter a valid postcode'
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
        $this->addElement('select', 'property_address', array(
            'label'     => 'Please select your address',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- please select ---'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the property address',
                            'notEmptyInvalid' => 'Please select the property address'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/property-address.phtml'))
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
    }

    public function isValid($formData = array())
    {
        if ((isset($formData['ins_property_postcode']) && trim($formData['ins_property_postcode']) != '')) {
            $postcode = trim($formData['ins_property_postcode']);
            $postcodeLookup = new Manager_Core_Postcode();
            $addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $postcode));
            $addressList = array('' => '--- please select ---');
            if (isset($postcode) && preg_match('/^IM|^GY|^JE/i', $postcode)) {
                return;
            }
            else {
                foreach($addresses as $address) {
                    $addressList[$address['id']] = $address['singleLineWithoutPostcode'];
                }
            }
            $ins_address = $this->getElement('property_address');
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
        if (isset($formData['ins_property_postcode'])) {
            $this->getElement('property_number_name')->setRequired(false);
        }

        return parent::isValid($formData);
    }
}