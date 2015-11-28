<?php
/**
* Model definition for the rent guarantee rental payamnet table.
*/
class Datasource_Insurance_RentGuaranteeClaim_RentalPayment extends Zend_Db_Table_Multidb {

    const DATE_DUE_NOT_APPLICABLE = 'N/A';
    const DATE_PAID_NOT_APPLICABLE = 'N/A';
    protected $_name = 'rent_guarantee_claims_rent_payments';
    protected $_id = 'id';
    protected $_multidb = 'db_homelet_connect';
    protected $_csvHeaders = array('Date Due', 'Amount Due', 'Amount Paid', 'Date Paid', 'Arrears');
    protected $_csvDataMap = array('date_due', 'amount_due', 'amount_paid', 'date_paid', 'arrear_amount');

     /**
     * Load an existing rental payments for a claim from the database into the object
     * Takes date paid value of '01/01/1970' to mean no date, i.e. 'N/A'
     *
     * @return array Ready for formatting as JSON output
     */
    public function getRentalPayments($referenceNumber) {

        $select = $this->select()
                ->where("reference_number = ? ",$referenceNumber)
                ->order('id ASC');

        $rentalPayment = $this->fetchAll($select);
        if(count($rentalPayment) == 0) {
            // No warning given as this is a common/normal scenario
            $returnVal = array();
        } else {
            $rentalPaymentArray = $rentalPayment->toArray();
            $returnVal = array();
            foreach ($rentalPaymentArray as $rentPay) {
                $tmpDueDateYear = substr($rentPay['date_due'], 0, 4);
                $tmpDueDateMonth = substr($rentPay['date_due'], 5, 2);
                $tmpDueDateDay = substr($rentPay['date_due'], 8, 2);
                $tmpDueDate = "{$tmpDueDateDay}/{$tmpDueDateMonth}/{$tmpDueDateYear}";

                $tmpPaidDateYear = substr($rentPay['date_paid'], 0, 4);
                $tmpPaidDateMonth = substr($rentPay['date_paid'], 5, 2);
                $tmpPaidDateDay = substr($rentPay['date_paid'], 8, 2);
                $tmpPaidDate = "{$tmpPaidDateDay}/{$tmpPaidDateMonth}/{$tmpPaidDateYear}";

                array_push($returnVal, array(
                    'id' =>  $rentPay['id'],
                    'reference_number' =>  $referenceNumber,
                    'date_due' => $tmpDueDate !== '00/00/0000' ? $tmpDueDate : self::DATE_DUE_NOT_APPLICABLE,
                    'amount_paid' =>  $rentPay['amount_paid'],
                    'amount_due' =>  $rentPay['amount_due'],
                    'date_paid' => $tmpPaidDate !== '00/00/0000' ? $tmpPaidDate : self::DATE_PAID_NOT_APPLICABLE,
                ));
            }
        }

        $returnVal = $this->sortRentalPayment($returnVal);

        return array('data' => $returnVal, 'recordType' => 'object');
    }

    public function sortRentalPayment($data) {

        // Put into new array and order data by date, type
        $paymentData = array();
        foreach ($data as $key => $val) {

            // Add data to new array
            $paymentData["id_{$key}"] = $val;

            // Generate a sortable date field
            if ($val['date_due'] != 'N/A') {
                // Sort by date due
                $sortDate = $val['date_due'];
            } else {
                // Sort by date received
                $sortDate = $val['date_paid'];
            }
            $day = substr($sortDate, 0, 2);
            $month = substr($sortDate, 3, 2);
            $year = substr($sortDate, 6, 4);
            $paymentData["id_{$key}"]['sortableDate'] = mktime(0, 0, 0, $month, $day, $year);
        }

        uasort($paymentData, array('Datasource_Insurance_RentGuaranteeClaim_RentalPayment', '_actualSortRentalPayment'));

        // Add in arrears values to results array - now uses BC Math to prevent
        //   stuffs like '-0.00' from appearing.  True story.
        $accumulator = '0.00';
        bcscale(2);
        foreach($paymentData as $key => $data) {
            $accumulator = bcadd(
                $accumulator,
                bcsub(
                    $data['amount_due'],
                    $data['amount_paid']
                )
            );
            $paymentData[$key]['arrear_amount'] = $accumulator;
        }

        return $paymentData;
    }

    /**
     * Private sort function used by sortRentalPayment()'s call to uasort().
     *
     * @param array $a Array to compare.
     * @param array $b Array to compare.
     *
     * @return int Comparison result.
     */
    private function _actualSortRentalPayment($a, $b) {

        // Ascending sort by date
        if ($a['sortableDate'] < $b['sortableDate']) {
            return -1;
        } elseif ($a['sortableDate'] > $b['sortableDate']) {
            return 1;
        // Ascending sort by type
        } elseif ($a['date_due'] != 'N/A') {
            return -1;
        } elseif ($a['date_due'] == 'N/A') {
            return 1;
        // Equivalence
        } else {
            return 0;
        }
    }

    /**
     * Set options
     *
     * Date paid is optional
     *
     * @param array data An array of rent payment id, reference number, date due, amount due
     * amount paid, date paid & arrear amount
     * @return void
     */
    public function insertRentalPayments($data, $referenceNumber) {
        $dataArray = array();
        $insertIds = array();
        foreach($data->insertRecords as $insertPayment) {
            if (strtoupper($insertPayment->date_due) === self::DATE_DUE_NOT_APPLICABLE) {
                $dateDue = '';
            } else {
                $dateDue = Application_Core_Utilities::ukDateToMysql(
                    $insertPayment->date_due
                );
            }
            if (strtoupper($insertPayment->date_paid) === self::DATE_PAID_NOT_APPLICABLE) {
                $datePaid = '';
            } else {
                $datePaid = Application_Core_Utilities::ukDateToMysql(
                    $insertPayment->date_paid
                );
            }
            $dataArray['reference_number'] = $referenceNumber;
            $dataArray['date_due'] = $dateDue;
            $dataArray['amount_due'] = $insertPayment->amount_due;
            $dataArray['amount_paid'] = $insertPayment->amount_paid;
            $dataArray['date_paid'] = $datePaid;
            $insertIds[] = $this->insert($dataArray);
        }

        return $insertIds;
    }

    /**
     * Set options
     *
     * @param array data An array of rent payment id, reference number, date due, amount due
     * amount paid, date paid & arrear amount
     * @return void
     */
    public function updateRentalPayments($data) {

        $dataArray = array();
        foreach($data->updateRecords as $updatePayment) {
                $dateDue = Application_Core_Utilities::ukDateToMysql(
                    $updatePayment->date_due
                );
            if (strtoupper($updatePayment->date_paid) === self::DATE_PAID_NOT_APPLICABLE) {
                $datePaid = '';
            } else {
                $datePaid = Application_Core_Utilities::ukDateToMysql(
                    $updatePayment->date_paid
                );
            }
            $dataArray['id'] = $updatePayment->id;
            $dataArray['reference_number'] = $updatePayment->reference_number;
            $dataArray['date_due'] = $dateDue;
            $dataArray['amount_due'] = $updatePayment->amount_due;
            $dataArray['amount_paid'] = $updatePayment->amount_paid;
            $dataArray['date_paid'] = $datePaid;
            $where = $this->getAdapter()->quoteInto('id = ?', $dataArray['id'] .
                'AND'.'reference_number = ?', $dataArray['reference_number']);
            $this->update($dataArray,$where);
        }
        return true;
    }

    /**
     * Set options
     *
     * @param array data An array of rent payment id
     * @return void
     */
    public function deleteRentalPayments($data, $referenceNumber) {
        $dataArray = array();
        foreach($data->deleteRecords as $deletePayment) {
            $where = $this->getAdapter()->quoteInto("`id` IN ({$deletePayment->id}) AND reference_number = ?", $referenceNumber);
            $this->delete($where);
        }
        return true;
    }

    /**
     * Gets rental payment by reference number.
     *
     * @param int $referenceNumber
     */
    public function getRentPaymentsByReferenceNumber($referenceNumber) {
        $select = $this->select();
        $select->where("reference_number = ? ",$referenceNumber);
        $data = $this->fetchAll($select);
        return $data->toArray();
    }

    /**
     * Processes a set of rental payment data into a CSV string complete with
     * column headers
     *
     * @param string $referenceNumber
     * @return $csv_out string CSV formatted data
     */
    public function processToCsv($referenceNumber) {

        $lines = $this->getRentalPayments($referenceNumber);
        $lines = $lines['data'];
        $csv_out = '';

        // Create header
        $header = implode(',', $this->_csvHeaders) . "\n";
        $csv_out .= $header;

        // Process lines of rental payment data
        foreach ($lines as $line) {
            $csv_lineData = array();
            foreach($this->_csvDataMap as $csvDataMap) {
                $csv_lineData[] = $line[$csvDataMap];
            }
            // Formulate line
            $csv_out .= implode(',', $csv_lineData) . "\n";
        }

        return $csv_out;
    }

    /**
     *
     * Delete all the Rent Payments for the given Reference number
     * @param int $referenceNum
     * @return void;
     */
    public function deleteByReferenceNumber($referenceNumber) {
        $where = $this->getAdapter()->quoteInto('reference_number = ?', $referenceNumber);
        $this->delete($where);
    }

     /**
     * Load an existing rental payments count for a claim from the database into the object
     *
     * @return Model_Insurance_OnlineClaims_RentalPayment
     */
     public function getRentalPaymentsError($referenceNumber) {

        $select = $this->select()
                ->where("reference_number = ? ",$referenceNumber)
                ->order('id ASC');

        $rentalPayment = $this->fetchAll($select);

        if (count($rentalPayment) == 0) {
            // No warning given as this is a common/normal scenario
            $returnVal = array();
        } else {
            $rentalPaymentArray = $rentalPayment->toArray();
                $returnVal = array();
                foreach ($rentalPaymentArray as $rentPay) {
                    array_push($returnVal, array(
                        'id'                =>  $rentPay['id'],
                        'reference_number'  =>  $referenceNumber,
                        'date_due'          =>  date('d/m/Y',strtotime($rentPay['date_due'])),
                        'amount_paid'       =>  $rentPay['amount_paid'],
                        'amount_due'        =>  $rentPay['amount_due'],
                        'date_paid'         =>  date('d/m/Y',strtotime($rentPay['date_paid'])),
                        'arrear_amount'     =>  $rentPay['arrear_amount']
                    ));
                }
        }
        return $returnVal;
    }
}
?>