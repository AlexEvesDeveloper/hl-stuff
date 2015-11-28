<?php
/**
* Object Data model to describe the Legacy portfolio Table
* This table hold portfolio quote information, each property in a portfolio is
* held as a single row indexed by Id and linked by policy number
* 
**/

    class Model_Insurance_Portfolio_LegacyProperty extends Model_Abstract {
    
    /**
    * Unique Autonumber primary key
    */
    public $id;
    
    /**
    * Muliple index policynumber
    */
    public $policyNumber;
    
    /**
    * Premium amount for quote (currency - probably a friki' float(10,2))
    */
    public $premium;
    
    /**
    * Quote Amount  (currency - probably a friki' float(10,2))
    */
    public $quote = "";
    
    /**
    * Ipt on qoute  (currency - probably a friki' float(10,2))
    */
    public $ipt;
    
    /**
    * Pipe Separated policy options (AAARRRRRRGGGGG!!!!!!!)
    */
    public $policyOptions;
    
    /**
    * Amounts covered for policy options
    * Pipe separated (AAARRRRRRGGGGG!!!!!!!)
    */
    public $amountsCovered;
    
    /**
    * policy options premiums
    * Pipe separated (AAARRRRRRGGGGG!!!!!!!)
    */
    public $optionPremiums =  "|||";
    
    /**
    * Line one property address
    */
    public $propAddress1;
    
    /**
    * Line three property address
    */
    public $propAddress3;
    
    /**
    * Line five property address
    */
    public $propAddress5;
    
    /**
    * Property postcode
    */
    public $propPostcode;
    
    /**
    *  Property riskarea
    */
    public $riskArea;
    
    /**
    * Discount - float(4,2)
    */
    public $discount;
    
    /**
    * Surcharge - float(4,2)
    */
    public $surcharge;
    
    /**
    *  Rate set - Integer
    */
    public $rateSetId;
    
    /**
    * Excess ID - Integer
    */
    public $excessId;
    
    /**
    * Options Discount
    * Pipe separated (AAARRRRRRGGGGG!!!!!!!)
    */
    public $optionDiscounts = "|||";
    
    /**
    * Risk area b ?
    */
    public $riskAreaB;
}
?>