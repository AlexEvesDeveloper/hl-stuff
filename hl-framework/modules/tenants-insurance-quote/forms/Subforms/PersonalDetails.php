<?php

class TenantsInsuranceQuote_Form_Subforms_PersonalDetails extends Zend_Form_SubForm
{
    /**
     * Static array of title options
     *
     * @var array
     */
    public static $titles = array(
        'Mr' => 'Mr',
        'Mrs' => 'Mrs',
        'Ms' => 'Ms',
        'Miss' => 'Miss',
        'Sir' => 'Sir',
        'Mr and Mrs' => 'Mr and Mrs',
        'Dr' => 'Dr',
        'Professor' => 'Professor',
        'Rev' => 'Rev',
        'Other' => 'Other'
    );

    /**
     * Create personal details subform
     *
     * @return void
     */
    public function init()
    {
        // Add title element
        $this->addElement('select', 'title', array(
            'label'     => 'Title',
            'required'  => true,
            'multiOptions' => self::$titles,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your title',
                            'notEmptyInvalid' => 'Please select your title'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        // Add other title element
        $this->addElement('text', 'other_title', array(
            'label' => 'Other title',
            'required' => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your title',
                            'notEmptyInvalid' => 'Please enter your title'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'class' => 'form-control',
            )
        ));

        // Add first name element
        $this->addElement('text', 'first_name', array(
            'label'      => 'First name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your first name',
                            'notEmptyInvalid' => 'Please enter your first name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'First name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
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

        // Add last name element
        $this->addElement('text', 'last_name', array(
            'label'      => 'Last name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your last name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'Last name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
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

        // Add phone number element
        $this->addElement('text', 'phone_number', array(
            'label'      => 'Contact phone number',
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
                'data-ctfilter' => 'yes',
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'phone',
                'class' => 'form-control',
            )
        ));

        // Add mobile number element
        $this->addElement('text', 'mobile_number', array(
            'label'      => 'Mobile number (if different)',
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
                'data-ctfilter' => 'yes',
                'data-type' => 'mobile',
                'class' => 'form-control',
            )
        ));

        // Add e-mail element
        $this->addElement('text', 'email_address', array(
            'label'      => 'Email address',
            'required'   => true,
            'filters'    => array('StringTrim'),
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
        
        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Invalid email address"
            )
        );
        $this->getElement('email_address')->addValidator($emailValidator);

        // Add DOB element
        $this->addElement('text', 'date_of_birth_at', array(
            'label'     => 'Date of birth (dd/mm/yyyy)',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter your date of birth'
                    )
                )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'date',
                'class' => 'form-control',
            )
        ));
        $dob = $this->getElement('date_of_birth_at');
        $validator = new Zend_Validate_DateCompare();
        // todo: Parameterise valid date range
        $minYear = max(1902, date('Y') - 150); // On 32-bit systems (dev, staging) mktime cannot handle years below 1902
        $maxYear = date('Y') - 18;
        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), $minYear));
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), $maxYear));
        $validator->setMessages(array(
            'msgMinimum' => 'Date of birth cannot be more than ' . (date('Y') - $minYear) . ' years in the past',
            'msgMaximum' => 'Date of birth cannot be less than 18 years in the past'
        ));
        $dob->addValidator($validator, true);

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/personal-details.phtml'))
        ));

        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Grab view and add the date picker JavaScript files into the page head
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
                '/assets/common/js/policyDobDatePicker.js',
                'text/javascript'
        );
    }

    /**
     * Force fields to be read only.
     *
     * @return void
     */
    public function setReadOnly()
    {
        $this->getElement('title')->setAttrib('disabled', 'disabled');

        if ($this->getElement('other_title')->getValue()) {
            $this->getElement('other_title')->setAttrib('readonly', 'readonly');
        }

        if ($this->getElement('first_name')->getValue()) {
            $this->getElement('first_name')->setAttrib('readonly', 'readonly');
        }

        if ($this->getElement('last_name')->getValue()) {
            $this->getElement('last_name')->setAttrib('readonly', 'readonly');
        }

        if ($this->getElement('phone_number')->getValue()) {
            $this->getElement('phone_number')->setAttrib('readonly', 'readonly');
        }

        if ($this->getElement('mobile_number')->getValue()) {
            $this->getElement('mobile_number')->setAttrib('readonly', 'readonly');
        }

        if ($this->getElement('email_address')->getValue()) {
            $this->getElement('email_address')->setAttrib('readonly', 'readonly');
        }
    }

    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
    public function isValid($formData = array()) {
        
        // If a landline phone number is given, mobile is not mandatory
        if (isset($formData['phone_number']) && trim($formData['phone_number']) != '') {
            $this->getElement('mobile_number')->setRequired(false);
        }
        
        // If a mobile phone number is given, landline is not mandatory
        if (isset($formData['mobile_number']) && trim($formData['mobile_number']) != '') {
            $this->getElement('phone_number')->setRequired(false);
        }
		
		// Check other title requirement
		if (isset($formData['title']) && ($formData['title'] == "Other")) {
			$this->getElement('other_title')->setRequired(true);
		} else {
			$this->getElement('other_title')->setRequired(false);
		}
         
        //Check to ensure the email does not already exist in the dbase.
        
         
        // Call original isValid()
        return parent::isValid($formData);
    }

}
