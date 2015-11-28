<?php

namespace Iris\Common\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class YearMonthDurationTransformer
 *
 * @package Iris\Common\Form\DataTransformer
 * @author Paul Swift <paul.swift@barbon.com>
 */
class YearMonthDurationTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return array(
                'years' => 0,
                'months' => 0,
            );
        }

        $years = floor($value / 12);
        $months = $value % 12;

        $transform = array(
            'years' => $years,
            'months' => $months,
        );

        return $transform;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (
            !is_array($value) ||
            !isset($value['years']) ||
            !isset($value['months'])
        ) {
            throw new TransformationFailedException;
        }

        $transform = $value['years'] * 12 + $value['months'];

        return $transform;
    }
}
