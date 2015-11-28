<?php
/**
* This is an exact copy of the customer table
;
public $legacy portfolio does not stare customer data
;
public $it just emails admin,
* Quite frankly I can't see why it stores anything at all
* @param
* @return
* @author John Burrin
* @since
*/

class Model_Insurance_Portfolio_Property extends Model_Abstract {
    public $id;
    
    public $refno;
    public $houseNumber;
    public $building;
    public $address1;
    public $address2;
    public $address3;
    public $address4;
    public $address5;
    public $postcode;
    public $tenantOccupation;
    public $buildingsSumInsured;
    public $buildingsAccidentalDamage;
    public $buildingsNilExcess;
    public $contentsSumInsured;
    public $contentsAccidentalDamage;
    public $contentsNilExcess;
    public $limitedContents;
    public $buildingsRiskArea = 0;
    public $contentsRiskArea = 0;
}
    
?>