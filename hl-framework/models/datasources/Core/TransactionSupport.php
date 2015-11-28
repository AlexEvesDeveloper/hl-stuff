<?php
/**
 * Model definition for the TransactionSupport table
 *
 * Class Datasource_Core_TransactionSupport
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Core_TransactionSupport extends Zend_Db_Table_Multidb
{
    /**
     * @var string table name
     */
    protected $_name = 'TransactionSupport';

    /**
     * @var string primary id
     */
    protected $_primary = 'TransID';

    /**
     * @var string dstabase
     */
    protected $_multidb = 'db_legacy_referencing';

    /**
     * Create a Transaction Support record
     *
     * @param Model_Core_TransactionSupport $transactionSupport
     * @return bool
     */
    function createTransactionSupport(Model_Core_TransactionSupport $transactionSupport)
    {
        $data = array(
            'TransID'          => $transactionSupport->getTransId(),
            'EnquiryID'        => $transactionSupport->getEnquiryId(),
            'ProductID'        => $transactionSupport->getProductId(),
            'AgentTypeID'      => $transactionSupport->getAgentTypeId(),
            'DealAgentTypeID'  => $transactionSupport->getDealAgentTypeId(),
            'Guarantor'        => $transactionSupport->getGuarantor(),
            'Band'             => $transactionSupport->getBand(),
            'Duration'         => $transactionSupport->getDuration(),
            'Renewal'          => $transactionSupport->getRenewal(),
            'RunningAmount'    => $transactionSupport->getRunningAmount(),
            'Insurance'        => $transactionSupport->getInsurance(),
            'IPT'              => $transactionSupport->getIpt(),
            'Income'           => $transactionSupport->getIncome(),
            'Invoiced'         => $transactionSupport->getInvoiced(),
            'Transdate'        => $transactionSupport->getTransdate(),
            'StatusChangeDate' => $transactionSupport->getStatusChangeDate()
        );
        // New transaction support so just insert
        if (!$this->insert($data)) {
            // Failed insertion
            Application_Core_Logger::log("Can't insert transaction support in table {$this->_name}", 'error');
            return false;
        }

        return true;
    }

    /**
     * Updates a Transaction Support record
     *
     * @param Model_Core_TransactionSupport $transactionSupport
     * @return bool
     */
    public function updateTransactionSupport(Model_Core_TransactionSupport $transactionSupport)
    {
        // Firstly we need to see if this already exists
        $select = $this->select();
        $select->where('TransID = ?', $transactionSupport->getTransId());
        $row = $this->fetchRow($select);

        // There should only be a single transaction support with the given id
        if (count($row) == 1) {
            $data = array(
                'TransID'          => $transactionSupport->getTransId(),
                'EnquiryID'        => $transactionSupport->getEnquiryId(),
                'ProductID'        => $transactionSupport->getProductId(),
                'AgentTypeID'      => $transactionSupport->getAgentTypeId(),
                'DealAgentTypeID'  => $transactionSupport->getDealAgentTypeId(),
                'Guarantor'        => $transactionSupport->getGuarantor(),
                'Band'             => $transactionSupport->getBand(),
                'Duration'         => $transactionSupport->getDuration(),
                'Renewal'          => $transactionSupport->getRenewal(),
                'RunningAmount'    => $transactionSupport->getRunningAmount(),
                'Insurance'        => $transactionSupport->getInsurance(),
                'IPT'              => $transactionSupport->getIpt(),
                'Income'           => $transactionSupport->getIncome(),
                'Invoiced'         => $transactionSupport->getInvoiced(),
                'Transdate'        => $transactionSupport->getTransdate(),
                'StatusChangeDate' => $transactionSupport->getStatusChangeDate(),
            );

            $where = $this->_db->quoteInto('TransID = ?', $transactionSupport->getTransId());
            $this->update($data, $where);
            return true;
        }

        Application_Core_Logger::log("Can't update transaction support claim in table {$this->_name}", 'error');
        return false;
    }

    /**
     * Gets a Transaction Support record
     *
     * @param int $id
     * @return Model_Core_TransactionSupport|null
     */
    public function getTransactionSupport($id)
    {
        $select = $this->select();
        $select->where('TransID = ?', $id);
        $row = $this->fetchRow($select);

        // There should only be a single transactionSupport with the given id
        if (count($row) == 1) {
            $transactionSupport = new Model_Core_TransactionSupport();
            $transactionSupport
                ->setTransId($row->TransID)
                ->setEnquiryId($row->EnquiryID)
                ->setProductId($row->ProductID)
                ->setAgentTypeId($row->AgentTypeID)
                ->setDealAgentTypeId($row->DealAgentTypeID)
                ->setGuarantor($row->Guarantor)
                ->setBand($row->Band)
                ->setDuration($row->Duration)
                ->setRenewal($row->Renewal)
                ->setRunningAmount($row->RunningAmount)
                ->setInsurance($row->Insurance)
                ->setIpt($row->IPT)
                ->setIncome($row->Income)
                ->setInvoiced($row->Invoiced)
                ->setTransdate($row->Transdate)
                ->setStatusChangeDate($row->StatusChangeDate);
            return $transactionSupport;
        }
        return null;
    }

}
