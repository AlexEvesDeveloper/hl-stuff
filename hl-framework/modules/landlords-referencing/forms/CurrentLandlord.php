<?php

class LandlordsReferencing_Form_CurrentLandlord extends Zend_Form
{
    public function init()
    {
        //Prospective landlord name
        $this->addElement('select', 'personal_title', array(
            'label'     => 'Title',
            'required'  => true,
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
                            'isEmpty' => 'Please select a title',
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
            'label'      => 'First Name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a first name'
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
            'label'      => 'Last Name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a last name'
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
            'required'  => false
        ));

        // Add postcode element
        $this->addElement('text', 'property_postcode', array(
            'required'   => true,
            'label' => '',
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
                            'isEmpty' => 'Please select a property address',
                            'notEmptyInvalid' => 'Please select a property address'
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

        //Phone number entry
        $this->addElement('text', 'telephone_day', array(
            'label'      => 'Tel No (day):',
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
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));

        //Fax number entry
        $this->addElement('text', 'fax_number', array(
            'label'      => 'Fax No',
            'required'   => false,
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
            'label'      => 'Tel No (evening):',
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
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));

        // Email entry
        $this->addElement('text', 'email', array(
            'label'      => 'Email address',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));

        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Invalid email address"
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
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        //Finally, remove the default decorators from the hidden elements.
        $this->property_number_name->removeDecorator('HtmlTag');
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

        $referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($session->referenceId);

        //Locate the current residence object, so that we can attach the current landlords
        //reference to that.
        foreach ($reference->referenceSubject->residences as $residence) {
            if ($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
                $currentResidence = $residence;
                break;
            }
        }

        //Create a new residential referee if one does not exist already.
        if (empty($currentResidence->refereeDetails)) {
            $refereeManager = new Manager_Referencing_ResidenceReferee();
            $currentResidence->refereeDetails = $refereeManager->createReferee($currentResidence->id);
        }

        //There is currently no way to capture the type of residential referee, so
        //assign a default for now.
        $currentResidence->refereeDetails->type = Model_Referencing_ResidenceRefereeTypes::PRIVATE_LANDLORD;

        //Record the referee's name.
        if (empty($currentResidence->refereeDetails->name)) {
            $nameManager = new Manager_Core_Name();
            $currentResidence->refereeDetails->name = $nameManager->createName();
        }

        $currentResidence->refereeDetails->name->title = $data['personal_title'];
        $currentResidence->refereeDetails->name->firstName = $data['first_name'];
        $currentResidence->refereeDetails->name->lastName = $data['last_name'];

        //Record the referee's contact details
        if (empty($currentResidence->refereeDetails->contactDetails)) {
            $contactDetailsManager = new Manager_Core_ContactDetails();
            $currentResidence->refereeDetails->contactDetails = $contactDetailsManager->createContactDetails();
        }

        $currentResidence->refereeDetails->contactDetails->telephone1 = $data['telephone_day'];
        $currentResidence->refereeDetails->contactDetails->telephone2 = $data['telephone_evening'];
        $currentResidence->refereeDetails->contactDetails->fax1 = $data['fax_number'];
        $currentResidence->refereeDetails->contactDetails->email1 = $data['email'];

        //Record the referee's address.
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

        if (empty($currentResidence->refereeDetails->address)) {
            $addressManager = new Manager_Core_Address();
            $currentResidence->refereeDetails->address = $addressManager->createAddress();
        }

        $currentResidence->refereeDetails->address->addressLine1 = $addressLine1;
        $currentResidence->refereeDetails->address->addressLine2 = $addressLine2;
        $currentResidence->refereeDetails->address->town = $town;
        $currentResidence->refereeDetails->address->postCode = $postCode;

        //Update progress.

        //And update...
        $referenceManager->updateReference($reference);
    }

    public function isValid($formData = array())
    {
        if ((isset($formData['property_postcode']) && '' != trim($formData['property_postcode']))) {
            $postcode = trim($formData['property_postcode']);
            $postcodeLookup = new Manager_Core_Postcode();
            $addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $postcode));

            $addressList = array('' => '--- please select ---');
            foreach ($addresses as $address) {
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

    public function getMessagesFlattened()
    {
        return $this->getMessages();
    }
}