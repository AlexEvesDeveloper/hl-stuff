<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Model;

use Barbon\HostedApi\AppBundle\Form\Common\Model\Address;
use Barbon\HostedApi\AppBundle\Form\Common\Model\ProspectiveLandlord;
use Barbon\IrisRestClient\Annotation as Iris;
use Barbon\IrisRestClient\Client\IrisClient;
use DateTime;

/**
 * @Iris\Entity\ReferencingCase
 */
class ReferencingCase
{
    /**
     * @var boolean
     */
    private $isVisible;

    /**
     * @Iris\Id
     * @Iris\Field
     * @var string
     */
    private $caseId;

    /**
     * @Iris\Field
     * @var float
     */
    private $totalRent;

    /**
     * @Iris\Field(format = "Y-m-d")
     * @var DateTime
     */
    private $tenancyStartDate;

    /**
     * @Iris\Field
     * @var int
     */
    private $tenancyTerm;

    /**
     * @Iris\Field
     * @var int
     */
    private $numberOfTenants;

    /**
     * @Iris\Field
     * @var int
     */
    private $propertyType;

    /**
     * @Iris\Field
     * @var int
     */
    private $propertyLetType;

    /**
     * @Iris\Field
     * @var int
     */
    private $rentGuaranteeOfferingType;

    /**
     * @Iris\Field
     * @var int
     */
    private $propertyBuiltInRangeType;

    /**
     * @Iris\Field
     * @var int
     */
    private $numberOfBedrooms;

    /**
     * @Iris\Field(optional = true)
     * @var Address
     */
    private $address;

    /**
     * @Iris\Field(optional = true)
     * @var ProspectiveLandlord
     */
    private $prospectiveLandlord;

    /**
     * @var ReferencingApplication[]
     */
    private $applications;

    /**
     * Is visible
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * Set if is visible
     *
     * @param boolean $isVisible
     * @return $this
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
        return $this;
    }

    /**
     * Get case Id
     *
     * @return string
     */
    public function getCaseId()
    {
        return $this->caseId;
    }

    /**
     * Set case Id
     *
     * @param string $caseId
     * @return $this
     */
    public function setCaseId($caseId)
    {
        $this->caseId = $caseId;
        return $this;
    }

    /**
     * Get total rent
     *
     * @return mixed
     */
    public function getTotalRent()
    {
        return $this->totalRent;
    }

    /**
     * Set total rent
     *
     * @param mixed $totalRent
     * @return $this
     */
    public function setTotalRent($totalRent)
    {
        $this->totalRent = $totalRent;
        return $this;
    }

    /**
     * Get tenancy start date
     *
     * @return DateTime
     */
    public function getTenancyStartDate()
    {
        return $this->tenancyStartDate;
    }

    /**
     * Set tenancy start date
     *
     * @param DateTime $tenancyStartDate
     * @return $this
     */
    public function setTenancyStartDate(DateTime $tenancyStartDate = null)
    {
        $this->tenancyStartDate = $tenancyStartDate;
        return $this;
    }

    /**
     * Get tenancy term
     *
     * @return mixed
     */
    public function getTenancyTerm()
    {
        return $this->tenancyTerm;
    }

    /**
     * Set tenancy term
     *
     * @param mixed $tenancyTerm
     * @return $this
     */
    public function setTenancyTerm($tenancyTerm)
    {
        $this->tenancyTerm = $tenancyTerm;
        return $this;
    }

    /**
     * Get number of tenants
     *
     * @return mixed
     */
    public function getNumberOfTenants()
    {
        return $this->numberOfTenants;
    }

    /**
     * Set number of tenants
     *
     * @param mixed $numberOfTenants
     * @return $this
     */
    public function setNumberOfTenants($numberOfTenants)
    {
        $this->numberOfTenants = $numberOfTenants;
        return $this;
    }

    /**
     * Get property type
     *
     * @return mixed
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * Set property type
     *
     * @param mixed $propertyType
     * @return $this
     */
    public function setPropertyType($propertyType)
    {
        $this->propertyType = $propertyType;
        return $this;
    }

    /**
     * Get property let type
     *
     * @return mixed
     */
    public function getPropertyLetType()
    {
        return $this->propertyLetType;
    }

    /**
     * Set property let type
     *
     * @param mixed $propertyLetType
     * @return $this
     */
    public function setPropertyLetType($propertyLetType)
    {
        $this->propertyLetType = $propertyLetType;
        return $this;
    }

    /**
     * Get rent guarantee offering type
     *
     * @return mixed
     */
    public function getRentGuaranteeOfferingType()
    {
        return $this->rentGuaranteeOfferingType;
    }

    /**
     * Set rent guarantee offering type
     *
     * @param mixed $rentGuaranteeOfferingType
     * @return $this
     */
    public function setRentGuaranteeOfferingType($rentGuaranteeOfferingType)
    {
        $this->rentGuaranteeOfferingType = $rentGuaranteeOfferingType;
        return $this;
    }

    /**
     * Get property builtin range
     *
     * @return mixed
     */
    public function getPropertyBuiltInRangeType()
    {
        return $this->propertyBuiltInRangeType;
    }

    /**
     * Set property builtin range
     *
     * @param mixed $propertyBuiltInRangeType
     * @return $this
     */
    public function setPropertyBuiltInRangeType($propertyBuiltInRangeType)
    {
        $this->propertyBuiltInRangeType = $propertyBuiltInRangeType;
        return $this;
    }

    /**
     * Get number of bedrooms
     *
     * @return mixed
     */
    public function getNumberOfBedrooms()
    {
        return $this->numberOfBedrooms;
    }

    /**
     * Set number of bedrooms
     *
     * @param mixed $numberOfBedrooms
     * @return $this
     */
    public function setNumberOfBedrooms($numberOfBedrooms)
    {
        $this->numberOfBedrooms = $numberOfBedrooms;
        return $this;
    }

    /**
     * Get address
     *
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param Address $address
     * @return $this
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get prospective landlord
     *
     * @return ProspectiveLandlord
     */
    public function getProspectiveLandlord()
    {
        return $this->prospectiveLandlord;
    }

    /**
     * Set prospective landlord
     *
     * @param ProspectiveLandlord $prospectiveLandlord
     * @return $this
     */
    public function setProspectiveLandlord(ProspectiveLandlord $prospectiveLandlord)
    {
        $this->prospectiveLandlord = $prospectiveLandlord;
        return $this;
    }

    /**
     * Get applications
     *
     * @return ReferencingApplication[]
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Set applications
     *
     * @param ReferencingApplication[] $applications
     * @return $this
     */
    public function setApplications(array $applications)
    {
        $this->applications = $applications;
        return $this;
    }

    /**
     * Submit the case for referencing
     *
     * @param IrisClient $client
     * @deprecated
     */
    public function submit(IrisClient $client)
    {
        $client->put(sprintf('/referencing/v1/individual/case/%s/submit', urlencode($this->getCaseId())), array(
            'headers' => array(
                'Content-Type' => 'application/json',
            )
        ));
    }
}