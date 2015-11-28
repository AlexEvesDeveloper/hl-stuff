<?php
class LandlordsReferencing_Form_ProspectiveLandlord extends Zend_Form
{
    public function init()
    {
        $session = new Zend_Session_Namespace('referencing_global');

        $this->addElement('checkbox', 'declaration', array(
            'required' => true,
            'checkedValue' => '1',
            'uncheckedValue' => null,
            'label_placement' => 'prepend',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'You have not ticked the box to confirm you are a landlord. You cannot proceed with this application unless you are a landlord and you tick this box.',
                            'notEmptyInvalid' => 'You must agree to the declaration'
                        )
                    )
                )
            )
        ));

        //Prospective landlord name
        $this->addElement('select', 'personal_title', array(
            'label' => 'Title',
            'required' => true,
            'multiOptions' => array(
                'Not Known' => 'Not Known',
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
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
    
        //First name entry
        $this->addElement('text', 'first_name', array(
            'label' => 'First Name',
            'required' => true,
            'filters' => array('StringTrim'),
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
                'data-ctfilter' => 'yes',
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'name',
                'class' => 'form-control',
            )
        ));
        
        //Last name entry
        $this->addElement('text', 'last_name', array(
            'label' => 'Last Name',
            'required' => true,
            'filters' => array('StringTrim'),
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
                'data-ctfilter' => 'yes',
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'name',
                'class' => 'form-control',
            )
        ));
        
        
        //Prospective landlord address details.
        $this->addElement('hidden', 'property_number_name', array(
            'required' => false
        ));

        // Add postcode element
        $this->addElement('text', 'property_postcode', array(
            'label' => '',
            'required' => true,
            'filters' => array('StringTrim'),
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
            'label' => 'Please select your address',
            'required' => true,
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
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));        

        // Grab view and add the address lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/common/js/addressLookup.js',
            'text/javascript'
        );
        // Add in any js specific to landlords referencing
        $view->headScript()->appendFile(
            '/assets/landlords-referencing/js/addressLookup.js',
            'text/javascript'
        );

        //Phone number entry
        $this->addElement('text', 'telephone_day', array(
            'label' => 'Tel No (day):',
            'required' => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'A daytime number is required',
                            'notEmptyInvalid' => 'A daytime number is required'
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
                'data-ctfilter' => 'yes',
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'phone',
                'class' => 'form-control',
            )
        ));
        
        //Fax number entry
        $this->addElement('text', 'fax_number', array(
            'label' => 'Fax No',
            'required' => false,
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^((\+44\s?\(0\)\s?\d{2,4})|(\+44\s?(01|02|03|07|08)\d{2,3})|(\+44\s?(1|2|3|7|8)\d{2,3})|(\(\+44\)\s?\d{3,4})|(\(\d{5}\))|((01|02|03|07|08)\d{2,3})|(\d{5}))(\s|-|.)(((\d{3,4})(\s|-)(\d{3,4}))|((\d{6,7})))$/',
                        'messages' => 'Not a valid fax number'
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));
        
        //Evening phone number
        $this->addElement('text', 'telephone_evening', array(
            'label' => 'Tel No (evening):',
            'required' => false,
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^((\+44\s?\(0\)\s?\d{2,4})|(\+44\s?(01|02|03|07|08)\d{2,3})|(\+44\s?(1|2|3|7|8)\d{2,3})|(\(\+44\)\s?\d{3,4})|(\(\d{5}\))|((01|02|03|07|08)\d{2,3})|(\d{5}))(\s|-|.)(((\d{3,4})(\s|-)(\d{3,4}))|((\d{6,7})))$/',
                        'messages' => 'Not a valid phone number'
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));

        //Email entry. If the user is a PLL then we need to ensure that they do not erase their
        //email address and create an empty email value in the dbase.
        if($session->userType == Model_Referencing_ReferenceUserTypes::PRIVATE_LANDLORD) {
            $this->addElement('text', 'email', array(
                'label' => 'Email address',
                'required' => true,
                'filters' => array('StringTrim'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => 'Please enter your email address'
                            )
                        )
                    )
                ),
                'attribs' => array(
                    'data-ctfilter' => 'yes',
                    'data-required' => 'required',
                    'data-validate' => 'validate',
                    'data-type' => 'email',
                    'class' => 'form-control',
                )
            ));
        }
        else {
            $this->addElement('text', 'email', array(
                'label' => 'Email address',
                'required' => false,
                'filters' => array('StringTrim'),
                'attribs' => array(
                    'data-ctfilter' => 'yes',
                    'data-validate' => 'validate',
                    'data-type' => 'email',
                    'class' => 'form-control',
                )
            ));
        }

        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT => "Invalid email address"
            )
        );
        $this->getElement('email')->addValidator($emailValidator);        
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'landlords-referencing/prospective-landlord.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Label'
        ));

        //Finally, remove the default decorators from the hidden elements.
        $this->property_number_name->removeDecorator('HtmlTag');
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

        return parent::isValid($formData);
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


        //Retrieve the main reference details.
        $referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($session->referenceId);


        //Create a new PLL record if not already done so.
        if(empty($reference->propertyLease->prospectiveLandlord)) {

            $prospectiveLandlordManager = new Manager_Referencing_ProspectiveLandlord();
            $reference->propertyLease->prospectiveLandlord = $prospectiveLandlordManager->insertPlaceholder(
                $session->referenceId);
        }
        
        
        //Record the PLL's name
        if(empty($reference->propertyLease->prospectiveLandlord->name)) {

            $nameManager = new Manager_Core_Name();
            $reference->propertyLease->prospectiveLandlord->name = $nameManager->createName();
        }

        $reference->propertyLease->prospectiveLandlord->name->title = $data['personal_title'];
        $reference->propertyLease->prospectiveLandlord->name->firstName = $data['first_name'];
        $reference->propertyLease->prospectiveLandlord->name->lastName = $data['last_name'];


        //Record the PLL's contact details
        if(empty($reference->propertyLease->prospectiveLandlord->contactDetails)) {

        	$contactDetailsManager = new Manager_Core_ContactDetails();
            $reference->propertyLease->prospectiveLandlord->contactDetails = $contactDetailsManager->createContactDetails();
        }

        $reference->propertyLease->prospectiveLandlord->contactDetails->telephone1 = $data['telephone_day'];
        $reference->propertyLease->prospectiveLandlord->contactDetails->telephone2 = $data['telephone_evening'];
        $reference->propertyLease->prospectiveLandlord->contactDetails->fax1 = $data['fax_number'];
        $reference->propertyLease->prospectiveLandlord->contactDetails->email1 = $data['email'];

        
        //Record the PLL's address.
        $postcodeManager = new Manager_Core_Postcode();
        $propertyAddress = $postcodeManager->getPropertyByID($data['property_address'], false);
        
        $addressLine1 =
            (($propertyAddress['organisation'] != '') ? "{$propertyAddress['organisation']}, " : '')
            . (($propertyAddress['houseNumber'] != '') ? "{$propertyAddress['houseNumber']} " : '')
            . (($propertyAddress['buildingName'] != '') ? "{$propertyAddress['buildingName']}, " : '')
            . $propertyAddress['address2'];
        
        $addressLine2 = $propertyAddress['address4'];
        $town = $propertyAddress['address5'];
        $postCode = $data['property_postcode'];     	
        
        if(empty($reference->propertyLease->prospectiveLandlord->address)) {
            $addressManager = new Manager_Core_Address();
            $reference->propertyLease->prospectiveLandlord->address = $addressManager->createAddress();
        }
        
        $reference->propertyLease->prospectiveLandlord->address->addressLine1 = $addressLine1;
        $reference->propertyLease->prospectiveLandlord->address->addressLine2 = $addressLine2;
        $reference->propertyLease->prospectiveLandlord->address->town = $town;
        $reference->propertyLease->prospectiveLandlord->address->postCode = $postCode;

        
        //And update... both the reference and the PLL customer details, which are held in the
        //legacy datasources.
        $referenceManager->updateReference($reference);
        
        
        //Next, update the customer record if the user is a PLL. If the user is a reference subject,
        //then we do not want to overwrite the PLL (customer) details with the data they have
        //entered.
        if($session->userType == Model_Referencing_ReferenceUserTypes::PRIVATE_LANDLORD) {

            //Retrieve the primary customer details - ugly I know.
            $session = new Zend_Session_Namespace('referencing_global');

            $customerManager = new Manager_Referencing_Customer();
            $customer = $customerManager->getCustomer($session->customerId);
            $customer->setTitle($data['personal_title']);
            $customer->setFirstName($data['first_name']);
            $customer->setLastName($data['last_name']);
            $customer->setTelephone(Model_Core_Customer::TELEPHONE1, $data['telephone_day']);
            $customer->setTelephone(Model_Core_Customer::TELEPHONE2, $data['telephone_evening']);
            $customer->setFax($data['fax_number']);
            $customer->setEmailAddress($data['email']);
            $customer->setAddressLine(Model_Core_Customer::ADDRESSLINE1, $addressLine1);
            $customer->setAddressLine(Model_Core_Customer::ADDRESSLINE2, $addressLine2);
            $customer->setAddressLine(Model_Core_Customer::ADDRESSLINE3, $town);
            $customer->setPostCode($postCode);
            $customerManager->updateCustomer($customer);
        }
    }


    public function forcePopulate($formData)
    {
        //Populate the form elements with data retrieved from the datasource, unless
        //the user has provided new datas.
//        $auth = Zend_Auth::getInstance();
//        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));
//        $session = $auth->getStorage()->read();
        $session = new Zend_Session_Namespace('referencing_global');

        $customerManager = new Manager_Referencing_Customer();
        $customer = $customerManager->getCustomer($session->customerId);

        if(empty($formData['personal_title'])) {
            $formData['personal_title'] = $customer->getTitle();
        }

        if(empty($formData['first_name'])) {

            $formData['first_name'] = $customer->getFirstName();
        }

        if(empty($formData['last_name'])) {

            $formData['last_name'] = $customer->getLastName();
        }

        if(empty($formData['property_postcode'])) {

            $formData['property_postcode'] = $customer->getPostCode();
        }

        if(empty($formData['telephone_day'])) {

            $formData['telephone_day'] = $customer->getTelephone(Model_Core_Customer::TELEPHONE1);
        }

        if(empty($formData['fax_number'])) {

            $formData['fax_number'] = $customer->getFax();
        }

        if(empty($formData['telephone_evening'])) {

            $formData['telephone_evening'] = $customer->getTelephone(Model_Core_Customer::TELEPHONE2);
        }

        if(empty($formData['email'])) {

            $formData['email'] = $customer->getEmailAddress();
        }

        $postcode = new Manager_Core_Postcode();
        $addresses = $postcode->getPropertiesByPostcode($formData['property_postcode']);

        $filterString = $customer->getAddressLine(1);

        $addressList = array();
        $addressID=0;
        foreach($addresses as $address) {
            $addressList[$address['id']] = $address['singleLineWithoutPostcode'];
            $cleanAddress = str_replace(",", "", $address['singleLineWithoutPostcode']);
            if (stripos($cleanAddress, $filterString) === 0) {
                $addressID = $address['id'];
            }
        }
        // Add some validation
        $property_address = $this->getElement('property_address');
        $property_address->setMultiOptions($addressList);
        $validator = new Zend_Validate_InArray(array(
            'haystack' => array_keys($addressList)
        ));
        $validator->setMessages(array(
            Zend_Validate_InArray::NOT_IN_ARRAY => 'Insured address does not match with postcode'
        ));
        $property_address->addValidator($validator, true);

        // Set the address to selected

        $property_address->setValue($addressID);
        //Allow Zend to complete the population process.
        $this->populate($formData);
        $this->property_address->setValue($addressID);
    }


    public function getMessagesFlattened()
    {
        return $this->getMessages();
    }
}
