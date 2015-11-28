<?php

/**
 * Represents an entry in the whitelabel table
 *
 * This table is used (amoung other things probably) to brand documents
 * Many of these members will be deprecated as the new frame work expands are are only included here
 * for documentation purposes only
 */
class Model_Core_WhiteLabel extends Model_Abstract {

    /**
    * Company name
    */
    public $companyName;
    
    /**
    * name of the stylesheet used in the leacy systems
    */
    public $styleSheet;
    
    /**
    * the agent scheme number
    */
    public $agentSchemeNumber;

    /**
    * Merchant id of the of the brand for purchasing insurance,
    */
    public $merchantId;

    /**
    * Merchant id of the of the brand for purchasing referecing products,
    */
    public $referenceMerchantId;

    /**
    * The White Label id field, Primary key
    */
    public $whiteLabelID;

    /**
    * Image location of the brand logo, note in the table this is a FULL html img tag
    */
    public $logo;
    
    /**
    * Two Letter code, default HL, this will be copied to the quote table when the quote is created
    */    
    public $twoLetterCode = 'HL';
    
    /**
    * link to the whitelabelPolicy table
    */    
    public $whiteLabelPolicyID;
    
    /**
    * link to the whitelabelFrontPageHTML Table
    */
    public $whiteLabelFrontPageHTMLID;
    
    /**
    * No idea
    */    
    public $templateSetID;
    
    /**
    * link to the agentProduct table
    */    
    public $agentPolicyProfileID;
    
    /**
    * link to the agentProductProfile table
    */    
    public $agentProductProfileID;
    
    /**
    * relative link to the location of some setup files
    */    
    public $setupFile;
}

?>