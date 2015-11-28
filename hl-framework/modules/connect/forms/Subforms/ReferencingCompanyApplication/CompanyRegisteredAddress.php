<?php

class Connect_Form_Subforms_ReferencingCompanyApplication_CompanyRegisteredAddress extends Zend_Form_SubForm {
    /**
     * Create company registered address subform
     *
     * @return void
     */
    public function init() {

        // Add registered address address element
        $this->addElement('text', 'registered_address', array(
            'label'      => 'Address',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the registered address',
                            'notEmptyInvalid' => 'Please enter the registered address'
                        )
                    )
                )
            )
        ));

        // Add registered address postcode element
        $this->addElement('text', 'registered_postcode', array(
            'label'      => 'Postcode',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the registered postcode',
                            'notEmptyInvalid' => 'Please enter the registered postcode'
                        )
                    )
                ),
                array(
                    'Postcode'
                )
            )
        ));

        // Add registered address years-at element
        $this->addElement('text', 'registered_years', array(
            'label'      => 'Period at this address (years)',
            'required'   => true,
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the period at this address (years)',
                            'notEmptyInvalid' => 'Please enter the period at this address (years)'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d{1,}$/',
                        'messages' => 'Period at this address (years) must contain at least one digit'
                    )
                )
            )
        ));

        // Add registered address months-at element
        $this->addElement('text', 'registered_months', array(
            'label'      => 'Period at this address (months)',
            'required'   => true,
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the period at this address (months)',
                            'notEmptyInvalid' => 'Please enter the period at this address (months)'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d{1,}$/',
                        'messages' => 'Period at this address (months) must contain at least one digit'
                    )
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'referencing/subforms/company-application-companyregisteredaddress.phtml'))
        ));
		
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }

}