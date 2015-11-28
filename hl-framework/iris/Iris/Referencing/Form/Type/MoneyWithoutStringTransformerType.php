<?php

namespace Iris\Referencing\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MoneyWithoutStringTransformerType
 *
 * @package Iris\Referencing\Form\Type
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class MoneyWithoutStringTransformerType extends MoneyType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // I don't call the parent buildForm method because I don't want the
        // localised view transformer to apply.
    }
}
