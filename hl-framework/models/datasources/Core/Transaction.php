<?php
/**
 * Model definition for the Transaction table
 *
 * Class Datasource_Core_Transaction
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Core_Transaction extends Zend_Db_Table_Multidb
{
    /**
     * @var string table name
     */
    protected $_name = 'Transaction';

    /**
     * @var string primary id
     */
    protected $_primary = 'ID';

    /**
     * @var string dstabase
     */
    protected $_multidb = 'db_legacy_referencing';

    /**
     * Create a transaction record
     *
     * @param Model_Core_Transaction $transaction
     * @return int|null
     */
    function createTransaction(Model_Core_Transaction $transaction)
    {
        $data = array(
            'PreviousID'         =>  $transaction->getPreviousId(),
            'EnquiryID'          =>  $transaction->getEnquiryId(),
            'Amount'             =>  $transaction->getAmount(),
            'StatusID'           =>  $transaction->getStatusId(),
            'InvoiceID'          =>  $transaction->getInvoiceId(),
            'CreditNoteID'       =>  $transaction->getCreditNoteId(),
            'InsuranceNetAmount' =>  $transaction->getInsuranceNetAmount(),
            'TransactionDate'    =>  $transaction->getTransactionDate(),
            'TermID'             =>  $transaction->getTermId(),
            'MTAID'              =>  $transaction->getMtaId()
        );
        // New transaction so just insert
        $transId = $this->insert($data);
        if ( ! $transId) {
            // Failed insertion
            Application_Core_Logger::log("Can't insert transaction in table {$this->_name}", 'error');
            return null;
        }

        return $transId;
    }

    /**
     * Updates a transaction record
     *
     * @param Model_Core_Transaction $transaction
     * @return bool
     */
    public function updateTransaction(Model_Core_Transaction $transaction)
    {
        // Firstly we need to see if this already exists
        $select = $this->select();
        $select->where('ID = ?', $transaction->getId());
        $row = $this->fetchRow($select);

        // There should only be a single transaction with the given id
        if (count($row) == 1) {
            $data = array(
                'PreviousID'         => $transaction->getPreviousId(),
                'EnquiryID'          => $transaction->getEnquiryId(),
                'Amount'             => $transaction->getAmount(),
                'StatusID'           => $transaction->getStatusId(),
                'InvoiceID'          => $transaction->getInvoiceId(),
                'CreditNoteID'       => $transaction->getCreditNoteId(),
                'InsuranceNetAmount' => $transaction->getInsuranceNetAmount(),
                'TransactionDate'    => $transaction->getTransactionDate(),
            );

            $where = $this->_db->quoteInto('ID = ?', $transaction->getId());
            $this->update($data, $where);
            return true;
        }

        Application_Core_Logger::log("Can't update transaction claim in table {$this->_name}", 'error');
        return false;
    }

    /**
     * Gets a transaction record by id
     *
     * @param int $id
     * @return Model_Core_Transaction|null
     */
    public function getTransactionById($id)
    {
        $select = $this->select();
        $select->where('ID = ?', $id);
        $row = $this->fetchRow($select);

        // There should only be a single transaction with the given id
        if (count($row) == 1) {
            $transaction = new Model_Core_Transaction();
            $transaction
                ->setId($row->ID)
                ->setPreviousId($row->PreviousID)
                ->setEnquiryId($row->EnquiryID)
                ->setAmount($row->Amount)
                ->setStatusId($row->StatusID)
                ->setInvoiceId($row->InvoiceID)
                ->setCreditNoteId($row->CreditNoteID)
                ->setInsuranceNetAmount($row->InsuranceNetAmount)
                ->setTransactionDate($row->TransactionDate);
            return $transaction;
        }
        return null;
    }

    /**
     * Gets the latest transaction record for the given term Id
     *
     * @param int $termId
     * @param int|null $month
     * @param int|null $year
     * @return Model_Core_Transaction|null
     */
    public function getLatestTransactionByTermId($termId, $month=null, $year=null)
    {
        $select = $this->select();
        $select
            ->where('termId = ?', $termId)
            ->where('statusId = ?', Model_Core_Transaction::STATUS_LIVE);
        if ($month && $year) {
            $select
                ->where('YEAR(TransactionDate) = ?', $year)
                ->where('MONTH(TransactionDate) = ?', $month);
        }
        else if ($month) {
            $select->where('MONTH(TransactionDate) = ?', $month);
        }
        else {
            $select->order('ID DESC');
        }
        $select->limit(1);
        $row = $this->fetchRow($select);

        // There should only be a single transaction with the given id
        if (count($row) == 1) {
            $transaction = new Model_Core_Transaction();
            $transaction
                ->setId($row->ID)
                ->setPreviousId($row->PreviousID)
                ->setEnquiryId($row->EnquiryID)
                ->setAmount($row->Amount)
                ->setStatusId($row->StatusID)
                ->setInvoiceId($row->InvoiceID)
                ->setCreditNoteId($row->CreditNoteID)
                ->setInsuranceNetAmount($row->InsuranceNetAmount)
                ->setTransactionDate($row->TransactionDate);
            return $transaction;
        }
        return null;
    }

}
