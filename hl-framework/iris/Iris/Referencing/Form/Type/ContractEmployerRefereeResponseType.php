<?php

namespace Iris\Referencing\Form\Type;

use Iris\Common\Form\DataTransformer\YearMonthDurationTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ContractEmployerRefereeResponseType
 *
 * @package Iris\Referencing\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class ContractEmployerRefereeResponseType extends AbstractType implements StepTypeInterface
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
        // Minimum contract start date for back-end validation
        $minContractStartDate = new \DateTime();
        $minContractStartDate->sub(new \DateInterval('P100Y'));

        // Maximum contract start date for back-end validation
        $maxContractStartDate = new \DateTime();

        // Minimum and maximum contract start years for front end
        $minContractStartDateYear = $minContractStartDate->format('Y');
        $maxContractStartDateYear = $maxContractStartDate->format('Y');

        // Create array of years for front end, in order of starting with most recent year
        $yearRange = range($maxContractStartDateYear, $minContractStartDateYear);
        $contractStartYears = array_combine($yearRange, $yearRange);

        // Instantiate a data transformer to handle compound years+months <=> months field
        $yearMonthDurationTransformer = new YearMonthDurationTransformer();

        $builder
            ->add('employmentStartDate', 'date', array(
                'label' => 'Please confirm their employment contract start date',
                'years' => $contractStartYears,
                'empty_value' => array(
                    'year' => 'Year',
                    'month' => 'Month',
                    'day' => 'Day',
                ),
            ))
            ->add('jobTitle', 'text', array(
                'label' => 'Please confirm their current job title',
                'required' => false,
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
            ))
            // Contract duration, in years and months (transformed to months)
            ->add(
                $builder
                    ->create('contractDuration', new YearMonthDurationType(), array(
                        'label' => 'If contract, please state duration of contract:',
                    ))
                    ->addModelTransformer($yearMonthDurationTransformer)
            )
            ->add('annualIncome', new MoneyWithoutStringTransformerType(), array(
                'label' => 'Please confirm their gross basic annual income:',
                'currency' => 'GBP',
                'constraints' => array(
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
                    new Assert\NotNull(array(
                        'message' => 'Please confirm any regular overtime, bonuses and commission',
                    )),
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
                    new Assert\Regex(array('pattern' => '/^\w+/'))
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
        return 'contract_employer_referee_response';
    }
}