<?php

class Connect_Form_Subforms_ReferencingCompanyApplication_Property extends Zend_Form_SubForm {
    /**
     * Create property subform
     *
     * @return void
     */
	public function init() {   	
        // Add address element
        $this->addElement('text', 'property_address', array(
            'label'      => 'Address',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the company address',
                            'notEmptyInvalid' => 'Please enter the company address'
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
                            'isEmpty' => 'Please enter the company postcode',
                            'notEmptyInvalid' => 'Please enter the company postcode'
                        )
                    )
                ),
                array(
                    'Postcode'
                )
            )
        ));
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
                                            'isEmpty' => 'Please select a property type',
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
        								'4' => "I'm not purchasing Rent Guarantee",
        
        ),
                                    'separator' => '',
                                    'validators' => array(
        array(
                                            'NotEmpty', true, array(
                                                'messages' => array(
                                                    'isEmpty' => 'Please select a rent guarantee offer',
                                                    'notEmptyInvalid' => 'Please select a valid rent guarantee offer'
        			)
        		)
        	)
      	  )
        ));        
        // Add total rent element
        $this->addElement('text', 'tenant_renttotal', array(
            'label'      => 'Total rent for this property (per month)',
            'required'   => true,
            'attribs'   => array(
                'class' => 'currency'
            ),
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the total rent',
                            'notEmptyInvalid' => 'Please enter the total rent'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d{1,}$/',
                        'messages' => 'Amount of total rent must contain at least one digit'
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 0,
                        'messages' => 'Total rent must be above zero'
                    )
                )
            )
        ));

        // Add applicant share of rent element
        $this->addElement('text', 'tenant_rentshare', array(
            'label'      => 'Applicant share of rent (per month)',
            'required'   => true,
            'attribs'   => array(
                'class' => 'currency'
            ),
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the share of rent',
                            'notEmptyInvalid' => 'Please enter the share of rent'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d{1,}$/',
                        'messages' => 'Share of rent must contain at least one digit'
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 0,
                        'messages' => 'Share of rent must be above zero'
                    )
                )
            )
        ));

        // Add tenancy term element
        $this->addElement('text', 'tenant_term', array(
            'label'      => 'Tenancy term (months)',
            'required'   => true,
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the tenancy term',
                            'notEmptyInvalid' => 'Please enter the tenancy term'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d{1,}$/',
                        'messages' => 'Tenancy term must contain at least one digit'
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 0,
                        'messages' => 'Tenancy term must be above zero'
                    )
                )
            )
        ));

        // Add tenancy start date element
        $this->addElement('text', 'tenant_startdate', array(
            'label'     => 'Tenancy start date (dd/mm/yyyy)',
            'required'  => true,
            'filters'    => array('StringTrim')
        ));
        $tenant_startdate = $this->getElement('tenant_startdate');
        $validator = new Zend_Validate_DateCompare();
        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 60 * 60 * 24 * 365);
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 60 * 60 * 24 * 365);
        $validator->setMessages(array(
            'msgMinimum' => 'Tenancy start date too far in the past',
            'msgMaximum' => 'Tenancy start date too far in the future'
        ));
        $tenant_startdate->addValidator($validator, true);

        // Add number of tenants element
        $this->addElement('text', 'tenant_number', array(
            'label'      => 'Total number of tenants to be referenced for the property',
            'required'   => true,
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the tenant amount',
                            'notEmptyInvalid' => 'Please enter the tenant amount'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d{1,}$/',
                        'messages' => 'Tenant amount must contain at least one digit'
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 0,
                        'messages' => 'Tenant amount must be above zero'
                    )
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'referencing/subforms/company-application-property.phtml'))
        ));
        
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

    }

}
