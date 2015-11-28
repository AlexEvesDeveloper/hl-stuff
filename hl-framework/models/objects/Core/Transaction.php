<?php

class Model_Core_Transaction
{
    /*
     * Status if the transaction is live - if it still to be invoiced the invoice id will be zero
     */
    const STATUS_LIVE = 5;

    /*
     * Status if the transaction has been cancelled and is not to be invoiced
     */
    const STATUS_CANCELLED = 6;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $previousId;

    /**
     * @var int
     */
    private $enquiryId;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var int
     */
    private $statusId;

    /**
     * @var int
     */
    private $invoiceId;

    /**
     * @var int
     */
    private $creditNoteId;

    /**
     * @var float
     */
    private $insuranceNetAmount;

    /**
     * @var string
     */
    private $transactionDate;

    /**
     * @var int
     */
    private $termId;

    /**
     * @var int
     */
    private $mtaId;

    /**
     * Constructor to initial the defaults
     */
    public function __construct()
    {
        $this->setEnquiryId(0);
    }

    /**
     * Gets the amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Sets the amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Gets the credit note Id
     *
     * @return int
     */
    public function getCreditNoteId()
    {
        return $this->creditNoteId;
    }

    /**
     * Sets the credit note Id
     *
     * @param int $creditNoteId
     * @return $this
     */
    public function setCreditNoteId($creditNoteId)
    {
        $this->creditNoteId = $creditNoteId;
        return $this;
    }

    /**
     * Gets the Enquiry Id
     *
     * @return int
     */
    public function getEnquiryId()
    {
        return $this->enquiryId;
    }

    /**
     * Sets the Enquiry Id
     *
     * @param int $enquiryId
     * @return $this
     */
    public function setEnquiryId($enquiryId)
    {
        $this->enquiryId = $enquiryId;
        return $this;
    }

    /**
     * Gets the Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the Id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Gets the Insurance net amount
     *
     * @return float
     */
    public function getInsuranceNetAmount()
    {
        return $this->insuranceNetAmount;
    }

    /**
     * Sets the Insurance net amount
     *
     * @param float $insuranceNetAmount
     * @return $this
     */
    public function setInsuranceNetAmount($insuranceNetAmount)
    {
        $this->insuranceNetAmount = $insuranceNetAmount;
        return $this;
    }

    /**
     * Gets the Invoice id
     *
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * Sets the Invoice id
     *
     * @param int $invoiceId
     * @return $this
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
        return $this;
    }

    /**
     * Gets the Mta Id
     *
     * @return int
     */
    public function getMtaId()
    {
        return $this->mtaId;
    }

    /**
     * Sets the Mta Id
     *
     * @param int $mtaId
     * @return $this
     */
    public function setMtaId($mtaId)
    {
        $this->mtaId = $mtaId;
        return @$this;
    }

    /**
     * Gets the previous id
     *
     * @return int
     */
    public function getPreviousId()
    {
        return $this->previousId;
    }

    /**
     * Sets the previous id
     *
     * @param int $previousId
     * @return $this
     */
    public function setPreviousId($previousId)
    {
        $this->previousId = $previousId;
        return $this;
    }

    /**
     * Gets the status id
     *
     * @return int
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Sets the status id
     *
     * @param int $statusId
     * @return $this
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
        return $this;
    }

    /**
     * Gets the Term Id
     *
     * @return int
     */
    public function getTermId()
    {
        return $this->termId;
    }

    /**
     * Sets the Term Id
     *
     * @param int $termId
     * @return $this
     */
    public function setTermId($termId)
    {
        $this->termId = $termId;
        return $this;
    }

    /**
     * Gets the transaction date
     *
     * @return string
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    /**
     * Sets the transaction date
     *
     * @param string $transactionDate
     * @return $this
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;
        return $this;
    }

}
