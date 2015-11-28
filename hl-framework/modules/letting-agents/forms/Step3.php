<?php
class LettingAgents_Form_Step3 extends Zend_Form_Multilevel {
    public function init()
    {
    	$this->addSubForm(new LettingAgents_Form_Subforms_CompanyDetail(), 'subform_company-detail');
    	$this->addSubForm(new LettingAgents_Form_Subforms_TradingAddress(), 'subform_trading-address');
    	$this->addSubForm(new LettingAgents_Form_Subforms_CompanyFax(), 'subform_company-fax');
    	$this->addSubForm(new LettingAgents_Form_Subforms_AccountsAddress(), 'subform_accounts-address');
    	$this->addSubForm(new LettingAgents_Form_Subforms_HeadOffice(), 'subform_head-office');
        $this->addSubForm(new LettingAgents_Form_Subforms_MultipleEmails(), 'subform_multiple-emails');
    }
}
?>