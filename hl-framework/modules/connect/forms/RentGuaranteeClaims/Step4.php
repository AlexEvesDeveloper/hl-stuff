<?php

class Connect_Form_RentGuaranteeClaims_Step4 extends Zend_Form {
    /**
     * Define the OC step4 form elements
     *
     * @return void
     */
    public function init() {
        $this->setMethod('post');

        // Set decorators
        $this->clearDecorators();
        $this->setDecorators(array('Form'));
        $this->setElementDecorators(array ('ViewHelper', 'Label', 'Errors'));

        // Add confirmation element
        $this->addElement('checkbox', 'chk_confirm', array(
            'required'      => true,
            'checkedValue'  => '1',
            'uncheckedValue' => null,
            'style'            => 'height:15px;width:15px;',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tick confirm checkbox to submit'
                        )
                    )
                )
            )
        ));

        // Add name element
        $this->addElement('text', 'doc_confirmation_agent_name', array(
            'label'         => 'Your name',
            'required'      => true,
            'filters'       => array('StringTrim'),
            'maxlength'     => '100',
            'validators'    => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your name',
                            'notEmptyInvalid' => 'Please enter your name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']{2,}$/i',
                        'messages' => 'Your name must contain at least two alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        $this->addElement('select', 'landlord_proprietor_of_property', array(
            'label'         => 'Is the landlord the registered proprietor of the property (legal owner)?',
            'required'      => true,
            'multiOptions'  => array(
                '' => 'Please select',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators'    => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please specify whether your landlord is the registered proprietor of the property',
                        'notEmptyInvalid' => 'Please specify whether your landlord is the registered proprietor of the property'
                    )
                )
                )
            )
        ));

        // Add element for first declaration
        $this->addElement('checkbox', 'dec1_confirm', array(
            'required' => true,
            'checkedValue' => '1',
            'uncheckedValue' => null,
            'class' => 'larger-checkbox',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tick to confirm that the documents are uploaded and accurate.'
                        )
                    )
                )
            )
        ));

        // Add element for second declaration
        $this->addElement('checkbox', 'dec2_confirm', array(
            'required' => true,
            'checkedValue' => '1',
            'uncheckedValue' => null,
            'class' => 'larger-checkbox',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tick to confirm you understand that making a fraudulent claim is a criminal offence.'
                        )
                    )
                )
            )
        ));

        // Add the save & exit button
        $this->addElement('button', 'save_exit', array(
            'ignore'   => true,
            'label'    => 'Save & Exit         ',
            'onclick'  => "window.location = '/rentguaranteeclaims/saveclaim';"
        ));


        // Add the back button
        $this->addElement('button', 'back', array(
            'ignore'   => true,
            'label'    => 'Back         ',
            'onclick'  => 'window.location="step3"'
        ));

        // Add the next button
        $this->addElement('button', 'next', array(
            'ignore'   => true,
            'label'    => 'Submit Claim',
            'onclick'  => 'fnSubmitDocuments(2)'
        ));

        $this->addElement('hidden', 'hd_type', array(
            'value' => 2
        ));

        $next = $this->getElement('next');
        $next->clearDecorators();
        $next->setDecorators(array('ViewHelper'));

        $back = $this->getElement('back');
        $back->clearDecorators();
        $back->setDecorators(array('ViewHelper'));

        $saveExit = $this->getElement('save_exit');
        $saveExit->clearDecorators();
        $saveExit->setDecorators(array('ViewHelper'));

        $chkConfirm = $this->getElement('chk_confirm');
        $chkConfirm->clearDecorators();
        $chkConfirm->setDecorators(array('ViewHelper'));

        $dec1Confirm = $this->getElement('dec1_confirm');
        $dec1Confirm->clearDecorators();
        $dec1Confirm->setDecorators(array('ViewHelper'));

        $dec2Confirm = $this->getElement('dec2_confirm');
        $dec2Confirm->clearDecorators();
        $dec2Confirm->setDecorators(array('ViewHelper'));

        Application_Core_FormUtils::removeFormErrors($this);

        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));        
        
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' =>
                'rentguaranteeclaims/subforms/supporting-documents.phtml'))
        ));
    }
}
?>