<?php

namespace Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model;

use Barbondev\IRISSDK\Common\Model\Address;

/**
 * Class AddressHistory
 * @todo Should implement \JsonSerializable and have private properties.  Currently doesn't for PHP <5.4 compatibility.
 *
 * @package Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class AddressHistory
{
    /**
     * @var string
     */
    public $addressHistoryUuId;

    /**
     * @var int
     */
    public $durationMonths;

    /**
     * @var bool
     */
    public $isForeign;

    /**
     * @var string
     */
    public $startedAt;

    /**
     * @var Address
     */
    public $address;

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
     * Set addressHistoryUuId
     *
     * @param string $addressHistoryUuId
     * @return $this
     */
    public function setAddressHistoryUuId($addressHistoryUuId)
    {
        $this->addressHistoryUuId = $addressHistoryUuId;
        return $this;
    }

    /**
     * Get addressHistoryUuId
     *
     * @return string
     */
    public function getAddressHistoryUuId()
    {
        return $this->addressHistoryUuId;
    }

    /**
     * Set durationMonths
     *
     * @param int $durationMonths
     * @return $this
     */
    public function setDurationMonths($durationMonths)
    {
        $this->durationMonths = $durationMonths;
        return $this;
    }

    /**
     * Get durationMonths
     *
     * @return int
     */
    public function getDurationMonths()
    {
        return $this->durationMonths;
    }

    /**
     * Set isForeign
     *
     * @param boolean $isForeign
     * @return $this
     */
    public function setIsForeign($isForeign)
    {
        $this->isForeign = $isForeign;
        return $this;
    }

    /**
     * Get isForeign
     *
     * @return boolean
     */
    public function getIsForeign()
    {
        return $this->isForeign;
    }

    /**
     * Set startedAt
     *
     * @param string $startedAt
     * @return $this
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    /**
     * Get startedAt
     *
     * @return string
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }
}