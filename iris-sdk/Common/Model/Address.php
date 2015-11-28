<?php

namespace Barbondev\IRISSDK\Common\Model;

/**
 * Class Address
 * @todo Should implement \JsonSerializable and have private properties.  Currently doesn't for PHP <5.4 compatibility.
 *
 * @package Barbondev\IRISSDK\Common\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Address
{
    /**
     * @var string
     */
    public $flat;

    /**
     * @var string
     */
    public $houseName;

    /**
     * @var string
     */
    public $houseNumber;

    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $locality;

    /**
     * @var string
     */
    public $district;

    /**
     * @var string
     */
    public $town;

    /**
     * @var string
     */
    public $county;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $postcode;

    /**
     * @var double
     */
    public $latitude = null;

    /**
     * @var double
     */
    public $longitude = null;

    /**
     * @var bool
     */
    public $foreign = false;

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
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
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
     * Get county
     *
     * @return string
     */
    public function getCounty()
    {
        return $this->county;
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
     * Get district
     *
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
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
     * Get flat
     *
     * @return string
     */
    public function getFlat()
    {
        return $this->flat;
    }

    /**
     * Set houseName
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
     * Get houseName
     *
     * @return string
     */
    public function getHouseName()
    {
        return $this->houseName;
    }

    /**
     * Set houseNumber
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
     * Get houseNumber
     *
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * Set latitude
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
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
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
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
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
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
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
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
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
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Get is foreign
     *
     * @return bool|null
     */
    public function getForeign()
    {
        return $this->foreign;
    }

    /**
     * Set is foreign
     *
     * @param bool $foreign
     * @return $this
     */
    public function setForeign($foreign = false)
    {
        $this->foreign = $foreign;
        return $this;
    }
}
