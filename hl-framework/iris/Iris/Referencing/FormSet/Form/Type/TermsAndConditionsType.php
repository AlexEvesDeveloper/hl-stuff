<?php

namespace Iris\Referencing\FormSet\Form\Type;

use Iris\Common\Form\DataTransformer\InverseBooleanTransformer;
use Iris\Referencing\Form\Type\StepTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TermsAndConditionsType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class TermsAndConditionsType extends AbstractFormStepType implements StepTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $inverseBooleanTransformer = new InverseBooleanTransformer();

        $builder
            ->add('application_correct', 'checkbox', array(
                'mapped' => false,
                'constraints' => array(
                    new Assert\True(array(
                        'message' => 'You must agree to this statement to progress',
                    ))
                ),
            ))
            ->add('referee_contact', 'checkbox', array(
                'mapped' => false,
                'constraints' => array(
                    new Assert\True(array(
                        'message' => 'You must agree to this statement to progress',
                    ))
                ),
            ))
            ->add(
                // Inverse boolean transformer, to turn a true value into a false value
                // - this field is check to opt out (false)
                $builder->create('canContactApplicantByPhoneAndPost', 'checkbox')
                    ->addModelTransformer($inverseBooleanTransformer)
            )
            ->add('canContactApplicantBySMSAndEmail', 'checkbox', array(
                'required' => false,
            ))
            ->add('agree_terms', 'checkbox', array(
                'mapped' => false,
                'constraints' => array(
                    new Assert\True(array(
                        'message' => 'You must agree to this statement to progress',
                    ))
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'terms_and_conditions';
    }
}
