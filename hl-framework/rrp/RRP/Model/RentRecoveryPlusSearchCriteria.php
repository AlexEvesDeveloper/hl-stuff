<?php

namespace RRP\Model;

/**
 * Class RentRecoveryPlusSearchCriteria
 *
 * @package RRP\Model
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusSearchCriteria
{
    /**
     * @var string
     */
    private $landlordName;

    /**
     * @var string
     */
    private $policyNumber;

    /**
     * @var string
     */
    private $propertyPostcode;

    /**
     * @var int
     */
    private $resultsPerPage;

    /**
     * Set Landlord Name
     *
     * @param string $landlordName
     * @return $this
     */
    public function setLandlordName($landlordName)
    {
        $this->landlordName = $landlordName;
        return $this;
    }

    /**
     * Get Landlord Name
     *
     * @return string
     */
    public function getLandlordName()
    {
        return $this->landlordName;
    }

    /**
     * Set Property Postcode
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
     * Get Property Postcode
     *
     * @return string
     */
    public function getPropertyPostcode()
    {
        return $this->propertyPostcode;
    }

    /**
     * Set policyNumber
     *
     * @param string $policyNumber
     * @return $this
     */
    public function setPolicyNumber($policyNumber)
    {
        $this->policyNumber = $policyNumber;
        return $this;
    }

    /**
     * Get policyNumber
     *
     * @return string
     */
    public function getPolicyNumber()
    {
        return $this->policyNumber;
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

    /**
     * Builds the query string
     *
     * @param $formName
     * @return string
     */
    public function getQueryString($formName)
    {
        $queryString =
            $formName . '[policyNumber]=' . $this->policyNumber .
            '&' . $formName . '[landlordName]=' . $this->landlordName .
            '&' . $formName . '[propertyPostcode]=' . $this->propertyPostcode .
            '&' . $formName . '[resultsPerPage]=' . $this->resultsPerPage;
        return $queryString;
    }

}