<?php

class Connect_Form_Subforms_RentguaranteeRentRecoveryPlusApplication_Product extends Zend_Form_SubForm {

     /**
     * Create product subform
     *
     * @return void
     */
    public function init()
    {
        // Add policy term selection element
        $this->addElement('radio', 'term', array(
            'label'     => 'Policy Term',
            'required'  => true,
            'multiOptions' => array(
                '6' =>  '6 Months',
                '12' => '12 Months'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a policy term',
                            'notEmptyInvalid' => 'Please select a policy term'
                        )
                    )
                )
            )
        ));

        // Add reference type selection element
        $this->addElement('radio', 'type', array(
            'label'     => 'Reference Type',
            'required'  => true,
            'multiOptions' => array(
                'Insight' => 'Insight',
                'Enhance' => 'Enhance',
                'Optimum' => 'Optimum',
                'Other Credit Check'   => 'Other Provider (credit check only)',
                'Other Full Reference' => 'Other Provider (full reference)'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a reference type',
                            'notEmptyInvalid' => 'Please select a reference type'
                        )
                    )
                )
            )
        ));

        // Add Provider Details text field
        $this->addElement('text', 'provider', array(
            'label'      => 'Provider Name',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter provider name',
                            'notEmptyInvalid' => 'Please enter provider name'
                        )
                    )
                )
            )
        ));
        
        // Add reference type selection element
        $this->addElement('radio', 'excess', array(
            'label'     => 'Excess',
            'required'  => true,
            'multiOptions' => array(
                'nilexcess' => 'Nil Excess',
                '1 Month Excess'    => '1 Month Excess',                
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a excess',
                            'notEmptyInvalid' => 'Please select a excess'
                        )
                    )
                )
            )
        ));

        // Add policy continuation selection element
        $this->addElement('radio', 'continuation', array(
            'label'     => 'Continuation of existing policy',
            'required'  => true,
            'multiOptions' => array(
                'yes' =>  'Yes',
                'no'  => 'No'
            ),
            'separator' => ' ',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please select answer to question: Is this the continuation of an existing policy?',
                        'notEmptyInvalid' => 'Please select answer to question: Is this the continuation of an existing policy?'
                    )
                )
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguarantee/subforms/rent-recovery-plus-application-product.phtml'))
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
        if ($formData['type'] === 'Other Credit Check' || $formData['type'] === 'Other Full Reference') {            
            $this->getElement('provider')->setRequired(true);             
        } else {
            $this->getElement('provider')->setRequired(false);         
        }
        // Call original isValid()
        return parent::isValid($formData);
    }
}