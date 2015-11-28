<?php

class Connect_Form_RentGuaranteeClaims_Step1 extends Zend_Form {

    private $PHONE_RGX = '/^((\+44\s?\(0\)\s?\d{2,4})|(\+44\s?(01|02|03|07|08)\d{2,3})|(\+44\s?(1|2|3|7|8)\d{2,3})|(\(\+44\)\s?\d{3,4})|(\(\d{5}\))|((01|02|03|07|08)\d{2,3})|(\d{5}))(\s|-|.)(((\d{3,4})(\s|-)(\d{3,4}))|((\d{6,7})))$/';

    /**
     * Define the OC form elements
     *
     * @return void
     */
    public function init() {
        $this->setMethod('post');

        // Add agent name element
        $this->addElement(
            'span',
            'agent_name',
            array(
                'label' 	=> 'Name of letting agency',
                'required' 	=> false,
                'readonly' 	=> true,
                'filters' 	=> array('StringTrim'),
                'class'     => 'formvalue',
                'validators' => array(
                    array(
                        'NotEmpty',
                        true,
                        array(
                            'messages' => array (
                                'isEmpty' => 'Please enter letting agent name',
                                'notEmptyInvalid' => 'Please enter letting agent name'
                            )
                        )
                    )
                )
            )
        );

        // Add agent scheme number
        $this->addElement(
            'span',
            'agent_schemenumber',
            array(
                'label' 	=> 'Agent scheme number',
                'required' 	=> false,
                'readonly' 	=> true,
                'filters' 	=> array('StringTrim'),
                'class'     => 'formvalue',
                'validators' => array(
                    array(
                        'NotEmpty',
                        true,
                        array(
                            'messages' => array (
                                'isEmpty' => 'Please enter agent scheme number',
                                'notEmptyInvalid' => 'Please enter agent scheme number'
                            )
                        )
                    )
                )
            )
        );

        // Add agent contact name element
        $this->addElement(
            'text',
            'agent_contact_name',
            array(
                'label' => 'Contact name',
                'required' => true,
                'filters' => array('StringTrim'),
                'maxlength' => '100',
                'validators' => array(
                    array(
                        'NotEmpty',
                        true,
                        array(
                            'messages' => array(
                                'isEmpty' => 'Please enter your contact name',
                                'notEmptyInvalid' => 'Please enter your contact name'
                            )
                        )
                    )
                )
            )
        );

        // Add the landlord find address button
        $this->addElement('submit', 'landlords_address_lookup', array(
            'ignore'   => true,
            'label'    => 'Find address',
            'class'    => 'button',
            'onclick'    => 'getPropertiesByPostcode($(\'#landlord_postcode\').val(), \'landlord_postcode\', \'landlord_address\',\'no_landlord_address_selector\'); return false;'
        ));


        // Add agent postcode
        $this->addElement('text', 'agent_postcode', array(
            'label'         => 'Postcode',
            'required'      => true,
            'filters'       => array('StringTrim'),
            'maxlength'     => '10',
            'validators'    => array(
                    array(
                        'NotEmpty',
                        true,
                        array(
                            'messages' => array(
                                'isEmpty' => 'Please enter your office postcode',
                                'notEmptyInvalid' => 'Please enter your office postcode'
                            )
                        )
                    ),
                    array(
                        'regex',
                        true,
                        array(
                            'pattern' => '/^[0-9a-z]{2,}\ ?[0-9a-z]{2,}$/i',
                            'messages' => 'Postcode must be in postcode format'
                        )
                    )
                ),
            )
        );

        Application_Core_FormUtils::createManualAddressInput(
            $this,
            'agent_housename',
            'agent_street',
            'agent_town',
            'agent_city',
            false,
            '',
            true
        );

        // Add agent phone number element
        $this->addElement('text', 'agent_telephone', array(
            'label'         =>  'Telephone number',
            'required'      =>  true,
            'class'         =>  'input-pos-float',
            'validators' => array(
            	'TelephoneNumber',
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your telephone number'
                        )
                    )
                )
            )
        ));

        // Add agent e-mail element
        $this->addElement('text', 'agent_email', array(
            'label'      => 'Email address',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'maxlength'  => '100',
            'validators' => array(
                array ('NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter your email address'
                    )
                ))
            )
        ));

        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(array(
            Zend_Validate_EmailAddress::INVALID_HOSTNAME => "Domain name invalid in email address",
            Zend_Validate_EmailAddress::INVALID_FORMAT => "Invalid email address"
        ));
        $this->getElement('agent_email')->addValidator($emailValidator);

        // Add agents directly authorised by FCA
        $this->addElement('span', 'agent_dir_by_fca', array(
            'label' => 'Directly Authorised by the Financial Conduct Authority',
            'filters' => array('StringTrim'),
            'class'     => 'formvalue'
        ));

        $this->addElement('span', 'agent_ar_by_barbon', array(
            'label' => 'Appointed Representative for Barbon Insurance Group Ltd',
            'filters' => array('StringTrim'),
            'class'     => 'formvalue'
        ));

        // Landlord1
        $subHeaderHtml = '<span style="font-size:smaller;">Please provide title, first name and surname</span>';
        $this->addElement('text', 'landlord1_name', array(
            'label' => "Full name<br>$subHeaderHtml",
            'required' => true,
            'filters' => array('StringTrim'),
            'maxlength'     => '80',
               'validators' => array(
                array ('NotEmpty', true, array(
                    'messages' => array(
                    'isEmpty' => 'Please landlords name'
                    )
                ))
            )
        ));

        // landlor company Name
        $this->addElement('text', 'landlord_company_name', array(
            'label'         => 'Company name',
            'required'      => false,
            'filters'       => array('StringTrim'),
            'maxlength'     => '100',
        ));

        // Add Landlord postcode
        $subHeaderHtml = '<span style="font-size:smaller;">Landlords residential address (This cannot be a C/O address due to the requirements for Legal Proceedings)</span>';
        $this->addElement('text', 'landlord_postcode', array(
            'label'         => "Landlord Home Address Postcode<br>$subHeaderHtml",
            'required'      => true,
            'filters'       => array('StringTrim'),
            'maxlength'     => '10',
            'validators'    => array(
                array(
                    'NotEmpty',
                    true,
                    array(
                        'messages' => array(
                            'isEmpty' => 'Please enter Landlord\'s postcode',
                            'notEmptyInvalid' => 'Please enter Landlord\'s postcode'
                        )
                    )
                ), array(
                    'regex',
                    true,
                    array(
                        'pattern' => '/^[0-9a-z]{2,}\ ?[0-9a-z]{2,}$/i',
                        'messages' => 'Postcode must be in postcode format'
                    )
                )
            ),
        ));

        Application_Core_FormUtils::createManualAddressInput(
            $this,
            'landlord_housename',
            'landlord_street',
            'landlord_town',
            'landlord_city',
            false
        );

        $this->addElement('select', 'landlord_address', array(
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
                            'isEmpty' => 'Please select landlord address',
                            'notEmptyInvalid' => 'Please select landlord address'
                        )
                    )
                )
            )
        ));

        // Remove 'nnn not found in haystack' error
        $this->getElement('landlord_address')->setRegisterInArrayValidator(false);

        // Add hidden element for postcode
        $this->addElement('hidden', 'landlord_address_id', array(
            'value' => 1, 'class' => 'noborder'
        ));


        // Add agent phone number element
        $this->addElement('text', 'landlord_telephone', array(
            'label'         => 'Telephone number',
            'required'      => false,
            'class'         => 'input-pos-float',
            'validators'    => array(
                array('NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter landlord phone number'
                    )
                )),
                array('regex', true, array(
                    'pattern' => $this->PHONE_RGX,
                    'messages' => 'Not a valid phone number'
                ))
            )
        ));

        // Add Landlord e-mail element
        $this->addElement('text', 'landlord_email', array(
            'label'         => 'Email address',
            'required'      => false,
            'filters'       => array('StringTrim'),
            'maxlength'     => '100',
            'validators' => array(
                array ('NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please enter landlord email address'
                    )
                ))
            )
        ));

        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT => "Invalid email address"
            )
        );

        $this->getElement('landlord_email')->addValidator($emailValidator);

        // Set decorators
        $this->clearDecorators();
        $this->setDecorators(array ('Form'));
        $this->setElementDecorators(array ('ViewHelper', 'Label', 'Errors'));

        // Add the next button
        $this->addElement('submit', 'next', array(
            'ignore' => true,
            'label' => 'Continue to Step 2',
            'onclick' => 'window.location="step2"'
        ));

        // Add the save and exit button
        $this->addElement('button', 'save', array(
            'ignore' => true, 'label' => 'Save & Exit'
        ));

        // Landlord Address decorators
        $landlordAddressLookUp = $this->getElement('landlords_address_lookup');
        $landlordAddressLookUp->clearDecorators();
        $landlordAddressLookUp->setDecorators(array ('ViewHelper'));

        // Nav decorators
        $next = $this->getElement('next');
        $next->clearDecorators();
        $next->setDecorators(array ('ViewHelper'));

        $save_and_exit = $this->getElement('save');
        $save_and_exit->clearDecorators();
        $save_and_exit->setDecorators(array ('ViewHelper'));

        $this->setDecorators(
            array(
                array(
                    'ViewScript',
                    array(
                        'viewScript' => 'rentguaranteeclaims/subforms/you-and-landlords.phtml'
                    )
                )
            )
        );

        //Allow HTML to be inserted into the labels.
        $this->getElement('landlord1_name')->getDecorator('Label')->setOption('escape', false);
        $this->getElement('landlord_postcode')->getDecorator('Label')->setOption('escape', false);
    }

    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     * @return bool
     */
    public function isValid($formData = array()) {

        if ((isset($formData['landlord_postcode']) && trim($formData['landlord_postcode']) != '')) {
            Application_Core_FormUtils::getAddressByPostcode(
                $this,
                $formData,
                trim($formData['landlord_postcode']),
                'landlord_address',
                'landlord_housename',
                'landlord_street',
                'landlord_town',
                'landlord_city',
                'landlord\'s'
            );
        }

        // Ensure that all of landlord1_name, landlord_company_name are mandatory if all empty, all non-mandatory otherwise
        if (
            (isset($formData ['landlord1_name']) && trim($formData ['landlord1_name']) != '')
            ||
            (isset($formData ['landlord_company_name']) && trim($formData ['landlord_company_name']) != '')
        ) {
            // One or more of the fields have a value, make them all non-mandatory
            $this->getElement('landlord1_name')->setRequired(false);
            $this->getElement('landlord_company_name')->setRequired(false);
        }

        // Hide Errors
        Application_Core_FormUtils::removeFormErrors($this);
        // Call original isValid()
        return parent::isValid($formData);
    }


}
?>
