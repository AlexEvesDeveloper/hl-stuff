<?php

namespace Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model;

/**
 * Class ReferencingApplicationFindResult
 *
 * @package Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ReferencingApplicationFindResult
{
    /**
     * @var string
     */
    private $referencingApplicationUuId;

    /**
     * @var string
     */
    private $referenceNumber;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $applicantFirstName;

    /**
     * @var string
     */
    private $applicantLastName;

    /**
     * @var string
     */
    private $flat;

    /**
     * @var string
     */
    private $houseName;

    /**
     * @var string
     */
    private $houseNumber;

    /**
     * @var string
     */
    private $street;

    /**
     * @var int
     */
    private $statusId;

    /**
     * @var string Used application-side to tag where a result record came from
     */
    private $dataSource;

    /**
     * Set applicantFirstName
     *
     * @param string $applicantFirstName
     * @return $this
     */
    public function setApplicantFirstName($applicantFirstName)
    {
        $this->applicantFirstName = $applicantFirstName;
        return $this;
    }

    /**
     * Get applicantFirstName
     *
     * @return string
     */
    public function getApplicantFirstName()
    {
        return $this->applicantFirstName;
    }

    /**
     * Set applicantLastName
     *
     * @param string $applicantLastName
     * @return $this
     */
    public function setApplicantLastName($applicantLastName)
    {
        $this->applicantLastName = $applicantLastName;
        return $this;
    }

    /**
     * Get applicantLastName
     *
     * @return string
     */
    public function getApplicantLastName()
    {
        return $this->applicantLastName;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set flat
     *
     * @param string $flat
     * @return $this
     */
    public function setFlat($flat)
    {
        $this->flat = $flat;
        return $this;
    }

    /**
     * Get flat
     *
     * @return string
     */
    public function getFlat()
    {
        return $this->flat;
    }

    /**
     * Set houseName
     *
     * @param string $houseName
     * @return $this
     */
    public function setHouseName($houseName)
    {
        $this->houseName = $houseName;
        return $this;
    }

    /**
     * Get houseName
     *
     * @return string
     */
    public function getHouseName()
    {
        return $this->houseName;
    }

    /**
     * Set houseNumber
     *
     * @param string $houseNumber
     * @return $this
     */
    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber = $houseNumber;
        return $this;
    }

    /**
     * Get houseNumber
     *
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * Set referenceNumber
     *
     * @param string $referenceNumber
     * @return $this
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
        return $this;
    }

    /**
     * Get referenceNumber
     *
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * Set referencingApplicationUuId
     *
     * @param string $referencingApplicationUuId
     * @return $this
     */
    public function setReferencingApplicationUuId($referencingApplicationUuId)
    {
        $this->referencingApplicationUuId = $referencingApplicationUuId;
        return $this;
    }

    /**
     * Get referencingApplicationUuId
     *
     * @return string
     */
    public function getReferencingApplicationUuId()
    {
        return $this->referencingApplicationUuId;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return $this
     */
    public function setStreet($street)
    {
        $this->street = $street;
        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set status ID
     *
     * @param int $statusId
     * @return $this
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
        return $this;
    }

    /**
     * Get status ID
     *
     * @return int
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Set data source
     *
     * @param string $dataSource
     * @return $this
     */
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * Get data source
     *
     * @return int
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }
}