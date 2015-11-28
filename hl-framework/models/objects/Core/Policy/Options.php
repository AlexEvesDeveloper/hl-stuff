<?php
class Model_Core_Policy_Options extends Model_Abstract
{
    protected $policyOptionID;   
    protected $policyOption; 
    protected $printableName;
    protected $minimumSumInsured;
    protected $maximumSumInsured;
    protected $optionType;
    
    public function populate($data)
    {
        $this->setPolicyOptionID($data['policyOptionID']);
        $this->setPolicyOption($data['policyOption']);
        $this->setPrintableName($data['printableName']);
        $this->setMinimumSumInsured($data['minimumSumInsured']);
        $this->setMaximumSumInsured($data['maximumSumInsured']);
        $this->setOptionType($data['optionType']);
    }
    
	/**
     * @return the $policyOptionID
     */
    public function getPolicyOptionId ()
    {
        return $this->policyOptionID;
    }

	/**
     * @return the $policyOption
     */
    public function getPolicyOption ()
    {
        return $this->policyOption;
    }

	/**
     * @return the $printableName
     */
    public function getPrintableName ()
    {
        return $this->printableName;
    }

	/**
     * @return the $minimumSumInsured
     */
    public function getMinimumSumInsured ()
    {
        return $this->minimumSumInsured;
    }

	/**
     * @return the $maximumSumInsured
     */
    public function getMaximumSumInsured ()
    {
        return $this->maximumSumInsured;
    }

	/**
     * @return the $optionType
     */
    public function getOptionType ()
    {
        return $this->optionType;
    }

	/**
     * @param $policyOptionID the $policyOptionID to set
     */
    public function setPolicyOptionID ($policyOptionID)
    {
        $this->policyOptionID = $policyOptionID;
    }

	/**
     * @param $policyOption the $policyOption to set
     */
    public function setPolicyOption ($policyOption)
    {
        $this->policyOption = $policyOption;
    }

	/**
     * @param $printableName the $printableName to set
     */
    public function setPrintableName ($printableName)
    {
        $this->printableName = $printableName;
    }

	/**
     * @param $minimumSumInsured the $minimumSumInsured to set
     */
    public function setMinimumSumInsured ($minimumSumInsured)
    {
        $this->minimumSumInsured = $minimumSumInsured;
    }

	/**
     * @param $maximumSumInsured the $maximumSumInsured to set
     */
    public function setMaximumSumInsured ($maximumSumInsured)
    {
        $this->maximumSumInsured = $maximumSumInsured;
    }

	/**
     * @param $optionType the $optionType to set
     */
    public function setOptionType ($optionType)
    {
        $this->optionType = $optionType;
    }

    
    
}