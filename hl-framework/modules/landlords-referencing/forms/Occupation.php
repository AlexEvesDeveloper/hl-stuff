<?php

class LandlordsReferencing_Form_Occupation extends Zend_Form
{
    public function init()
    {
        //If the applicant does not have future employment then tick this box
        $this->addElement('checkbox', 'cancel_future_employment', array(
            'label'         => 'If the applicant does not have future employment then tick this box',
            'required'      => false,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'If the applicant does not have future employment then tick this box'
                        )
                    )
                )
            )
        ));

        //Company name entry
        $this->addElement('text', 'company_name', array(
            'label'      => 'Company Name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the Company name'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'class' => 'form-control',
            )
        ));

        //First name entry
        $this->addElement('text', 'contact_name', array(
            'label'      => 'Contact Name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the contact name'
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
        $this->addElement('text', 'contact_position', array(
            'label'      => 'Contact Position',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the contact position'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'data-required' => 'required',
                'class' => 'form-control',
            )
        ));


        // Add house number/name element
        $this->addElement('hidden', 'property_number_name', array(
            'required'  => false,
        ));

        // Add postcode element
        $this->addElement('text', 'property_postcode', array(
            'label'      => '',
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
            'label'     => 'Please select the address',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- please select ---'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the company address',
                            'notEmptyInvalid' => 'Please select the company address'
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
        $this->addElement('text', 'telephone_number', array(
            'label'      => 'Tel',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the contact telephone number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^((\+44\s?\(0\)\s?\d{2,4})|(\+44\s?(01|02|03|07|08)\d{2,3})|(\+44\s?(1|2|3|7|8)\d{2,3})|(\(\+44\)\s?\d{3,4})|(\(\d{5}\))|((01|02|03|07|08)\d{2,3})|(\d{5}))(\s|-|.)(((\d{3,4})(\s|-)(\d{3,4}))|((\d{6,7})))$/',
                        'messages' => 'Not a valid telephone number'
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
            'label'      => 'Fax',
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

        // Email entry
        $this->addElement('text', 'email', array(
            'label'      => 'Email',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the contact email address'
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

        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Invalid email address"
            )
        );
        $this->getElement('email')->addValidator($emailValidator);

        //Identify any additional form elements applicable for data capture. To do this we first
        //need to understand which occupation we are processing.
        $referenceManager = new Manager_Referencing_Reference();

        $session = new Zend_Session_Namespace('referencing_global');
        $reference = $referenceManager->getReference($session->referenceId);

        switch ($session->currentFlowItem) {
            case Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION:
                $chronology = Model_Referencing_OccupationChronology::CURRENT;
                $classifier = Model_Referencing_OccupationImportance::FIRST;
                break;
            case Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION:
                $chronology = Model_Referencing_OccupationChronology::CURRENT;
                $classifier = Model_Referencing_OccupationImportance::SECONDARY;
                break;
            case Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION:
                $chronology = Model_Referencing_OccupationChronology::FUTURE;
                $classifier = Model_Referencing_OccupationImportance::FIRST;
                break;
        }

        //Find the occupation currently being processed.
        if (!empty($reference->referenceSubject->occupations)) {
            foreach ($reference->referenceSubject->occupations as $occupation) {
                if ($occupation->chronology == $chronology) {
                    if ($occupation->importance == $classifier) {
                        $thisOccupation = $occupation;
                        break;
                    }
                }
            }
        }

        if (empty($thisOccupation)) {
            throw new Zend_Exception("Unable to locate occupation details.");
        }

        //Now that the occupation has been found, identify what additional form elements are applicable
        //for data capture.
        $session->occupationType = $thisOccupation->type;
        $session->occupationChronology = $thisOccupation->chronology;
        $session->occupationClassifier = $thisOccupation->importance;

        switch($thisOccupation->type) {
            case Model_Referencing_OccupationTypes::EMPLOYMENT:
                $this->_addEmploymentElements();
                break;
            case Model_Referencing_OccupationTypes::CONTRACT:
                $this->_addContractElements();
                break;
            case Model_Referencing_OccupationTypes::SELFEMPLOYMENT:
                $this->_addSelfEmploymentElements();
                break;
            case Model_Referencing_OccupationTypes::INDEPENDENT:
                $this->_addIndependentElements();
                break;
            case Model_Referencing_OccupationTypes::RETIREMENT:
                $this->_addRetirementElements();
                break;
        }

        //Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'landlords-referencing/occupation.phtml'))
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
    
    protected function _addEmploymentElements()
    {
        //Identify labels applicable to either employment or contract.
        $session = new Zend_Session_Namespace('referencing_global');
        if ($session->occupationType == Model_Referencing_OccupationTypes::EMPLOYMENT) {
            $incomeLabel = 'Salary';
            $errorMessage = 'Please enter the salary';
        }
        else {
            $incomeLabel = 'Earnings';
            $errorMessage = 'Please enter the earnings';
        }

        //Income entry
        $this->addElement('text', 'income', array(
            'label'      => $incomeLabel,
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('Digits', true, array(
                        'messages' => array(
                            'notDigits' => "$incomeLabel can only contain numbers",
                            'digitsStringEmpty' => $errorMessage
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'currency',
                'class'=>'currency form-control',
            )
        ));

        //Payroll entry
        $this->addElement('text', 'reference_number', array(
            'label'      => 'Payroll No',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the payroll number'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control'
            )
        ));

        //Payroll entry
        $this->addElement('text', 'position', array(
            'label'      => 'Position Held',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the position held'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control'
            )
        ));

        //Starting date. Use the 'tenancy start date' dropdowns for now, then re-purpose later.
        $this->addElement('text', 'tenancy_start_date', array(
            'label'     => 'Starting Date in this position',
            'required'  => true,
            'filters'    => array('StringTrim')
        ));

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
                '/assets/landlords-referencing/js/referencingBirthDatePicker.js',
                'text/javascript'
        );

        //Is the position permanent
        $this->addElement('select', 'is_permanent', array(
            'label'     => 'Is this position permanent?',
            'required'  => true,
            'multiOptions' => array(
                '' => 'Please Select',
                'Yes' => 'Yes',
                'No' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select if this position is permanent',
                            'notEmptyInvalid' => 'Please select if this position is permanent'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control'
            )
        ));

        //Will the position change. Not applicable to future employers.
        if ($session->occupationChronology != Model_Referencing_OccupationChronology::FUTURE) {
            $this->addElement('select', 'will_change', array(
                'label'     => 'Will this employment change before or during the tenancy?',
                'required'  => true,
                'multiOptions' => array(
                    '' => 'Please Select',
                    'Yes' => 'Yes',
                    'No' => 'No'
                ),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => 'Please select if this employment will change',
                                'notEmptyInvalid' => 'Please select if this employment will change'
                            )
                        )
                    )
                ),
                'attribs' => array(
                    'class' => 'form-control'
                )
            ));
        }
    }
    
    protected function _addContractElements()
    {
        $this->_addEmploymentElements();
    }
    
    protected function _addRetirementElements()
    {
        //Income entry
        $this->addElement('text', 'income', array(
            'label'      => 'Pension',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the pension'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'currency',
                'class'=>'currency form-control',
            )
        ));
        
        //Payroll entry
        $this->addElement('text', 'reference_number', array(
            'label'      => 'Pension Ref No',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the pension reference number'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control'
            )
        ));
    }
    
    
    protected function _addSelfEmploymentElements()
    {
        $this->_addIndependentElements();
    }
    
    
    protected function _addIndependentElements()
    {
        //Income entry
        $this->addElement('text', 'income', array(
            'label'      => 'Income',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the income per year'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'currency',
                'class'=>'currency form-control',
            )
        ));
    }

    public function isValid($formData = array())
    {
        if ((isset($formData['property_postcode']) && trim($formData['property_postcode']) != '')) {
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

        if (isset($formData['cancel_future_employment']) && $formData['cancel_future_employment'] == 1) {
             $this->getElement('company_name')->setRequired(false);
             $this->getElement('contact_name')->setRequired(false);
             $this->getElement('contact_position')->setRequired(false);

             $this->getElement('property_number_name')->setRequired(false);
             $this->getElement('property_postcode')->setRequired(false);
             $this->getElement('property_address')->setRequired(false);

             $this->getElement('telephone_number')->setRequired(false);
             $this->getElement('email')->setRequired(false);
             $this->getElement('income')->setRequired(false);
             $this->getElement('reference_number')->setRequired(false);
             $this->getElement('position')->setRequired(false);
             $this->getElement('is_permanent')->setRequired(false);
             $this->getElement('tenancy_start_date')->setRequired(false);
        }

        return parent::isValid($formData);
    }

    public function saveData()
    {
        $session = new Zend_Session_Namespace('referencing_global');
        $data = $this->getValues();

        $referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($session->referenceId);

        //Derive the occupation chronology from the current flow item, so that we can locate
        //the relevant occupation to update.
        switch ($session->currentFlowItem) {
            case Model_Referencing_DataEntry_FlowItems::CURRENT_OCCUPATION:
                $chronology = Model_Referencing_OccupationChronology::CURRENT;
                $classification = Model_Referencing_OccupationImportance::FIRST;
                break;
            case Model_Referencing_DataEntry_FlowItems::SECOND_OCCUPATION:
                $chronology = Model_Referencing_OccupationChronology::CURRENT;
                $classification = Model_Referencing_OccupationImportance::SECOND;
                break;
            case Model_Referencing_DataEntry_FlowItems::FUTURE_OCCUPATION:
                $chronology = Model_Referencing_OccupationChronology::FUTURE;
                $classification = Model_Referencing_OccupationImportance::FIRST;
                break;
        }

        //Attept to locate the relevant occupation.
        $occupationManager = new Manager_Referencing_Occupation();
        $thisOccupation = $occupationManager->findSpecificOccupation(
            $reference->referenceSubject->occupations,
            $chronology,
            $classification);

        if (empty($thisOccupation)) {
            //The occupation to process does not exist, so create it first.
            $thisOccupation = $occupationManager->createNewOccupation($session->referenceId, $chronology, $classification);

            if (empty($reference->referenceSubject->occupations)) {
                $reference->referenceSubject->occupations = array();
            }
            $reference->referenceSubject->occupations[] = $thisOccupation;
        }

        //Now update $thisOccupation with the occupational details provided by the ReferenceSubject.
        if (empty($thisOccupation->refereeDetails)) {
            $refereeManager = new Manager_Referencing_OccupationReferee();
            $thisOccupation->refereeDetails = $refereeManager->createReferee($thisOccupation->id);
        }

        //Add general details.
        $thisOccupation->refereeDetails->organisationName = $data['company_name'];
        $thisOccupation->refereeDetails->position = $data['contact_position'];
        $thisOccupation->income = new Zend_Currency(
            array(
                'value' => $data['income'],
                'precision' => 0
            )
        );

        //Add the referee name if required.
        if (empty($thisOccupation->refereeDetails->name)) {
            $nameManager = new Manager_Core_Name();
            $thisOccupation->refereeDetails->name = $nameManager->createName();
        }

        $nameArray = preg_split("/\s/", $data['contact_name']);
        if (count($nameArray) >= 2) {
            $thisOccupation->refereeDetails->name->firstName = array_shift($nameArray);
            $thisOccupation->refereeDetails->name->lastName = array_pop($nameArray);
        }
        else if (count($nameArray) == 1) {

            $thisOccupation->refereeDetails->name->firstName = array_shift($nameArray);
        }

        //Capture and process the referee address.
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

        if (empty($thisOccupation->refereeDetails->address)) {
            $addressManager = new Manager_Core_Address();
            $thisOccupation->refereeDetails->address = $addressManager->createAddress();
        }

        $thisOccupation->refereeDetails->address->addressLine1 = $addressLine1;
        $thisOccupation->refereeDetails->address->addressLine2 = $addressLine2;
        $thisOccupation->refereeDetails->address->town = $town;
        $thisOccupation->refereeDetails->address->postCode = $postCode;

        //Capture and process the referee contact details.
        if (empty($thisOccupation->refereeDetails->contactDetails)) {
            $contactDetailsManager = new Manager_Core_ContactDetails();
            $thisOccupation->refereeDetails->contactDetails = $contactDetailsManager->createContactDetails();
        }

        $thisOccupation->refereeDetails->contactDetails->telephone1 = $data['telephone_number'];
        $thisOccupation->refereeDetails->contactDetails->fax1 = $data['fax_number'];
        $thisOccupation->refereeDetails->contactDetails->email1 = $data['email'];

        if (!empty($data['tenancy_start_date'])) {
            $thisOccupation->startDate = new Zend_Date($data['tenancy_start_date'], Zend_Date::DATES);
        }

        if (!empty($data['is_permanent'])) {
            if ($data['is_permanent'] == 'Yes') {
                $thisOccupation->isPermanent = true;
            }
            else {
                $thisOccupation->isPermanent = false;
            }
        }


        //Now capture the optional details and insert into the occupation variables array.
        if (!empty($data['reference_number'])) {
            if (empty($thisOccupation->variables)) {
                $thisOccupation->variables = array();
            }
            $thisOccupation->variables[Model_Referencing_OccupationVariables::PAYROLL_NUMBER] = $data['reference_number'];
        }

        if (!empty($data['position'])) {
            if (empty($thisOccupation->variables)) {
                $thisOccupation->variables = array();
            }
            $thisOccupation->variables[Model_Referencing_OccupationVariables::POSITION] = $data['position'];
        }

        //Identify if a future occupation is applicable and required.
        if (!empty($data['will_change'])) {
            if($data['will_change'] == 'Yes') {
                //If its going to change then we need to create a future employer record, if not already
                //done so.
                $futureOccupation = $occupationManager->findSpecificOccupation(
                    $reference->referenceSubject->occupations,
                    Model_Referencing_OccupationChronology::FUTURE,
                    Model_Referencing_OccupationImportance::FIRST);

                if (empty($futureOccupation)) {
                    $createFutureOccupation = true;
                }
                else {
                    $createFutureOccupation = false;
                }

                if ($createFutureOccupation) {
                    $futureOccupation = $occupationManager->createNewOccupation(
                        $session->referenceId,
                        Model_Referencing_OccupationChronology::FUTURE,
                        Model_Referencing_OccupationImportance::FIRST);

                    $futureOccupation->type = Model_Referencing_OccupationTypes::EMPLOYMENT;
                    $reference->referenceSubject->occupations[] = $futureOccupation;
                }
            }
            else {
                //Delete any future employer records.
                $futureOccupation = $occupationManager->findSpecificOccupation(
                    $reference->referenceSubject->occupations,
                    Model_Referencing_OccupationChronology::FUTURE,
                    Model_Referencing_OccupationImportance::FIRST);

                if (!empty($futureOccupation)) {
                    $occupationManager->deleteOccupation($futureOccupation);
                }
            }
        }

        //And update...
        $referenceManager->updateReference($reference);
        return $thisOccupation;
    }

    public function getMessagesFlattened()
    {
        return $this->getMessages();
    }
}
