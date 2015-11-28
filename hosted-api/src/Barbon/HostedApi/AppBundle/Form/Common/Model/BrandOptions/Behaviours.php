<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model\BrandOptions;

use Barbon\IrisRestClient\Annotation as Iris;
use JsonSerializable;

class Behaviours implements JsonSerializable
{
    /**
     * Array of field => default value suppressions.
     *
     * @Iris\Field(optional = true)
     * @var array
     */
    private $suppressFields = [];

    /**
     * Get suppressFields
     *
     * @Iris\Field(optional = true)
     * @return array
     */
    public function getSuppressFields()
    {
        return $this->suppressFields;
    }

    /**
     * Set suppressFields
     *
     * @param array $suppressFields
     * @return $this
     */
    public function setSuppressFields(array $suppressFields)
    {
        $this->suppressFields = $suppressFields;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'suppressFields' => $this->getSuppressFields()
        ];
    }
}