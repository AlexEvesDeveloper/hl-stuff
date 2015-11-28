<?php
/**
* Model definition for the rent guarantee rent payments table.
*/
class Datasource_Insurance_KeyHouse_RentPayments extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_keyhouse';
    protected $_name = 'rent_payments';
    protected $_primary = 'id';

   /**
    * Inserts new rent payment records for a claim.
    *
    * @param array
    *
    * @return boolean
    */
    public function save($data) {
        $returnVal = "";
        if(is_array($data)) {
            unset($data['id']);
            if($data['reference_number'] != "")
                $returnVal = $this->insert($data);
        }
        return $returnVal;
    }
}
?>