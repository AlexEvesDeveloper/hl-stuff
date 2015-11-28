<?php

namespace Iris\IndividualApplication\Model;

/**
 * Class SearchIndividualApplicationsCriteria
 *
 * @package Iris\IndividualApplication\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class SearchIndividualApplicationsCriteria
{
    /**
     * @var string
     */
    private $referenceNumber;

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
    private $propertyAddress;

    /**
     * @var string
     */
    private $propertyTown;

    /**
     * @var string
     */
    private $propertyPostcode;

    /**
     * @var string
     */
    private $applicationStatus;

    /**
     * @var string
     */
    private $productType;

    /**
     * @var int
     */
    private $resultsPerPage;

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
     * Set applicationStatus
     *
     * @param string $applicationStatus
     * @return $this
     */
    public function setApplicationStatus($applicationStatus)
    {
        $this->applicationStatus = $applicationStatus;
        return $this;
    }

    /**
     * Get applicationStatus
     *
     * @return string
     */
    public function getApplicationStatus()
    {
        return $this->applicationStatus;
    }

    /**
     * Set productType
     *
     * @param string $productType
     * @return $this
     */
    public function setProductType($productType)
    {
        $this->productType = $productType;
        return $this;
    }

    /**
     * Get productType
     *
     * @return string
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * Set propertyAddress
     *
     * @param string $propertyAddress
     * @return $this
     */
    public function setPropertyAddress($propertyAddress)
    {
        $this->propertyAddress = $propertyAddress;
        return $this;
    }

    /**
     * Get propertyAddress
     *
     * @return string
     */
    public function getPropertyAddress()
    {
        return $this->propertyAddress;
    }

    /**
     * Set propertyPostcode
     *
     * @param string $propertyPostcode
     * @return $this
     */
    public function setPropertyPostcode($propertyPostcode)
    {
        $this->propertyPostcode = $propertyPostcode;
        return $this;
    }

    /**
     * Get propertyPostcode
     *
     * @return string
     */
    public function getPropertyPostcode()
    {
        return $this->propertyPostcode;
    }

    /**
     * Set propertyTown
     *
     * @param string $propertyTown
     * @return $this
     */
    public function setPropertyTown($propertyTown)
    {
        $this->propertyTown = $propertyTown;
        return $this;
    }

    /**
     * Get propertyTown
     *
     * @return string
     */
    public function getPropertyTown()
    {
        return $this->propertyTown;
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
     * Set resultsPerPage
     *
     * @param int $resultsPerPage
     * @return $this
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;
        return $this;
    }

    /**
     * Get resultsPerPage
     *
     * @return int
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     * One-way hash of object properties
     *
     * @param array|null $extra Optional array of extra items to include in hash (eg, requester's ASN, limit)
     * @return string The hash of the object's properties
     */
    public function getHash(array $extra = array())
    {
        // Get array of object's properties and values
        $properties = get_object_vars($this);

        // Merge in anything extra
        $properties = array_merge($properties, $extra);

        // Serialize and hash
        $hash = sha1(serialize($properties));

        return $hash;
    }
}