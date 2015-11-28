<?php

class Connect_Form_Subforms_RentguaranteeRentRecoveryPlusApplication_Property extends Zend_Form_SubForm {
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
                ''             => '--- Please select ---', 	 
                'Let Only'     => 'Let Only', 	 
                'Managed'      => 'Managed', 	 
                'Rent Collect' => 'Rent Collect', 	 
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
        
        // Add LL permission element
        $this->addElement('radio', 'LL_Permission', array(
            'label'     => 'Where a Let Only agreement is in place you may give your Landlord permission to report and progress a claim on your behalf. Please tick this box to confirm if you have granted this permission, and that your Landlord has been made aware of, and has confirmed understanding of, the policy conditions around claiming.',
            'required'  => false,
            'multiOptions' => array(
                'yes' => 'Yes',
                'no'  => 'No'
            ),
            'separator' => ' ',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                         'isEmpty' => 'Please select answer to question: Where a Let Only agreement is in place you may give your Landlord permission to report and progress a claim on your behalf. Please tick this box to confirm if you have granted this permission, and that your Landlord has been made aware of, and has confirmed understanding of, the policy conditions around claiming.',
                         'notEmptyInvalid' => 'Please select answer to question: Where a Let Only agreement is in place you may give your Landlord permission to report and progress a claim on your behalf. Please tick this box to confirm if you have granted this permission, and that your Landlord has been made aware of, and has confirmed understanding of, the policy conditions around claiming.'
                          
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

        // Add address line 2 element
        $this->addElement('text', 'property_address2', array(
            'label'      => 'Address Line 2',
            'required'   => false,
            'filters'    => array('StringTrim')
        ));

        // Add town/city address element
        $this->addElement('text', 'property_address3', array(
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

        // Add county address element
        $this->addElement('text', 'property_address4', array(
            'label'      => 'County',
            'required'   => false,
            'filters'    => array('StringTrim'),
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
                ),
            )
        ));
        
        // Add deposit element
        $this->addElement('text', 'property_deposit', array(
            'label'      => 'Deposit Amount',
            'required'   => true,
            'attribs'   => array(
                'class' => 'currency'
            ),
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the deposit amount',
                            'notEmptyInvalid' => 'Please enter the deposit amount'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d{1,}$/',
                        'messages' => 'Amount of deposit must contain at least one digit'
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => -1,
                        'messages' => 'Deposit amount must be zero or more'
                    )
                )
            )
        ));

        // Add rent in advance selection element
        $this->addElement('radio', 'rent_in_advance', array(
            'label'     => 'Has the first month’s rent been paid in advance',
            'required'  => true,
            'multiOptions' => array(
                'yes' =>  'Yes',
                'no'  => 'No'
            ),
            'separator' => ' ',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please select answer to question: Has the first month’s rent been paid in advance?',
                        'notEmptyInvalid' => 'Please select answer to question: Has the first month’s rent been paid in advance?'
                    )
                )
                )
            )
        ));


        // Add tenancy start date element
        $this->addElement('text', 'tenancy_startdate', array(
            'label'      => 'Tenancy start date (dd/mm/yyyy)',
            'required'   => true,
            'filters'    => array('StringTrim'),             
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Tenancy start date can not be empty',
                            'notEmptyInvalid' => 'Please enter a valid tenancy start date'
                        )
                    )
                )
            )
        ));
        
        $tenancy_startdate = $this->getElement('tenancy_startdate');
        $validator = new Zend_Validate_DateCompare();
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 60 * 60 * 24 * 365); // 365 days in future from todays date
        $validator->setMessages(array(
            'msgMinimum' => 'Tenancy start date too far in the past',
            'msgMaximum' => 'Tenancy start date too far in the future'
        ));
        $tenancy_startdate->addValidator($validator, true);
        
        // Add policy start date element
        $this->addElement('text', 'policy_startdate', array(
            'label'      => 'Policy start date (dd/mm/yyyy)',
            'required'   => true,
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
        $tenant_startdate = $this->getElement('policy_startdate');
        $validator = new Zend_Validate_DateCompare();
        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) - (86400 * 14));
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + (86400 * 365));
        $validator->setMessages(array(
            'msgMinimum' => 'Policy start date cannot be more than 14 days in the past',
            'msgMaximum' => 'Policy start date too far in the future'
        ));
        $tenant_startdate->addValidator($validator, true);

        // Add policy end date element
        $this->addElement('text', 'policy_enddate', array(
            'label'     => 'Policy end date (dd/mm/yyyy)',
            'required'  => true,
            'filters'   => array('StringTrim'),
          //  'attribs'   => array('disabled' => 'disabled'),
            'attribs'   => array('readonly' => true),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Policy end date can not be empty',
                            'notEmptyInvalid' => 'Please enter a valid Policy end date'
                        )
                    )
                )
	    )
        ));
             
        $policy_enddate = $this->getElement('policy_enddate');
//        $validator = new Zend_Validate_DateCompare();
//        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 60 * 60 * 24);
//        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 60 * 60 * 24 * 365);
//        $validator->setMessages(array(
//            'msgMinimum' => 'Policy end date too far in the past',
//            'msgMaximum' => 'Policy end date too far in the future'
//        ));
//        $policy_enddate->addValidator($validator, true);

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguarantee/subforms/rent-recovery-plus-application-property.phtml'))
        ));

        $this->setElementFilters(array('StripTags'));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
    
    public function isValid($formData = array()) {
        $isValid=parent::isValid($formData);
                
        if ($formData['property_managed'] === 'Let Only') {            
            $this->getElement('LL_Permission')->setRequired(true);             
        } else {
            $this->getElement('LL_Permission')->setRequired(false);         
        }
 
/*
        $tenancyDate = new Zend_Date ($formData['tenancy_startdate']);
        $policyDate = new Zend_Date ($formData['policy_startdate']);
        $daysDiff = $policyDate->sub($tenancyDate)->toValue()/60/60/24;
        
        // Policy start date should be between 0 to max 14 days after the tenancy start date
        if ($daysDiff < 0 || $daysDiff > 14) {
            $this->getElement('tenancy_startdate')->addError('Policy start date should be maximum 14 days after the tenancy start date');
           
            $isValid=false;
        }
*/
        
        // Monthly rental amount cannot be greater than £2500 for reference type 'Other Credit Check' and 'Insight'
        if(($formData['type'] === 'Other Credit Check' || $formData['type'] === 'Insight') && $formData['property_rental'] > 2500) {
            $this->getElement('property_rental')->addError('Monthly rental amount cannot be greater than £2500 for the selected reference type');
           
            $isValid=false;
        }
                    
        return $isValid;
        //return parent::isValid($formData);        
    }
}
