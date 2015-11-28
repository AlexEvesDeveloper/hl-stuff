<?php
class Manager_Insurance_TenantsContentsPlus_QuickQuote extends Manager_Insurance_TenantsContentsPlus_Quote {

    public function __construct($agentSchemeNumber = null) {
        $this->_offline = true;
        $this->_quoteModel = new Model_Insurance_LegacyQuote();
        if ($agentSchemeNumber != "" && !is_null($agentSchemeNumber)) $this->_quoteModel->agentSchemeNumber = $agentSchemeNumber;
        
        // Default the settings
        $agent = new Datasource_Core_Agents();
        $this->_quoteModel->agentRateSetID = $agent->getRatesetID($this->_quoteModel->agentSchemeNumber);
        $this->_quoteModel->status = 'Quote';
        $this->_quoteModel->startTime = date("H:m:s");
    }

}
?>