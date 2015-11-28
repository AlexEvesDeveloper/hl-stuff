<?php
/**
* Model definition for the rent guarantee claims guarantors table.
*/
class Datasource_Insurance_KeyHouse_Guarantors extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_keyhouse';
    protected $_name = 'guarantors';
    protected $_primary = 'id';
    /**
     * Data not to be sent to KH
     * @var array
     */
    protected $removals = array('id', 'address_id');

   /**
    * Inserts new guarantor records for a claim.
    *
    * @param array $data
    *
    * @return boolean
    */
    public function save($data) {
        $returnVal = "";
        if(is_array($data)) {
            if($data['reference_number'] != "") {
                // Remove non-required fields
                foreach ($this->removals as $removal) {
                    unset($data[$removal]);
                }
                $returnVal = $this->insert($data);
            }
        }
        return $returnVal;
    }
}
?>