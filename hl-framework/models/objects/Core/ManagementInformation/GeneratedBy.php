<?php

/**
 * models the GeneratedBy table
 */
class Model_Core_ManagementInformation_GeneratedBy extends Model_Abstract {
    /**
     * Policy number the record is related to
     *
     */
    public $policyNumber;
    
    /**
     * Csu id of the user that entered the quote
     * 87 is the Web user
     */
    public $csuId =87;
}
?>