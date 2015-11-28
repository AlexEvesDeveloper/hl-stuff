<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;

/**
 * Tenancy Agreement Form
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class TenancyAgreementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('application_correct', 'checkbox', array(
                'mapped' => false,
                'constraints' => array(
                    new Constraints\True(array(
                        'message' => 'You must agree to this statement to progress',
                    ))
                ),
            ))
            ->add('referee_contact', 'checkbox', array(
                'mapped' => false,
                'constraints' => array(
                    new Constraints\True(array(
                        'message' => 'You must agree to this statement to progress',
                    ))
                ),
            ))
            ->add('agree_terms', 'checkbox', array(
                'mapped' => false,
                'constraints' => array(
                    new Constraints\True(array(
                        'message' => 'You must agree to this statement to progress',
                    ))
                ),
            ))
            ->add('applications', 'collection', array(
                'type' => new ApplicationMarketingPreferencesType(array(
                    'user_type' => $options['user_type'])
                ),
                'options' => array(
                    'label' => false,
                ),
            ))
            ->add('agree', 'submit', array(
                'label' => 'Continue',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            // Default to 'agent' for backwards compatibility
            'user_type' => 'agent',
            'data_class' => 'Barbon\HostedApi\AppBundle\Form\Reference\Model\TenancyAgreement',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'multi_tenancy_agreement';
    }
}
