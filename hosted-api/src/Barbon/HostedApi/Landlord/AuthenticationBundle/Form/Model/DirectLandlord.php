<?php

namespace Barbon\HostedApi\Landlord\AuthenticationBundle\Form\Model;

use Barbon\HostedApi\AppBundle\Form\Common\Model\Address;
use Barbon\IrisRestClient\Annotation as Iris;
use Symfony\Component\Validator\Constraints as Assert;
use JsonSerializable;

/**
 * @Iris\Entity\DirectLandlord
 * @Iris\Transmission(requestType = "json",responseType = {"json"})
 */
final class DirectLandlord implements JsonSerializable
{
    /**
     * @Iris\Id
     * @Iris\Field
     * @var int
     */
    private $directLandlordId;

    /**
     * @Iris\Field
     * @var string
     */
    private $password;

    /**
     * @Iris\Field
     * @Assert\NotBlank(message = "Please enter first name")
     * @Assert\Regex(pattern = "/^[-a-zA-Z0-9\w]+$/")
     * @var string
     */
    private $firstName;

    /**
     * @Iris\Field
     * @var string
     */
    private $email;

    /**
     * @Iris\Field
     * @Assert\NotBlank(message = "Please enter last name")
     * @Assert\Regex(pattern = "/^[-a-zA-Z0-9\w]+$/")
     * @var string
     */
    private $lastName;

    /**
     * @Iris\Field(optional = true)
     * @Assert\Length(min = 9)
     * @Assert\Regex(pattern = "/^[0-9+\(\)#\.\s\/ext-]{1,20}$/")
     * @var string
     */
    private $dayPhone;

    /**
     * @Iris\Field(optional = true)
     * @Assert\Length(min = 9)
     * @Assert\Regex(pattern = "/^[0-9+\(\)#\.\s\/ext-]{1,20}$/")
     * @var string
     */
    private $eveningPhone;

    /**
     * @Iris\Field(accessor = "isForeigner",optional = true)
     * @var boolean
     */
    private $foreigner;

    /**
     * @Iris\Field
     * @var int
     */
    private $securityQuestion;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $securityAnswer;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $title;

    /**
     * @Iris\Field
     * @var Address
     */
    private $address;

    public function __construct()
    {
        $this->setSecurityQuestion(1);
        $this->setSecurityAnswer('default answer');
    }

    /**
     * Get directLandlordId
     *
     * @return int
     */
    public function getDirectLandlordId()
    {
        return $this->directLandlordId;
    }

    /**
     * Set directLandlordId
     *
     * @param int $directLandlordId
     * @return $this
     */
    public function setDirectLandlordId($directLandlordId)
    {
        $this->directLandlordId = $directLandlordId;
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set firstName
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
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set lastName
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
     * Get dayPhone
     *
     * @return string
     */
    public function getDayPhone()
    {
        return $this->dayPhone;
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
     * Get eveningPhone
     *
     * @return string
     */
    public function getEveningPhone()
    {
        return $this->eveningPhone;
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
     * Is foreigner
     *
     * @return boolean
     */
    public function isForeigner()
    {
        return $this->foreigner;
    }

    /**
     * Set if is foreigner
     *
     * @param boolean $foreigner
     * @return $this
     */
    public function setForeigner($foreigner)
    {
        $this->foreigner = $foreigner;
        return $this;
    }

    /**
     * Get securityQuestion
     *
     * @return int
     */
    public function getSecurityQuestion()
    {
        return $this->securityQuestion;
    }

    /**
     * Set securityQuestion
     *
     * @param int $securityQuestion
     * @return $this
     */
    public function setSecurityQuestion($securityQuestion)
    {
        $this->securityQuestion = $securityQuestion;
        return $this;
    }

    /**
     * Get securityAnswer
     *
     * @return string
     */
    public function getSecurityAnswer()
    {
        return $this->securityAnswer;
    }

    /**
     * Set securityAnswer
     *
     * @param string $securityAnswer
     * @return $this
     */
    public function setSecurityAnswer($securityAnswer)
    {
        $this->securityAnswer = $securityAnswer;
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

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'password' => $this->getPassword(),
            'firstName' => $this->getFirstName(),
            'email' => $this->getEmail(),
            'lastName' => $this->getLastName(),
            'dayPhone' => $this->getDayPhone(),
            'eveningPhone' => $this->getEveningPhone(),
            'foreigner' => $this->isForeigner(),
            'securityQuestion' => $this->getSecurityQuestion(),
            'securityAnswer' => $this->getSecurityAnswer(),
            'title' => $this->getTitle(),
            'address' => $this->getAddress()
        ];
    }
}
