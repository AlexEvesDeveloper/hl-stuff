<?php

class Model_Core_TransactionSupport
{
    /**
     * @var int
     */
    private $transId;

    /**
     * @var int
     */
    private $enquiryId;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $agentTypeId;

    /**
     * @var int
     */
    private $dealAgentTypeId;

    /**
     * @var int
     */
    private $guarantor;

    /**
     * @var string
     */
    private $band;

    /**
     * @var int
     */
    private $duration;

    /**
     * @var int
     */
    private $renewal;

    /**
     * @var float
     */
    private $runningAmount;

    /**
     * @var float
     */
    private $insurance;

    /**
     * @var float
     */
    private $ipt;

    /**
     * @var float
     */
    private $income;

    /**
     * @var int
     */
    private $invoiced;

    /**
     * @var string
     */
    private $transDate;

    /**
     * @var string
     */
    private $statusChangeDate;

    /**
     * Constructor to initialise the defaults
     */
    public function __construct()
    {
        $this
            ->setEnquiryId(0)
            ->setAgentTypeId(0)
            ->setDealAgentTypeId(0)
            ->setGuarantor(0)
            ->setRenewal(0)
            ->setInvoiced(0)
            ->setStatusChangeDate('0000-00-00');
    }

    /**
     * @return int
     */
    public function getAgentTypeId()
    {
        return $this->agentTypeId;
    }

    /**
     * Sets the Agent Type Id
     *
     * @param int $agentTypeId
     * @return $this
     */
    public function setAgentTypeId($agentTypeId)
    {
        $this->agentTypeId = $agentTypeId;
        return $this;
    }

    /**
     * Gets the Band
     *
     * @return string
     */
    public function getBand()
    {
        return $this->band;
    }

    /**
     * Sets the Band
     *
     * @param string $band
     * @return $this
     */
    public function setBand($band)
    {
        $this->band = $band;
        return $this;
    }

    /**
     * Gets the Deal Agent Type Id
     *
     * @return int
     */
    public function getDealAgentTypeId()
    {
        return $this->dealAgentTypeId;
    }

    /**
     * Sets the Deal Agent Type Id
     *
     * @param int $dealAgentTypeId
     * @return $this
     */
    public function setDealAgentTypeId($dealAgentTypeId)
    {
        $this->dealAgentTypeId = $dealAgentTypeId;
        return $this;
    }

    /**
     * Gets the Duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Sets the Duration
     *
     * @param int $duration
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
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
     * Gets the Guarantor
     *
     * @return int
     */
    public function getGuarantor()
    {
        return $this->guarantor;
    }

    /**
     * Sets the Guarantor
     *
     * @param int $guarantor
     * @return $this
     */
    public function setGuarantor($guarantor)
    {
        $this->guarantor = $guarantor;
        return $this;
    }

    /**
     * Gets the Income
     *
     * @return float
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Sets the Income
     *
     * @param float $income
     * @return $this
     */
    public function setIncome($income)
    {
        $this->income = $income;
        return $this;
    }

    /**
     * Gets the Insurance
     *
     * @return float
     */
    public function getInsurance()
    {
        return $this->insurance;
    }

    /**
     * Sets the Insurance
     *
     * @param float $insurance
     * @return $this
     */
    public function setInsurance($insurance)
    {
        $this->insurance = $insurance;
        return $this;
    }

    /**
     * Gets the Invoiced
     *
     * @return int
     */
    public function getInvoiced()
    {
        return $this->invoiced;
    }

    /**
     * Sets the Invoiced
     *
     * @param int $invoiced
     * @return $this
     */
    public function setInvoiced($invoiced)
    {
        $this->invoiced = $invoiced;
        return $this;
    }

    /**
     * Gets the Ipt
     *
     * @return float
     */
    public function getIpt()
    {
        return $this->ipt;
    }

    /**
     * Sets the Ipt
     *
     * @param float $ipt
     * @return $this
     */
    public function setIpt($ipt)
    {
        $this->ipt = $ipt;
        return $this;
    }

    /**
     * Gets the Product Id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Sets the Product Id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * Gets the Renewal
     *
     * @return int
     */
    public function getRenewal()
    {
        return $this->renewal;
    }

    /**
     * Sets the Renewal
     *
     * @param int $renewal
     * @return $this
     */
    public function setRenewal($renewal)
    {
        $this->renewal = $renewal;
        return $this;
    }

    /**
     * Gets the Running Amount
     *
     * @return float
     */
    public function getRunningAmount()
    {
        return $this->runningAmount;
    }

    /**
     * Sets the Running Amount
     *
     * @param float $runningAmount
     * @return $this
     */
    public function setRunningAmount($runningAmount)
    {
        $this->runningAmount = $runningAmount;
        return $this;
    }

    /**
     * Gets the Status Change Date
     *
     * @return string
     */
    public function getStatusChangeDate()
    {
        return $this->statusChangeDate;
    }

    /**
     * Sets the Status Change Date
     *
     * @param string $statusChangeDate
     * @return $this
     */
    public function setStatusChangeDate($statusChangeDate)
    {
        $this->statusChangeDate = $statusChangeDate;
        return $this;
    }

    /**
     * Gets the Trans Date
     *
     * @return string
     */
    public function getTransDate()
    {
        return $this->transDate;
    }

    /**
     * Sets the Trans Date
     *
     * @param string $transDate
     * @return $this
     */
    public function setTransDate($transDate)
    {
        $this->transDate = $transDate;
        return $this;
    }

    /**
     * Gets the Trans Id
     *
     * @return int
     */
    public function getTransId()
    {
        return $this->transId;
    }

    /**
     * Sets the Trans Id
     *
     * @param int $transId
     * @return $this
     */
    public function setTransId($transId)
    {
        $this->transId = $transId;
        return $this;
    }

}
