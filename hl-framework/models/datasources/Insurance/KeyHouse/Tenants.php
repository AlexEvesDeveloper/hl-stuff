<?php
/**
* Model definition for the rent guarantee tenants table.
*/
class Datasource_Insurance_KeyHouse_Tenants extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_keyhouse';
    protected $_name = 'tenants';
    protected $_primary = 'id';

   /**
    * Inserts new tenants records for a claim.
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