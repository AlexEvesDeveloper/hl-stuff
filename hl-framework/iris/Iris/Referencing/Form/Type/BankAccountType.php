<?php

namespace Iris\Referencing\Form\Type;

use Iris\Validator\Constraints\BankAccountNumber;
use Iris\Validator\Constraints\BankSortCode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormInterface;

/**
 * Class BankAccountType
 *
 * @package Iris\Referencing\Form\Type
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class BankAccountType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Note: must be text type to prevent conversion of
            // integers i.e. stop 0001 2345 converting to 12345
            ->add('accountNumber', 'text', array(
                'label' => 'Bank Account Number',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'bankaccount',
                    )),
                    new BankAccountNumber(array(
                        'groups' => 'bankaccount',
                        'message' => 'Please enter a valid 8 number UK account number, e.g. "11223344"',
                    ))
                )
            ))

            // Note: must be a text type in order to allow hyphens
            // i.e. 123456
            ->add('accountSortcode', 'text', array(
                'label' => 'Bank Sort Code',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => 'bankaccount',
                    )),
                    new BankSortCode(array(
                        'groups' => 'bankaccount',
                        'message' => 'Please enter a valid 6 number UK sort code without hyphens, e.g. "228374"',
                    ))
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Barbondev\IRISSDK\Common\Model\BankAccount',

            /**
             * Customise the validation group used depending on submitted data
             *
             * @param FormInterface $form
             * @return array
             */
            'validation_groups' => function(FormInterface $form) {
                $accountNoData = $form->get('accountNumber')->getData();
                $accountSortCodeData = $form->get('accountSortcode')->getData();

                // Note: Only validate when either field is supplied.
                // If neither field is supplied, no validation should occur.
                if (!empty($accountNoData) || !empty($accountSortCodeData)) {
                    return array('bankaccount');
                }

                return array();
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bank_account';
    }
}
