<?php

namespace Iris\Referencing\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RootStepType
 *
 * @package Iris\Referencing\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class RootStepType extends AbstractType
{
    /**
     * @var StepTypeInterface
     */
    private $stepType;

    /**
     * Constructor - expects to be passed a form type that implements StepTypeInterface.
     *
     * @param StepTypeInterface $stepType
     */
    public function __construct(StepTypeInterface $stepType)
    {
        $this->stepType = $stepType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('step', $this->stepType, $options['stepTypeOptions'])
        ;

        if (!$options['removeBack']) {
            $builder->add('back', 'submit');
        }

        if (!$options['removeNext']) {
            $builder->add('next', 'submit');
        }

        if (!$options['removeSubmit']) {
            $builder->add('submit', 'submit');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'step';
    }

    /**
     * Setter for private stepType property, fluent interface.
     *
     * @param \Iris\Referencing\Form\Type\StepTypeInterface $stepType
     * @return $this
     */
    public function setStepType(StepTypeInterface $stepType)
    {
        $this->stepType = $stepType;
        return $this;
    }

    /**
     * Getter for private stepType property.
     *
     * @return \Iris\Referencing\Form\Type\StepTypeInterface
     */
    public function getStepType()
    {
        return $this->stepType;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setOptional(array(
                'stepTypeOptions',
                'removeBack',
                'removeNext',
                'removeSubmit'
            ))
            ->setDefaults(array(
                'stepTypeOptions' => array(),
                'removeBack' => false,
                'removeNext' => false,
                'removeSubmit' => true,
            ))
        ;
    }

    /**
     * Are we only changing this form? Not progressing through the wizard?
     *
     * @deprecated
     * @return bool
     */
    private function isChangingOnlyThisForm()
    {
        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = \Zend_Registry::get('iris_container')->get('request');

        if ($request->query->has('changeOnlyThisForm')) {
            return (bool)$request->query->get('changeOnlyThisForm');
        }

        return false;
    }
}