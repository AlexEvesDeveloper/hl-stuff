<?php

namespace Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Guzzle\Service\Command\OperationCommand;
use Barbondev\IRISSDK\Common\Model\Address;
use Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ProspectiveLandlord;

/**
 * Class ReferencingCase
 *
 * @package Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ReferencingCase extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $referencingCaseUuId;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var float
     */
    private $totalRent;

    /**
     * @var string
     */
    private $tenancyStartDate;

    /**
     * @var int
     */
    private $tenancyTermInMonths;

    /**
     * @var int
     */
    private $numberOfTenants;

    /**
     * @var int
     */
    private $propertyType;

    /**
     * @var int
     */
    private $propertyLetType;

    /**
     * @var int
     */
    private $rentGuaranteeOfferingType;

    /**
     * @var ProspectiveLandlord
     */
    private $prospectiveLandlord;

    /**
     * @var int
     */
    private $propertyBuiltInRangeType;

    /**
     * @var int
     */
    private $numberOfBedrooms;

    /**
     * @var array
     */
    private $applications;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $uriSegments = $command->getRequest()->getUrl(true)->getPathSegments();
        $referencingCaseUuId = end($uriSegments);
        if (!is_string($referencingCaseUuId) || 36 != strlen($referencingCaseUuId)) {
            $referencingCaseUuId = null;
        }

        $data = $command->getResponse()->json();

        $address = isset($data['address']) ? self::hydrateModelProperties(
            new Address(),
            $data['address']
        ) : null;

        $prospectiveLandlordAddress = isset($data['prospectiveLandlord']['address']) ? self::hydrateModelProperties(
            new Address(),
            $data['prospectiveLandlord']['address']
        ) : null;

        $prospectiveLandlord = isset($data['prospectiveLandlord']) ? self::hydrateModelProperties(
            new ProspectiveLandlord(),
            $data['prospectiveLandlord'],
            array(),
            array(
                'address' => $prospectiveLandlordAddress,
            )
        ) : null;

        $applications = array();
        if (isset($data['applications']) && is_array($data['applications'])) {
            foreach ($data['applications'] as $key => $object) {

                $application = ReferencingApplication::hydrate($object);

                $applications[$key] = $application;

            }
        }

        return self::hydrateModelProperties(
            new self(),
            $data,
            array(
                'caseId' => 'referencingCaseUuId',
                'tenancyTerm' => 'tenancyTermInMonths',
            ),
            array(
                'caseId' => $referencingCaseUuId,
                'address' => $address,
                'prospectiveLandlord' => $prospectiveLandlord,
                'applications' => $applications,
            )
        );
    }

    /**
     * Set referencingCaseUuId
     *
     * @param string $referencingCaseUuId
     * @return $this
     */
    public function setReferencingCaseUuId($referencingCaseUuId)
    {
        $this->referencingCaseUuId = $referencingCaseUuId;
        return $this;
    }

    /**
     * Get referencingCaseUuId
     *
     * @return string
     */
    public function getReferencingCaseUuId()
    {
        return $this->referencingCaseUuId;
    }

    /**
     * Set address
     *
     * @param \Barbondev\IRISSDK\Common\Model\Address $address
     * @return $this
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return \Barbondev\IRISSDK\Common\Model\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set numberOfTenants
     *
     * @param int $numberOfTenants
     * @return $this
     */
    public function setNumberOfTenants($numberOfTenants)
    {
        $this->numberOfTenants = $numberOfTenants;
        return $this;
    }

    /**
     * Get numberOfTenants
     *
     * @return int
     */
    public function getNumberOfTenants()
    {
        return $this->numberOfTenants;
    }

    /**
     * @param int $numberOfBedrooms
     * @return $this
     */
    public function setNumberOfBedrooms($numberOfBedrooms)
    {
        $this->numberOfBedrooms = $numberOfBedrooms;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfBedrooms()
    {
        return $this->numberOfBedrooms;
    }

    /**
     * @param int $propertyBuiltInRangeType
     * @return $this
     */
    public function setPropertyBuiltInRangeType($propertyBuiltInRangeType)
    {
        $this->propertyBuiltInRangeType = $propertyBuiltInRangeType;
        return $this;
    }

    /**
     * @return int
     */
    public function getPropertyBuiltInRangeType()
    {
        return $this->propertyBuiltInRangeType;
    }

    /**
     * Set propertyLetType
     *
     * @param int $propertyLetType
     * @return $this
     */
    public function setPropertyLetType($propertyLetType)
    {
        $this->propertyLetType = $propertyLetType;
        return $this;
    }

    /**
     * Get propertyLetType
     *
     * @return int
     */
    public function getPropertyLetType()
    {
        return $this->propertyLetType;
    }

    /**
     * Set propertyType
     *
     * @param int $propertyType
     * @return $this
     */
    public function setPropertyType($propertyType)
    {
        $this->propertyType = $propertyType;
        return $this;
    }

    /**
     * Get propertyType
     *
     * @return int
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * Set prospectiveLandlord
     *
     * @param \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ProspectiveLandlord $prospectiveLandlord
     * @return $this
     */
    public function setProspectiveLandlord(ProspectiveLandlord $prospectiveLandlord)
    {
        $this->prospectiveLandlord = $prospectiveLandlord;
        return $this;
    }

    /**
     * Get prospectiveLandlord
     *
     * @return \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ProspectiveLandlord
     */
    public function getProspectiveLandlord()
    {
        return $this->prospectiveLandlord;
    }

    /**
     * Set rentGuaranteeOfferingType
     *
     * @param int $rentGuaranteeOfferingType
     * @return $this
     */
    public function setRentGuaranteeOfferingType($rentGuaranteeOfferingType)
    {
        $this->rentGuaranteeOfferingType = $rentGuaranteeOfferingType;
        return $this;
    }

    /**
     * Get rentGuaranteeOfferingType
     *
     * @return int
     */
    public function getRentGuaranteeOfferingType()
    {
        return $this->rentGuaranteeOfferingType;
    }

    /**
     * Set tenancyStartDate
     *
     * @param string $tenancyStartDate
     * @return $this
     */
    public function setTenancyStartDate($tenancyStartDate)
    {
        $this->tenancyStartDate = $tenancyStartDate;
        return $this;
    }

    /**
     * Get tenancyStartDate
     *
     * @return string
     */
    public function getTenancyStartDate()
    {
        return $this->tenancyStartDate;
    }

    /**
     * Set tenancyTermInMonths
     *
     * @param int $tenancyTermInMonths
     * @return $this
     */
    public function setTenancyTermInMonths($tenancyTermInMonths)
    {
        $this->tenancyTermInMonths = $tenancyTermInMonths;
        return $this;
    }

    /**
     * Get tenancyTermInMonths
     *
     * @return int
     */
    public function getTenancyTermInMonths()
    {
        return $this->tenancyTermInMonths;
    }

    /**
     * Set totalRent
     *
     * @param float $totalRent
     * @return $this
     */
    public function setTotalRent($totalRent)
    {
        $this->totalRent = $totalRent;
        return $this;
    }

    /**
     * Get totalRent
     *
     * @return float
     */
    public function getTotalRent()
    {
        return $this->totalRent;
    }

    /**
     * Set applications
     *
     * @param array $applications
     * @return $this
     */
    public function setApplications(array $applications)
    {
        $this->applications = $applications;
        return $this;
    }

    /**
     * Get applications
     *
     * @return \Guzzle\Common\Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }
}