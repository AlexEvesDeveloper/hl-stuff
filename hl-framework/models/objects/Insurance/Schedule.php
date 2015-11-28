<?php
/**
 * Object Data model to describe the Schedule Data
 * 
 * TODO: ARRRRRRRRRRGGGGGHHHHHHHHH!!!!!!!
 */
class Model_Insurance_Schedule extends Model_Abstract {
    /**
    * Payement reference number
    */
    public $paymentRefNo;
    
    /**
    * Payment due in each month
    */
    public $months = array(
            'january' => 0,
            'february' => 0,
            'march' => 0,
            'april' => 0,
            'may' => 0,
            'june' => 0,
            'july' => 0,
            'august' => 0,
            'september' => 0,
            'october' => 0,
            'november' => 0,
            'december' => 0 
        );
    
    /**
    * Policy Number
    */
    public $policyNumber;
    
    /**
    * Fee for doing it for six months
    */
    public $sixMonthFee = 0;
    
    /**
    * Fee for doing it by direct debit (per month)
    */
    public $ddFee = 0;
    
    /**
    * TODO: What is this one?
    */
    public $banked = 0;
}
?>