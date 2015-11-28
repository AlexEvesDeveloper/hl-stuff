<?php

/**
 * Business rules class which provides rent guarantee claims rental payemnt services.
 */
class Manager_Insurance_RentGuaranteeClaim_RentalPayment {

    protected $_rentPaymentModel;

    /**
     * Returns saved rent payment for a claim.
     * @param int $referenceNumber
     *
     * This method will retrieve rent payment for a claim information stored in the database
     *
     * @return Manager_Insurance_OnlineClaim_RentPayment
     * Returns this object populated with relevant information, or null if no
     * relevant information has been stored.
     */
    public function getRentalPayments($referenceNumber) {
        if (empty($this->_rentPaymentModel)) {
            $this->_rentPaymentModel = new Datasource_Insurance_RentGuaranteeClaim_RentalPayment();
        }
        return $this->_rentPaymentModel->getRentalPayments(
            $referenceNumber
        );
    }

    /**
     * create new rental payment .
     *
     * This method provides a convenient way of inserting a rental payment.
     *
     * @param array $rentalPayment
     * An Model_Insurance_OnlineClaim_RentPayment object containing all the
     * rentla payment information.
     *
     * @return boolean
     * True if the rental payment was successfully inserted, false otherwise.
     */
    public function createRentalPayments($rentalPayment, $referenceNumber) {
        if (empty($this->_rentPaymentModel)) {
            $this->_rentPaymentModel = new Datasource_Insurance_RentGuaranteeClaim_RentalPayment();
        }
        return $this->_rentPaymentModel->insertRentalPayments(
            $rentalPayment, $referenceNumber
        );
    }

    /**
     * Update rental payment .
     *
     * This method provides a convenient way of updating a rental payment.
     *
     * @param array $rentalPayment
     * An Model_Insurance_OnlineClaim_RentPayment object containing all the
     * rentla payment information.
     *
     * @return boolean
     * True if the rental payment was successfully updated, false otherwise.
     */
    public function updateRentalPayments($rentalPayment) {
        if (empty($this->_rentPaymentModel)) {
            $this->_rentPaymentModel = new Datasource_Insurance_RentGuaranteeClaim_RentalPayment();
        }
        return $this->_rentPaymentModel->updateRentalPayments($rentalPayment);
    }

    /**
     * Removes rental payments.
     *
     * This method removes all rental payments associated with the claim
     * passed in.
     *
     * @param array $rentalPayment
     * The rental payment id / Claim Reference number used to identify the exact rental payments to delete,
     *
     *
     * @return void
     */
    public function removeRentalPayments($rentalPayment, $referenceNumber) {
        if (empty($this->_rentPaymentModel)) {
            $this->_rentPaymentModel = new Datasource_Insurance_RentGuaranteeClaim_RentalPayment();
        }
        $this->_rentPaymentModel->deleteRentalPayments($rentalPayment, $referenceNumber);
    }

    /**
     * Returns count of saved rent payment for a claim.
     *
     * @param int $referenceNumber
     * @return Manager_Insurance_OnlineClaim_RentPayment
     * Returns the count of rental payment for a specified claim
     */
    public function getRentalPaymentsError($referenceNumber) {
        if(empty($this->_rentPaymentModel)) {
            $this->_onlineclaimModel = new Datasource_Insurance_RentGuaranteeClaim_RentalPayment();
        }
        return $this->_onlineclaimModel->getRentalPaymentsError($referenceNumber);
    }

}

?>