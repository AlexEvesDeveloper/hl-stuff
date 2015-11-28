<?php

/**
 * models a CreditCard payment
 */
class Model_Core_Creditcard extends Model_Abstract {

    /**
    * A the reference number of the related policy
    *
    **/
    public  $refNo;
    
    /**
    * The policy number of the related policy
    *
    **/
    public  $policyNumber;
    
    /**
    * The frequency of the paymnet
    *
    **/
    public  $paymentFrequency;
    
    /**
    * Name on the Card
    *
    **/
    public  $cardName;
    
    /**
    * the LAST 4 digits on the card
    *
    **/
    public  $cardNumber;
    
    /**
    * The type of card Visa, Mastercard etc.
    *
    **/
    public  $cardType;
    
    /**
    * The expiry date on the card expressed as MM/YY
    * 
    **/
    public  $expiryDate;
    
    /**
    * The start date on the card expressed as MM/YY
    *
    **/
    public  $startDate;
    
    /**
    * The Issue number on the card as 01 etc
    *
    **/
    public  $issueNo;
    
    /**
    * The date the payment was taken (Transaction date)
    *
    **/
    public  $paymentDate;
    
    /**
    * The payment reference number (In out case I we use the policy number)
    *
    **/
    public  $paymentRefNo;
    
    /**
    * The merchant id we use (default will be hanove04)
    *
    **/
    public  $merchantId = "hanove04";
    
    /**
    * Dunno!!
    *
    **/
    public  $expWarnLetterSent;
}
?>