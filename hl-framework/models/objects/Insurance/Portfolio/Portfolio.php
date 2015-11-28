<?php
/**
* Object Data model to describe the portfolioStat Table
* This table hold portfolio quote information, and is linked to the portfolio table by policynumber
* 
**/

class Model_Insurance_Portfolio_Portfolio extends Model_Abstract {
    /**
    * an ID
    */
    public $id;
    
    /**
    * Referance number (this is a portfolio UWP number)
    */
    public $refNo;
    
    /**
    * Quote number
    */
    public $quote = 0.00;
    
    /**
    * Date of inception
    */
    public $date;
    
    /**
    * Policy number 
    */
    public $policyNumber;
    
    /**
    * Agent scheme number
    */
    public $agentSchemeNo;
    
    /**
    * Id of csu entering the policy into the homelet system
    */
    public $csuId = 0;
    
    /**
    * Policy holder name
    */
    public $name;
    
    /**
    * Contact email address
    */
    public $email;
    
    /**
    * Contact telephone number
    */
    public $telephone;
    
    /**
    * Number of houses on the policy
    */
    public $numOfHouse;
    
    /**
    *
    */
    public $heardFrom;
    
    /**
    *
    */
    public $referred = "No";
    
    /**
    * 
    */
    public $hpc;
    
    /**
    * Customer referance number, linked to customer table
    */
    public $customerRefNo;
}
?>