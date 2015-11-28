<?php

namespace Iris\Referencing\Form\Type;

use Iris\Common\Form\DataTransformer\BooleanExpandedModelTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BooleanExpandedType
 *
 * @package Iris\Referencing\Form\Type
 * @author Ashley J. Dawson
 */
class BooleanExpandedType extends AbstractType
{
    /**
     * Field name constant
     */
    const FIELD_NAME = 'value';

    /**
     * @var array Choices
     */
    public static $choices = array(
        1 => 'Yes',
        0 => 'No',
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::FIELD_NAME, 'choice', array(
                'choices' => self::$choices,
                'expanded' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->addModelTransformer(new BooleanExpandedModelTransformer())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'boolean_expanded';
    }
}