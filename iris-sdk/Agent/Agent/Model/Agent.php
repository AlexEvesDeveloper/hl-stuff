<?php

namespace Barbondev\IRISSDK\Agent\Agent\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;
use Barbondev\IRISSDK\Common\Model\Address;
use Barbondev\IRISSDK\Agent\Brand\Model\Brand;

/**
 * Class Agent
 *
 * @package Barbondev\IRISSDK\Agent\Agent\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Agent extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var Brand
     */
    private $agentBrand;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $fax;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        $brandAddress = self::hydrateModelProperties(
            new Address(),
            $data['agentBrand']['address']
        );

        $brand = self::hydrateModelProperties(
            new Brand(),
            $data['agentBrand'],
            array(),
            array(
                'address' => $brandAddress,
            )
        );

        $address = self::hydrateModelProperties(
            new Address(),
            $data['address']
        );

        return self::hydrateModelProperties(
            new self(),
            $data,
            array(),
            array(
                'address' => $address,
                'agentBrand' => $brand,
            )
        );
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
     * Set agentBrand
     *
     * @param \Barbondev\IRISSDK\Agent\Brand\Model\Brand $agentBrand
     * @return $this
     */
    public function setAgentBrand(Brand $agentBrand)
    {
        $this->agentBrand = $agentBrand;
        return $this;
    }

    /**
     * Get agentBrand
     *
     * @return \Barbondev\IRISSDK\Agent\Brand\Model\Brand
     */
    public function getAgentBrand()
    {
        return $this->agentBrand;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return $this
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}