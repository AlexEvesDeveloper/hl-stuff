<?php
/**
 * Model definition for the address datasource.
 */
class Datasource_Core_Addresses extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'address_details';
    protected $_primary = 'id';
    /**#@-*/
    
    /**
     * Inserts a new, empty address into the datasource and returns a corresponding object.
     *
     * @return Model_Core_Address
     * Encapsulates the details of the newly inserted address.
     */
    public function createAddress()
    {
        $addressId = $this->insert(array());
        if (empty($addressId)) {
            
            // Failed insertion
            Application_Core_Logger::log("Can't create record in table {$this->_name}", 'error');
            $returnVal = null;
        }
        else {
         
            $address = new Model_Core_Address();
            $address->id = $addressId;
            $returnVal = $address;
        }
        
        return $returnVal;
    }
    
    /**
     * Updates an existing Address.
     *
     * @param Model_Core_Address
     * The address details to update in the datasource.
     *
     * @return void
     */
    public function updateAddress($address)
    {
        if(empty($address)) {
            
            return;
        }
        
        
        $data = array(
            'flat_number' => $address->flatNumber,
            'house_name' => $address->houseName,
            'house_number' => $address->houseNumber,
            'address_line1' => $address->addressLine1,
            'address_line2' => $address->addressLine2,
            'town' => $address->town,
            'county' => $address->county,
            'postcode' => $address->postCode,
            'country' => $address->country,
            'is_overseas' => ($address->isOverseasAddress) ? 1 : 0
        );
        
        $where = $this->quoteInto('id = ?', $address->id);
        $this->update($data, $where);
    }
    
    /**
     * Returns an existing address.
     *
     * @param integer $addressId
     * The unique address identifier.
     *
     * @return mixed
     * A Model_Core_Address encapsulating the address details, or null if
     * the address cannot be found.
     */
    public function getById($addressId)
    {
        if(empty($addressId)) {
            return null;
        }
        
        $select = $this->select();
        $select->where('id = ?', $addressId);
        $addressRow = $this->fetchRow($select);
        
        if(empty($addressRow)) {
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ':Unable to find address.');
            $returnVal = null;
        } else {
            $address = new Model_Core_Address();
            $address->id = $addressRow->id;
            $address->flatNumber = $addressRow->flat_number;
            $address->houseName = $addressRow->house_name;
            $address->houseNumber = $addressRow->house_number;
            $address->addressLine1 = $addressRow->address_line1;
            $address->addressLine2 = $addressRow->address_line2;
            $address->town = $addressRow->town;
            $address->county = $addressRow->county;
            $address->postCode = $addressRow->postcode;
            $address->country = $addressRow->country;
            $address->isOverseasAddress = ($addressRow->is_overseas == 1) ? true : false;
            $returnVal = $address;
        }
        
        return $returnVal;
    }
}
?>