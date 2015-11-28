<?php

/**
* Objects of this class can be used to store details of a previous claim types
* recognised by the system. For example, an object of this class could encapsulate
* 'Aircraft Or Items Dropped Thereform' as a previous claim type that a customer
* may specify when incepting a policy.
*/
class Model_Insurance_PreviousClaimType extends Model_Abstract {

    protected $_id;
    protected $_type;
    protected $_description;


    public function __construct($id = null, $claimType = null, $claimTypeText = null) {

        $this->_id = $id;
        $this->_type = $claimType;
        $this->_description = $claimTypeText;
    }

    public function getClaimTypeID() {

        return $this->_id;
    }
    
    public function getClaimType() {

        return $this->_type;
    }

    public function getClaimTypeText() {

        return $this->_description;
    }

    public function setClaimTypeID($id) {

        $this->_id = $id;
    }

    public function setClaimType($claimType) {

        $this->_type = $claimType;
    }

    public function setClaimTypeText($claimTypeText) {

        $this->_description = $claimTypeText;
    }
}

?>