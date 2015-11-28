<?php

/**
 * Object Data model to describe the policy notes table
 */
class Model_Core_Note extends Model_Abstract {

    /**
    * Policy number of the policy the note belongs to.
    * 
    * @var string.
    */
    public $policyNumber;
    
    /**
    * Date the note was entered into the system
    */
    public $dateOfEntry;
    
    /**
    * The note itself.
    * 
    * @var string
    */
    public $notes;
}

?>