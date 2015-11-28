<?php
class Form_PortfolioInsuranceQuote_insuredAddress extends Zend_Form {
    /**
     * Pull in the sub forms that comprise Portfolio add property
     *
     * @return void
     */
    public function init()
    {
        $this->addElement('hidden','covercount', array(
            'required'  => true,
        ));
        
        $this->addElement('hidden','propertyid', array(
            'required'  => false,
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
                'data-ctfilter' => 'yes'
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
                'data-ctfilter' => 'yes'
            )
        ));

        // Add address text boxes
        $this->addElement('text', 'ins_address_line1', array(
            'label'      => '',
            'required'   => false,
            'readonly'     => 'readonly',
            'class'         => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'ins_address_line2', array(
            'label'      => '',
            'required'   => false,
            'readonly'     => 'readonly',
            'class'         => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'ins_address_line3', array(
            'label'      => '',
            'required'   => false,
            'readonly'     => 'readonly',
            'class'         => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'ins_address_postcode', array(
            'label'      => '',
            'required'   => false,
            'readonly'     => 'readonly',
            'class'         => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        // Add Employment status element
        $this->addElement('select', 'employment_status', array(
            'label'     => 'Tenant status',
            'required'  => true,
            // TODO: Remove the hard coded options
            'multiOptions' => array(
                '' => 'Please Select',
                'Employed' => 'Employed',
                'Unemployed' => 'Unemployed',
                'DSS with AST' => 'DSS with AST',
                'DSS' => 'DSS without AST',
                'Student' => 'Student',
                'Self Employed' => 'Self Employed',
                'Retired' => 'Retired',
                'Unknown' => 'Unknown'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select tenants occupation status',
                            'notEmptyInvalid' => 'Please select tenants occupation status'
                        )
                    )
                )
            )
        ));
        
        $this->employment_status->setAttrib('onchange','employmentChange();');
        
        // Now they want a checkbox for Comprehensive Buildings Insurance
        // This will be a dummy field sor javascript selection purposes only
        // 
        $this->addElement('checkbox', 'comprehensive_buildings_insurance', array(
            'required'      => false,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Comprehensive Buildings Insurance'
                        )
                    )
                )
            )
        ));
        
        // Add Comprehensive Buildings Insurance element
        $this->addElement('text', 'buildings_cover', array(
            'label'     => 'Rebuild value',
            'attribs'     => array(
                                'class'=>'currency'
                            ),
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a rebuild value for this building',
                            'notEmptyInvalid' => 'Please enter a valid rebuild value for this building'
                        )
                    )
                )
            )
        ));
        
        // Add Accidental damage element
        $this->addElement('checkbox', 'buildings_accidental_damage', array(
            'required'      => false,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Accidental damage'
                        )
                    )
                )
            )
        ));
        
        // Add Buildings nil excess element
        $this->addElement('checkbox', 'buildings_nil_excess', array(
            'required'      => false,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Buildings nil excess'
                        )
                    )
                )
            )
        ));
        
        // Add Full Contents  Insurance element
        $this->addElement('text', 'contents_cover', array(
            'label'     => 'Contents value',
            'attribs'     => array(
                                'class'=>'currency'
                            ),        
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Full Contents Insurance',
                            'notEmptyInvalid' => 'Full Contents Insurance'
                        )
                    ),
                    'Between', true, array(
                        'min' => 0,
                        'max' => 20000,
                        'messages' => array(
                            'notBetween' => 'Value must be Less than 20000',
                        )
                    )
                )
            )
        ));
        
        $this->contents_cover->setAttrib('onchange','contentsChange();');
        // Add Contents Accidental damage element
        $this->addElement('checkbox', 'contents_accidental_damage', array(
            'required'      => false,
            'value'         => '0',
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Accidental damage'
                        )
                    )
                )
            )
        ));
        
        // Add Contents nil excess element
        $this->addElement('checkbox', 'contents_nil_excess', array(
            'required'      => false,
            'value'         => '0',
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Contents nil excess'
                        )
                    )
                )
            )
        ));
        
        // Add Limited Contents  element
        $this->addElement('checkbox', 'limited_contents', array(
            'required'      => false,
            'unCheckedValue' => '0', // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Contents Insurance for Unfurnished Properties'
                        )
                    )
                )
            )
        ));
        
         
        // Now they want a checkbox for Full Contents Insurance
        // This will be a dummy field sor javascript selection purposes only
        //
        $this->addElement('checkbox', 'full_contents_insurance', array(
            'required'      => false,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Full Contents Insurance'
                        )
                    )
                )
            )
        ));         
        
        /*
        $this->addElement('hidden', 'action', array(
            'label'         => '',
            'required'      => true,
            'value'         => "add",
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'action'
                        )
                    )
                )
            )
        ));
        */
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/insured-address.phtml'))
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
        $coverCount = 0;
        if (isset($formData['buildings_accidental_damage']) && $formData['buildings_accidental_damage'] == 1 ){
            $coverCount++;
                $this->getElement('buildings_cover')->setRequired(true);
        }
        
        if (isset($formData['buildings_nil_excess']) && $formData['buildings_nil_excess'] == 1 ){
            $coverCount++;
            $this->getElement('buildings_cover')->setRequired(true);
        }
        
        if (isset($formData['contents_accidental_damage']) && $formData['contents_accidental_damage'] == 1 ){
            $coverCount++;
                $this->getElement('contents_cover')->setRequired(true);
        }
        
        if (isset($formData['contents_nil_excess']) && $formData['contents_nil_excess'] == 1 ){
            $coverCount++;
            $this->getElement('contents_cover')->setRequired(true);
        }
        
        if (isset($formData['limited_contents']) && $formData['contents_cover'] > 0 ){
            $coverCount++;
            $limitedContents = $this->getElement('limited_contents');
            $limitedContentsValidator = new Zend_Validate_LessThan(array('max' => 1));
            $limitedContentsValidator->setMessage(
                            '&nbsp;Limited Contents cannot be selected with Full Contents',
                            Zend_Validate_LessThan::NOT_LESS);
            $limitedContents->addValidator($limitedContentsValidator, true);
        }
        
        if ((isset($formData['limited_contents']) && $formData['limited_contents'] == 1)|| (isset($formData['contents_cover']) && ($formData['contents_cover'] > 0) || (isset($formData['buildings_cover']) && $formData['buildings_cover'] > 0)) ) {
            $coverCount++;
        }
        
        // Set up validation for Building cover maximun
        // value can be empty OR Greater Than 50k and refer after 1000k
        $buildingsCover = $this->getElement('buildings_cover');
        $buildingsValidator = new Zend_Validate_GreaterThan(array('min' => 49999));
        $buildingsValidator->setMessage('Buildings cover must be over £50000',Zend_Validate_GreaterThan::NOT_GREATER);
        $buildingsCover->addValidator($buildingsValidator, true);
        
        // Set up validation for contents cover maximun
        // value can be empty OR Greater Than 10k and refer after 60k
        $contentsCover = $this->getElement('contents_cover');
        $contentsValidator = new Zend_Validate_GreaterThan(array('min' => 9999));
        $contentsValidator->setMessage('Contents cover must be over £10000',Zend_Validate_GreaterThan::NOT_GREATER);
        $contentsCover->addValidator($contentsValidator, true);
        
        $formData['covercount'] = $coverCount;
        $covers = $this->getElement('covercount');
        
        $optionsValidator  = new Zend_Validate_GreaterThan(array('min' => 0));
        $optionsValidator->setMessage(
                            "You have not selected a cover",
                            Zend_Validate_GreaterThan::NOT_GREATER);
        
        $covers->addValidator($optionsValidator,true);
        
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
        
        if (isset($formData['ins_address_postcode']) && $formData['ins_address_postcode'] != '') {
            $this->getElement('ins_postcode')->setRequired(false);
            $this->getElement('ins_address')->setRequired(false);
        }
        
        // Call original isValid()
        return parent::isValid($formData);
    }
    
    /**
     * Returns errors flattened into a 2d array
     *
     * @return array
     */
    public function getMessagesFlattened() {
        return $this->getMessages();
    }
}
?>
