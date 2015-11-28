<?php
/**
* Model definition for the rent guarantee guarantors table.
*/
class Datasource_Insurance_RentGuaranteeClaim_Guarantor extends Zend_Db_Table_Multidb {
    protected $_name = 'rent_guarantee_claims_guarantors';
    protected $_id = 'id';
    protected $_multidb = 'db_homelet_connect';

     /**
     * Load an existing guarantors for a claim from the database into the object
     *
     * @return array of reference number, guarantor name, house name, street
     * town, city, postcode
     */
    public function getGuarantors($referenceNumber) {

        $select = $this->select();
        $select->where("reference_number = ? ",$referenceNumber);
        $data = $this->fetchAll($select);;
        return $data->toArray();
    }

    /**
     * Set options
     *
     * @param array data An array of reference number, guarantor name, house name, street
     * town, city, postcode
     * @return void
     */
    public function insertGuarantors($data, $guarantorId, $referenceNumber) {

        $dataArray = array();
        //delete guarantor information before inserting to avoid dupliaction
        $this->removeGuarantors($referenceNumber);
        $totalGuarantors = empty($data['total_guarantors']) ? '0' : $data['total_guarantors'];

        for ($i = 1; $i <= $totalGuarantors; $i++) {
            if ($i <= 4) {
                if (
                    isset($data['guarantor_address_'.$i]) &&
                    $data['guarantor_address_'.$i] != '' &&
                    $data['guarantor_address_'.$i] != '-'
                ) {
                    // Address lookup worked
                    $coreAddressManager = new Manager_Core_Postcode();
                    $guarantorAddress = $coreAddressManager->getPropertyByID(
                        $data['guarantor_address_'.$i]
                    );

                    $address_id = $guarantorAddress['id'];
                    $houseName  = $guarantorAddress['houseNumber'] != ''
                        ? $guarantorAddress['houseNumber']
                        : $guarantorAddress['buildingName'];
                    $street     = $guarantorAddress['address1'] != ''
                        ? $guarantorAddress['address1']
                        : $guarantorAddress['address2'];
                    $town       = $guarantorAddress['address4'];
                    $city       = $guarantorAddress['address5'];
                    $postcode   = $guarantorAddress['postcode'];
                }
                else {
                    // Address lookup failed, hence manual entry
                    $address_id = '-';
                    $houseName  = $data['guarantor_housename_' . $i];
                    $street     = $data['guarantor_street_' . $i];
                    $town       = $data['guarantor_town_' . $i];
                    $city       = $data['guarantor_city_' . $i];
                    $postcode   = $data['guarantor_postcode_' . $i];
                }

                $dataArray['reference_number']  = $referenceNumber;
                $dataArray['guarantor_name']    = $data['guarantor_name_' . $i];

                $dataArray['hometelno']         = $data['guarantor_hometelno_' . $i];
                $dataArray['worktelno']         = $data['guarantor_worktelno_' . $i];
                $dataArray['mobiletelno']       = $data['guarantor_mobiletelno_' . $i];
                $dataArray['email']             = $data['guarantor_email_' . $i];
                $dataArray['dob']               = Application_Core_Utilities::ukDateToMysql($data['guarantors_dob_' . $i]);
                $dataArray['address_id']        = $address_id;
                $dataArray['house_name']        = $houseName;
                $dataArray['street']            = $street;
                $dataArray['town']              = $town;
                $dataArray['city']              = $city;
                $dataArray['postcode']          = $postcode;
                $dataArray['homeletrefno']      = $data['guarantor_homeletrefno_' . $i];

                if ($guarantorId != '') {
                    $where = $this->getAdapter()->quoteInto('id = ?', $guarantorId);
                    $this->update($data,$where);
                }
                else {
                    $this->insert($dataArray);
                }
            }
        }
    }
    /**
     * Remove guarantor information from the data storage.
     *
     * Method responsible for removing guarantor information stored against the
     * $referenceNumber passed in.
     *
     * @param string $referenceNumber
     * Identifier for the guarantor underwriting information.
     *
     * @return void
     */
    public function removeGuarantors($referenceNumber) {

        $where = $this->getAdapter()->quoteInto(
            'reference_number = ?',
            $referenceNumber
        );
        $this->delete($where);
    }
}
?>