<?php

namespace Barbondev\IRISSDK\Landlord\Landlord\Model;

use Barbondev\IRISSDK\Common\Model\Address;
use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;


/**
 * Class Landlord
 *
 * @package Barbondev\IRISSDK\Landlord\Landlord\Model
 * @author Paul Swift <paul.swift@barbon.com>
 */
class Landlord extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var int
     */
    private $securityQuestion;

    /**
     * @var string
     */
    private $securityAnswer;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var string
     */
    private $dayPhone;

    /**
     * @var string
     */
    private $eveningPhone;

    /**
     * @var string
     */
    private $occupation;

    /**
     * @var boolean
     */
    private $foreigner;

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
        $addressData = $data['address'];

        $address = new Address();

        // Reflect the address object ready to iterate through its properties
        $addressReflection = new \ReflectionObject($address);

        // Loop through address properties looking for matches with incoming
        //   data
        foreach ($addressReflection->getProperties() as $reflectionProperty) {

            $addressField = $reflectionProperty->getName();

            if (isset($addressData[$addressField])) {

                // String equivalent of the setter, based on the property name
                $setMethod = 'set' . ucfirst($addressField);

                // Check that the setter method exists, if so, use it
                if (method_exists($address, $setMethod)) {
                    $address->$setMethod($addressData[$addressField]);
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
     * @param string $dayPhone
     * @return $this
     */
    public function setDayPhone($dayPhone)
    {
        $this->dayPhone = $dayPhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getDayPhone()
    {
        return $this->dayPhone;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $eveningPhone
     * @return $this
     */
    public function setEveningPhone($eveningPhone)
    {
        $this->eveningPhone = $eveningPhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getEveningPhone()
    {
        return $this->eveningPhone;
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
     * @param boolean $foreigner
     * @return $this
     */
    public function setForeigner($foreigner)
    {
        $this->foreigner = $foreigner;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getForeigner()
    {
        return $this->foreigner;
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
     * @param string $occupation
     * @return $this
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
        return $this;
    }

    /**
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * @param string $securityAnswer
     * @return $this
     */
    public function setSecurityAnswer($securityAnswer)
    {
        $this->securityAnswer = $securityAnswer;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecurityAnswer()
    {
        return $this->securityAnswer;
    }

    /**
     * @param int $securityQuestion
     * @return $this
     */
    public function setSecurityQuestion($securityQuestion)
    {
        $this->securityQuestion = $securityQuestion;
        return $this;
    }

    /**
     * @return int
     */
    public function getSecurityQuestion()
    {
        return $this->securityQuestion;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Fax
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
     * Get Fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }
}