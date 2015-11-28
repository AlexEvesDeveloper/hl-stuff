<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;
use JsonSerializable;

/**
 * @Iris\Entity\Address
 */
final class Address implements JsonSerializable
{
    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $flat;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $houseName;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $houseNumber;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $street;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $locality;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $district;

    /**
     * @Iris\Field
     * @var string
     */
    private $town;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $county;

    /**
     * @Iris\Field(optional = true)
     * @var string
     */
    private $country;

    /**
     * @Iris\Field
     * @var string
     */
    private $postcode;

    /**
     * @Iris\Field(optional = true)
     * @var float
     */
    private $latitude;

    /**
     * @Iris\Field(optional = true)
     * @var float
     */
    private $longitude;

    /**
     * @Iris\Field(accessor = "isForeign",optional = true)
     * @var boolean
     */
    private $foreign;


    /**
     * Get flat
     *
     * @return string
     */
    public function getFlat()
    {
        return $this->flat;
    }

    /**
     * Set flat
     *
     * @param string $flat
     * @return $this
     */
    public function setFlat($flat)
    {
        $this->flat = $flat;
        return $this;
    }

    /**
     * Get house name
     *
     * @return string
     */
    public function getHouseName()
    {
        return $this->houseName;
    }

    /**
     * Set house name
     *
     * @param string $houseName
     * @return $this
     */
    public function setHouseName($houseName)
    {
        $this->houseName = $houseName;
        return $this;
    }

    /**
     * Get house number
     *
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * Set house number
     *
     * @param string $houseNumber
     * @return $this
     */
    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber = $houseNumber;
        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return $this
     */
    public function setStreet($street)
    {
        $this->street = $street;
        return $this;
    }

    /**
     * Get locality
     *
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set locality
     *
     * @param string $locality
     * @return $this
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
        return $this;
    }

    /**
     * Get district
     *
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Set district
     *
     * @param string $district
     * @return $this
     */
    public function setDistrict($district)
    {
        $this->district = $district;
        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set town
     *
     * @param string $town
     * @return $this
     */
    public function setTown($town)
    {
        $this->town = $town;
        return $this;
    }

    /**
     * Get county
     *
     * @return string
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * Set county
     *
     * @param string $county
     * @return $this
     */
    public function setCounty($county)
    {
        $this->county = $county;
        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country
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
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Get latitude
     *
     * @param float $latitude
     * @return $this
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return $this
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * Is foreign
     *
     * @return boolean
     */
    public function isForeign()
    {
        return $this->foreign;
    }

    /**
     * Set if is foreign
     *
     * @param boolean $foreign
     * @return $this
     */
    public function setForeign($foreign)
    {
        $this->foreign = $foreign;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'flat' => $this->getFlat(),
            'houseName' => $this->getHouseName(),
            'houseNumber' => $this->getHouseNumber(),
            'street' => $this->getStreet(),
            'locality' => $this->getLocality(),
            'district' => $this->getDistrict(),
            'town' => $this->getTown(),
            'county' => $this->getCounty(),
            'country' => $this->getCountry(),
            'postcode' => $this->getPostcode(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'foreign' => $this->isForeign()
        ];
    }
}
