<?php

class Connect_Form_RentGuaranteeClaims_Step2 extends Zend_Form
{
    /**
     * Define the OC step2 form elements
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');

        // Add Hosuing Acts
        $this->addElement('select', 'housing_act_adherence', array(
            'label'     => 'Is the property let in accordance with the Housing Acts of 1988, 1996, 2004 ' .
                                'and any subsequent or superseding legislation, AND, has the Landlord complied ' .
                                ' with the requirements of the Tenancy Deposit Scheme (TDS)?',
            'required'  => true,
            'filters'   => array('StringTrim'),
            'multiOptions' => array(
                '' => 'Please select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select housing act adherence',
                            'notEmptyInvalid' => 'Please select housing act adherence'
                        )
                    )
                )
            )
        ));

        // Add tenant start date
         $this->addElement('text', 'tenancy_start_date', array(
            'label'            => 'Tenancy start date (DD/MM/YYYY)',
             'required'   => true,
             'validators'    => array(
                array(
                    'NotEmpty',true,array(
                        'messages'    => array(
                            'isEmpty' => 'Please select tenancy start date',
                            'notEmptyInvalid' => 'Please select tenancy start date'
                       )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                        'messages' => 'Tenancy start date must be in dd/mm/yyyy format'
                    )
                )
            )
        ));

        // Add tenant start date
         $this->addElement('text', 'tenancy_end_date', array(
            'label'            => 'Tenancy end date (DD/MM/YYYY)',
             'required'   => false,
             'validators'    => array(
                array(
                    'NotEmpty',true,array(
                        'messages'    => array(
                            'isEmpty' => 'Please select tenancy end date',
                            'notEmptyInvalid' => 'Please select end date'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                        'messages' => 'Tenancy end date must be in dd/mm/yyyy format'
                    )
                )
            )
        ));

        // Add original cover start date
        $this->addElement('text', 'original_cover_start_date', array(
            'label'            => 'Policy start date (DD/MM/YYYY)',
            'required'   => true,
            'validators'    => array(
                array(
                    'NotEmpty',true,array(
                    'messages'    => array(
                        'isEmpty'    => 'Please select original cover start date',
                        'notEmptyInvalid' => 'Please select original cover start date'
                    )
                )
                ),
                array(
                    'regex', true, array(
                    'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                    'messages' => 'Original cover start date must be in dd/mm/yyyy format'
                )
                )
            )
        ));

        // Add rental property postcode
        $this->addElement('text', 'tenancy_postcode', array(
            'label'      => 'Rental property address postcode',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'maxlength'  => '10',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter rental property address postcode',
                            'notEmptyInvalid' => 'Please enter rental property address postcode'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z]{2,}\ ?[0-9a-z]{2,}$/i',
                        'messages' => 'Rental property postcode must be in postcode format'
                    )
                )
            )
        ));

        // Add the find address button
        $this->addElement('submit', 'tenancy_address_lookup', array(
            'ignore'   => true,
            'label'    => 'Find Address',
            'class'    => 'button',
            'onclick'    => 'getPropertiesByPostcode($(\'#tenancy_postcode\').val(), \'tenancy_postcode\', \'tenancy_address\',\'no_property_address_selector\'); return false;'
        ));

        // Add agent address select element
        $this->addElement('select', 'tenancy_address', array(
            'class' =>'postcode_address',
            'required'  => false,
            'filters' => array('StringTrim'),
            'multiOptions' => array(
                '' => 'Please select'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select property address',
                            'notEmptyInvalid' => 'Please select property address'
                        )
                    )
                )
            )
        ));

        // Remove 'nnn not found in haystack' error
        $this->getElement('tenancy_address')->setRegisterInArrayValidator(false);

        // Add hidden element for postcode
        $this->addElement('hidden', 'tenancy_address_id', array(
            'label'  => '',
            'value'  => 1,
            'class'  => 'noborder'
        ));

        // Add in additional validation fields using utility class
        Application_Core_FormUtils::createManualAddressInput(
            $this,
            'tenancy_housename',
            'tenancy_street',
            'tenancy_town',
            'tenancy_city',
            false,
            'tenant\'s'
        );

        // Add monthly rental
        $this->addElement('text', 'monthly_rent', array(
            'label'      => 'Monthly rent on this property',
            'required'   => true,
            'filters'    => array('Digits'),
            'class'         => 'input-pos-float',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your monthly rent',
                            'notEmptyInvalid' => 'Please enter your monthly rent'
                        )
                    )
                )
            )
        ));

        // Add Deposit Held
        $this->addElement('text', 'deposit_amount', array(
            'label'      => 'Deposit held',
            'required'   => true,
            'filters'    => array('Digits'),
            'class'         => 'input-pos-float',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your deposit amount',
                            'notEmptyInvalid' => 'Please enter your deposit amount'
                        )
                    )
                )
            )
        ));

        // Add Rental Arrears
        $this->addElement('text', 'rent_arrears', array(
            'label'      => 'Total rent arrears',
            'required'   => false,
            'filters'    => array('Digits'),
            'class'         => 'input-pos-float',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your rent arrears',
                            'notEmptyInvalid' => 'Please enter rent arrears in number'
                        )
                    )
                )
            )
        ));

        // Add date of first arrear
        $this->addElement('text', 'first_arrear_date', array(
            'label'     => 'Date of first arrears (DD/MM/YYYY)',
            'required'   => false,
            'validators'    => array(
                array(
                    'NotEmpty',true,array(
                        'messages'    => array(
                            'isEmpty'    => 'Please select first arrear date',
                            'notEmptyInvalid' => 'Please select first arrear date'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                        'messages' => 'First arrears date must be in dd/mm/yyyy format'
                    )
                )
            )
        ));

        // Add date deposit received
        $this->addElement('text', 'deposit_received_date', array(
            'label'     => 'Date when deposit received (DD/MM/YYYY)',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your deposit received date',
                            'notEmptyInvalid' => 'Please enter your rent arrears in number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                        'messages' => 'Deposit received date must be in dd/mm/yyyy format'
                    )
                )
            )
        ));

        $this->addElement('select', 'recent_complaints', array(
            'label'        => 'Have there been any recent complaints, for example regarding disrepair?',
            'required'     => true,
            'multiOptions' => array(
                ''  => 'Please Select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Have there been any recent complaints, for example regarding disrepair?',
                        'notEmptyInvalid' => 'Have there been any recent complaints, for example regarding disrepair?'
                    )
                ))
            )
        ));

        $this->addElement('textarea', 'recent_complaints_further_details', array(
            'label'     => '',
            'required'  => false,
            'class'     => 'additionalinfo fullwidth',
            'rows'      => '5',
            'cols'      => '77',
            'maxlength' => '250',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please provide further details for recent complaints',
                        'notEmptyInvalid' => 'Please provide further details for recent complaints'
                    )
                )
                )
            ),
        ));

        $this->addElement('text', 'policy_number', array(
            'label'             => 'Policy number',
            'required'          => 'true',
            'maxlength'         => '15',
            'validators'        => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the policy number',
                            'notEmptyInvalid' => 'Please enter the policy number'
                        )
                    )
                )
            )
        ));

        $this->addElement('select', 'grounds_for_claim', array(
            'label'        => 'What are the grounds for this claim?',
            'required'     => true,
            'multiOptions' => array(
                ''              => 'Please Select',
                'rent-arrears'  => 'Rent arrears',
                'other'         => 'Other',
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter the grounds for the claim',
                        'notEmptyInvalid' => 'Please enter the grounds for the claim'
                    )
                ))
            )
        ));

        $this->addElement('textarea', 'grounds_for_claim_further_details', array(
            'label'     => '',
            'required'  => false,
            'class'     => 'additionalinfo fullwidth',
            'rows'      => '5',
            'cols'      => '77',
            'maxlength' => '250',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please provide further details for grounds for claim',
                        'notEmptyInvalid' => 'Please provide further details for grounds for claim'
                    )
                )
                )
            ),
        ));

        // Add element for tenants vacated
        $this->addElement('select', 'tenant_vacated', array(
            'required'  => true,
            'label'  => 'Has the tenant(s) vacated?',
            'filters'   => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Has the tenant(s) vacated?',
                        'notEmptyInvalid' => 'Has the tenant(s) vacated?'
                    )
                )
                )
            ),
            'multiOptions' => array(
                ''  => 'Please select',
                '1' => 'Yes',
                '0' => 'No'
            )
        ));

        $this->addElement('text', 'arrears_at_vacant_possession', array(
            'required'  => false,
            'label'     => 'Please state the arrears at the date vacant possession was obtained to the nearest pound (£)',
            'filters'   => array('StringTrim'),
            'validators'    => array(
                array(
                    'NotEmpty',true,array(
                        'messages'    => array(
                            'isEmpty'    => 'Arrears at the date vacant possession was obtained',
                            'notEmptyInvalid' => 'Arrears at the date vacant possession was obtained'
                        )
                    )
                ),
            )
        ));

        // Add tenant vacated date
        $this->addElement('text', 'tenant_vacated_date', array(
            'label'     => 'Date tenants vacated (DD/MM/YYYY)',
            'required'   => false,
            'validators'    => array(
                array(
                    'NotEmpty',true,array(
                    'messages'    => array(
                        'isEmpty'    => 'Date tenants vacated',
                        'notEmptyInvalid' => 'Date tenants vacated'
                    )
                )
                ),
                array(
                    'regex', true, array(
                    'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                    'messages' => 'Tenant vacated date must be in dd/mm/yyyy format'
                )
                )
            )
        ));

        // Add rental property postcode
        $this->addElement('text', 'tenantsforwarding_postcode', array(
            'label'      => 'Tenant\'s forwarding address postcode',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength'  => '10',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Tenant\'s forwarding address postcode',
                        'notEmptyInvalid' => 'Tenant\'s forwarding address postcode'
                    )
                )
                ),
                array(
                    'regex', true, array(
                    'pattern' => '/^[0-9a-z]{2,}\ ?[0-9a-z]{2,}$/i',
                    'messages' => 'Tenant\'s forwarding postcode must be in postcode format'
                )
                )
            )
        ));

        // Add the find address button
        $this->addElement('submit', 'tenantsforwarding_address_lookup', array(
            'ignore'   => true,
            'label'    => 'Find Address',
            'class'    => 'button',
            'onclick'    => 'getPropertiesByPostcode($(\'#tenantsforwarding_postcode\').val(), \'tenantsforwarding_postcode\', \'tenantsforwarding_address\',\'forwarding_property_address_selector\'); return false;'
        ));

        // Add agent address select element
        $this->addElement('select', 'tenantsforwarding_address', array(
            'class' =>'postcode_address',
            'required'  => false,
            'filters' => array('StringTrim'),
            'multiOptions' => array(
                '' => 'Please select'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please select forwarding address',
                        'notEmptyInvalid' => 'Please select forwarding address'
                    )
                )
                )
            )
        ));

        // Remove 'nnn not found in haystack' error
        $this->getElement('tenantsforwarding_address')->setRegisterInArrayValidator(false);

        // Add hidden element for postcode
        $this->addElement('hidden', 'tenantsforwarding_address_id', array(
            'label'  => '',
            'value'  => 1,
            'class'  => 'noborder'
        ));

        // Add in additional validation fields using utility class
        Application_Core_FormUtils::createManualAddressInput(
            $this,
            'tenantsforwarding_housename',
            'tenantsforwarding_street',
            'tenantsforwarding_town',
            'tenantsforwarding_city',
            false,
            'tenant\'s'
        );

        $this->addElement('checkbox', 'tenant_occupation_confirmed_by_tel', array(
            'label' => 'By telephone'
        ));

        // Add date deposit received
        $this->addElement('text', 'tenant_occupation_confirmed_by_tel_dateofcontact', array(
            'label'     => 'Date of contact (DD/MM/YYYY)',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter your date of contact when tenant occupation of the property was confirmed by telephone',
                        'notEmptyInvalid' => 'Please enter your date of contact when tenant occupation of the property was confirmed by telephone'
                    )
                )
                ),
                array(
                    'regex', true, array(
                    'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                    'messages' => 'Date of contact must be in dd/mm/yyyy format'
                )
                )
            )
        ));

        $this->addElement('text', 'tenant_occupation_confirmed_by_tel_tenantname', array(
            'label'      => 'Name of tenant contacted',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength'  => '30',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter the tenant contacted when tenant occupation of the property was confirmed by telephone',
                        'notEmptyInvalid' => 'Please enter the tenant contacted when tenant occupation of the property was confirmed by telephone'
                    )
                )
                )
            )
        ));


        $this->addElement('checkbox', 'tenant_occupation_confirmed_by_email', array(
            'label' => "By email<br><span style=\"font-size:smaller;\">Please provide a copy of the email</span>"
        ));

        $this->addElement('text', 'tenant_occupation_confirmed_by_email_dateofcontact', array(
            'label'     => 'Date of contact (DD/MM/YYYY)',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter your date of contact when tenant occupation of the property was confirmed by email',
                        'notEmptyInvalid' => 'Please enter your date of contact when tenant occupation of the property was confirmed by email'
                    )
                )
                ),
                array(
                    'regex', true, array(
                    'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                    'messages' => 'Date of contact must be in dd/mm/yyyy format'
                )
                )
            )
        ));

        $this->addElement('text', 'tenant_occupation_confirmed_by_email_tenantname', array(
            'label'      => 'Name of tenant contacted',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength'  => '30',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter the tenant contacted when tenant occupation of the property was confirmed by email',
                        'notEmptyInvalid' => 'Please enter the tenant contacted when tenant occupation of the property was confirmed by email'
                    )
                )
                )
            )
        ));

        $this->addElement('checkbox', 'tenant_occupation_confirmed_by_visit', array(
            'label' => 'In person'
        ));

        $this->addElement('text', 'tenant_occupation_confirmed_by_visit_dateofvisit', array(
            'label'     => 'Date of visit (DD/MM/YYYY)',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter your date of visit when tenant occupation of the property was confirmed',
                        'notEmptyInvalid' => 'Please enter your date of visit when tenant occupation of the property was confirmed'
                    )
                )
                ),
                array(
                    'regex', true, array(
                    'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                    'messages' => 'Date of visit must be in dd/mm/yyyy format'
                )
                )
            )
        ));

        $this->addElement('text', 'tenant_occupation_confirmed_by_visit_individualattending', array(
            'label'      => 'Name of individual attending the visit',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength'  => '30',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter the name of the individual who attended the visit to confirm tenant occupation of the property',
                        'notEmptyInvalid' => 'Please enter the name of the individual who attended the visit to confirm tenant occupation of the property'
                    )
                )
                )
            )
        ));

        $this->addElement('text', 'tenant_occupation_confirmed_by_visit_tenantname', array(
            'label'      => 'Name of tenant spoken to',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength'  => '30',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter the tenant spoken to when tenant occupation of the property was confirmed by a visit',
                        'notEmptyInvalid' => 'Please enter the tenant spoken to when tenant occupation of the property was confirmed by a visit'
                    )
                )
                )
            )
        ));

        $this->addElement('select', 'section21_served', array(
            'label'        => 'Have you served a Section 21 notice seeking possession?',
            'required'     => true,
            'multiOptions' => array(
                ''  => 'Please Select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Have you served a Section 21 notice seeking possession?',
                        'notEmptyInvalid' => 'Have you served a Section 21 notice seeking possession?'
                    )
                )
                )
            )
        ));


        $this->addElement('text', 'section21_expiry', array(
            'label'     => 'Section 21 notice expiry date (DD/MM/YYYY)',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter the date the Section 21 notice expires',
                        'notEmptyInvalid' => 'Please enter the date the Section 21 notice expires'
                    )
                )
                ),
                array(
                    'regex', true, array(
                    'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                    'messages' => 'Section 21 notice expiry date must be in dd/mm/yyyy format'
                )
                )
            )
        ));

        $this->addElement('select', 'section21_moneydepositreceived', array(
            'label'        => 'Was a money deposit received on or after 6 April 2007?',
            'multiOptions' => array(
                ''  => 'Please Select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Was a money deposit received on or after 6 April 2007?',
                        'notEmptyInvalid' => 'Was a money deposit received on or after 6 April 2007?'
                    )
                )
                )
            )
        ));

        $this->addElement('select', 'section21_money_held_under_tds_deposit_scheme', array(
            'label'        => 'Was the deposit held under a TDS authorised under part 6 of the Housing Act 2004?',
            'multiOptions' => array(
                ''  => 'Please Select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Was the deposit held under a TDS authorised under part 6 of the Housing Act 2004?',
                        'notEmptyInvalid' => 'Was the deposit held under a TDS authorised under part 6 of the Housing Act 2004?'
                    )
                )
                )
            )
        ));

        $this->addElement('select', 'section21_tds_complied_with', array(
            'label'        => 'Had the initial requirements of the TDS been complied with in relation to the deposit?',
            'multiOptions' => array(
                ''  => 'Please Select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Had the initial requirements of the TDS been complied with in relation to the deposit?',
                        'notEmptyInvalid' => 'Had the initial requirements of the TDS been complied with in relation to the deposit?'
                    )
                )
                )
            )
        ));

        $this->addElement('select', 'section21_tds_prescribed_information_to_tenant', array(
            'label'        => 'Had you or the landlord given the tenant(s), and anyone who paid the deposit on behalf '
                . 'of the tenant(s), the prescribed information in relation to the deposit and the operation of the TDS?',
            'multiOptions' => array(
                ''  => 'Please Select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Had you or the landlord given the tenant, and anyone who paid the deposit on behalf '
                        . 'of the tenant, the prescribed information in relation to the deposit and the operation of the TDS?',
                        'notEmptyInvalid' => 'Had you or the landlord given the tenant, and anyone who paid the deposit on behalf '
                        . 'of the tenant, the prescribed information in relation to the deposit and the operation of the TDS?'
                    )
                )
                )
            )
        ));

        $this->addElement('select', 'section21_landlord_deposit_in_property_form', array(
            'label'        => 'Did the landlord receive a deposit in the form of property on or after 6 April 2007?',
            'multiOptions' => array(
                ''  => 'Please Select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Did the landlord receive a deposit in the form of property on or after 6 April 2007?',
                        'notEmptyInvalid' => 'Did the landlord receive a deposit in the form of property on or after 6 April 2007?'
                    )
                )
                )
            )
        ));

        $this->addElement('select', 'section21_returned_at_notice_serve_date', array(
            'label'        => 'At the date the Section 21 notice was served, had the property been returned to the person from whom it was received?',
            'multiOptions' => array(
                ''  => 'Please Select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'At the date the Section 21 notice was served, had the property been returned to the person from whom it was received?',
                        'notEmptyInvalid' => 'At the date the Section 21 notice was served, had the property been returned to the person from whom it was received?'
                    )
                )
                )
            )
        ));

        $this->addElement('select', 'section8_served', array(
            'label'        => 'Have you served a Section 8 notice requiring possession?',
            'required'     => true,
            'multiOptions' => array(
                ''  => 'Please Select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Have you served a Section 8 notice requiring possession?',
                        'notEmptyInvalid' => 'Have you served a Section 8 notice requiring possession?'
                    )
                )
                )
            )
        ));

        $this->addElement('text', 'section8_expiry', array(
            'label'     => 'Section 8 notice expiry date (DD/MM/YYYY)',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter the date the Section 8 notice expires',
                        'notEmptyInvalid' => 'Please enter the date the Section 8 notice expires'
                    )
                )
                ),
                array(
                    'regex', true, array(
                    'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                    'messages' => 'Section 8 notice expiry date must be in dd/mm/yyyy format'
                )
                )
            )
        ));

        $this->addElement('select', 'section8_demand_letter_sent', array(
            'label'        => 'Has a demand letter been sent to the tenant(s) requesting the outstanding arrears? ' .
                'If yes, you will be required to provide copies.',
            'required'     => true,
            'multiOptions' => array(
                ''  => 'Please Select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Has a demand letter been sent to the tenant(s) requesting the outstanding arrears?',
                        'notEmptyInvalid' => 'Has a demand letter been sent to the tenant(s) requesting the outstanding arrears?'
                    )
                )
                )
            )
        ));

        $this->addElement('textarea', 'section8_over18_occupants', array(
            'label'        => '',
            'class'     => 'additionalinfo fullwidth',
            'maxlength' => '250',
        ));

        // Hidden element for total guarantors to create dynamic guarantor elements
         $this->addElement('hidden', 'totalguarantors', array(
            'label' => '',
            'value' => '0',
            'class' => 'noborder'
        ));

        // How many guarantors are there?
        $this->addElement('select', 'total_guarantors', array(
            'label'             => 'How many Guarantors are there?',
            'required'          => true,
            'filters'           => array('StringTrim'),
            'multiOptions'         => array(
                ''  => 'Please Select',
                '0' => '0',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please specify the number of guarantors',
                            'notEmptyInvalid' => 'Please specify the number of guarantors'
                        )
                    )
                )
            )
        ));

        // Add guarantor name element
        $this->addElement('text', 'guarantor_name', array(
            'label'      => 'Full name',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength'  => '80',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter guarantor\'s full name',
                            'notEmptyInvalid' => 'Please enter guarantor\'s full name'
                        )
                    )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));

        // Guarantor Home Telno
        $this->addElement('text', 'guarantor_hometelno', array(
            'label'      => 'Home telephone number',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => "Please enter the guarantor home telephone number",
                        'notEmptyInvalid' => "Please enter the guarantor home telephone number"
                    )
                )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));

        // Guarantor Work Telno
        $this->addElement('text', 'guarantor_worktelno', array(
            'label'      => 'Work telephone number',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => "Please enter the guarantor work telephone number",
                        'notEmptyInvalid' => "Please enter the guarantor work telephone number"
                    )
                )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));

        // Guarantor Mobile Telno
        $this->addElement('text', 'guarantor_mobiletelno', array(
            'label'      => 'Mobile number',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => "Please enter the guarantor mobile telephone number",
                        'notEmptyInvalid' => "Please enter the guarantor mobile telephone number"
                    )
                )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));

        // Guarantor Email
        $this->addElement('text', 'guarantor_email', array(
            'label'      => 'Email address',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength'  => '50',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => "Please enter the guarantor email address",
                        'notEmptyInvalid' => "Please enter the guarantor email address"
                    )
                )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));



        // Add guarantor postcode
        $this->addElement('text', 'guarantor_postcode', array(
            'label'      => 'Postcode',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength'  => '10',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter guarantor address postcode',
                            'notEmptyInvalid' => 'Please enter guarantor address postcode'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z]{2,}\ ?[0-9a-z]{2,}$/i',
                        'messages' => 'Guarantors postcode must be in postcode format'
                    )
                )
            )
        ));

        // Add the find address button
        $this->addElement('submit', 'address_lookup', array(
            'ignore'    => true,
            'label'     => 'Find Address',
            'class'     => 'button',
            'onclick'    => 'getPropertiesByPostcode($(\'#guarantor_postcode\').val(), \'guarantor_postcode\', \'guarantor_address\',\'guarantor_child\');$(\'#guarantor_add_selector\').show(); return false;'
        ));

        // Add agent address select element
        $this->addElement('select', 'guarantor_address', array(
            'required'  => false,
            'multiOptions' => array(
                '' => 'Please select'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select guarantor address',
                            'notEmptyInvalid' => 'Please select guarantor address'
                        )
                    )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));

        $this->addElement('text', 'guarantor_homeletrefno', array(
            'label'         => 'HomeLet reference number',
            'required'      => false,
            'filters'       => array('StringTrim'),
            'maxlength'     => '10',
            'validators'    => array(
                array(
                    'NotEmpty',true,array(
                        'messages'    => array(
                            'isEmpty'    => "Please select the HomeLet reference number",
                            'notEmptyInvalid' => "Please select the HomeLet reference number"
                        )
                    )
                )
            )
        ));

        // Tenants DOB
        $this->addElement('text', 'guarantors_dob', array(
            'label'             => "Date of birth (DD/MM/YYYY)",
            'required'            => false,
            'class'                => "hasDatepicker",
            'maxlength'            => '10',
            'filters'            => array('StringTrim'),
            'validators'    => array(
                array(
                    'NotEmpty',true,array(
                    'messages'    => array(
                        'isEmpty'    => "Please select the guarantor date of birth",
                        'notEmptyInvalid' => "Please select the guarantor date of birth"
                    )
                )
                ),
                array(
                    'regex', true, array(
                    'pattern' => '/^\d\d\/\d\d\/\d\d\d\d$/',
                    'messages' => "Guarantor date of birth incomplete"
                )
                )
            )
        ));



        // Remove 'nnn not found in haystack' error
        $this->getElement('guarantor_address')->setRegisterInArrayValidator(false);

        // Add hidden element for postcode
        $this->addElement('hidden', 'guarantoraddress_id', array(
            'label'  => '',
            'value'  => 1,
            'class'  => 'noborder'
        ));

        // Add in additional validation fields using utility class
        Application_Core_FormUtils::createManualAddressInput(
            $this,
            'guarantor_housename',
            'guarantor_street',
            'guarantor_town',
            'guarantor_city',
            false,
            'guarantor\'s'
        );

        //How many tenants are named on the tenancy agreement?
        $this->addElement('select', 'total_tenants', array(
            'label'             => "How many tenants are named on the tenancy agreement?",
            'required'             => true,
            'filters'            => array('StringTrim'),
            'multiOptions'         => array(
            '' => 'Please Select',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please specify the number of tenants on the tenancy agreement',
                            'notEmptyInvalid' => 'Please specify the number of tenants on the tenancy agreement'
                        )
                    )
                )
            )
        ));

        // Hidden element for total tenants to create dynamic tenant elements
         $this->addElement('hidden', 'totaltenants', array(
            'label'     => '',
             'value'    => '0',
             'class'    =>    'noborder'
        ));

        // Tenant Name
        $this->addElement('text', 'tenant_name', array(
            'label'      => 'Full name',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength'  => '80',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter tenant\'s full name',
                            'notEmptyInvalid' => 'Please enter tenant\'s full name'
                        )
                    )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));

        // Tenant Home Telno
        $this->addElement('text', 'tenant_hometelno', array(
            'label'      => 'Home telephone number',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => "Please enter tenant home telephone number",
                        'notEmptyInvalid' => "Please enter tenant home telephone number"
                    )
                )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));

        // Tenant Work Telno
        $this->addElement('text', 'tenant_worktelno', array(
            'label'      => 'Work telephone number',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => "Please enter tenant work telephone number",
                        'notEmptyInvalid' => "Please enter tenant work telephone number"
                    )
                )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));

        // Tenant Mobile Telno
        $this->addElement('text', 'tenant_mobiletelno', array(
            'label'      => 'Mobile number',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength'  => '11',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => "Please enter tenant mobile telephone number",
                        'notEmptyInvalid' => "Please enter tenant mobile telephone number"
                    )
                )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));

        // Tenant Email
        $this->addElement('text', 'tenant_email', array(
            'label'      => 'Email address',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength'  => '50',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => "Please enter tenant email address",
                        'notEmptyInvalid' => "Please enter tenant email address"
                    )
                )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));



        // Policy Number / IRN
        $this->addElement('text', 'rg_policy_ref', array(
            'label'             => "HomeLet reference number",
            'required'          => false,
            'maxlength'            => '10',
            'filters'            => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the HomeLet reference number',
                            'notEmptyInvalid' => 'Please enter the HomeLet reference number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/\d{7}/',
                        'messages' => 'The HomeLet reference number must be at least seven digits long'
                    )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));

        // Tenants DOB
        $this->addElement('text', 'tenants_dob', array(
            'label'             => "Date of birth (DD/MM/YYYY)",
            'required'            => false,
            'class'                => "",
            'maxlength'            => '10',
            'filters'            => array('StringTrim'),
            'validators'    => array(
                array(
                    'NotEmpty',true,array(
                        'messages'    => array(
                            'isEmpty'    => 'Please select tenant date of birth',
                            'notEmptyInvalid' => 'Please select tenant date of birth'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/(?P<d>\d{2})(?P<sep>\D)(?P<m>\d{2})\2(?P<y>\d{4})/',
                        'messages' => 'Tenants date of birth must be in dd/mm/yyyy format'
                    )
                )
            ),
            'onblur'    =>    'validateguarantor()'
        ));

        // Set decorators
        $this->clearDecorators();
        $this->setDecorators(array('Form'));
        $this->setElementDecorators(array ('ViewHelper', 'Label', 'Errors'));

        // Add the back button
        $this->addElement('submit', 'back', array(
            'ignore'   => true,
            'label'    => 'Back'
        ));

        // Tenant Address decorators
        $tenantAddressLookUp  =   $this->getElement('tenancy_address_lookup');
        $tenantAddressLookUp->clearDecorators();
        $tenantAddressLookUp->setDecorators(array ('ViewHelper'));

        // Tenant Address decorators
        $tenantsForwardingAddressLookUp  =   $this->getElement('tenantsforwarding_address_lookup');
        $tenantsForwardingAddressLookUp->clearDecorators();
        $tenantsForwardingAddressLookUp->setDecorators(array ('ViewHelper'));

        // Address decorators
        $addressLookUp  =   $this->getElement('address_lookup');
        $addressLookUp->clearDecorators();
        $addressLookUp->setDecorators(array ('ViewHelper'));

        // Add the next button
        $this->addElement('submit', 'next', array(
            'ignore'   => true,
            'label'    => 'Continue to Step 3',
        ));

        // Add the save & exit button
        $this->addElement('button', 'save_exit', array(
            'ignore'   => true,
            'label'    => 'Save & Exit         ',
            'onclick'  => "window.location = '/rentguaranteeclaims/saveclaim';"
        ));

        $next = $this->getElement('next');
        $next->clearDecorators();
        $next->setDecorators(array('ViewHelper'));

        $back = $this->getElement('back');
        $back->clearDecorators();
        $back->setDecorators(array('ViewHelper'));

        $saveExit = $this->getElement('save_exit');
        $saveExit->clearDecorators();
        $saveExit->setDecorators(array('ViewHelper'));

        // Hide errors
        Application_Core_FormUtils::removeFormErrors($this);

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguaranteeclaims/subforms/tenant-and-property.phtml'))
        ));

        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        $this->getElement('tenant_occupation_confirmed_by_email')->getDecorator('Label')->setOption('escape', false);
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
        $validationResult=parent::isValid($formData);
        //$validationResult = true;

        // Constrain date input fields
        $tenancyStartDate = $this->getElement('tenancy_start_date');
        $tenancyRenewalDate = $this->getElement('tenancy_end_date');
        $originalCoverStartDate = $this->getElement('original_cover_start_date');
        $depositReceivedDate = $this->getElement('deposit_received_date');
        $firstArrearDate = $this->getElement('first_arrear_date');
        
        if ($formData['grounds_for_claim'] === 'rent-arrears'){   
            $this->getElement('rent_arrears')->setRequired(true);
            $this->getElement('first_arrear_date')->setRequired(true);
        }
            
        // - Tenancy start date must be in the past
        $validator = new Zend_Validate_DateCompare();
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 1);
        $validator->setMessages(array(
            'msgMaximum' => 'Tenancy start date must be in the past'
        ));
        $tenancyStartDate->addValidator($validator, true);

        // - Original cover start date must be on or after tenancy start date
        // - Original cover start date must be in the past
        if (isset($formData['tenancy_start_date']) && $formData['tenancy_start_date'] != '')
        {
            $validator = new Zend_Validate_DateCompare();
            $validator->minimum = new Zend_Date($formData['tenancy_start_date']);
            $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 1);
            $validator->setMessages(array(
                'msgMinimum' => 'Original cover start date must be after tenancy start date',
                'msgMaximum' => 'Original cover start date must be in the past'
            ));
            $originalCoverStartDate->addValidator($validator, true);

            // - Tenancy renewal date, if supplied, must be after tenancy start date
            $validator = new Zend_Validate_DateCompare();
            $validator->minimum = new Zend_Date($formData['tenancy_start_date']);
            $validator->setMessages(array(
                'msgMinimum' => 'Tenancy renewal date must be after tenancy start date'
            ));
            $tenancyRenewalDate->addValidator($validator, true);

            // - Date when deposit received must be in the past
            $validator = new Zend_Validate_DateCompare();
            $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 1);
            $validator->setMessages(array(
                'msgMaximum' => 'Date when deposit received must be in the past'
            ));
            $depositReceivedDate->addValidator($validator, true);
        }

        // - Date of first arrears must be in the past, and within the past 12 months
        $validatorChain = new Zend_Validate();
        $validator = new Zend_Validate_DateCompare();
        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y') - 1));
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 1);
        $validator->setMessages(array(
            'msgMinimum' => 'Date of first arrears must be within the past 12 months',
            'msgMaximum' => 'Date of first arrears must be in the past'
        ));
        $validatorChain->addValidator($validator, true);

        if (isset($formData['tenancy_start_date']) && $formData['tenancy_start_date'] != '')
        {
            // - Date of first arrears must be after the tenancy start date
            $validator = new Zend_Validate_DateCompare();
            $validator->minimum = new Zend_Date($formData['tenancy_start_date']);
            $validator->setMessages(array(
                'msgMinimum' => 'Date of first arrears must be after tenancy start date'
            ));
            $validatorChain->addValidator($validator, true);
        }

        $firstArrearDate->addValidator($validatorChain, true);

        // If a postcode is or was present, look it up and populate the allowed values of the associated dropdown
        if ((isset($formData['tenancy_postcode']) && trim($formData['tenancy_postcode']) != '')) {
            Application_Core_FormUtils::getAddressByPostcode(
                $this,
                $formData,
                trim($formData['tenancy_postcode']),
                'tenancy_address',
                'tenancy_housename',
                'tenancy_street',
                'tenancy_town',
                'tenancy_city',
                'rental property\'s'
            );
        }

        if ((isset($formData['tenant_vacated']) && intval(trim($formData['tenant_vacated'])) === 1)) {
            $this->getElement('tenant_vacated_date')->setRequired(true);
            $this->getElement('arrears_at_vacant_possession')->setRequired(true);
            $this->getElement('tenantsforwarding_postcode')->setRequired(true);
        }
        else if ((isset($formData['tenant_vacated']) && intval(trim($formData['tenant_vacated'])) === 0)) {
            if ($formData['tenant_occupation_confirmed_by_tel'] == 1) {
                $this->getElement('tenant_occupation_confirmed_by_tel_dateofcontact')->setRequired(true);
                $this->getElement('tenant_occupation_confirmed_by_tel_tenantname')->setRequired(true);
            }

            if ($formData['tenant_occupation_confirmed_by_email'] == 1) {
                $this->getElement('tenant_occupation_confirmed_by_email_dateofcontact')->setRequired(true);
                $this->getElement('tenant_occupation_confirmed_by_email_tenantname')->setRequired(true);
            }

            if ($formData['tenant_occupation_confirmed_by_visit'] == 1) {
                $this->getElement('tenant_occupation_confirmed_by_visit_dateofvisit')->setRequired(true);
                $this->getElement('tenant_occupation_confirmed_by_visit_individualattending')->setRequired(true);
                $this->getElement('tenant_occupation_confirmed_by_visit_tenantname')->setRequired(true);
            }
        }

        if ((isset($formData['total_guarantors']) && trim($formData['total_guarantors']) != '')) {
            $this->addGuarantors($formData);
        }

        if ((isset($formData['total_tenants']) && trim($formData['total_tenants']) != '')) {
            $this->addTenants($formData);
        }

        if ((isset($formData['recent_complaints']) && trim($formData['recent_complaints']) == 1)) {
            $this->getElement('recent_complaints_further_details')->setRequired(true);
        }

        if ((isset($formData['grounds_for_claim']) && trim($formData['grounds_for_claim']) == 'other')) {
            $this->getElement('grounds_for_claim_further_details')->setRequired(true);
        }

        if ((isset($formData['section21_served']) && trim($formData['section21_served']) == 1)) {
            $this->getElement('section21_expiry')->setRequired(true);
            $this->getElement('section21_moneydepositreceived')->setRequired(true);
            $this->getElement('section21_tds_complied_with')->setRequired(true);
            $this->getElement('section21_money_held_under_tds_deposit_scheme')->setRequired(true);
            $this->getElement('section21_tds_prescribed_information_to_tenant')->setRequired(true);
            $this->getElement('section21_landlord_deposit_in_property_form')->setRequired(true);
            $this->getElement('section21_returned_at_notice_serve_date')->setRequired(true);
        }

        if ((isset($formData['section8_served']) && trim($formData['section8_served']) == 1)) {
            $this->getElement('section8_expiry')->setRequired(true);
        }

        $parentValidationResult = parent::isValid($formData);

        if ((isset($formData['tenant_vacated']) && trim($formData['tenant_vacated']) != '' && trim($formData['tenant_vacated']) == 0)) {
            // Require at least one method of confirmation of tenants continued occupation of the
            // property to be completed.
            if ($formData['tenant_occupation_confirmed_by_tel'] != 1 &&
                $formData['tenant_occupation_confirmed_by_email'] != 1 &&
                $formData['tenant_occupation_confirmed_by_visit'] != 1) {
                // one of these confirmation check boxes
                $this->addError('At least one confirmation method of the tenants continued occupation ' .
                'of the property must be provided');
                $validationResult = false;
            }
        }

        // Call original isValid()
        return ($validationResult & $parentValidationResult);
    }

    public function addGuarantors($formData)
    {
        for ($i = 1; $i <= $formData['total_guarantors']; $i++) {

            // Add guarantor name element
            $this->addElement('text', 'guarantor_name_' . $i, array(
                'label'      => 'Full name',
                'required'   => true,
                'filters'    => array('StringTrim'),
                'maxlength'  => '80',
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => "Please enter guarantor {$i}'s name",
                                'notEmptyInvalid' => "Please enter guarator {$i}'s name"
                            )
                        )
                    )
                )
            ));

            // Guarantor Home Telno
            $this->addElement('text', 'guarantor_hometelno_' . $i, array(
                'label'      => 'Home telephone number',
                'required'   => false,
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => "Please enter guarantor {$i}'s home telephone number",
                            'notEmptyInvalid' => "Please enter guarantor {$i}'s home telephone number"
                        )
                    )
                    )
                )
            ));

            // Guarantor Work Telno
            $this->addElement('text', 'guarantor_worktelno_' . $i, array(
                'label'      => 'Work telephone number',
                'required'   => false,
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => "Please enter guarantor {$i}'s work telephone number",
                            'notEmptyInvalid' => "Please enter guarantor {$i}'s work telephone number"
                        )
                    )
                    )
                )
            ));

            // Guarantor Mobile Telno
            $this->addElement('text', 'guarantor_mobiletelno_' . $i, array(
                'label'      => 'Mobile number',
                'required'   => false,
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => "Please enter guarantor {$i}'s mobile telephone number",
                            'notEmptyInvalid' => "Please enter guarantor {$i}'s mobile telephone number"
                        )
                    )
                    )
                )
            ));

            if ($formData['guarantor_mobiletelno_' . $i] == '' && $formData['guarantor_hometelno_' . $i] == '') {
                $this->getElement('guarantor_mobiletelno_' . $i)->setRequired(true);
                $this->getElement('guarantor_hometelno_' . $i)->setRequired(true);
            }

            // Guarantor Email
            $this->addElement('text', 'guarantor_email_' . $i, array(
                'label'      => 'Email address',
                'required'   => false,
                'filters'    => array('StringTrim'),
                'maxlength'  => '50',
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => "Please enter guarantor {$i}'s email address",
                            'notEmptyInvalid' => "Please enter guarantor {$i}'s email address"
                        )
                    )
                    )
                )
            ));




            // Add guarantor postcode
            $this->addElement('text', 'guarantor_postcode_' . $i, array(
                'label'      => 'Postcode',
                'required'   => true,
                'filters'    => array('StringTrim'),
                'maxlength'  => '10',
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => "Please enter guarantor {$i}'s postcode",
                                'notEmptyInvalid' => "Please enter guarantor {$i}'s postcode"
                            )
                        )
                    ),
                    array(
                        'regex', true, array(
                            'pattern' => '/^[0-9a-z]{2,}\ ?[0-9a-z]{2,}$/i', // TODO: temporary regex, needs to use postcode validator once available
                            'messages' => "Guarantor {$i}'s postcode must be in postcode format"
                        )
                    )
                )
            ));

            // Add the find address button
            $this->addElement('submit', 'address_lookup_' . $i, array(
                'ignore'   => true,
                 'class'    => 'lookupbutton',
                'onclick'    => 'getPropertiesByPostcode($(\'#guarantor_postcode_'.$i.'\').val(), \'guarantor_postcode_'.$i.'\', \'guarantor_address_'.$i.'\',\'guarantor_child_'.$i.'\'); return false;'
            ));
            $this->getElement('address_lookup_' . $i)->removeDecorator('label');

            // Add hidden element for postcode
            $this->addElement('hidden', 'guarantoraddress_id_'.$i, array(
                'label'  => '',
                'value'  => 1,
                'class'  => 'noborder'
            ));

             // Add agent address select element
            $this->addElement('select', 'guarantor_address_' . $i, array(
                'label'     => 'Please select',
                'required'  => true,
                'multiOptions' => array(
                    '' => 'Please select'
                ),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => "Please select guarantor {$i}'s address",
                                'notEmptyInvalid' => "Please select guarantor {$i}'s address"
                            )
                        )
                    )
                )
            ));
            $this->getElement('guarantor_address_' . $i)->removeDecorator('label');
            // Remove 'nnn not found in haystack' error
            $this->getElement('guarantor_address_' . $i)->setRegisterInArrayValidator(false);

            // Add in manual entry forms if guarantor address not present
            if ((isset($formData['guarantor_postcode_' . $i]) && trim($formData['guarantor_postcode_' . $i]) != '')) {
                Application_Core_FormUtils::getAddressByPostcode(
                    $this,
                    $formData,
                    trim($formData['guarantor_postcode_' . $i]),
                    'guarantor_address_' . $i,
                    'guarantor_housename_' . $i,
                    'guarantor_street_' . $i,
                    'guarantor_town_' . $i,
                    'guarantor_city_' . $i,
                    "guarantor {$i}'s"
                );
                $this->getElement('guarantor_address_' . $i)->setRequired(false);
            }

            $this->addElement('text', 'guarantor_homeletrefno_' . $i, array(
                'label'         => "HomeLet reference number",
                'required'      => true,
                'maxlength'     => '10',
                'filters'       => array('StringTrim'),
                'validators'    => array(
                    array(
                        'NotEmpty',true,array(
                        'messages'    => array(
                            'isEmpty'    => "Please enter guarantor {$i}'s HomeLet reference number",
                            'notEmptyInvalid' => "Please enter guarantor {$i}'s HomeLet reference number"
                        )
                    )
                    ),
                    array(
                        'regex', true, array(
                        'pattern' => '/\d{7}/',
                        'messages' => "Our reference number for guarantor {$i} must be at least seven digits long"
                    )
                    )
                )
            ));

            $this->addElement('text', 'guarantors_dob_' . $i, array(
                'label'             => "Date of Birth (DD/MM/YYYY)",
                'required'            => true,
                'class'                => "hasDatepicker",
                'maxlength'            => '10',
                'filters'            => array('StringTrim'),
                'validators'    => array(
                    array(
                        'NotEmpty',true,array(
                        'messages'    => array(
                            'isEmpty'    => "Please select guarantor {$i}'s date of birth",
                            'notEmptyInvalid' => "Please select guarantor {$i}'s date of birth"
                        )
                    )
                    ),
                    array(
                        'regex', true, array(
                        'pattern' => '/^\d\d\/\d\d\/\d\d\d\d$/',
                        'messages' => "Guarantor {$i}'s date of birth incomplete"
                    )
                    )
                )
            ));
        }

        // Hide errors
        Application_Core_FormUtils::removeFormErrors($this);
    }

    public function addTenants($formData)
    {
        for ($i = 1; $i <= $formData['total_tenants']; $i++) {

            // Tenant Name
            $this->addElement('text', 'tenant_name_' . $i, array(
                'label'      => 'Full name',
                'required'   => true,
                'filters'    => array('StringTrim'),
                'maxlength'  => '80',
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => "Please enter tenant {$i}'s name",
                                'notEmptyInvalid' => "Please enter tenant {$i}'s name"
                            )
                        )
                    )
                ),
                'onblur'    =>    'validateguarantor()'
            ));

            // Tenant Home Telno
            $this->addElement('text', 'tenant_hometelno_' . $i, array(
                'label'      => 'Home telephone number',
                'required'   => false,
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => "Please enter tenant {$i}'s home telephone number",
                            'notEmptyInvalid' => "Please enter tenant {$i}'s home telephone number"
                        )
                    )
                    )
                ),
                'onblur'    =>    'validateguarantor()'
            ));

            // Tenant Work Telno
            $this->addElement('text', 'tenant_worktelno_' . $i, array(
                'label'      => 'Work telephone number',
                'required'   => false,
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => "Please enter tenant {$i}'s work telephone number",
                            'notEmptyInvalid' => "Please enter tenant {$i}'s work telephone number"
                        )
                    )
                    )
                ),
                'onblur'    =>    'validateguarantor()'
            ));

            // Tenant Mobile Telno
            $this->addElement('text', 'tenant_mobiletelno_' . $i, array(
                'label'      => 'Mobile number',
                'required'   => false,
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => "Please enter tenant {$i}'s mobile telephone number",
                            'notEmptyInvalid' => "Please enter tenant {$i}'s mobile telephone number"
                        )
                    )
                    )
                ),
                'onblur'    =>    'validateguarantor()'
            ));

            if ($formData['tenant_mobiletelno_' . $i] == '' && $formData['tenant_hometelno_' . $i] == '') {
                $this->getElement('tenant_mobiletelno_' . $i)->setRequired(true);
                $this->getElement('tenant_hometelno_' . $i)->setRequired(true);
            }

            // Tenant Email
            $this->addElement('text', 'tenant_email_' . $i, array(
                'label'      => 'Email address',
                'required'   => false,
                'filters'    => array('StringTrim'),
                'maxlength'  => '50',
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => "Please enter tenant {$i}'s email address",
                            'notEmptyInvalid' => "Please enter tenant {$i}'s email address"
                        )
                    )
                    )
                ),
                'onblur'    =>    'validateguarantor()'
            ));




            // IRN
            $this->addElement('text', 'rg_policy_ref_' . $i, array(
                'label'             => "HomeLet reference number",
                'required'          => false,
                'maxlength'            => '10',
                'filters'            => array('StringTrim'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => "Please enter our reference number for tenant {$i}",
                                'notEmptyInvalid' => "Please enter our reference number for tenant {$i}"
                            )
                        )
                    ),
                    array(
                        'regex', true, array(
                            'pattern' => '/\d{7}/',
                            'messages' => "Our reference number for tenant {$i} must be at least seven digits long"
                        )
                    )
                ),
                'onblur'    =>    'validateguarantor()'
            ));

            // Tenants DOB
            $this->addElement('text', 'tenants_dob_' . $i, array(
                'label'             => "Date of Birth (DD/MM/YYYY)",
                'required'            => true,
                'class'                => "hasDatepicker",
                'maxlength'            => '10',
                'filters'            => array('StringTrim'),
                'validators'    => array(
                    array(
                        'NotEmpty',true,array(
                            'messages'    => array(
                                'isEmpty'    => "Please select tenant {$i}'s date of birth",
                                'notEmptyInvalid' => "Please select tenant {$i}'s date of birth"
                            )
                        )
                    ),
                    array(
                        'regex', true, array(
                             'pattern' => '/^\d\d\/\d\d\/\d\d\d\d$/',
                             'messages' => "Tenant {$i}'s date of birth incomplete"
                        )
                    )
                ),
                'onblur'    =>    'validateguarantor()'
            ));
        }

        // Hide errors
        Application_Core_FormUtils::removeFormErrors($this);
    }
}
