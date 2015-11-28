<?php

class Connect_Form_ReferencingCompanyApplication extends Zend_Form_Multilevel {

    public function init() {
    	
    	$this->addSubForm(new Connect_Form_Subforms_ReferencingCompanyApplication_Property(), 'subform_property');
    	$this->addSubForm(new Connect_Form_Subforms_ReferencingCompanyApplication_Product(), 'subform_product');
        $this->addSubForm(new Connect_Form_Subforms_ReferencingCompanyApplication_Landlord(), 'subform_landlord');
        $this->addSubForm(new Connect_Form_Subforms_ReferencingCompanyApplication_CompanyDetails(), 'subform_companydetails');
        $this->addSubForm(new Connect_Form_Subforms_ReferencingCompanyApplication_CompanyRegisteredAddress(), 'subform_companyregisteredaddress');
        $this->addSubForm(new Connect_Form_Subforms_ReferencingCompanyApplication_CompanyTradingAddress(), 'subform_companytradingaddress');
        $this->addSubForm(new Connect_Form_Subforms_ReferencingCompanyApplication_Additional(), 'subform_additional');
        $this->addSubForm(new Connect_Form_Subforms_ReferencingCompanyApplication_Declaration(), 'subform_declaration');
    }

}