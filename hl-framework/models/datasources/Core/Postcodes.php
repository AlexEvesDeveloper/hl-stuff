<?php
/**
* Model definition for the postcode tables.
* 
*/
class Datasource_Core_Postcodes extends Zend_Db_Table_Multidb {
    protected $_name = 'postcodefull';
    protected $_id = 'id';
    protected $_multidb = 'db_homelet_insurance_com';
    
    /* NOTES
      The current postcodefull database structure contains the following fields :-
        ORD = Department
        ORG = Organisation
        SBN = Sub Building (eg. flat number)
        BNA = Building Name
        POB = PO Box
        NUM = House Number
        address1 = Usually Street Name but don't assume that!
        address2
        address3
        address4
        address5
        postcode = Postcode (obviously)
        CTA = County (Administrative)
        CTP = County (Former Postal County)
        CTT = County (Traditional)
        SCD = Royal Mail Sortcode
        CAT = User Category (eg. residential, non-residential, large, etc..)
        NDP = Number of Delivery Points in Postcode
        DPS = Delivery Point Suffix
        id
        
        02/02/2011 - PB
    */
    
    
    /**
     * Fetch an individual address by the passed ID
     *
     * @param id Id of the address
     * @return array
     */
    public function getPropertyByID($id){
        $select = $this->select();
        $select->where('id = ?', (int)$id);
        $address = $this->fetchRow($select);
        return $address;
    }
    
    /**
     * Fetch a list of addresses by the postcode
     *
     * @param postcode Postcode for the search
     * @param houseNumber Optional house number for the search
     * @return array
     */
    public function getPropertiesByPostcode($postcode, $houseNumber = null) {
        // First we need to check that the postcode is valid
        $select = $this->select();
        $select->where('postcode = ?', $postcode);
        if ($houseNumber) {
            $select->where('NUM = ?', $houseNumber);
        }
        $addresses = $this->fetchAll($select);
        return $addresses;
    }
}
?>