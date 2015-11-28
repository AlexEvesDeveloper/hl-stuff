<?php

class Connect_Form_Subforms_ReferencingCompanyApplication_Declaration extends Zend_Form_SubForm {
    /**
     * Create declaration and consent subform
     *
     * @return void
     */
    public function init() {

        $this->addElement('checkbox', 'consent_information_stored', array(
            'required'      => true,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'decorators'    => array('ViewHelper'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'We are unable to process your application if you do not agree to the referencing terms'
                        )
                    )
                )
            )
        ));

        // Add company director/secretary name element
        $this->addElement('text', 'representive_name', array(
            'label'         => 'Company director/secretary name in full',
            'required'      => true,
            'filters'       => array('StringTrim'),
            'validators'    => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the company director/secretary\'s name',
                            'notEmptyInvalid' => 'Please enter the company director/secretary\'s name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'Company director/secretary\'s name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add position element
        $this->addElement('text', 'representive_position', array(
            'label'     => 'Position',
            'required'  => true,
            'filters'   => array('StringTrim'),
            'validators'    => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the company director/secretary\'s position',
                            'notEmptyInvalid' => 'Please enter the company director/secretary\'s position'
                        )
                    )
                )
            )
        ));

        // Add date element
        $this->addElement('text', 'application_date', array(
            'label'     => 'Date (dd/mm/yyyy)',
            'required'  => true,
            'filters'   => array('StringTrim'),
            'readonly'  => true
        ));
        $application_date = $this->getElement('application_date');
        $validator = new Zend_Validate_DateCompare();
        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $validator->setMessages(array(
            'msgMinimum' => 'Application date cannot be in the past',
            'msgMaximum' => 'Application date cannot be in the future'
        ));
        $application_date->addValidator($validator, true);

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'referencing/subforms/company-application-declaration.phtml'))
        ));

        $this->setElementFilters(array('StripTags'));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Add submit button
        $this->addElement('submit', 'complete', array(
            'label' => 'Complete'
        ));

        $element = $this->getElement('complete');
        $element->removeDecorator('label');
    }

}