<?php

class LandlordsReferencing_Form_ReferenceSubject extends Zend_Form
{
    public function init()
    {
        //Reference subject title element
        $this->addElement('select', 'personal_title', array(
            'label' => 'Title',
            'required' => true,
            'multiOptions' => array(
                // This value must be empty to force a validation failure
                '' => 'Not Known',
                'Mr' => 'Mr',
                'Ms' => 'Ms',
                'Mrs' => 'Mrs',
                'Miss' => 'Miss',
                'Dr' => 'Dr',
                'Prof' => 'Professor',
                'Sir' => 'Sir'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the title',
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
            'label' => 'First name',
            'required' => true,
            'filters' => array('StringTrim'),
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
        
        //Middle name entry
        $this->addElement('text', 'middle_name', array(
            'label' => 'Middle name',
            'required' => false,
            'filters' => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a middle name'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));

        //Last name entry
        $this->addElement('text', 'last_name', array(
            'label' => 'Last name',
            'required' => true,
            'filters' => array('StringTrim'),
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

        //Other name entry
        $this->addElement('text', 'other_name', array(
            'label' => 'Other name which known by',
            'required' => false,
            'filters' => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));
        
        //Phone number entry
        $this->addElement('text', 'telephone_day', array(
            'label' => 'Daytime telephone number',
            'required' => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a daytime phone number'
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
        
        //Evening phone number
        $this->addElement('text', 'mobile_number', array(
            'label' => 'Mobile number',
            'required' => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a mobile phone number'
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
                'class' => 'form-control',
            )
        ));
        
        // Email entry
        $this->addElement('text', 'email', array(
            'label' => 'Email address',
            'required' => false,
            'filters' => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));
        
        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT => "Invalid email address"
            )
        );
        $this->getElement('email')->addValidator($emailValidator);
    
        //Date of birth. Use the 'tenancy start date' dropdowns for now, then re-purpose later.
        $this->addElement('text', 'tenancy_start_date', array(
            'label' => 'Date of birth',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a date of birth'
                        )
                    )
                ),
            )
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

        // Add bank account number element
        $this->addElement('text', 'bank_account_number', array(
            'label' => 'Bank account number',
            'required' => false,
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        // Add bank sort code element
        $this->addElement('text', 'bank_sortcode_number', array(
            'label' => 'Bank sort code',
            'required' => false,
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        //Residential status element
        $this->addElement('select', 'residential_status', array(
            'label' => 'Current residential status',
            'required' => true,
            'multiOptions' => array(
                '' => 'Please Select',
                Model_Referencing_ResidenceStatus::OWNER => 'Home Owner',
                Model_Referencing_ResidenceStatus::TENANT => 'Tenant',
                Model_Referencing_ResidenceStatus::LIVING_WITH_RELATIVES => 'Living with Relatives'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the residential status',
                            'notEmptyInvalid' => 'Please select a valid residential status'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
        
        //Occupational status element
        $this->addElement('select', 'occupational_type', array(
            'label' => 'Current occupation status',
            'required' => true,
            'multiOptions' => array(
                '' => 'Please Select',
                Model_Referencing_OccupationTypes::EMPLOYMENT => 'Permanently Employed',
                Model_Referencing_OccupationTypes::CONTRACT => 'On Contract',
                Model_Referencing_OccupationTypes::SELFEMPLOYMENT => 'Self Employed',
                Model_Referencing_OccupationTypes::RETIREMENT => 'Pensioner',
                Model_Referencing_OccupationTypes::STUDENT => 'Student',
                Model_Referencing_OccupationTypes::UNEMPLOYMENT => 'Unemployed',
                Model_Referencing_OccupationTypes::INDEPENDENT => 'Of Independant Means',
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the occupational status',
                            'notEmptyInvalid' => 'Please select a valid occupational status'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        //Drop down displayed only when applicant is Unemployed or a Student.
        $this->addElement('select', 'is_future_employment_secured', array(
            'label' => 'Will the applicant start a new employed position during the tenancy term?',
            'required' => false,
            'multiOptions' => array(
                '' => 'Please Select',
                'Yes' => 'Yes',
                'No' => 'No',
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        //Add total gross annual income
        $this->addElement('text', 'total_annual_income', array(
            'label' => 'Total gross annual income (&pound;)',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the total gross annual income'
                        )
                    )
                ),
                array(
                    'Digits', true, array(
                        'messages' => array(
                            'notDigits' => 'Total gross annual income can only contain digits',
                            'digitsStringEmpty' => 'Total gross annual income can only contain digits'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'currency',
                'class' => 'currency form-control',
            )
        ));

        //Occupation will change element
        $this->addElement('select', 'occupation_will_change', array(
            'label' => 'Is occupation about to change?',
            'required' => true,
            'multiOptions' => array(
                '' => 'Please Select',
                'Yes' => 'Yes',
                'No' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select if this occupation is about to change',
                            'notEmptyInvalid' => 'Please select if this occupation is about to change'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        //Has adverse credit history
        $this->addElement('select', 'has_adverse_credit', array(
            'label' => 'Does the applicant have CCJs or adverse credit history?',
            'required' => true,
            'multiOptions' => array(
                '' => 'Please Select',
                'Yes' => 'Yes',
                'No' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select if there is adverse credit history',
                            'notEmptyInvalid' => 'Please select if there is adverse credit history'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'landlords-referencing/reference-subject.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
    
    public function forcePopulate($formData)
    {
        //Populate SOME of the form elements with data retrieved from the datasource, unless
        //the user has provided new datas.
        $session = new Zend_Session_Namespace('referencing_global');

        $referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($session->referenceId);

        if (empty($formData['personal_title'])) {
            $this->personal_title->setValue($reference->referenceSubject->name->title);
            $formData['personal_title'] = $reference->referenceSubject->name->title;
        }

        if (empty($formData['first_name'])) {
            $formData['first_name'] = $reference->referenceSubject->name->firstName;
        }

        if (empty($formData['last_name'])) {
            $formData['last_name'] = $reference->referenceSubject->name->lastName;
        }

        if (empty($formData['email'])) {
            $formData['email'] = $reference->referenceSubject->contactDetails->email1;
        }


        //Allow Zend to complete the population process.
        $this->populate($formData);
    }
    
    public function saveData()
    {
        $session = new Zend_Session_Namespace('referencing_global');
        $data = $this->getValues();

        $referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($session->referenceId);

        //Record the reference subject's personal details.
        if (empty($reference->referenceSubject->name)) {
            //If here then things are a bit weird - we should have the reference subject name
            //captured from an earlier form.
            $nameManager = new Manager_Core_Name();
            $reference->referenceSubject->name = $nameManager->createName();
        }

        $reference->referenceSubject->name->title = $data['personal_title'];
        $reference->referenceSubject->name->firstName = $data['first_name'];
        $reference->referenceSubject->name->middleName = $data['middle_name'];
        $reference->referenceSubject->name->lastName = $data['last_name'];
        $reference->referenceSubject->name->maidenName = $data['other_name'];

        //Reference subject contact details
        if (empty($reference->referenceSubject->contactDetails)) {

            $contactDetailsManager = new Manager_Core_ContactDetails();
            $reference->referenceSubject->contactDetails = $contactDetailsManager->createContactDetails();
        }

        $reference->referenceSubject->contactDetails->telephone1 = $data['telephone_day'];
        $reference->referenceSubject->contactDetails->telephone2 = $data['mobile_number'];
        $reference->referenceSubject->contactDetails->email1 = $data['email'];

        //Reference subject miscellaneous.
        $reference->referenceSubject->dob = new Zend_Date($data['tenancy_start_date'], Zend_Date::DATES);

        if ('Yes' == $data['has_adverse_credit']) {
            $reference->referenceSubject->hasAdverseCredit = true;
        }
        else {
            $reference->referenceSubject->hasAdverseCredit = false;
        }


        //Bank account details. Bank account details are optional.
        if (empty($data['bank_account_number']) || empty($data['bank_sortcode_number'])) {
            //No bank account details have been provided.
            if (!empty($reference->referenceSubject->bankAccount)) {
                //We have an existing bank account details record - delete this to reflect the
                //user input.
                $bankAccountManager = new Manager_Referencing_BankAccount();
                $bankAccountManager->deleteBankAccount($reference->referenceSubject->bankAccount);
            }
        }
        else {
            if (empty($reference->referenceSubject->bankAccount)) {
                $bankAccountManager = new Manager_Referencing_BankAccount();
                $reference->referenceSubject->bankAccount = $bankAccountManager->insertPlaceholder($session->referenceId);
            }

            $reference->referenceSubject->bankAccount->accountNumber = $data['bank_account_number'];
            $reference->referenceSubject->bankAccount->sortCode = $data['bank_sortcode_number'];

            //Run the bank account details through the validators.
            $reference->referenceSubject->bankAccount->isValidated = false;
            $bankManager = new Manager_Core_Bank();
            if ($bankManager->isSortCodeValid($data['bank_sortcode_number'])) {
                if ($bankManager->isAccountNumberValid($data['bank_sortcode_number'], $data['bank_account_number'])) {
                    $reference->referenceSubject->bankAccount->isValidated = true;
                }
            }
        }

        //Create a current residence record, if not already done so. Ensure the current residence record
        //reflects the residential status provided by the user.
        if (empty($reference->referenceSubject->residences)) {
            $residenceManager = new Manager_Referencing_Residence();
            $residence = $residenceManager->insertPlaceholder(
                $session->referenceId, Model_Referencing_ResidenceChronology::CURRENT);
            $residence->status = $data['residential_status'];

            $reference->referenceSubject->residences = array();
            $reference->referenceSubject->residences[] = $residence;
        }
        else {

            //Locate the current residence record, and set the residential status accordingly.
            foreach ($reference->referenceSubject->residences as $residence) {
                if ($residence->chronology == Model_Referencing_ResidenceChronology::CURRENT) {
                    $residence->status = $data['residential_status'];
                    break;
                }
            }
        }

        //Create or locate the current occupation record.
        $occupationManager = new Manager_Referencing_Occupation();
        if (empty($reference->referenceSubject->occupations)) {
            $isNew = true;
            $currentOccupation = $occupationManager->createNewOccupation(
                $session->referenceId,
                Model_Referencing_OccupationChronology::CURRENT,
                Model_Referencing_OccupationImportance::FIRST);
        }
        else {
            $isNew = false;

            //Locate the current primary occupation record, and set the occupation type accordingly.
            $currentOccupation = $occupationManager->findSpecificOccupation(
                $reference->referenceSubject->occupations,
                Model_Referencing_OccupationChronology::CURRENT,
                Model_Referencing_OccupationImportance::FIRST);
        }

        //Update the current occupation record to reflect the user inputs.
        $currentOccupation->type = $data['occupational_type'];
        $currentOccupation->income = new Zend_Currency(
            array(
                'value' => $data['total_annual_income'],
                'precision' => 0
            )
        );
        if ($data['occupation_will_change'] == 'No') {
            $currentOccupation->isPermanent = true;
        }
        else {
            $currentOccupation->isPermanent = false;
        }

        //Add the current occupation to the ReferenceSubject, if it is new.
        if ($isNew) {
            $reference->referenceSubject->occupations = array();
            $reference->referenceSubject->occupations[] = $currentOccupation;
        }

        //Identify if a future occupation record is required.
        if (isset($data['is_future_employment_secured'])) {
            if ('Yes' == $data['is_future_employment_secured']) {
                //See if a future occupation record exists already.
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
                //No future occupation record is required at this time, so ensure that
                //any existing are deleted.
                $futureOccupation = $occupationManager->findSpecificOccupation(
                    $reference->referenceSubject->occupations,
                    Model_Referencing_OccupationChronology::FUTURE,
                    Model_Referencing_OccupationImportance::FIRST);

                if (!empty($futureOccupation)) {
                    $occupationManager->deleteOccupation($futureOccupation);
                }
            }
        }

        //Write the updates to the datasources.
        $referenceManager->updateReference($reference);
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
        // If a landline phone number is given, mobile is not mandatory
        if (isset($formData['telephone_day']) && '' != trim($formData['telephone_day'])) {
            $this->getElement('mobile_number')->setRequired(false);
        }
        
        // If a mobile phone number is given, landline is not mandatory
        if (isset($formData['mobile_number']) && '' != trim($formData['mobile_number'])) {
            $this->getElement('telephone_day')->setRequired(false);
        }
        
        // If a occupational_type is 3 or 7, occupational_type is mandatory
        if (isset($formData['occupational_type'])) {
            if ('3' == trim($formData['occupational_type']) || '7' == trim($formData['occupational_type'])) {
                $this->getElement('is_future_employment_secured')->setRequired(true);
            }
            else {
                $this->getElement('is_future_employment_secured')->setRequired(false);
            }
        }

        // Redmine ref #6195: Check that tenant will be greater than 18 years of age when the tenancy begins.  Code is "inspired" by legacy Referencing/src/ref-www/frontEnd/validate/tenantDetails.php
        // Get tenancy start date
        $session = new Zend_Session_Namespace('referencing_global');
        $referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($session->referenceId);
        $startTime = $reference->propertyLease->tenancyStartDate->getTimestamp();
        // 18 years is 567648000 seconds
        $dobTime = $formData['tenancy_start_date']; // This is actually the DoB from the form.  wtf?
        $dobStamp = mktime(0, 0, 0, substr($dobTime, 3, 2), substr($dobTime, 0, 2), substr($dobTime, 6, 4));
        if (($startTime - $dobStamp) < 567648000) {
            $badValidator = new Zend_Validate_ForceError();
            $badValidator->setMessages(
                array(
                    Zend_Validate_ForceError::MSG_NOTVALID => 'This tenant will not be 18 years old at the start of the tenancy. We are unable to complete this reference. Please note that you must provide a correct Date of Birth or the results of your reference will be inaccurate and any associated insurance product will be invalidated.'
                )
            );
            $this->getElement('tenancy_start_date')->addValidator($badValidator);
        }

        // Call original isValid()
        return parent::isValid($formData);
    }

    public function getMessagesFlattened()
    {
        return $this->getMessages();
    }
}