<?php

namespace Iris\Referencing\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PensionProviderRefereeResponseType
 *
 * @package Iris\Referencing\Form\Type
 * @author Paul Swift <paul.swift@barbon.com>
 */
class PensionProviderRefereeResponseType extends AbstractType implements StepTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('annualIncome', new MoneyWithoutStringTransformerType(), array(
                'label' => 'Please enter the Gross Pension received by the applicant',
                'currency' => 'GBP',
                'constraints' => array(
                    new Assert\NotNull(array(
                        'message' => 'Please confirm the gross pension',
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
        return 'pension_provider_referee_response';
    }
}