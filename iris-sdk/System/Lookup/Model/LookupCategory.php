<?php

namespace Barbondev\IRISSDK\System\Lookup\Model;

use Guzzle\Common\Collection;

/**
 * Class LookupCategory
 *
 * @package Barbondev\IRISSDK\System\Lookup\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class LookupCategory
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Collection of LookupItem
     */
    private $items;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new Collection();
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

    /**
     * Set items
     *
     * @param \Guzzle\Common\Collection $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Get items
     *
     * @return \Guzzle\Common\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add item
     *
     * @param LookupItem $item
     */
    public function addItem(LookupItem $item)
    {
        $this->items->add($item->getId(), $item);
    }

    /**
     * Get item by ID
     *
     * @param int $id
     * @return LookupItem
     * @throws \InvalidArgumentException
     */
    public function getItemById($id)
    {
        if ($this->items->hasKey($id)) {
            return $this->items->get($id);
        }

        throw new \InvalidArgumentException(sprintf('Lookup item with ID %s does not exist', $id));
    }
}