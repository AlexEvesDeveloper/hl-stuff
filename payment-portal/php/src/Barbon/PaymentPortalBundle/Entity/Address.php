<?php

namespace Barbon\PaymentPortalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class Address
 *
 * @package Barbon\PaymentPortalBundle\Entity
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @ORM\Table(name="addresses")
 * @ORM\Entity
 * @Serializer\ExclusionPolicy("all")
 */
class Address
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array
     *
     * @ORM\Column(name="addressLines", type="json_array", nullable=true)
     * @Assert\Type(type="array")
     * @Serializer\Expose
     */
    private $lines;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=60, nullable=true)
     * @Serializer\Expose
     */
    private $town;

    /**
     * @var string
     *
     * @ORM\Column(name="county", type="string", length=60, nullable=true)
     * @Serializer\Expose
     */
    private $county;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=10, nullable=true)
     * @Serializer\Expose
     */
    private $postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=120, nullable=true)
     * @Serializer\Expose
     */
    private $country;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Get lines
     *
     * @return array
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Set lines
     *
     * @param array $lines
     * @return $this
     */
    public function setLines(array $lines)
    {
        $this->lines = $lines;
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
}