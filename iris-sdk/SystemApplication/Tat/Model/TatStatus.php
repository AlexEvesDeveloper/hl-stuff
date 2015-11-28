<?php

namespace Barbondev\IRISSDK\SystemApplication\Tat\Model;

use Barbondev\IRISSDK\Common\Model\Address;
use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;


/**
 * Class TatStatus
 *
 * @package Barbondev\IRISSDK\SystemApplication\Tat\Model
 * @author Paul Swift <paul.swift@barbon.com>
 */
class TatStatus extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var string
     */
    private $status;

    /**
     * @var boolean
     */
    private $incomeStatus;

    /**
     * @var boolean
     */
    private $additionalIncomeStatus;

    /**
     * @var boolean
     */
    private $futureIncomeStatus;

    /**
     * @var boolean
     */
    private $landlordStatus;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        $address = new Address();

        // Reflect the address object ready to iterate through its properties
        $addressReflection = new \ReflectionObject($address);

        // Loop through address properties looking for matches with incoming
        //   data
        foreach ($addressReflection->getProperties() as $reflectionProperty) {

            $addressField = $reflectionProperty->getName();

            if (isset($data[$addressField])) {

                // String equivalent of the setter, based on the property name
                $setMethod = 'set' . ucfirst($addressField);

                // Check that the setter method exists, if so, use it
                if (method_exists($address, $setMethod)) {
                    $address->$setMethod($data[$addressField]);
                }

            }

        }

        return self::hydrateModelProperties(
            new self(),
            $data,
            array(),
            array(
                'address' => $address
            )
        );
    }

    /**
     * @param boolean $additionalIncomeStatus
     * @return $this
     */
    public function setAdditionalIncomeStatus($additionalIncomeStatus)
    {
        $this->additionalIncomeStatus = $additionalIncomeStatus;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getAdditionalIncomeStatus()
    {
        return $this->additionalIncomeStatus;
    }

    /**
     * @param \Barbondev\IRISSDK\Common\Model\Address $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return \Barbondev\IRISSDK\Common\Model\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param boolean $futureIncomeStatus
     * @return $this
     */
    public function setFutureIncomeStatus($futureIncomeStatus)
    {
        $this->futureIncomeStatus = $futureIncomeStatus;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getFutureIncomeStatus()
    {
        return $this->futureIncomeStatus;
    }

    /**
     * @param boolean $incomeStatus
     * @return $this
     */
    public function setIncomeStatus($incomeStatus)
    {
        $this->incomeStatus = $incomeStatus;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIncomeStatus()
    {
        return $this->incomeStatus;
    }

    /**
     * @param boolean $landlordStatus
     * @return $this
     */
    public function setLandlordStatus($landlordStatus)
    {
        $this->landlordStatus = $landlordStatus;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getLandlordStatus()
    {
        return $this->landlordStatus;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}