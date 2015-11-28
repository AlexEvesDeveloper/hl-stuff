<?php

class Connect_Form_Subforms_Settings_EmailAddresses extends Zend_Form_SubForm {

    /**
     * Create e-mail addresses form.
     *
     * @return void
     */
    public function init() {

        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => 'Domain name invalid in e-mail address',
                Zend_Validate_EmailAddress::INVALID_FORMAT      => 'Invalid e-mail address'
            )
        );

        // Add an e-mail element for each e-mail category
        foreach(Model_Core_Agent_EmailMapCategory::iterableKeys() as $categoryConstName => $val) {

            $categoryFriendlyName = Model_Core_Agent_EmailMapCategory::toString($val);

            // Add e-mail element
            $this->addElement('text', "email{$categoryConstName}", array(
                'label'      => $categoryFriendlyName,
                'required'   => false,
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => "Please enter your {$categoryFriendlyName} e-mail address"
                            )
                        )
                    )
                )
            ));
            $this->getElement("email{$categoryConstName}")->addValidator($emailValidator);

        }

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'settings/subforms/emailaddresses.phtml'))
        ));
        
        
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

    }

    /**
     * Overridden isValid() method for pre-validation code.
     *
     * @param array $formData data typically from a POST or GET request.
     *
     * @return bool
     */
    public function isValid($formData = array()) {

        // "General" e-mail address is compulsory
        $this->getElement("emailGENERAL")->setRequired(true);

        // Call original isValid()
        return parent::isValid($formData);
    }
}