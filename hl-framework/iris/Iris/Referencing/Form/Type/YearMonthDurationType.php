<?php

namespace Iris\Referencing\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class YearMonthDurationType
 *
 * @package Iris\Referencing\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class YearMonthDurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('years', 'integer', array(
                'attr' => array(
                    'min' => 0,
                ),
                'constraints' => array(
                    new Assert\GreaterThanOrEqual(array('value' => 0)),
                    new Assert\LessThanOrEqual(array('value' => 110)),
                ),
            ))
            ->add('months', 'integer', array(
                'attr' => array(
                    'min' => 0,
                    'max' => 11,
                ),
                'constraints' => array(
                    new Assert\GreaterThanOrEqual(array('value' => 0)),
                    new Assert\LessThanOrEqual(array('value' => 11)),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'year_month_duration';
    }
}
