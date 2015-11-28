<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Type;

use Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup\FinancialRefereeValidationGroupSelector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

final class FinancialRefereeType extends AbstractType
{
    /**
     * @var ChoiceListInterface
     */
    private $financialRefereeTypeLookup;

    /**
     * @var EventSubscriberInterface
     */
    private $decoratorSubscriber;

    /**
     * @var EventSubscriberInterface
     */
    private $notApplicantPhoneConstraintSubscriber;

    /**
     * @var EventSubscriberInterface
     */
    private $notApplicantEmailConstraintSubscriber;


    /**
     * Constructor
     *
     * @param ChoiceListInterface $financialRefereeTypeLookup
     * @param EventSubscriberInterface $decoratorSubscriber
     * @param EventSubscriberInterface $notApplicantPhoneConstraintSubscriber
     * @param EventSubscriberInterface $notApplicantEmailConstraintSubscriber
     */
    public function __construct(
        ChoiceListInterface $financialRefereeTypeLookup,
        EventSubscriberInterface $decoratorSubscriber,
        EventSubscriberInterface $notApplicantPhoneConstraintSubscriber,
        EventSubscriberInterface $notApplicantEmailConstraintSubscriber
    )
    {
        $this->financialRefereeTypeLookup = $financialRefereeTypeLookup;
        $this->decoratorSubscriber = $decoratorSubscriber;
        $this->notApplicantPhoneConstraintSubscriber = $notApplicantPhoneConstraintSubscriber;
        $this->notApplicantEmailConstraintSubscriber = $notApplicantEmailConstraintSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isVisible', 'hidden', array(
                'required' => false,
                'attr' => array(
                    'class' => 'is-visible'
                )
            ))
            ->add($builder->create('financialRefereeType', 'choice', array(
                'choice_list' => $this->financialRefereeTypeLookup, // todo: limit choices available depending on referee status
                'empty_value' => '- Please Select -',
                'required' => false,
                'attr' => array(
                    'class' => 'form-refresh'
                ),
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => ' This should not be blank',
                    )),
                ),
            ))->addEventSubscriber($this->decoratorSubscriber))
            ->addEventSubscriber($this->notApplicantPhoneConstraintSubscriber)
            ->addEventSubscriber($this->notApplicantEmailConstraintSubscriber)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Barbon\HostedApi\AppBundle\Form\Common\Model\FinancialReferee',

            'validation_groups' => function(FormInterface $form) {
                $validationGroup = new FinancialRefereeValidationGroupSelector();
                return $validationGroup->chooseGroups($form);
            },

            'allow_extra_fields' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'financial_referee';
    }
}
