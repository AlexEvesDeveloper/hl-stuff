<?php

/**
 * models a direct debit payment
 */
class Model_Core_Directdebit extends Model_Abstract {
    /**
     * Reference number related to this dd
     *
     */
    public $refNo;
    
    /**
     * Policy number related to this dd
     *
     */
    public $policyNumber;
    
    /**
     * Payment frequency, either monthly or annually
     *
     */
    public $paymentFrequency;
    
    /**
     * Name of the account holder
     *
     */
    public $accountName;
    
    /**
     * Bank account number
     *
     */
    public $accountNumber;
    
    /**
     * Bank Sort Code
     *
     */
    public $sortCode;
    
    /**
     * Date the paymnet is due to go out
     *
     */
    public $paymentDate;
    
    /**
     * Payment reference number
     *
     */
    public $paymentRefNo;
    
    /**
     * AUDDIS - the Automated Direct Debit Instruction Service - (yep googled it!!!)
     *
     */
    public $AUDDIS = "0N";
    
    /**
     * Error Mark ???
     *
     */
    public $errorMark = "No";
}