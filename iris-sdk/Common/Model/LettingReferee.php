<?php

namespace Barbondev\IRISSDK\Common\Model;

/**
 * Class LettingReferee
 * @todo Should implement \JsonSerializable and have private properties.  Currently doesn't for PHP <5.4 compatibility.
 *
 * @package Barbondev\IRISSDK\Common\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class LettingReferee
{
    /**
     * @var int
     */
    public $type;

    /**
     * @var string
     */
    public $name;

    /**
     * @var Address
     */
    public $address;

    /**
     * @var string
     */
    public $dayPhone;

    /**
     * @var string
     */
    public $eveningPhone;

    /**
     * @var string
     */
    public $fax;

    /**
     * @var string
     */
    public $email;

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
     * Set dayPhone
     *
     * @param string $dayPhone
     * @return $this
     */
    public function setDayPhone($dayPhone)
    {
        $this->dayPhone = $dayPhone;
        return $this;
    }

    /**
     * Get dayPhone
     *
     * @return string
     */
    public function getDayPhone()
    {
        return $this->dayPhone;
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
     * Set eveningPhone
     *
     * @param string $eveningPhone
     * @return $this
     */
    public function setEveningPhone($eveningPhone)
    {
        $this->eveningPhone = $eveningPhone;
        return $this;
    }

    /**
     * Get eveningPhone
     *
     * @return string
     */
    public function getEveningPhone()
    {
        return $this->eveningPhone;
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
     * Set type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
}