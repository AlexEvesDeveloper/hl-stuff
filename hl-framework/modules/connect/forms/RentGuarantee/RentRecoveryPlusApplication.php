<?php

class Connect_Form_RentGuarantee_RentRecoveryPlusApplication extends Zend_Form_Multilevel { 

    public function init()
    {
        $this->addSubForm(new Connect_Form_Subforms_RentguaranteeRentRecoveryPlusApplication_Product(), 'subform_product');
        $this->addSubForm(new Connect_Form_Subforms_RentguaranteeRentRecoveryPlusApplication_Property(), 'subform_property');
        $this->addSubForm(new Connect_Form_Subforms_RentguaranteeRentRecoveryPlusApplication_Landlord(), 'subform_landlord');
        $this->addSubForm(new Connect_Form_Subforms_RentguaranteeRentRecoveryPlusApplication_Payment(), 'subform_payment');
        $this->addSubForm(new Connect_Form_Subforms_RentguaranteeRentRecoveryPlusApplication_Declaration(), 'subform_declaration');

        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors',
            array('HtmlTag', array('tag' => 'div')),
        ));
    }

}