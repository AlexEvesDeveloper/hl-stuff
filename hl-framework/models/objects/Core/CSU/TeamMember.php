<?php
class Model_Core_CSU_TeamMember extends Model_Abstract
{
    protected $id;
    protected $csuTeamId;
    protected $csuId;
    protected $positionTypeId;
    protected $csu;
    
    public function populate($data)
    {
        $this->setId($data['id']);
        $this->setCsuTeamId($data['csuTeamID']);
        $this->setCsuId($data['csuid']);
        $this->setPositionTypeId($data['positionTypeID']);
        $csuds = new Datasource_Core_Csu();
        $this->setCsu($csuds->getCsuByID($data['csuid']));
    }
    
	/**
     * @return the $id
     */
    public function getId ()
    {
        return $this->id;
    }

	/**
     * @return the $csuTeamId
     */
    public function getCsuTeamId ()
    {
        return $this->csuTeamId;
    }

	/**
     * @return the $csuId
     */
    public function getCsuId ()
    {
        return $this->csuId;
    }

	/**
     * @return the $positionTypeId
     */
    public function getPositionTypeId ()
    {
        return $this->positionTypeId;
    }

	/**
     * @param $id the $id to set
     */
    public function setId ($id)
    {
        $this->id = $id;
    }

	/**
     * @param $csuTeamId the $csuTeamId to set
     */
    public function setCsuTeamId ($csuTeamId)
    {
        $this->csuTeamId = $csuTeamId;
    }

	/**
     * @param $csuId the $csuId to set
     */
    public function setCsuId ($csuId)
    {
        $this->csuId = $csuId;
    }

	/**
     * @param $positionTypeId the $positionTypeId to set
     */
    public function setPositionTypeId ($positionTypeId)
    {
        $this->positionTypeId = $positionTypeId;
    }
	/**
     * @return the $csu
     */
    public function getCsu ()
    {
        return $this->csu;
    }

	/**
     * @param $csu the $csu to set
     */
    public function setCsu ($csu)
    {
        $this->csu = $csu;
    }


    
    
}