<?php

namespace Barbondev\IRISSDK\IndividualApplication\Lookup\Model;

/**
 * Class LookupItem
 *
 * @package Barbondev\IRISSDK\IndividualApplication\Lookup\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class LookupItem
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * Set id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
}