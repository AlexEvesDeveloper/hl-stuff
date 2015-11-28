<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\LettingReferee
 */
class LettingReferee
{
    /**
     * @Iris\Field
     * @var int
     */
    private $lettingRefereeId;

    /**
     * @Iris\Field
     * @var int
     */
    private $type;

    /**
     * @Iris\Field
     * @var string
     */
    private $name;

    /**
     * @Iris\Field
     * @var string
     */
    private $dayPhone;

    /**
     * @Iris\Field
     * @var string
     */
    private $eveningPhone;

    /**
     * @Iris\Field
     * @var string
     */
    private $fax;

    /**
     * @Iris\Field
     * @var string
     */
    private $email;


    /**
     * @Iris\Field
     * @var Address
     */
    private $address;

    /**
     * Get letting referee id
     *
     * @return int
     */
    public function getLettingRefereeId()
    {
        return $this->lettingRefereeId;
    }

    /**
     * Set letting referee id
     *
     * @param int $lettingRefereeId
     * @return $this
     */
    public function setLettingRefereeId($lettingRefereeId)
    {
        $this->lettingRefereeId = $lettingRefereeId;
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Get daytime phone
     *
     * @return string
     */
    public function getDayPhone()
    {
        return $this->dayPhone;
    }

    /**
     * Set daytime phone
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
     * Get evening phone
     *
     * @return string
     */
    public function getEveningPhone()
    {
        return $this->eveningPhone;
    }

    /**
     * Set evening phone
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
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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