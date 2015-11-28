<?php
/**
* Model definition for the keyhouse rent guarantee claims table.
*/
class Datasource_Insurance_KeyHouse_Validation extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_keyhouse';
    protected $_name = 'keyhouse_validation';
    protected $_primary = 'id';

    /**
     *
     * Get claim validation data for a particular claim
     *
     * @param int $refNo
     * @return Array
     */
    public function getValidatedByRefNo($refNo) {
        $returnVal = array();
        $fetchClaimVal = $this->select()
            ->where('reference_number = ?', $refNo);
        $validationSummary = $this->fetchAll($fetchClaimVal);
        if(count($validationSummary) > 0) {
            $returnVal = $validationSummary->toArray();
        }
        return array_pop($returnVal);
    }
}
