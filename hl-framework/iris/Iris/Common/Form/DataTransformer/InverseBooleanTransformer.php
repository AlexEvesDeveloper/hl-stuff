<?php

namespace Iris\Common\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class InverseBooleanTransformer
 *
 * @package Iris\Common\Form\DataTransformer
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class InverseBooleanTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return false;
        }

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
