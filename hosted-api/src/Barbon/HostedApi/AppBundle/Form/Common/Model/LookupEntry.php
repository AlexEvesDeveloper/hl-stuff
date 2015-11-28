<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\LookupEntry
 */
class LookupEntry
{
    /**
     * @Iris\Field
     * @var int
     */
    private $index;

    /**
     * @Iris\Field
     * @var string
     */
    private $value;

    /**
     * Get index
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set index
     *
     * @param int $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
