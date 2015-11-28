<?php
class Model_Core_Discount_CsuProductOptionDiscount extends Model_Abstract
{ 
    protected $csuDiscount;
    protected $productOptionDiscount;
    protected $productOptions;
    
    /**
     * Populates the object attributes
     * 
     * @param object $csuDiscount 
     * @param object $productOptionDiscount
     * @param object $productOptions
     */
    public function __construct($csuDiscount, $productOptionDiscount, 
        $productOptions)
    {
        $this->setCsuDiscount($csuDiscount);
        $this->setProductOptionDiscount($productOptionDiscount);
        $this->setProductOptions($productOptions);
    }
    
	/**
     * @return the $csuDiscount object
     */
    public function getCsuDiscount ()
    {
        return $this->csuDiscount;
    }

	/**
     * @return the $productOptionDiscount object
     */
    public function getProductOptionDiscount ()
    {
        return $this->productOptionDiscount;
    }

	/**
     * @return the $productOptions object
     */
    public function getProductOptions ()
    {
        return $this->productOptions;
    }

	/**
     * @param $csuDiscount the $csuDiscount object to set
     */
    public function setCsuDiscount ($csuDiscount)
    {
        $this->csuDiscount = $csuDiscount;
    }

	/**
     * @param $productOptionDiscount the $productOptionDiscount object to set
     */
    public function setProductOptionDiscount (
    $productOptionDiscount)
    {
        $this->productOptionDiscount = $productOptionDiscount;
    }

	/**
     * @param $productOptions the $productOptions object to set
     */
    public function setProductOptions ($productOptions)
    {
        $this->productOptions = $productOptions;
    }

    
    
}