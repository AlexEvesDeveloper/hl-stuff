<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use ArrayAccess;
use Barbon\IrisRestClient\Annotation as Iris;
use Countable;
use Iterator;

/**
 * @Iris\Entity\ReferencingApplicationCollection(class="Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingApplication")
 */
class ReferencingApplicationCollection implements ArrayAccess, Iterator, Countable
{
    /**
     * @var array
     */
    private $items;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return (isset($this->items[$offset]));
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->items[] = $value;
        }
        else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->items[$this->position];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->items);
    }
}
