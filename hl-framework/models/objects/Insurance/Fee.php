<?php
/**
 * Object Data model to describe the Fees Data
 * This object does not have a table, but just describes a fee
 *
 */
class Model_Insurance_Fee extends Model_Abstract {

    /**
     * Tenants Contents+ Monthly fee
     */
    public $tenantspMonthlyFee;

    /**
     * Monthly Fee for SPE only
     */
    public $monthlyFeeSP;

    /**
     * Six month fee, BV says probably best to use $this->sixMonthFee exclusively instead
     */
    public $adminFee;

    /**
     * Six month fee
     */
    public $sixMonthFee;

    /**
     * Cancellation fee
     */
    public $cancellationFee;
}
?>