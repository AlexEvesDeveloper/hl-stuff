<?php

namespace Iris\Referencing\FormSet\Model;

/**
 * Class LinkRefHolder
 *
 * @package Iris\Referencing\FormSet\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class LinkRefHolder
{
    /**
     * @var string
     */
    private $linkRef;

    /**
     * Constructor
     *
     * @param string|null $linkRef
     */
    public function __construct($linkRef = null)
    {
        $this->linkRef = $linkRef;
    }

    /**
     * Set linkRef
     *
     * @param string $linkRef
     * @return $this
     */
    public function setLinkRef($linkRef)
    {
        $this->linkRef = $linkRef;
        return $this;
    }

    /**
     * Get linkRef
     *
     * @return string
     */
    public function getLinkRef()
    {
        return $this->linkRef;
    }
}