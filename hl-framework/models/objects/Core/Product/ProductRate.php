<?php
class Model_Core_Product_ProductRate extends Model_Abstract
{
    protected $rateId;
    protected $rateName;
    protected $riskarea;
    protected $productOptionsId;
    protected $rateSetId;
    protected $agentsRateId;
    protected $grossRate;
    protected $netRate;
    protected $startDate;
    protected $endDate;
    protected $renewalStartDate;
    protected $renewalEndDate;

    /**
     * Populates the object with all necessary info
     *
     * @param array $props
     * @see Datasource_Core_Product_ProductRates
     */
    public function populate($props)
    {
        $this->setRateId($props['rateID']);
        $this->setRateName($props['rateName']);
        $this->setRiskarea($props['riskarea']);
        $this->setProductOptionsId($props['productOptionsID']);
        $this->setRateSetId($props['rateSetID']);
        $this->setAgentsRateId($props['agentsRateID']);
        $this->setGrossRate($props['grossRate']);
        $this->setNetRate($props['netRate']);
        $this->setStartDate($props['startDate']);
        $this->setEndDate($props['endDate']);
        $this->setRenewalStartDate($props['renewalStartDate']);
        $this->setRenewalEndDate($props['renewalEndDate']);
    }

	/**
     * @return the $rateId
     */
    public function getRateId ()
    {
        return $this->rateId;
    }

	/**
     * @return the $rateName
     */
    public function getRateName ()
    {
        return $this->rateName;
    }

	/**
     * @return the $riskarea
     */
    public function getRiskarea ()
    {
        return $this->riskarea;
    }

	/**
     * @return the $productOptionsId
     */
    public function getProductOptionsId ()
    {
        return $this->productOptionsId;
    }

	/**
     * @return the $rateSetId
     */
    public function getRateSetId ()
    {
        return $this->rateSetId;
    }

	/**
     * @return the $agentsRateId
     */
    public function getAgentRateID ()
    {
        return $this->agentsRateId;
    }

	/**
     * @return the $grossRate
     */
    public function getGrossRate ()
    {
        return $this->grossRate;
    }

	/**
     * @return the $netRate
     */
    public function getNetRate ()
    {
        return $this->netRate;
    }

	/**
     * @return the $startDate
     */
    public function getStartDate ()
    {
        return $this->startDate;
    }

	/**
     * @return the $endDate
     */
    public function getEndDate ()
    {
        return $this->endDate;
    }

	/**
     * @return the $renewalStartDate
     */
    public function getRenewalStartDate ()
    {
        return $this->renewalStartDate;
    }

	/**
     * @return the $renewalEndDate
     */
    public function getRenewalEndDate ()
    {
        return $this->renewalEndDate;
    }

	/**
     * @param $rateId the $rateId to set
     */
    public function setRateId ($rateId)
    {
        $this->rateId = $rateId;
    }

	/**
     * @param $rateName the $rateName to set
     */
    public function setRateName ($rateName)
    {
        $this->rateName = $rateName;
    }

	/**
     * @param $riskarea the $riskarea to set
     */
    public function setRiskarea ($riskarea)
    {
        $this->riskarea = $riskarea;
    }

	/**
     * @param $productOptionsId the $productOptionsId to set
     */
    public function setProductOptionsId ($productOptionsId)
    {
        $this->productOptionsId = $productOptionsId;
    }

	/**
     * @param $rateSetId the $rateSetId to set
     */
    public function setRateSetId ($rateSetId)
    {
        $this->rateSetId = $rateSetId;
    }

	/**
     * @param $agentsRateId the $agentsRateId to set
     */
    public function setAgentsRateId ($agentsRateId)
    {
        $this->agentsRateId = $agentsRateId;
    }

	/**
     * @param $grossRate the $grossRate to set
     */
    public function setGrossRate ($grossRate)
    {
        $this->grossRate = $grossRate;
    }

	/**
     * @param $netRate the $netRate to set
     */
    public function setNetRate ($netRate)
    {
        $this->netRate = $netRate;
    }

	/**
     * @param $startDate the $startDate to set
     */
    public function setStartDate ($startDate)
    {
        $this->startDate = $startDate;
    }

	/**
     * @param $endDate the $endDate to set
     */
    public function setEndDate ($endDate)
    {
        $this->endDate = $endDate;
    }

	/**
     * @param $renewalStartDate the $renewalStartDate to set
     */
    public function setRenewalStartDate ($renewalStartDate)
    {
        $this->renewalStartDate = $renewalStartDate;
    }

	/**
     * @param $renewalEndDate the $renewalEndDate to set
     */
    public function setRenewalEndDate ($renewalEndDate)
    {
        $this->renewalEndDate = $renewalEndDate;
    }
}