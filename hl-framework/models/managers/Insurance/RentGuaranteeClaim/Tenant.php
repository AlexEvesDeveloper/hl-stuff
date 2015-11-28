<?php

/**
 * Business rules class which provides rent guarantee claims tenant services.
 */
class Manager_Insurance_RentGuaranteeClaim_Tenant {

	protected $_tenantModel;

	/**
	 * Returns saved tenant details for a claim.
     *
     * @param int $referenceNumber
     *
	 * This method will retrieve tenant details for a claim information stored in the database
	 *
	 * @return Manager_Insurance_RentGuaranteeClaim_Tenant
	 * Returns this object populated with relevant information, or null if no
	 * relevant information has been stored.
	 */
	public function getTenants($referenceNumber) {
	
		if(empty($this->_tenantModel)) {
			
			$this->_tenantModel = new Datasource_Insurance_RentGuaranteeClaim_Tenant();
		}
		
		return $this->_tenantModel->getTenants($referenceNumber);
	}

	/**
     * create new tenant.
     *
     * This method provides a convenient way of inserting new tenants.
     *
     * @param array $tenantInfo, int $referenceNumber
     *
     * An Manager_Insurance_RentGuaranteeClaim_Tenant object containing all the
     * tenant information.
     *
     * @return boolean
     * True if the tenant information was successfully inserted, false otherwise.
     */
    public function createTenants($tenantInfo,$referenceNumber) {
        
        if(empty($this->_tenantModel)) {
            
            $this->_tenantModel = new Datasource_Insurance_RentGuaranteeClaim_Tenant();
        }
        
        return $this->_tenantModel->insertTenants($tenantInfo,$referenceNumber);
    }

	/**
     * Update tenant info.
     *
     * This method provides a convenient way of updating tenant information.
     *
     * @param array $tenantInfo
     * An Manager_Insurance_RentGuaranteeClaim_Tenant object containing all the
     * tenant information.
     *
     * @return boolean
     * True if the tenant info was successfully updated, false otherwise.
     */
    public function updateTenants($tenantInfo) {
        
        if(empty($this->_tenantModel)) {
            
            $this->_tenantModel = new Datasource_Insurance_RentGuaranteeClaim_Tenant();
        }
        
        return $this->_tenantModel->updateTenants($tenantInfo);
    }
	
	
	/**
	 * Remove tenant info.
	 *
	 * This method removes all tenant info associated with the claim
	 * passed in.
	 *
	 * @param array $tenantInfo
	 * The tenant id / Claim Reference number used to identify the exact tenant information to 
	 * delete
	 * 
	 *
	 * @return void
	 */
	public function removeTenants($tenantInfo) {
		
        if(empty($this->_tenantModel)) {
            
            $this->_tenantModel = new Datasource_Insurance_RentGuaranteeClaim_Tenant();
        }
        
        $this->_tenantModel->deleteTenants($tenantInfo);
	}

}

?>