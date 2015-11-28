<?php

class Connect_Form_Rentguarantee_DocumentAddress extends Zend_Form
{
    /**
     * Initialise the form
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');
        
        $this->addElement
        (
            'text',
            'cor_house_number_name',
            array
            (
                'label'     => 'House number or name',
                 'required'  => true,
                 'filters'    => array('StringTrim'),
                 'validators' => array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => 'Please enter a house number or name',
                                'notEmptyInvalid' => 'Please enter a house number or name'
                            )
                        )
                    ),
                    array(
                        'regex', true, array(
                            'pattern' => '/^[0-9a-z\ \-\/]{1,}$/i',
                            'messages' => 'House number or name must contain at least one alphanumeric character and only basic punctuation (space, hyphen and forward slash)'
                        )
                    )
                 )
            )
        );
        
        // Add postcode element
        $this->addElement('text', 'cor_postcode', array(
            'label'      => 'Postcode',
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
                        'pattern' => '/^[0-9a-z]{2,}\ ?[0-9a-z]{2,}$/i', // TODO: temporary regex, needs to use postcode validator once available
                        'messages' => 'Postcode must be in postcode format'
                    )
                )
            )
        ));
        
        // Add address select element
        $this->addElement('select', 'cor_address', array(
            'label'     => 'Please select your address',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- please select ---'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your correspondence address',
                            'notEmptyInvalid' => 'Please select your correspondence address'
                        )
                    )
                )
            )
        ));
        
        // Prevent checking of the multi options on the lookup field, the form will always
        // leave this blank within the form object
        $this->getElement('cor_address')->setRegisterInArrayValidator(false);
        
        // Continue and Back
        $this->addElement('submit', 'formsubmit_back', array('name' => 'formsubmit_back', 'label' => 'Back'));
        $this->addElement('submit', 'formsubmit_continue', array('name' => 'formsubmit_continue', 'label' => 'Continue'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguarantee/subforms/rentguarantee-document-address.phtml'))
        ));
        
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        $back = $this->getElement('formsubmit_back');
        $back->setDecorators(array(
            array('ViewHelper', array('escape' => false))
        ));
        
        $next = $this->getElement('formsubmit_continue');
        $next->setDecorators(array(
            array('ViewHelper', array('escape' => false))
        ));
    }
}