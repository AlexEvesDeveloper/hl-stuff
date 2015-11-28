<?php

namespace Iris\Common\Form\DataTransformer;

use Iris\Referencing\Form\Type\BooleanExpandedType;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class BooleanExpandedModelTransformer
 *
 * @package Iris\Common\Form\DataTransformer
 * @author Ashley J. Dawson
 */
class BooleanExpandedModelTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return array(
                BooleanExpandedType::FIELD_NAME => null,
            );
        }

        if (true === $value) {
            return array(
                BooleanExpandedType::FIELD_NAME => 1,
            );
        }

        if (false === $value) {
            return array(
                BooleanExpandedType::FIELD_NAME => 0,
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!isset($value[BooleanExpandedType::FIELD_NAME])) {
            return null;
        }

        $value = $value[BooleanExpandedType::FIELD_NAME];

        if (null === $value) {
            return null;
        }

        if (1 == $value) {
            return true;
        }

        if (0 == $value) {
            return false;
        }
    }
}