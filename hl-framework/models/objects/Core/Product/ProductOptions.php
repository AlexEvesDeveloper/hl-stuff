<?php
class Model_Core_Product_ProductOptions extends Model_Abstract
{
    protected $productOptionsId;
    protected $productId;
    protected $policyOptionId;
    protected $optionStartDate;
    protected $optionEndDate;

    /**
     * Populates the object attributes.
     * 
     * Optional use utility function which tightly couples this class to the 
     * datasource names. 
     * 
     * @param array $data
     */
    public function populate($data)
    {
        $this->setProductOptionsId($data['productOptionsID']);
        $this->setProductId($data['productID']);
        $this->setPolicyOptionId($data['policyOptionID']);
        $this->setOptionStartDate($data['optionStartDate']);
        $this->setOptionEndDate($data['optionEndDate']);
    }
    
	/**
     * @return the $productOptionsId
     */
    public function getProductOptionsId ()
    {
        return $this->productOptionsId;
    }

	/**
     * @return the $productId
     */
    public function getProductId ()
    {
        return $this->productId;
    }

	/**
     * @return the $policyOptionId
     */
    public function getPolicyOptionId ()
    {
        return $this->policyOptionId;
    }

	/**
     * @return the $optionStartDate
     */
    public function getOptionStartDate ()
    {
        return $this->optionStartDate;
    }

	/**
     * @return the $optionEndDate
     */
    public function getOptionEndDate ()
    {
        return $this->optionEndDate;
    }

	/**
     * @param $productOptionsId the $productOptionsId to set
     */
    public function setProductOptionsId ($productOptionsId)
    {
        $this->productOptionsId = $productOptionsId;
    }

	/**
     * @param $productId the $productId to set
     */
    public function setProductId ($productId)
    {
        $this->productId = $productId;
    }

	/**
     * @param $policyOptionId the $policyOptionId to set
     */
    public function setPolicyOptionId ($policyOptionId)
    {
        $this->policyOptionId = $policyOptionId;
    }

	/**
     * @param $optionStartDate the $optionStartDate to set
     */
    public function setOptionStartDate ($optionStartDate)
    {
        $this->optionStartDate = $optionStartDate;
    }

	/**
     * @param $optionEndDate the $optionEndDate to set
     */
    public function setOptionEndDate ($optionEndDate)
    {
        $this->optionEndDate = $optionEndDate;
    }

    
    
}