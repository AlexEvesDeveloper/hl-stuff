<?php
/**
 * Business rules class which provides rent guarantee claims guarantor services.
 */
class Manager_Insurance_RentGuaranteeClaim_Guarantor {

	protected $_guarantorModel;

	/**
	 * Returns saved guarantor details for a claim.
     *
     * @param int $referenceNumber
     *
	 * This method will retrieve guarantor details for a claim information stored in the database
	 *
	 * @return Manager_Insurance_RentGuaranteeClaim_Guarantor
	 * Returns this object populated with relevant information, or null if no
	 * relevant information has been stored.
	 */
	public function getGuarantors($referenceNumber) {

		if(empty($this->_guarantorModel)) {

			$this->_guarantorModel = new Datasource_Insurance_RentGuaranteeClaim_Guarantor();
		}

		return $this->_guarantorModel->getGuarantors($referenceNumber);
	}

	/**
     * create new guarantor.
     *
     * @param array $guarantorInfo, int $referenceNumber
     *
     * This method provides a convenient way of inserting new guarantors.
     *
     * @param Manager_Insurance_RentGuaranteeClaim_Guarantor $guarantorInfo
     * An Manager_Insurance_RentGuaranteeClaim_Guarantor object containing all the
     * guarantor information.
     *
     * @return boolean
     * True if the guarantor information was successfully inserted, false otherwise.
     */
    public function createGuarantors($guarantorInfo, $referenceNumber) {

        if(empty($this->_guarantorModel)) {

            $this->_guarantorModel = new Datasource_Insurance_RentGuaranteeClaim_Guarantor();
        }

        return $this->_guarantorModel->insertGuarantors($guarantorInfo, '', $referenceNumber);
    }

	/**
     * Update guarantor info.
     *
     * @param array $guarantorInfo, int $guarantorId, int $referenceNumber
     *
     * This method provides a convenient way of updating guarantor information.
     *
     * @param Manager_Insurance_RentGuaranteeClaim_Guarantor $guarantorInfo, $guarantorId
     * An Manager_Insurance_RentGuaranteeClaim_Guarantor object containing all the
     * guarantor information.
     *
     * @return boolean
     * True if the guarantor info was successfully updated, false otherwise.
     */
    public function updateGuarantors($guarantorInfo,$guarantorId,$referenceNumber) {

        if(empty($this->_guarantorModel)) {

            $this->_guarantorModel = new Datasource_Insurance_RentGuaranteeClaim_Guarantor();
        }

        return $this->_guarantorModel->insertGuarantors($guarantorInfo,$guarantorId,$referenceNumber);
    }


	/**
	 * Remove guarantor info.
	 *
	 * This method removes all guarantor info associated with the claim
	 * passed in.
	 *
	 * @param array $guarantorInfo
	 * The guarantor id / Claim Reference number used to identify the exact guarantor information to
	 * delete
	 *
	 *
	 * @return void
	 */
	public function removeGuarantors($guarantorInfo) {

        if(empty($this->_guarantorModel)) {

            $this->_guarantorModel = new Datasource_Insurance_RentGuaranteeClaim_Guarantor();
        }

        $this->_guarantorModel->removeGuarantors($guarantorInfo);
	}

}

?>