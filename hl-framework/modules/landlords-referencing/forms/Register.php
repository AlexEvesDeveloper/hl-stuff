<?php
class LandlordsReferencing_Form_Register extends Zend_Form {

    public function init() {
        $this->addSubForm(new LandlordsReferencing_Form_Subforms_DataProtection(), 'subform_dataprotection');
        
		$this->setMethod('post');

        //Prospective landlord name
        $this->addElement('select', 'title', array(
            'label'     => 'Title *',
            'required'  => true,
            'multiOptions' => array(
                'Mr' => 'Mr',
                'Ms' => 'Ms',
                'Mrs' => 'Mrs',
                'Miss' => 'Miss',
                'Dr' => 'Dr',
                'Prof' => 'Professor',
                'Sir' => 'Sir'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please select your title',
                        'notEmptyInvalid' => 'Please select a valid title'
                    )
                )
                )
            )
        ));

        //First name entry
        $this->addElement('text', 'first_name', array(
            'label'      => 'First Name *',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your first name'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        //Last name entry
        $this->addElement('text', 'last_name', array(
            'label'      => 'Last Name *',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your last name'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        //Prospective landlord address details.
		$hiddenElement = new Zend_Form_Element_Hidden('property_number_name');
		$hiddenElement->setRequired(false);
		$hiddenElement->clearDecorators();
		$this->addElement($hiddenElement);
		

        // Add postcode element
        $this->addElement('text', 'property_postcode', array(
            'label'      => 'Postcode *',
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
                'data-ctfilter' => 'yes'
            )
        ));

        // Add address select element
        $this->addElement('select', 'property_address', array(
            'label'     => 'Please select your address *',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- please select ---'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your property address',
                            'notEmptyInvalid' => 'Please select your property address'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));        
      
        
        //Phone number entry
        $this->addElement('text', 'phone_number', array(
            'label'      => 'Telephone Number *',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your phone number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^((\+44\s?\(0\)\s?\d{2,4})|(\+44\s?(01|02|03|07|08)\d{2,3})|(\+44\s?(1|2|3|7|8)\d{2,3})|(\(\+44\)\s?\d{3,4})|(\(\d{5}\))|((01|02|03|07|08)\d{2,3})|(\d{5}))(\s|-|.)(((\d{3,4})(\s|-)(\d{3,4}))|((\d{6,7})))$/',
                        'messages' => 'Not a valid phone number'
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        //Fax number entry
        $this->addElement('text', 'fax_number', array(
            'label'      => 'Fax Number',
            'required'   => false,
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^((\+44\s?\(0\)\s?\d{2,4})|(\+44\s?(01|02|03|07|08)\d{2,3})|(\+44\s?(1|2|3|7|8)\d{2,3})|(\(\+44\)\s?\d{3,4})|(\(\d{5}\))|((01|02|03|07|08)\d{2,3})|(\d{5}))(\s|-|.)(((\d{3,4})(\s|-)(\d{3,4}))|((\d{6,7})))$/',
                        'messages' => 'Not a valid phone number'
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        //Mobile number entry. We do not currently capture this, so WTF do we ask for it?
        $this->addElement('text', 'mobile_number', array(
            'label'      => 'Mobile Number *',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your mobile number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^07([\d]{3})[(\D\s)]?[\d]{3}[(\D\s)]?[\d]{3}$/',
                        'messages' => 'Not a valid mobile phone number'
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        
		//The email elements.
		$emailElement = new Zend_Form_Element_Text('email');
		$emailElement->setLabel('Email *');
		$emailElement->setRequired(true);
		$emailElement->addFilter(new Zend_Filter_StringTrim());
		
		$validator = new Zend_Validate_NotEmpty();
		$validator->setMessage('Please enter your email address');
		$emailElement->addValidator($validator);
		
		$validator = new Zend_Validate_EmailAddress();
        $validator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Invalid email address"
            )
        );
        $emailElement->addValidator($validator);
		$this->addElement($emailElement);


		//The password element.
		$passwordElement = new Zend_Form_Element_Password('password');
		$passwordElement->setRequired(true);
		$passwordElement->setLabel('Password *');

        $validator = new Zend_Validate_NotEmpty();
        $validator->setMessage('Please set a password');
        $passwordElement->addValidator($validator);

		$passwordElement->addValidator(new Zend_Validate_PasswordStrength());
		
		$validator = new Zend_Validate_Identical();
		$validator->setToken('confirm_password');
		$validator->setMessage('Passwords are not the same', Zend_Validate_Identical::NOT_SAME);
		$passwordElement->addValidator($validator);
		$this->addElement($passwordElement);
		
		
		//The confirm password element.
		$confirmPasswordElement = new Zend_Form_Element_Password('confirm_password');
		$confirmPasswordElement->setRequired(true);
		$confirmPasswordElement->setLabel('Confirm Password *');
		
		$validator = new Zend_Validate_NotEmpty();
		$validator->setMessage('Please confirm your password');
		$confirmPasswordElement->addValidator($validator);
		$this->addElement($confirmPasswordElement);

        // Security question & answer
        $this->addElement('select', 'security_question', array(
            'label'     => 'Security Question *',
            'required'  => true,
            'multiOptions' => array('' => 'Please select'),
            'registerInArrayValidator' => false,
            'decorators' => array (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please select your security question',
                        'notEmptyInvalid' => 'Please select your security question'
                        )
                    )
                )
            )
        ));

        $this->addElement('text', 'security_answer', array(
            'label'      => 'Answer *',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter your security answer'
                    )
                )
                )
            ),
        ));

        $this->addElement('text', 'insurance_renewal_date', array(
            'label'     => 'Next Landlords Insurance Renewal Date (dd/mm/yyyy)',
            'required'  => false,
            'filters'    => array('StringTrim')
        ));
        
        $insuranceRenewalDate = $this->getElement('insurance_renewal_date');
        $validator = new Zend_Validate_DateCompare();
        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')));
//        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 60 * 60 * 24 * 30);
        $validator->setMessages(array(
            'msgMinimum' => 'Insurance renewal date cannot be in the past',
//            'msgMaximum' => 'Tenancy start date cannot be more than 30 days in the future'
        ));
        $insuranceRenewalDate->addValidator($validator, true);

        
        //Grab view and add the date picker JavaScript files into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headLink()->appendStylesheet(
            '/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css',
            'screen'
        );

        $view->headScript()->appendFile(
            '/assets/vendor/jquery-date/js/date.js',
            'text/javascript'
            )->appendFile(
                '/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
                'text/javascript'
            )->appendFile(
                '/assets/landlords-referencing/js/referencingInsuranceRenewalDatePicker.js',
                'text/javascript'
        );
        
        //The submit button
        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);


        //Apply decorators. This has to be done unusually for t'ings to work.
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        $submitElement->removeDecorator('label');
        $this->property_number_name->removeDecorator('HtmlTag');

 
        //Grab view and add the address lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/common/js/addressLookup.js',
            'text/javascript'
        );
    }
	
	
    public function isValid($formData = array())
    {
	    if ((isset($formData['property_postcode']) && trim($formData['property_postcode']) != '')) {
            $postcode = trim($formData['property_postcode']);
            $postcodeLookup = new Manager_Core_Postcode();
            $addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $postcode));
            
            $addressList = array('' => '--- please select ---');
            foreach($addresses as $address) {
                $addressList[$address['id']] = $address['singleLineWithoutPostcode'];
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
    	if (isset($formData['property_postcode'])) {
            $this->getElement('property_number_name')->setRequired(false);
        }
		
        // If a landline phone number is given, mobile is not mandatory
        if (isset($formData['phone_number']) && trim($formData['phone_number']) != '') {
            $this->getElement('mobile_number')->setRequired(false);
        }
        
        // If a mobile phone number is given, landline is not mandatory
        if (isset($formData['mobile_number']) && trim($formData['mobile_number']) != '') {
            $this->getElement('phone_number')->setRequired(false);
        }

        $result = parent::isValid($formData);

        $customerManager = new Manager_Core_Customer();
        $customer = $customerManager->getCustomerByEmailAddress($formData['email']);

        if ($customer) {
            // Customer exists, error the form
            $this->email->addError('This email is already in use. Have you signed up before?')->markAsError();
            return false;
        }
        else {
            return $result;
        }
	}
	
	
	/**
     * Saves the form data to the datastore.
     * 
     * @return void
     */
	public function saveData()
    {
		$session = new Zend_Session_Namespace('referencing_global');
    	$data = $this->getValues();
		
		// Create the new customer in the legacy datasource only.
		$customerManager = new Manager_Core_Customer();
		$customer = $customerManager->createNewCustomer($data['email'], Model_Core_Customer::CUSTOMER);
		
		// Update the newly created customer with values submitted on the registration form.
        //$customer->setTitle($data['personal_title']);
        $customer->setTitle($data['title']);
		$customer->setFirstName($data['first_name']);
		$customer->setLastName($data['last_name']);
		$customer->setTelephone(Model_Core_Customer::TELEPHONE1, $data['phone_number']);
		$customer->setTelephone(Model_Core_Customer::TELEPHONE2, $data['mobile_number']);
		$customer->setFax($data['fax_number']);
		$customer->setEmailAddress($data['email']);
		$customer->setPassword($data['password']);
        $customer->setSecurityQuestion($data['security_question']);
        $customer->setSecurityAnswer($data['security_answer']);
		
		//Address update
		$postcode = new Manager_Core_Postcode();
		$propertyAddress = $postcode->getPropertyByID($data['property_address'], false);
		
		$addressLine1 = 
			(($propertyAddress['organisation'] != '') ? "{$propertyAddress['organisation']}, " : '')
			. (($propertyAddress['houseNumber'] != '') ? "{$propertyAddress['houseNumber']} " : '')
			. (($propertyAddress['buildingName'] != '') ? "{$propertyAddress['buildingName']}, " : '')
			. $propertyAddress['address2'];        	
		
		$customer->setAddressLine(Model_Core_Customer::ADDRESSLINE1, $addressLine1);
		$customer->setAddressLine(Model_Core_Customer::ADDRESSLINE2, $propertyAddress['address4']);
		$customer->setAddressLine(Model_Core_Customer::ADDRESSLINE3, $propertyAddress['address5']);
		$customer->setPostCode($data['property_postcode']);
        $customer->typeID = Model_Core_Customer::CUSTOMER;
		
	
		// Update the customer record
		$customerManager->updateCustomer($customer);
		
//		// Log the customer in automatically
//        $auth = Zend_Auth::getInstance();
//        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

//        $customerManager = new Manager_Core_Customer();
//        $adapter = $customerManager->getAuthAdapter(array('email' => $data['email'], 'password' => $data['password']));
//        $auth->authenticate($adapter);

//        // Writer customer data to session
//        $storage = $auth->getStorage();
//        $storage->write($adapter->getResultRowObject(array(
//            'title',
//            'first_name',
//            'last_name',
//            'email_address',
//            'id')));

	    //Finally, set the necessary session variables.
        $session->awaitingvalidation = 1;
	    $session->customerId = $customer->getIdentifier(Model_Core_Customer::IDENTIFIER);

        // Save dpa preferences for direct landlord to insurance dpa system - direct landlords save their customer records to insurance
        $dpaManager = new Manager_Core_DataProtection();
        
        $item = new Model_Core_DataProtection_Item();
        $item->itemGroupId = $customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER);
        $item->entityTypeId = Model_Core_DataProtection_ItemEntityTypes::INSURANCE;
        
        // Phone and post
        $item->constraintTypeId = Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_PHONEANDPOST;
        $item->isAllowed = $data['subform_dataprotection']['dpa_phone_post'] == 1 ? true : false;
        $dpaManager->upsertItem($item);
        
        // Sms and email
        $item->constraintTypeId = Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_SMSANDEMAIL;
        $item->isAllowed = $data['subform_dataprotection']['dpa_sms_email'] == 1 ? true : false;
        $dpaManager->upsertItem($item);
        
        // Third party sale
        $item->constraintTypeId = Model_Core_DataProtection_ItemConstraintTypes::MARKETING_BY_THIRDPARTY;
        $item->isAllowed = $data['subform_dataprotection']['dpa_resale'] == 1 ? true : false;
        $dpaManager->upsertItem($item);

        // Save insurance renewal mi data, if provided
        if ($this->getElement('insurance_renewal_date')->getValue() != '') {
            $renewalDate = new Zend_Date($this->getElement('insurance_renewal_date')->getValue(), Zend_Date::DAY . '/' . Zend_Date::MONTH . '/' . Zend_Date::YEAR);

            $miInsuranceRenewalDataSource = new Datasource_Referencing_MiInsuranceRenewal();
            $miInsuranceRenewalDataSource->insertMiData($customer->getIdentifier(Model_Core_Customer::IDENTIFIER), $renewalDate);
        }

        // Create sign-up completion email
        $customer->sendAccountValidationEmail();
	}

	public function getMessagesFlattened() {
		
		return $this->getMessages();
	}
}
