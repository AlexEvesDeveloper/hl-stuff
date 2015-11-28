<?php
class Model_Core_Product extends Model_Abstract
{
    protected $_productId;
    protected $_productName;
    protected $_description;
    protected $_productStartDate;
    protected $_productEndDate;
    
    /**
     * Tightly binds this to the database table - it's ok though as this method's
     * use is strictly optional
     * @param array $data
     */
    public function populate($data)
    {
        $this->setId($data['productID']);
        $this->setName($data['productName']);
        $this->setDescription($data['description']);
        $this->setStartDate($data['prodStartDate']);
        $this->setEndDate($data['prodEndDate']);
    }
    
	/**
     * @return the $_productId
     */
    public function getId ()
    {
        return $this->_productId;
    }

	/**
     * @return the $_productName
     */
    public function getName ()
    {
        return $this->_productName;
    }

	/**
     * @return the $_description
     */
    public function getDescription ()
    {
        return $this->_description;
    }

	/**
     * @return the $_productStartDate
     */
    public function getStartDate ()
    {
        return $this->_productStartDate;
    }

	/**
     * @return the $_productEndDate
     */
    public function getEndDate ()
    {
        return $this->_productEndDate;
    }

	/**
     * @param $_productId the $_productId to set
     */
    public function setId ($_productId)
    {
        $this->_productId = $_productId;
    }

	/**
     * @param $_productName the $_productName to set
     */
    public function setName ($_productName)
    {
        $this->_productName = $_productName;
    }

	/**
     * @param $_description the $_description to set
     */
    public function setDescription ($_description)
    {
        $this->_description = $_description;
    }

	/**
     * @param $_productStartDate the $_productStartDate to set
     */
    public function setStartDate ($_productStartDate)
    {
        $this->_productStartDate = $_productStartDate;
    }

	/**
     * @param $_productEndDate the $_productEndDate to set
     */
    public function setEndDate ($_productEndDate)
    {
        $this->_productEndDate = $_productEndDate;
    }

    
    
}