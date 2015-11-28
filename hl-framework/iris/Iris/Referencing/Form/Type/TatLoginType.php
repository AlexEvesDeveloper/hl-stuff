<?php

namespace Iris\Referencing\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TatLoginType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class TatLoginType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Minimum date of birth date for back-end validation
        $minDobDate = new \DateTime();
        $minDobDate->sub(new \DateInterval('P100Y'));

        // Maximum date of birth date for back-end validation
        $maxDobDate = new \DateTime();
        // todo: Uncomment, only here for testing as test applicant's DOB is in 2014!
        //$maxDobDate->sub(new \DateInterval('P16Y'));

        // Minimum and maximum date of birth years for front end
        $minDobDateYear = $minDobDate->format('Y');
        $maxDobDateYear = $maxDobDate->format('Y');

        // Create array of years for front end, in order of starting with most recent year
        $yearRange = range($maxDobDateYear, $minDobDateYear);
        $dobYears = array_combine($yearRange, $yearRange);

        $builder
            ->add('agentSchemeNumber', 'integer', array(
                'constraints' => array(
                    // todo: Add constraints
                )
            ))
            ->add('applicationReferenceNumber', 'text', array(
                'label' => 'HomeLet Reference Number',
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/^[A-Z]+\d+$/',
                        'match' => true,
                        'message' => 'HomeLet Reference Number must start with one or more capital letters and end with one or more digits',
                    ))
                )
            ))
            ->add('applicantBirthDate', 'date', array(
                'label' => 'Date of birth',
                'constraints' => array(
                    new Assert\GreaterThanOrEqual(array('value' => $minDobDate)),
                    new Assert\LessThanOrEqual(array('value' => $maxDobDate)),
                ),
                'years' => $dobYears,
                'empty_value' => array(
                    'year' => 'Year',
                    'month' => 'Month',
                    'day' => 'Day',
                ),
            ))
            ->add('Login', 'submit')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tat_login';
    }
}