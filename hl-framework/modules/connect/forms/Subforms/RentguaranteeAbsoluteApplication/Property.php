<?php

class Connect_Form_Subforms_RentguaranteeAbsoluteApplication_Property extends Zend_Form_SubForm {
    /**
     * Create property subform
     *
     * @return void
     */
    public function init() {

    	// Add managed element
    	$this->addElement('select', 'property_managed', array(
    	                            'label'     => 'Property let type',
    	                            'required'  => true,
    	                            'multiOptions' => array(
    	                        	'' => '--- Please select ---',
    					'1' => 'Let Only',
    	                                '2' => 'Managed',            
    	                        	'3' => 'Rent Collect',                
    	
    	),
    	                            'separator' => '',
    	                            'validators' => array(
    	array(
    	                                    'NotEmpty', true, array(
    	                                        'messages' => array(
    	                                            'isEmpty' => 'Please select a property let type',
    	                                            'notEmptyInvalid' => 'Please select a valid property let type'
    					)
    				)
    			)
    		)
    	));
    	
    	// Add managed element
    	$this->addElement('select', 'how_rg_offered', array(
    	                                    'label'     => 'How is Rent Guarantee offered to your landlord',
    	                                    'required'  => true,
    	                                    'multiOptions' => array(
    	                                	'' => '--- Please select ---',
    	                                        '1' => 'Free of charge',            
    	                                        '2' => 'Included in Management Fees',
    	                                	'3' => 'Separate charge for Rent Guarantee to the landlord',
    	
    	),
    	                                    'separator' => '',
    	                                    'validators' => array(
    	array(
    	                                            'NotEmpty', true, array(
    	                                                'messages' => array(
    	                                                    'isEmpty' => 'Please select how Rent Guarantee is offered',
                                                    'notEmptyInvalid' => 'Please select a valid how Rent Guarantee is offered'
    					)
    				)
    			)
    		)
    	));    	
    	
        // Add house number + street address element
        $this->addElement('text', 'property_address1', array(
            'label'      => 'House Number + Street',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the house number + street address',
                            'notEmptyInvalid' => 'Please enter the house number + street address'
                        )
                    )
                )
            )
        ));

        // Add town/city address element
        $this->addElement('text', 'property_address2', array(
            'label'      => 'Town/City',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the town/city address',
                            'notEmptyInvalid' => 'Please enter the town/city address'
                        )
                    )
                )
            )
        ));

        // Add postcode element
        $this->addElement('text', 'property_postcode', array(
            'label'      => 'Postcode',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the postcode',
                            'notEmptyInvalid' => 'Please enter the postcode'
                        )
                    )
                ),
                array(
                    'Postcode'
                )
            )
        ));

        // Add the find address button
        $this->addElement('submit', 'property_address_lookup', array(
            'ignore'    => true,
            'label'     => 'Find address',
            'class'     => 'button'
        ));

        $this->addElement('select', 'property_address', array(
            'required' => false,
            'label' => '',
            'filters' => array('StringTrim'),
            'class' => 'postcode_address',
            'multiOptions' => array('' => 'Please select'),
            'validators' => array(
                array (
                    'NotEmpty',
                    true,
                    array(
                        'messages' => array(
                            'isEmpty' => 'Please select property address',
                            'notEmptyInvalid' => 'Please select property address'
                        )
                    )
                )
            )
        ));

        // Remove 'nnn not found in haystack' error
        $this->getElement('property_address')->setRegisterInArrayValidator(false);

        // Add rental element
        $this->addElement('text', 'property_rental', array(
            'label'      => 'Monthly Rental Amount',
            'required'   => true,
            'attribs'   => array(
                'class' => 'currency'
            ),
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the monthly rent',
                            'notEmptyInvalid' => 'Please enter the monthly rent'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d{1,}$/',
                        'messages' => 'Amount of monthly rent must contain at least one digit'
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 0,
                        'messages' => 'Monthly rent must be above zero'
                    )
                )
            )
        ));

        // Add tenancy start date element
        $this->addElement('text', 'tenant_startdate', array(
            'label'     => 'Tenancy start date (dd/mm/yyyy)',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Tenancy start date can not be empty',
                            'notEmptyInvalid' => 'Please enter a valid Tenancy start date'
                        )
                    )
                )
			)
        ));
        $tenant_startdate = $this->getElement('tenant_startdate');
        $validator = new Zend_Validate_DateCompare();
        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 60 * 60 * 24);
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 60 * 60 * 24 * 365);
        $validator->setMessages(array(
            'msgMinimum' => 'Tenancy start date too far in the past',
            'msgMaximum' => 'Tenancy start date too far in the future'
        ));
        $tenant_startdate->addValidator($validator, true);

        // Add policy start date element
        $this->addElement('text', 'policy_startdate', array(
            'label'     => 'Policy start date (dd/mm/yyyy)',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Policy start date can not be empty',
                            'notEmptyInvalid' => 'Please enter a valid Policy start date'
                        )
                    )
                )
			)
        ));
        $policy_startdate = $this->getElement('policy_startdate');
        $validator = new Zend_Validate_DateCompare();
        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 60 * 60 * 24);
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 60 * 60 * 24 * 365);
        $validator->setMessages(array(
            'msgMinimum' => 'Policy start date too far in the past',
            'msgMaximum' => 'Policy start date too far in the future'
        ));
        $policy_startdate->addValidator($validator, true);

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguarantee/subforms/absolute-application-property.phtml'))
        ));

        $this->setElementFilters(array('StripTags'));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }

}
