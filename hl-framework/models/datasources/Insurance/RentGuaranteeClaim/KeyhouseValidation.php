<?php
/**
* Model definition for the rent guarantee key house validation table.
*/
class Datasource_Insurance_RentGuaranteeClaim_KeyhouseValidation extends Zend_Db_Table_Multidb {

    protected $_name = 'rent_guarantee_claims_keyhouse_validation';
    protected $_referenceNumber = 'id';
    protected $_multidb = 'db_homelet_connect';

    /**
     * Inserts data
     *
     * @param int $referenceNum
     * @param int $validityStatus
     * @param str $message
     * @param int $khReferenceNum
     *
     * @return array
     */
    public function insertData($referenceNum, $validityStatus, $message, $khReferenceNum) {
        return $this->insert(array(
            'reference_number' => $referenceNum,
            'validity_status' => $validityStatus,
            'reason' => $message,
            'keyhouse_claim_reference_number' => $khReferenceNum,
            'updated_on' => date('Y-m-d')
        ));
    }

    /**
     * Gets the KH reference number
     *
     * @param int $referenceNumber
     *
     * @return Keyhouse Claim Number
     */
    public function getKHClaimNumber($referenceNumber) {
        $select = $this->select();
        $select->where('reference_number = ?', $referenceNumber);
        // Fix this
        $data = $this->fetchRow($select)->toArray();
        return $data['keyhouse_claim_reference_number'];
    }

    /**
     * Delete the keyhouse validation detail for the given Reference number
     *
     * @param int $referenceNum
     *
     * @return void
     */
    public function deleteByReferenceNumber($referenceNumber) {
        $where = $this->getAdapter()->quoteInto('reference_number = ?', $referenceNumber);
        $this->delete($where);
    }
}
?>