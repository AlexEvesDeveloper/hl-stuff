<?php
class Model_Core_Product_ProductOptionDiscount extends Model_Abstract
{
    protected $id;
    protected $productID;
    protected $productOptionID;
    protected $typeID;
    protected $value;
    protected $valid = 1;
    protected $isDefault;
    
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
        $this->setId($data['id']);
        $this->setProductID($data['productID']);
        $this->setProductOptionID($data['productOptionID']);
        $this->setTypeID($data['typeID']);
        $this->setValue($data['value']);
        $this->setValid($data['valid']);
        $this->setIsDefault($data['isDefault']);
    }
    
	/**
     * @return the $id
     */
    public function getId ()
    {
        return $this->id;
    }
    
    /**
     * @return the $valid
     */
    public function getValid()
    {
        return $this->valid;
    }

	/**
     * @return the $productID
     */
    public function getProductID ()
    {
        return $this->productID;
    }

	/**
     * @return the $productOptionID
     */
    public function getProductOptionID ()
    {
        return $this->productOptionID;
    }

	/**
     * @return the $typeID
     */
    public function getTypeID ()
    {
        return $this->typeID;
    }

	/**
     * @return the $value
     */
    public function getValue ()
    {
        return $this->value;
    }

    /**
     * @return the $isDefault
     */
    public function getIsDefault ()
    {
        return $this->isDefault;
    }
    
	/**
     * @param $id the $id to set
     */
    public function setId ($id)
    {
        $this->id = $id;
    }
    
    /**
     * @param $valid the $valid to set
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
    }

	/**
     * @param $productID the $productID to set
     */
    public function setProductID ($productID)
    {
        $this->productID = $productID;
    }

	/**
     * @param $productOptionID the $productOptionID to set
     */
    public function setProductOptionID ($productOptionID)
    {
        $this->productOptionID = $productOptionID;
    }

	/**
     * @param $typeID the $typeID to set
     */
    public function setTypeID ($typeID)
    {
        $this->typeID = $typeID;
    }

	/**
     * @param $value the $value to set
     */
    public function setValue ($value)
    {
        $this->value = $value;
    }

	/**
     * @param $isDefault the $isDefault to set
     */
    public function setIsDefault ($isDefault)
    {
        $this->isDefault = $isDefault;
    }


    
    
}