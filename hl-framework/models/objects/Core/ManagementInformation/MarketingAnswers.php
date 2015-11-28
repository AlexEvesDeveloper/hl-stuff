<?php

/**
 * models the Marketing answers tabletable
 */
class Model_Core_ManagementInformation_MarketingAnswers extends Model_Abstract {
    /**
     * Policy number the record is related to
     *
     */
    public $policyNumber;
    
    /**
     * The reference number of the policy
     * 
     */
    public $refNo;
    
    /**
    * the answer to the question
    */
    public $answer;
    
}
?>