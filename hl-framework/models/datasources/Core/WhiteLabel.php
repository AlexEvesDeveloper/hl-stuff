<?php
/**
* Model definition for the whitelable table
* 
* 
*/
class Datasource_Core_WhiteLabel extends Zend_Db_Table_Multidb {
    protected $_name = 'whitelabel';
    protected $_primary = 'whitelabelID';
    protected $_multidb = 'db_legacy_homelet';
    
    /**
    * function to return a whole whitlabel record
    * @param $agentSchemeNo String The agent shcheme number of the record to return
    * @return Model_Core_WhiteLabel or null on failure
    */
    public function fetchByAgentSchemeNo($agentSchemeNo){
        $whiteLableObject = new Model_Core_WhiteLabel();
        
        $select = $this->select()
          ->from($this->_name)
          ->where('agentschemenumber = ?', $agentSchemeNo);
          
        $row = $this->fetchRow($select);
        
        if (empty($row)) {
            $params = Zend_Registry::get('params');
            
            $select = $this->select()
            ->from($this->_name)
            ->where('agentschemenumber = ?', $params->homelet->defaultAgent);
            
          $row = $this->fetchRow($select); 
        }
        $whiteLableObject->companyName = $row['companyname'];
        $whiteLableObject->styleSheet = $row['stylesheet'];
        $whiteLableObject->agentSchemeNumber = $row['agentschemenumber'];
        $whiteLableObject->merchantId = $row['merchantid'];
        $whiteLableObject->referenceMerchantId = $row['refmerchantid'];
        $whiteLableObject->whiteLabelID = $row['whitelabelID'];
        $whiteLableObject->logo = $row['logo'];
        $whiteLableObject->twoLetterCode = $row['twolettercode'];
        $whiteLableObject->whiteLabelPolicyID = $row['whitelabelPolicyID'];
        $whiteLableObject->whiteLabelFrontPageHTMLID = $row['whitelabelFrontPageHTMLID'];
        $whiteLableObject->templateSetID = $row['templateSetID'];
        $whiteLableObject->agentPolicyProfileID = $row['agentPolicyProfileID'];
        $whiteLableObject->agentProductProfileID = $row['agentProductProfileID'];
        $whiteLableObject->setupFile = $row['setupfile'];
        return  $whiteLableObject;
    }
}
?>