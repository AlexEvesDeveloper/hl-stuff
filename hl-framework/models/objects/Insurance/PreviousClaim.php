<?php

/**
* Objects of this class can be used to store details of a previous claim made by the customer.
*/
class Model_Insurance_PreviousClaim extends Model_Abstract {

    protected $_refNo;
    protected $_previousClaimType;
    protected $_claimMonth; //1 to 12
    protected $_claimYear; //yyyy
    protected $_claimValue;


    public function __construct() {

        $this->_refNo = '';
        $this->_previousClaimType = new Model_Insurance_PreviousClaimType();
        $this->_claimMonth = '';
        $this->_claimYear = '';

        $this->_claimValue = new Zend_Currency(
            array(
                'value' => 0,
                'precision' => 2
            ));
    }

    public function getRefno() {

        return $this->_refNo;
    }

    /**
    * Provided to be consistent with the dbase. Not used.
    *
    * @return string
    * Returns the claim type. Not used.
    */
    public function getClaimType() {

        return $this->_previousClaimType;
    }

    /**
    * Returns the month on which the claim was made.
    *
    * @return string.
    * String representation of the month the claim was made.
    */
    public function getClaimMonth() {

        return $this->_claimMonth;
    }

    /**
    * Returns the year on which the claim was made.
    *
    * @return string.
    * String representation of the year the claim was made.
    */
    public function getClaimYear() {

        return $this->_claimYear;
    }

    /**
    * Returns the value made on the claim.
    *
    * @return Zend_Currency.
    * Encapsulates the value made on the claim.
    */
    public function getClaimValue() {

        return $this->_claimValue;
    }

    public function setRefno($refNo) {

        $this->_refNo = $refNo;
    }

    public function setClaimType($claimType) {

        $this->_previousClaimType = $claimType;
    }

    public function setClaimMonth($claimMonth) {

        $this->_claimMonth = $claimMonth;
    }

    public function setClaimYear($claimYear) {

        $this->_claimYear = $claimYear;
    }

    public function setClaimValue($claimValue) {

        if(!is_a($claimValue, 'Zend_Currency')) {

            throw new Exception(get_class() . __FUNCTION__ . ": invalid argument type received.");
        }
        $this->_claimValue = $claimValue;
    }

    public function equals($otherPreviousClaim) {

        $isCopy = false;

        if($this->_refNo == $otherPreviousClaim->getRefno()) {

            if($this->_claimMonth == $otherPreviousClaim->getClaimMonth()) {

                if($this->_claimYear == $otherPreviousClaim->getClaimYear()) {

                    if($this->_previousClaimType->getClaimTypeID() == $otherPreviousClaim->getClaimType()->getClaimTypeID()) {

                        //Finally compare the two Zend_Currency objects.
                        if($this->_claimValue->equals($otherPreviousClaim->getClaimValue())) {

                            $isCopy = true;
                        }
                    }
                }
            }
        }

        return $isCopy;
    }
}

?>