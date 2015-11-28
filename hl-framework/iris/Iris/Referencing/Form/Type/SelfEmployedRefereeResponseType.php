<?php

namespace Iris\Referencing\Form\Type;

use Iris\Common\Form\DataTransformer\YearMonthDurationTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SelfEmployedRefereeResponseType
 *
 * @package Iris\Referencing\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class SelfEmployedRefereeResponseType extends AbstractType implements StepTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Instantiate a data transformer to handle compound years+months <=> months field
        $yearMonthDurationTransformer = new YearMonthDurationTransformer();

        $builder
            ->add('isAccountancyServiceProvided', new BooleanExpandedType(), array(
                'label' => 'Do you provide an accountancy service to the above person?',
                'required' => false,
            ))
            // Accountancy service duration, in years and months (transformed to months)
            ->add(
                $builder
                    ->create('durationInAccountancyService', new YearMonthDurationType(), array(
                        'label' => 'How long have you acted in this capacity?',
                    ))
                    ->addModelTransformer($yearMonthDurationTransformer)
            )
            ->add('annualIncome', new MoneyWithoutStringTransformerType(), array(
                'label' => 'Please advise on the applicant\'s average annual income/drawings over the last 12 months',
                'currency' => 'GBP',
                'constraints' => array(
                    new Assert\NotNull(array(
                        'message' => 'Please confirm the average annual income over the last 12 months',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                        'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                    ))
                ),
            ))
            ->add('refereeName', 'text', array(
                'label' => 'Your Name / Position',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Regex(array('pattern' => '/^\w+/'))
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'self_employed_referee_response';
    }
}