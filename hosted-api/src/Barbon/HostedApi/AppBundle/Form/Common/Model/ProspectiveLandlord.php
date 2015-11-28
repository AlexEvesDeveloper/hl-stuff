<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\ProspectiveLandlord
 */
class ProspectiveLandlord
{
    /**
     * @var boolean
     */
    private $isVisible;

    /**
     * @Iris\Field
     * @var string
     */
    private $title;

    /**
     * @Iris\Field
     * @var string
     */
    private $firstName;

    /**
     * @Iris\Field
     * @var string
     */
    private $lastName;

    /**
     * @Iris\Field
     * @var string
     */
    private $email;

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
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set first name
     *
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set last name
     *
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
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