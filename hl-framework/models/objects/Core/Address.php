<?php

/**
 * Represents an address within the system. The address may be associated with a referencing
 * applicant, an insured address, the address of a company or letting etc. Anywhere an address is
 * used it can be incorporated into this class for consistency.
 *
 * This class does not apply Experian QAS-level rigor to addressing, but attempts to accommodate most addresses
 * in a readable and accessible manner.
 */
class Model_Core_Address extends Model_Abstract {

     /**
      * Uniquely identifies the address in the system.
      *
      * @var integer
      */
     public $id;

     /**
      * E.g. 173
      *
      * @var mixed
      * Integer or string, allowing for some flat numbers which
      * include non-alphanums.
      */
     public $flatNumber;

     /**
      * E.g. The Tarred Lung
      *
      * @var string
      */
     public $houseName;

     /**
      * E.g. 13
      *
      * @var mixed
      * Integer or string, allowing for some house numbers which
      * include non-alphanums.
      */
     public $houseNumber;

     /**
      * Street address. E.g. Catfish Road
      *
      * @var string
      */
     public $addressLine1;

     /**
      * Address part which isn't the street address and which isn't the town
      * address.
      *
      * @var string
      */
     public $addressLine2;

     /**
      * Town name.
      *
      * @var string
      */
     public $town;

     /**
      * County name.
      *
      * @var string
      */
     public $county;

     /**
      * The zip code. Includes spaces.
      *
      * @var string
      */
     public $postCode;

     /**
      * The country name. May be abbreviated, e.g. USA
      *
      * @var string
      */
     public $country;

     /**
      * Indicates UK address or otherwise.
      *
      * @var boolean
      */
     public $isOverseasAddress;

     /**
      *
      * Add some public getters
      * @return mixed
      */

     public function getFlatNumber(){
          return $this->flatNumber;
     }
     public function getHouseName(){
          return $this->houseName;
     }

     public function getHouseNumber(){
          return $this->houseNumber;
     }

     public function getAddressLine1(){
          return $this->addressLine1;
     }

     public function getAddressLine2(){
          return $this->addressLine2;
     }

     public function getTown(){
          return $this->town;
     }

     public function getCounty(){
          return $this->county;
     }

     public function getPostCode(){
          return $this->postCode;
     }

     public function getCountry(){
          return $this->country;
     }

     /**
      * Compares an incoming address to this address.
      *
      * @param Model_Core_Address $otherAddress
      * The address to compare against.
      *
      * @return boolean
      * True if the addresses match, false otherwise.
      */
     public function equals(Model_Core_Address $otherAddress) {

          $returnVal = true;

          if(strcasecmp($this->flatNumber, $otherAddress->flatNumber) != 0) {

               $returnVal = false;
}
          else if(strcasecmp($this->houseName, $otherAddress->houseName) != 0) {

               $returnVal = false;
          }
          else if(strcasecmp($this->houseNumber, $otherAddress->houseNumber) != 0) {

               $returnVal = false;
          }
          else if(strcasecmp($this->addressLine1, $otherAddress->addressLine1) != 0) {

               $returnVal = false;
          }
          else if(strcasecmp($this->addressLine2, $otherAddress->addressLine2) != 0) {

               $returnVal = false;
          }
          else if(strcasecmp($this->town, $otherAddress->town) != 0) {

               $returnVal = false;
          }
          else if(strcasecmp($this->county, $otherAddress->county) != 0) {

               $returnVal = false;
          }
          else if(strcasecmp($this->postCode, $otherAddress->postCode) != 0) {

               $returnVal = false;
          }
          else if(strcasecmp($this->country, $otherAddress->country) != 0) {

               $returnVal = false;
          }
          else if(strcasecmp($this->isOverseasAddress, $otherAddress->isOverseasAddress) != 0) {

               $returnVal = false;
          }

          return $returnVal;
     }

     /**
      * Returns string representation of Address object
      *
      * @param $separator String separator
      * @return String
      */
    public function toString($separator = ', ') {
        $returnVal  = '';
        $returnVal .= (!is_null($this->flatNumber) && trim($this->flatNumber) != '') ? trim($this->flatNumber) . $separator : '';
        $returnVal .= (!is_null($this->houseName) && trim($this->houseName) != '') ? trim($this->houseName) . $separator : '';
        $returnVal .= (!is_null($this->houseNumber) && trim($this->houseNumber) != '') ? trim($this->houseNumber) . $separator : '';
        $returnVal .= (!is_null($this->addressLine1) && trim($this->addressLine1) != '') ? trim($this->addressLine1) . $separator : '';
        $returnVal .= (!is_null($this->addressLine2) && trim($this->addressLine2) != '') ? trim($this->addressLine2) . $separator : '';
        $returnVal .= (!is_null($this->town) && trim($this->town) != '') ? trim($this->town) . $separator : '';
        $returnVal .= (!is_null($this->county) && trim($this->county) != '') ? trim($this->county) . $separator : '';
        $returnVal .= (!is_null($this->postCode && trim($this->postCode) != '')) ? trim($this->postCode) . $separator : '';
        $returnVal .= (!is_null($this->country) && trim($this->country) != '') ? trim($this->country) . $separator : '';
        $returnVal  = ($returnVal != '') ? substr($returnVal, 0, -strlen($separator)) : null;
        return $returnVal;
    }
}

?>