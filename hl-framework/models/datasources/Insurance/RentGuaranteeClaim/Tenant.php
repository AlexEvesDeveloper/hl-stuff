<?php
/**
* Model definition for the rent guarantee claims tenants table.
*/
class Datasource_Insurance_RentGuaranteeClaim_Tenant extends Zend_Db_Table_Multidb {
     /**#@+
     * Mandatory attributes
     */
    protected $_name    = 'rent_guarantee_claims_tenants';
    protected $_id        = 'id';
    protected $_multidb = 'db_homelet_connect';
    /**#@-*/

     /**
     * Load an existing tenants for a claim from the database into the object
     *
     * @return array
     */
    public function getTenants($referenceNumber) {
        $select = $this->select();
        $select->where("reference_number = ? ",$referenceNumber);
        $data = $this->fetchAll($select);;
        return $data->toArray();
    }

    /**
     * Set options
     *
     * @param array data An array of reference number, tenant name, house name, street
     * town, city, postcode
     * @return void
     */
    public function insertTenants($data,$referenceNumber) {
        $dataArray = array();
        // remove tenants inserted before
        $this->removeTenants($referenceNumber);
        //get guaranor address from already existing postocde address table
        $totalTenants = empty($data['total_tenants']) ? '0' : $data['total_tenants'];
        for($i = 1; $i <= $totalTenants; $i++) {
            if($i <= 7)    {
                $tenantDOB = Application_Core_Utilities::ukDateToMysql($data['tenants_dob_'.$i]);
                $dataArray['reference_number']          = $referenceNumber;
                $dataArray['tenant_name']               = $data['tenant_name_'.$i];
                $dataArray['tenant_hometelno']          = $data['tenant_hometelno_'.$i];
                $dataArray['tenant_worktelno']          = $data['tenant_worktelno_'.$i];
                $dataArray['tenant_mobiletelno']        = $data['tenant_mobiletelno_'.$i];
                $dataArray['tenant_email']              = $data['tenant_email_'.$i];
                $dataArray['tenant_dob']                = $tenantDOB;
                $dataArray['rg_policy_ref']             = $data['rg_policy_ref_'.$i];
                $this->insert($dataArray);
            }
        }
    }
    /**
     * Remove tenant information from the data storage.
     *
     * Method responsible for removing tenant information stored against the
     * $referenceNumber passed in.
     *
     * @param string $referenceNumber
     * Identifier for the tenant underwriting information.
     *
     * @return void
     */
    public function removeTenants($referenceNumber) {

        $where = $this->getAdapter()->quoteInto('reference_number = ?', $referenceNumber);
        $this->delete($where);
    }
}
?>
