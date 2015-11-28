<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

/**
 * todo: this class is a copy of TenancyAgreementType except without the Application collection. This can be refactored into
 * one class, and have the collection added dynamically. Other suggestions welcome.
 *
 * Tenancy Agreement No Marketing Form
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class TenancyAgreementNoMarketingType extends AbstractType
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
            ->add('agree', 'submit')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tenancy_agreement_no_marketing';
    }
}
