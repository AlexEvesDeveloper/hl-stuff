<?php

/**
 * Retrieve TCI+ or LI+ quote form.
 * 
 * @package Account_Form_RetrieveQuote
 */
class Account_Form_RetrieveQuote extends Zend_Form
{
    /**
     * Initialise the form
     * 
     * @todo Validation
     * @return void 
     */
    public function init()
    {
        // Set request method
        $this->setMethod('POST');

        // Add first name element
        $this->addElement('text', 'quote_number', array(
            'label'      => 'Quote number',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your quote number',
                            'notEmptyInvalid' => 'Please enter your quote number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z]{4,}\d+\/?\d+$/i',
                        'messages' => 'Quote number must use alphabetic and numeric characters and only basic punctuation (forward slash)'
                    )
                )
            ),
            'class' => 'form-control',
            'attribs' => array(
                'data-ctfilter' => 'yes'
            ),
        ));

        // Email element
        $this->addElement('text', 'email', array(
            'label'      => 'Email address*',
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
            'class' => 'form-control',
            'attribs' => array(
                'data-ctfilter' => 'yes'
            ),
        ));
        // Modify email error messages & add validator
        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Invalid email address"
            )
        );
        $this->getElement('email')->addValidator($emailValidator);

        // Add first name element
        $this->addElement('text', 'first_name', array(
            'label'      => 'First name*',
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
            'class' => 'form-control',
            'attribs' => array(
                'data-ctfilter' => 'yes'
            ),
        ));

        // Add last name element
        $this->addElement('text', 'last_name', array(
            'label'      => 'Last name*',
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
            'class' => 'form-control',
            'attribs' => array(
                'data-ctfilter' => 'yes'
            ),
        ));

        // Add DOB element
        $this->addElement('text', 'date_of_birth_at', array(
            'label'     => 'Date of birth (dd/mm/yyyy)*',
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
            'class' => 'form-control',
            'attribs' => array(
                'data-ctfilter' => 'yes'
            ),
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

        // Add postcode element
        $this->addElement('text', 'cor_postcode', array(
            'label' => 'Postcode (of your correspondence address)*',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a correspondence address postcode',
                            'notEmptyInvalid' => 'Please enter a correspondence address postcode'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z]{2,4}\ ?[0-9a-z]{3}$/i', // TODO: temporary regex, needs to use postcode validator once available
                        'messages' => 'Postcode must be in postcode format'
                    )
                )
            ),
            'class' => 'form-control',
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'    => true,
            'label'     => 'Retrieve',
            'class'     => 'btn btn-primary'
        ));

        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors',
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-group')),
        ));

        // Set up the decorator on the form and add in decorators which are removed
        $this->addDecorator('FormElements')
            ->addDecorator(
                'HtmlTag',
                array('tag' => 'div', 'class' => 'retrieve-quote-form')
            )
            ->addDecorator('Form');

        // Remove the label from the submit button
        $element = $this->getElement('submit');
        $element->removeDecorator('label');
    }
}
