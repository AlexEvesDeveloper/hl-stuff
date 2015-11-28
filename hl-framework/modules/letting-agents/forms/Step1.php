<?php

class LettingAgents_Form_Step1 extends Zend_Form_Multilevel {
    public function init()
    {
        $this->addSubForm(new LettingAgents_Form_Subforms_Campaign(), 'subform_campaign');
        $this->addSubForm(new LettingAgents_Form_Subforms_CompanyName(), 'subform_company-name');
        $this->addSubForm(new LettingAgents_Form_Subforms_PersonalDetails(), 'subform_personal-details');
       # $this->addSubForm(new LettingAgents_Form_Subforms_HeardAbout(), 'subform_heard-about');
       # $this->addSubForm(new LettingAgents_Form_Subforms_InterestedProducts(), 'subform_interested-products');
    }
}
?>