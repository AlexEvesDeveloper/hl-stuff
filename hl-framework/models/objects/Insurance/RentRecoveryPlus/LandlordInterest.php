<?php

/**
 * Class Model_Insurance_RentRecoveryPlus_LandlordInterest
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Model_Insurance_RentRecoveryPlus_LandlordInterest extends Model_Insurance_RentRecoveryPlus_AbstractResponseModel
{
    /**
     * @var string
     */
    private $policyNumber;
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
    private $emailAddress;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $address1;

    /**
     * @var string
     */
    private $address2;

    /**
     * @var string
     */
    private $address3;

    /**
     * @var string
     */
    private $postcode;

    /**
     * @var string
     */
    private $country;

    /**
     * @var bool
     */
    private $isForeignAddress;

    /**
     * Hydrate a single application
     *
     * @param array $data
     * @return object
     */
    public static function hydrate($data)
    {
        return self::hydrateModelProperties(
            new self(),
            $data,
            array(),
            array()
        );
    }
    /**
     * Hydrate from the database row names
     *
     * @param $data
     * @return object
     */
    public static function hydrateFromRow($data)
    {
        return self::hydrateModelProperties(
            new self(),
            $data,
            self::getDBNameProperties(),
            array()
        );
    }

    /**
     * Gets a array of the mapping between the database table name and class properties
     *
     * @return array
     */
    private static function getDBNameProperties()
    {
        return array(
            'policynumber'       => 'policyNumber',
            'firstname'          => 'firstName',
            'lastname'           => 'lastName',
            'email'              => 'emailAddress',
            'phone'              => 'phoneNumber',
            'is_foreign_address' => 'isForeignAddress'
        );
    }

    /**
     * Gets the policy number
     *
     * @return string
     */
    public function getPolicyNumber()
    {
        return $this->policyNumber;
    }

    /**
     * Sets the policy number
     *
     * @param string $policyNumber
     * @return $this
     */
    public function setPolicyNumber($policyNumber)
    {
        $this->policyNumber = $policyNumber;
        return $this;
    }

    /**
     * Gets the landlord email address
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Sets the landlord email address
     *
     * @param string $emailAddress
     * @return $this
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * Gets the landlord full name including title
     *
     * @return string
     */
    public function getFullName()
    {
        return sprintf('%s %s %s', $this->title, $this->firstName, $this->lastName);
    }

    /**
     * Gets the landlord first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Sets the landlord first name
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
     * Gets the landlord last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Sets the landlord last name
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
     * Gets the landlord phone number
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Sets the landlord phone number
     *
     * @param string $phoneNumber
     * @return $this
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * Sets the landlord title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets the landlord title
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
     * Gets the landlord's full address excluding postcode
     *
     * @return string
     */
    public function getAddress()
    {
        $address = $this->address1;
        if ($this->address2) {
            $address .= ', ' . $this->address2;
        }
        if ($this->address3) {
            $address .= ', ' . $this->address3;
        }
        return $address;
    }

    /**
     * Gets the landlord address line 1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Sets the landlord address line 1
     *
     * @param string $address1
     * @return $this
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * Gets the landlord address line 2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Sets the landlord address line 2
     *
     * @param string $address2
     * @return $this
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }


    /**
     * Gets the landlord address line 3
     *
     * @return string
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * Sets the landlord address line 3
     *
     * @param string $address3
     * @return $this
     */
    public function setAddress3($address3)
    {
        $this->address3 = $address3;
        return $this;
    }

    /**
     * Gets the landlord postcode
     *
     * @return float
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Sets the landlord postcode
     *
     * @param float $postcode
     * @return $this
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
        return $this;
    }

    /**
     * Gets the country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets the country
     *
     * @param string $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Gets the 'IsForeignAddress' flag
     *
     * @return boolean
     */
    public function getIsForeignAddress()
    {
        if ($this->isForeignAddress) {
            return true;
        }
        return false;
    }

    /**
     * Sets the 'IsForeignAddress' flag
     *
     * @param boolean $isForeignAddress
     * @return $this
     */
    public function setIsForeignAddress($isForeignAddress)
    {
        $this->isForeignAddress = $isForeignAddress;
        return $this;
    }

}
