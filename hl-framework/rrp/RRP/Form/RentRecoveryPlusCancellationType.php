<?php

namespace RRP\Form;

use RRP\Model\RentRecoveryPlusCancellation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Class RentRecoveryPlusCancellationType
 *
 * @package RRP\Form
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusCancellationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('policyNumber', 'hidden')
            ->add('policyExpiresAt', 'hidden')
            ->add('policyEndAt', 'date', array(
                'label' => 'Policy End Date (dd/mm/yyyy)',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-provide' => 'datepicker',
                ),
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'fullValidation',
                    ))
                ),
            ))
            ->setMethod('GET')
        ;
    }

    /**
     * Validation function for optional constraints
     *
     * @param RentRecoveryPlusCancellation $cancellation
     * @param ExecutionContextInterface $context
     */
    public function checkEndDate($cancellation, ExecutionContextInterface $context)
    {
        $expiresAt = new \DateTime($cancellation->getPolicyExpiresAt());
        $endAt = new \DateTime($cancellation->getPolicyEndAt());
        $now = new \DateTime();
        $now->setTime(0, 0, 0);
        if ($endAt > $expiresAt) {
            $context->addViolationAt(
                'policyEndAt',
                'Requested date exceeds existing end date of ' . $expiresAt->format('d-m-Y'),
                array(),
                null
            );
        }
        else if ($endAt < $now) {
            $context->addViolationAt(
                'policyEndAt',
                'Requested date is in the past',
                array(),
                null
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $optionsResolver)
    {
        $optionsResolver->setDefaults(array(
            'data_class' => 'RRP\Model\RentRecoveryPlusCancellation',
            'constraints' =>
                array(
                    new Assert\Callback(
                        array('methods' =>
                            array(
                                array($this, 'checkEndDate')
                            )
                        )
                    )
                ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rent_recovery_plus_cancellation';
    }
}