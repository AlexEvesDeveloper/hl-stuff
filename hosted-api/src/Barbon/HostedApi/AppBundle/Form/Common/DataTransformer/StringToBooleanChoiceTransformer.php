<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class StringToBooleanChoiceTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        return (true === $value ? 'true' : 'false');
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        return ('true' === strtolower($value) ? true : false);
    }
}