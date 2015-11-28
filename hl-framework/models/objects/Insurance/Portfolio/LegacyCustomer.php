<?php
class Model_Insurance_Portfolio_LegacyCustomer extends Model_Abstract {
    public $id;
    
    public $type_id = ""; 
    
    public $title = "";
    
    public $first_name = "";
    
    public $last_name = "";
    
    public $address1 = "";
    
    public $address2 = "";
    
    public $address3 = "";
    
    public $postcode = "";
    
    public $telephone1 = "";
    
    public $telephone2 = "";
    
    public $email_address = "";

    /**
     * @var null|string Represents a date of birth or NULL
     */
    public $date_of_birth_at = null;
    
    public $password  = "";
    
    public $country = "";
    
    public $foreign_address = "";
    
    public $occupation  = "";
    
    // This is Vank, because I've had to make customer to quote 1 - 1,
    public $refNo = "";
}
?>