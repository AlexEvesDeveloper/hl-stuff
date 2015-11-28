<?php

namespace Iris\Referencing\Form\Type;

use Iris\Common\Form\DataTransformer\YearMonthDurationTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LettingRefereeResponseType
 *
 * @package Iris\Referencing\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class LettingRefereeResponseType extends AbstractType implements StepTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Instantiate a data transformer to handle compound years+months <=> months field
        $yearMonthDurationTransformer = new YearMonthDurationTransformer();

        $builder
            // Duration of stay, in years and months (transformed to months)
            ->add(
                $builder
                    ->create('applicantStayDuration', new YearMonthDurationType(), array(
                        'label' => 'How long have they been a tenant in your property?',
                        'required' => false,
                    ))
                    ->addModelTransformer($yearMonthDurationTransformer)
            )
            ->add('monthlyRent', new MoneyWithoutStringTransformerType(), array(
                'label' => 'What is their current monthly rental figure?',
                'currency' => 'GBP',
                'required' => false,
                'constraints' => array(
                    new Assert\NotNull(array(
                        'message' => 'Please confirm the monthly rent',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                        'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                    ))
                ),
            ))
            ->add('hasRentPaidPromptly', new BooleanExpandedType(), array(
                'label' => 'Has the rent always been paid promptly?',
                'required' => false,
            ))
            ->add('isSatisfied', new BooleanExpandedType(), array(
                'label' => 'Has the tenancy been conducted in a satisfactory manner?',
                'required' => false,
            ))
            ->add('isGoodTenant', new BooleanExpandedType(), array(
                'label' => 'Do you consider the applicant to be a good tenant?',
                'required' => false,
            ))
            ->add('refereeName', 'text', array(
                'label' => 'Your Name / Position',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Regex(array('pattern' => '/^\w+/')),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'letting_referee_response';
    }
}