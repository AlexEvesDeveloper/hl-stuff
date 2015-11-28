<?php
class Model_Core_Discount_CsuDiscount extends Model_Abstract
{
    protected $id;
    protected $csuId;
    protected $productOptionDiscountId;
    protected $supervisorId;
    
    public function populate($data)
    {
        $this->setId($data['id']);
        $this->setCsuId($data['csuid']);
        $this->setProductOptionDiscountId($data['productOptDiscID']);
        $this->setSupervisorId($data['supervisorID']);
        return $this;
    }
    
	/**
     * @return the $id
     */
    public function getId ()
    {
        return $this->id;
    }

	/**
     * @return the $csuId
     */
    public function getCsuId ()
    {
        return $this->csuId;
    }

	/**
     * @return the $productOptionDiscountId
     */
    public function getProductOptionDiscountId ()
    {
        return $this->productOptionDiscountId;
    }

	/**
     * @return the $supervisorId
     */
    public function getSupervisorId ()
    {
        return $this->supervisorId;
    }

	/**
     * @param $id the $id to set
     */
    public function setId ($id)
    {
        $this->id = $id;
    }

	/**
     * @param $csuId the $csuId to set
     */
    public function setCsuId ($csuId)
    {
        $this->csuId = $csuId;
    }

	/**
     * @param $productOptionDiscountId the $productOptionDiscountId to set
     */
    public function setProductOptionDiscountId (
    $productOptionDiscountId)
    {
        $this->productOptionDiscountId = $productOptionDiscountId;
    }

	/**
     * @param $supervisorId the $supervisorId to set
     */
    public function setSupervisorId ($supervisorId)
    {
        $this->supervisorId = $supervisorId;
    }

    
    
}