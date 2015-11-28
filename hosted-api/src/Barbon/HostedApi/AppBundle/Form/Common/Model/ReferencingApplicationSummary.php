<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\ReferencingApplicationSummary
 */
class ReferencingApplicationSummary
{
    /**
     * @Iris\Field
     * @var string
     */
    private $applicationReference;

    /**
     * @Iris\Field
     * @var string
     */
    private $firstName;

    /**
     * @Iris\Field
     * @var string
     */
    private $lastName;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $flat;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $houseName;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $houseNumber;

    /**
     * @Iris\Field
     * @var string
     */
    private $street;

    /**
     * @Iris\Field
     * @var string
     */
    private $createdAt;

    /**
     * @Iris\Field
     * @var string
     */
    private $applicationUuid;

    /**
     * @Iris\Field(optional = true)
     * @var int
     */
    private $statusId;

    /**
     * Get application reference
     *
     * @return string
     */
    public function getApplicationReference()
    {
        return $this->applicationReference;
    }

    /**
     * Set application reference
     *
     * @param string $applicationReference
     * @return $this
     */
    public function setApplicationReference($applicationReference)
    {
        $this->applicationReference = $applicationReference;
        return $this;
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set first name
     *
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set last name
     *
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
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
     * Get house name
     *
     * @return string
     */
    public function getHouseName()
    {
        return $this->houseName;
    }

    /**
     * Set house name
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
     * Get house number
     *
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * Set house number
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
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
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
     * Get created at date
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set created at
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
     * Get application uuid
     *
     * @return string
     */
    public function getApplicationUuid()
    {
        return $this->applicationUuid;
    }

    /**
     * Set application uuid
     *
     * @param string $applicationUuid
     * @return $this
     */
    public function setApplicationUuid($applicationUuid)
    {
        $this->applicationUuid = $applicationUuid;
        return $this;
    }

    /**
     * Get status id
     *
     * @return int
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Set status id
     *
     * @param int $statusId
     * @return $this
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
        return $this;
    }
}
