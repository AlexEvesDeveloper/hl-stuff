<?php

namespace Iris\Referencing\Form\Type;

use Iris\Common\Form\DataTransformer\YearMonthDurationTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class EmployerRefereeResponseType
 *
 * @package Iris\Referencing\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class EmployerRefereeResponseType extends AbstractType implements StepTypeInterface
{
    /**
     * Permanent employment ID, mapped to categories from EMPLOYMENT_TYPE lookup.
     */
    const EMPLOYMENT_TYPE_PERMANENT = 1;

    /**
     * Contract employment ID, mapped to categories from EMPLOYMENT_TYPE lookup.
     */
    const EMPLOYMENT_TYPE_CONTRACT = 2;

    /**
     * Full time job ID, mapped to categories from JOB_TYPE lookup.
     */
    const JOB_TYPE_FULL_TIME = 1;

    /**
     * Part time job ID, mapped to categories from JOB_TYPE lookup.
     */
    const JOB_TYPE_PART_TIME = 2;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $pivotYear = (int) date('Y');

        // Create array of years for front end
        // For start date, years are in order of starting with most recent year
        $yearRange = range($pivotYear + 1, $pivotYear - 100);
        $employmentStartYears = array_combine($yearRange, $yearRange);

        $yearRange = range($pivotYear, $pivotYear + 4);
        $employmentEndYears = array_combine($yearRange, $yearRange);

        $yearMonthDurationTransformer = new YearMonthDurationTransformer();

        $builder
            ->add('employmentStartDate', 'date', array(
                'label' => 'Please confirm their employment contract start date',
                'years' => $employmentStartYears,
                'empty_value' => array(
                    'year' => 'Year',
                    'month' => 'Month',
                    'day' => 'Day',
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('jobTitle', 'text', array(
                'label' => 'Please confirm their current job title',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('jobType', 'choice', array(
                'choices' => array(
                    self::JOB_TYPE_FULL_TIME => 'Full time',
                    self::JOB_TYPE_PART_TIME => 'Part time',
                ),
                'empty_value' => false,
                'expanded' => true,
                'label' => 'Is this position full time or part time?',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('employmentType', 'choice', array(
                'choices' => array(
                    self::EMPLOYMENT_TYPE_PERMANENT => 'Permanent',
                    self::EMPLOYMENT_TYPE_CONTRACT => 'Contract',
                ),
                'empty_value' => false,
                'expanded' => true,
                'label' => 'Is this position permanent or contract?',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add(
                $builder
                    ->create('contractDuration', new YearMonthDurationType(), array(
                        'label' => 'What is the length of the contract?',
                    ))
                    ->addModelTransformer($yearMonthDurationTransformer)
            )
            ->add('annualIncome', new MoneyWithoutStringTransformerType(), array(
                'label' => 'Please confirm their gross basic annual income:',
                'currency' => 'GBP',
                'constraints' => array(
                    new Assert\GreaterThan(array(
                        'value' => 0,
                        'message' => '£0 cannot be entered as the applicants income, please enter the applicants gross annual income. If you are unable to do this please call us on 0845 155 8811',
                    )),
                    new Assert\NotNull(array(
                        'message' => 'Please confirm the gross basic annual income',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                        'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                    ))
                ),
            ))
            ->add('commission', new MoneyWithoutStringTransformerType(), array(
                'label' => 'Any regular overtime / bonuses / commission?',
                'currency' => 'GBP',
                'required' => false,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                        'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                    ))
                ),
            ))
            ->add('isIncomeStable', new BooleanExpandedType(), array(
                'label' => sprintf('Would you expect the applicant\'s income to remain at or above this level for the next %s months?', $options['tenancyTerm']),
                'required' => false,
            ))
            ->add('employmentEndDate', 'date', array(
                'label' => 'If the applicant\'s employment is due to end, please provide an end date:',
                'years' => $employmentEndYears,
                'empty_value' => array(
                    'year' => 'Year',
                    'month' => 'Month',
                    'day' => 'Day',
                ),
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array(
                'tenancyTerm',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'employer_referee_response';
    }
}