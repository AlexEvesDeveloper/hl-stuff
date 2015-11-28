<?php

/**
 * Manager class for White Label branding logic
 */
class Manager_Core_WhiteLabel {

    /**
    * function to return a whole whitlabel record
    * @param $agentSchemeNo String The agent shcheme number of the record to return
    * @return Model_Core_WhiteLabel or null on failure
    */
    public function fetchByAgentSchemeNumber($agentSchemeNumber){
        $whiteLabelData = new Model_Core_WhiteLabel();
        $whiteLabelObject = new Datasource_Core_WhiteLabel();
              
        return $whiteLabelObject->fetchByAgentSchemeNo($agentSchemeNumber);
    }
}

?>