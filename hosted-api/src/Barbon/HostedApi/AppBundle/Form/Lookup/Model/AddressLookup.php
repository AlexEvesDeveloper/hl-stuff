<?php

namespace Barbon\HostedApi\AppBundle\Form\Lookup\Model;

use Barbon\IrisRestClient\Annotation as Iris;
use JsonSerializable;

/**
 * @Iris\Entity\AddressLookup
 */
final class AddressLookup implements JsonSerializable
{
    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $organisationDepartment;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $organisation;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $subBuildingName;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $buildingName;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $poBoxNumber;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $buildingNumber;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $address1;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $address2;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $address3;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $address4;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $address5;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $postcode;

    /**
     * Get organisationDepartment
     *
     * @return string
     */
    public function getOrganisationDepartment()
    {
        return $this->organisationDepartment;
    }

    /**
     * Set organisationDepartment
     *
     * @param string $organisationDepartment
     * @return $this
     */
    public function setOrganisationDepartment($organisationDepartment)
    {
        $this->organisationDepartment = $organisationDepartment;
        return $this;
    }

    /**
     * Get organisation
     *
     * @return string
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * Set organisation
     *
     * @param string $organisation
     * @return $this
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
        return $this;
    }

    /**
     * Get subBuildingName
     *
     * @return string
     */
    public function getSubBuildingName()
    {
        return $this->subBuildingName;
    }

    /**
     * Set subBuildingName
     *
     * @param string $subBuildingName
     * @return $this
     */
    public function setSubBuildingName($subBuildingName)
    {
        $this->subBuildingName = $subBuildingName;
        return $this;
    }

    /**
     * Get buildingName
     *
     * @return string
     */
    public function getBuildingName()
    {
        return $this->buildingName;
    }

    /**
     * Set buildingName
     *
     * @param string $buildingName
     * @return $this
     */
    public function setBuildingName($buildingName)
    {
        $this->buildingName = $buildingName;
        return $this;
    }

    /**
     * Get poBoxNumber
     *
     * @return string
     */
    public function getPoBoxNumber()
    {
        return $this->poBoxNumber;
    }

    /**
     * Set poBoxNumber
     *
     * @param string $poBoxNumber
     * @return $this
     */
    public function setPoBoxNumber($poBoxNumber)
    {
        $this->poBoxNumber = $poBoxNumber;
        return $this;
    }

    /**
     * Get buildingNumber
     *
     * @return string
     */
    public function getBuildingNumber()
    {
        return $this->buildingNumber;
    }

    /**
     * Set buildingNumber
     *
     * @param string $buildingNumber
     * @return $this
     */
    public function setBuildingNumber($buildingNumber)
    {
        $this->buildingNumber = $buildingNumber;
        return $this;
    }

    /**
     * Get address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address1
     *
     * @param string $address1
     * @return $this
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return $this
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * Get address3
     *
     * @return string
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * Set address3
     *
     * @param string $address3
     * @return $this
     */
    public function setAddress3($address3)
    {
        $this->address3 = $address3;
        return $this;
    }

    /**
     * Get address4
     *
     * @return string
     */
    public function getAddress4()
    {
        return $this->address4;
    }

    /**
     * Set address4
     *
     * @param string $address4
     * @return $this
     */
    public function setAddress4($address4)
    {
        $this->address4 = $address4;
        return $this;
    }

    /**
     * Get address5
     *
     * @return string
     */
    public function getAddress5()
    {
        return $this->address5;
    }

    /**
     * Set address5
     *
     * @param string $address5
     * @return $this
     */
    public function setAddress5($address5)
    {
        $this->address5 = $address5;
        return $this;
    }

    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'organisationDepartment' => $this->getOrganisationDepartment(),
            'organisation' => $this->getOrganisation(),
            'subBuildingName' => $this->getSubBuildingName(),
            'buildingName' => $this->getBuildingName(),
            'poBoxNumber' => $this->getPoBoxNumber(),
            'buildingNumber' => $this->getBuildingNumber(),
            'address1' => $this->getAddress1(),
            'address2' => $this->getAddress2(),
            'address3' => $this->getAddress3(),
            'address4' => $this->getAddress4(),
            'address5' => $this->getAddress5(),
            'postcode' => $this->getPostcode()
        ];
    }
}