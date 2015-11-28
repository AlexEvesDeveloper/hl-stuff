<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;
use DateTime;

/**
 * @Iris\Entity\PreviousAddress
 */
class PreviousAddress
{
    /**
     * @var boolean
     */
    private $isVisible;

    /**
     * @Iris\Field
     * @var string
     */
    private $addressHistoryId;

    /**
     * @var DateTime
     */
    private $startDate;

    /**
     * @Iris\Field
     * @var int
     */
    private $durationMonths;

    /**
     * @Iris\Field(accessor = "isForeign")
     * @var boolean
     */
    private $isForeign;

    /**
     * @Iris\Field
     * @var Address
     */
    private $address;

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
     * Get address history id
     *
     * @return string
     */
    public function getAddressHistoryId()
    {
        return $this->addressHistoryId;
    }

    /**
     * Set address history id
     *
     * @param string $addressHistoryId
     * @return $this
     */
    public function setAddressHistoryId($addressHistoryId)
    {
        $this->addressHistoryId = $addressHistoryId;
        return $this;
    }

    /**
     * Get start date
     *
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set start date
     *
     * @param DateTime $startDate
     * @return $this
     */
    public function setStartDate(DateTime $startDate = null)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Get duration in months
     *
     * @return int
     */
    public function getDurationMonths()
    {
        return $this->durationMonths;
    }

    /**
     * Set duration in months
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
     * Is foreign
     *
     * @return boolean
     */
    public function isForeign()
    {
        return $this->isForeign;
    }

    /**
     * Set if is foreign
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
}