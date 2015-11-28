<?php

namespace Barbondev\IRISSDK\Utility\AddressFinder\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Common\Collection;

/**
 * Class PafAddress
 *
 * @package Barbondev\IRISSDK\Utility\AddressFinder\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class PafAddress extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $organisationDepartment;

    /**
     * @var string
     */
    private $organisation;

    /**
     * @var string
     */
    private $subBuildingName;

    /**
     * @var string
     */
    private $buildingName;

    /**
     * @var string
     */
    private $poBoxNumber;

    /**
     * @var string
     */
    private $buildingNumber;

    /**
     * @var string
     */
    private $addressLineOne;

    /**
     * @var string
     */
    private $addressLineTwo;

    /**
     * @var string
     */
    private $addressLineThree;

    /**
     * @var string
     */
    private $addressLineFour;

    /**
     * @var string
     */
    private $addressLineFive;

    /**
     * @var string
     */
    private $postcode;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $items = new Collection();

        foreach ($command->getResponse()->json() as $key => $item) {
            $items[$key] = self::hydrateModelProperties(
                new self(),
                $item,
                array(
                    'address1' => 'addressLineOne',
                    'address2' => 'addressLineTwo',
                    'address3' => 'addressLineThree',
                    'address4' => 'addressLineFour',
                    'address5' => 'addressLineFive',
                )
            );
        }

        return $items;
    }

    /**
     * Set addressLineFive
     *
     * @param string $addressLineFive
     * @return $this
     */
    public function setAddressLineFive($addressLineFive)
    {
        $this->addressLineFive = $addressLineFive;
        return $this;
    }

    /**
     * Get addressLineFive
     *
     * @return string
     */
    public function getAddressLineFive()
    {
        return $this->addressLineFive;
    }

    /**
     * Set addressLineFour
     *
     * @param string $addressLineFour
     * @return $this
     */
    public function setAddressLineFour($addressLineFour)
    {
        $this->addressLineFour = $addressLineFour;
        return $this;
    }

    /**
     * Get addressLineFour
     *
     * @return string
     */
    public function getAddressLineFour()
    {
        return $this->addressLineFour;
    }

    /**
     * Set addressLineOne
     *
     * @param string $addressLineOne
     * @return $this
     */
    public function setAddressLineOne($addressLineOne)
    {
        $this->addressLineOne = $addressLineOne;
        return $this;
    }

    /**
     * Get addressLineOne
     *
     * @return string
     */
    public function getAddressLineOne()
    {
        return $this->addressLineOne;
    }

    /**
     * Set addressLineThree
     *
     * @param string $addressLineThree
     * @return $this
     */
    public function setAddressLineThree($addressLineThree)
    {
        $this->addressLineThree = $addressLineThree;
        return $this;
    }

    /**
     * Get addressLineThree
     *
     * @return string
     */
    public function getAddressLineThree()
    {
        return $this->addressLineThree;
    }

    /**
     * Set addressLineTwo
     *
     * @param string $addressLineTwo
     * @return $this
     */
    public function setAddressLineTwo($addressLineTwo)
    {
        $this->addressLineTwo = $addressLineTwo;
        return $this;
    }

    /**
     * Get addressLineTwo
     *
     * @return string
     */
    public function getAddressLineTwo()
    {
        return $this->addressLineTwo;
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
     * Get buildingName
     *
     * @return string
     */
    public function getBuildingName()
    {
        return $this->buildingName;
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
     * Get buildingNumber
     *
     * @return string
     */
    public function getBuildingNumber()
    {
        return $this->buildingNumber;
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
     * Get organisation
     *
     * @return string
     */
    public function getOrganisation()
    {
        return $this->organisation;
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
     * Get organisationDepartment
     *
     * @return string
     */
    public function getOrganisationDepartment()
    {
        return $this->organisationDepartment;
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
     * Get poBoxNumber
     *
     * @return string
     */
    public function getPoBoxNumber()
    {
        return $this->poBoxNumber;
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
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
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
     * Get subBuildingName
     *
     * @return string
     */
    public function getSubBuildingName()
    {
        return $this->subBuildingName;
    }
}