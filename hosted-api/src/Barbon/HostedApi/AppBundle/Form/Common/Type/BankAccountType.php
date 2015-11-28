<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Type;

use Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup\BankAccountValidationGroupSelector;
use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\BankAccountNumber;
use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\BankSortCode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

final class BankAccountType extends AbstractType
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
                    new Constraints\NotBlank(array(
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
                    new Constraints\NotBlank(array(
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
            'data_class' => 'Barbon\HostedApi\AppBundle\Form\Common\Model\BankAccount',

            'validation_groups' => function(FormInterface $form) {
                $validationGroup = new BankAccountValidationGroupSelector();
                return $validationGroup->chooseGroups($form);
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