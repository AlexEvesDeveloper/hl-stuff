<?php

class LettingAgents_Form_Step4 extends Zend_Form_Multilevel {
    public function init()
    {
        $this->addSubForm(new LettingAgents_Form_Subforms_AgentType(), 'subform_agent-type');
		$this->addSubForm(new LettingAgents_Form_Subforms_ProfessionalIndemnity(), 'subform_professional-indemnity');
    }

}