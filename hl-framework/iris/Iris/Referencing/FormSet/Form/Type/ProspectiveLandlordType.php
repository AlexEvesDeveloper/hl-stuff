<?php

namespace Iris\Referencing\FormSet\Form\Type;

use Iris\Referencing\Form\Type\LandlordType;
use Iris\Referencing\Form\Type\StepTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ProspectiveLandlordType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ProspectiveLandlordType extends AbstractFormStepType implements StepTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prospectiveLandlord', new LandlordType())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'prospective_landlord';
    }
}