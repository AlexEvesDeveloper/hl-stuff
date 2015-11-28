<?php

class Model_Core_TestPolicy extends Model_Abstract {
    /**
     * Policy number of the policy entered
     **/
    public $policynumber;
    
    /**
     * Agent scheme number of the agent buying a policy in test mode
     **/
    public $agentschemeno;
    
    /**
     * CSU Id of the user that enter the poicy, for WEB this will be 87
     **/
    public $csuid;
    
    /**
     * Test Policy status
     **/
    public $isTestPolicy = "Yes";
}
?>