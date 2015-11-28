<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

final class InverseBooleanTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return !$value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        return !$value;
    }
}