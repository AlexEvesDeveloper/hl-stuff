<?php

class Connect_Form_Subforms_RentguaranteeRentRecoveryPlusApplication_Payment extends Zend_Form_SubForm {
    /**
     * Create payment subform
     *
     * @return void
     */
    public function init() {
             
        // Add Payment element
        $this->addElement('radio', 'payment', array(
            'label'     => 'Payment method',
            'required'  => true,
            'multiOptions' => array(
                'DirectDebitByLeadingAgent' =>  'Direct Debit *',
                'Cheque'  => 'Cheque'
            ),
            'separator' => '',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select Payment method',
                            'notEmptyInvalid' => 'Please select Payment method'
                        )
                    )
                )
            )
        ));                
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguarantee/subforms/rent-recovery-plus-application-payment.phtml'))
        ));

        $this->setElementFilters(array('StripTags'));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }

}