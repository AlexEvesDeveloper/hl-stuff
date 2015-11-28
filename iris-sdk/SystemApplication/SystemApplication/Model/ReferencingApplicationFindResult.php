<?php

namespace Barbondev\IRISSDK\SystemApplication\SystemApplication\Model;

/**
 * Class ReferencingApplicationFindResult
 *
 * @package Barbondev\IRISSDK\SystemApplication\SystemApplication\Model
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
    private $applicantMiddleName;

    /**
     * @var string
     */
    private $applicantLastName;

    /**
     * @var string
     */
    private $applicantDateOfBirth;

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
     * Get applicantDateOfBirth
     *
     * @return string
     */
    public function getApplicantDateOfBirth()
    {
        return $this->applicantDateOfBirth;
    }

    /**
     * Set applicantDateOfBirth
     *
     * @param string $applicantDateOfBirth
     * @return $this
     */
    public function setApplicantDateOfBirth($applicantDateOfBirth)
    {
        $this->applicantDateOfBirth = $applicantDateOfBirth;
        return $this;
    }

    /**
     * Get applicantMiddleName
     *
     * @return string
     */
    public function getApplicantMiddleName()
    {
        return $this->applicantMiddleName;
    }

    /**
     * Set applicantMiddleName
     *
     * @param string $applicantMiddleName
     * @return $this
     */
    public function setApplicantMiddleName($applicantMiddleName)
    {
        $this->applicantMiddleName = $applicantMiddleName;
        return $this;
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
}