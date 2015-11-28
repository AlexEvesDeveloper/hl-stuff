<?php
class Model_Core_CSU_Team extends Model_Abstract
{
    protected $id;
    protected $managerCsuId;
    protected $name;
    
    /**
     * 
     * @param array $data
     */
    public function populate($data)
    {
        $this->setId($data['id']);
        $this->setManagerCsuId($data['managerCsuId']);
        $this->setName($data['name']);
    }
    
	/**
     * @return the $id
     */
    public function getId ()
    {
        return $this->id;
    }

	/**
     * @return the $managerCsuId
     */
    public function getManagerCsuId ()
    {
        return $this->managerCsuId;
    }

	/**
     * @return the $name
     */
    public function getName ()
    {
        return $this->name;
    }

	/**
     * @param $id the $id to set
     */
    public function setId ($id)
    {
        $this->id = $id;
    }

	/**
     * @param $managerCsuId the $managerCsuId to set
     */
    public function setManagerCsuId ($managerCsuId)
    {
        $this->managerCsuId = $managerCsuId;
    }

	/**
     * @param $name the $name to set
     */
    public function setName ($name)
    {
        $this->name = $name;
    }

    
    
}