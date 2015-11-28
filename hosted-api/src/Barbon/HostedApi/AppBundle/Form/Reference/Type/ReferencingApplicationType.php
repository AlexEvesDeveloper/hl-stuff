<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Type;

use Barbon\HostedApi\AppBundle\Form\Reference\EventListener\ReferencingGuarantorDecoratorBridgeSubscriber;
use Barbon\HostedApi\AppBundle\Form\Reference\EventListener\RentShareConstraintSubscriber;
use Barbon\HostedApi\AppBundle\Form\Reference\ValidationGroup\ReferencingApplicationValidationGroupSelector;
use Barbon\HostedApi\AppBundle\Form\Common\EventListener\ApplicantEmailConstraintSubscriber;
use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\DateRange;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ReferencingApplicationType extends AbstractType
{
    /**
     * @var EventSubscriberInterface
     */
    private $applicationDecorateSubscriber;

    /**
     * @var EventSubscriberInterface
     */
    private $notApplicantEmailBridgeSubscriber;

    /**
     * @var ApplicantEmailConstraintSubscriber
     */
    private $notProspectiveLandlordEmailConstraintSubscriber;

    /**
     * @var ReferencingGuarantorDecoratorBridgeSubscriber
     */
    private $guarantorDecoratorBridgeSubscriber;

    /**
     * @var RentShareConstraintSubscriber
     */
    private $rentShareConstraintSubscriber;
    
    /**
     * @var ChoiceListInterface
     */
    private $productLookup;

    /**
     * @var ChoiceListInterface
     */
    private $titlesLookup;

    /**
     * @var ChoiceListInterface
     */
    private $completionMethodLookup;

    /**
     * @var FormTypeInterface
     */
    private $guarantorType;

    /**
     * Constructor
     *
     * @param FormTypeInterface $guarantorType
     * @param EventSubscriberInterface $applicationDecorateSubscriber
     * @param EventSubscriberInterface $notApplicantEmailBridgeSubscriber
     * @param ApplicantEmailConstraintSubscriber $notProspectiveLandlordEmailConstraintSubscriber
     * @param RentShareConstraintSubscriber $rentShareConstraintSubscriber
     * @param ReferencingGuarantorDecoratorBridgeSubscriber $guarantorDecoratorBridgeSubscriber
     * @param ChoiceListInterface $productLookup
     * @param ChoiceListInterface $titlesLookup
     * @param ChoiceListInterface $completionMethodLookup
     */
    public function __construct(
        FormTypeInterface $guarantorType,
        EventSubscriberInterface $applicationDecorateSubscriber,
        EventSubscriberInterface $notApplicantEmailBridgeSubscriber,
        ApplicantEmailConstraintSubscriber $notProspectiveLandlordEmailConstraintSubscriber,
        RentShareConstraintSubscriber $rentShareConstraintSubscriber,
        ReferencingGuarantorDecoratorBridgeSubscriber $guarantorDecoratorBridgeSubscriber,
        ChoiceListInterface $productLookup,
        ChoiceListInterface $titlesLookup,
        ChoiceListInterface $completionMethodLookup
    )
    {
        // Choice field lookup
        $this->productLookup = $productLookup;
        $this->titlesLookup = $titlesLookup;
        $this->completionMethodLookup = $completionMethodLookup;

        $this->applicationDecorateSubscriber = $applicationDecorateSubscriber;
        $this->notApplicantEmailBridgeSubscriber = $notApplicantEmailBridgeSubscriber;
        $this->rentShareConstraintSubscriber = $rentShareConstraintSubscriber;
        $this->guarantorDecoratorBridgeSubscriber = $guarantorDecoratorBridgeSubscriber;
        $this->notProspectiveLandlordEmailConstraintSubscriber = $notProspectiveLandlordEmailConstraintSubscriber;
        
        $this->guarantorType = $guarantorType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $guarantorDecoratorBridgeSubscriber = clone $this->guarantorDecoratorBridgeSubscriber;

        $builder
            ->add('isVisible', 'hidden', array(
                'required' => false,
                'attr' => array(
                    'class' => 'is-visible'
                )
            ))
            ->add($builder->create('productId', 'choice', array(
                'label' => 'Product',
                'choice_list' => $this->productLookup,
                'empty_value' => '- Please Select -',
                'attr' => array(
                    'class' => 'form-refresh'
                ),
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Please select a product',
                    )),
                ),
            ))->addEventSubscriber($guarantorDecoratorBridgeSubscriber))
            ->add('title', 'choice', array(
                'choice_list' => $this->titlesLookup,
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Please select a title',
                    )),
                ),
            ))
            ->add('firstName', 'text', array(
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Please enter first name',
                    )),
                    new Constraints\Regex(array(
                        'pattern' => '/^[-a-zA-Z0-9\w]+$/',
                        'message' => 'Please enter alphanumeric characters and spaces only',
                    ))
                )
            ))
            ->add('middleName', 'text', array(
                'required' => false,
                'constraints' => array(
                    new Constraints\Regex(array(
                        'pattern' => '/^[ -a-zA-Z0-9\w]*$/',
                        'message' => 'Please enter alphanumeric characters and spaces only',
                    ))
                ),
            ))
            ->add('lastName', 'text', array(
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Please enter a last name',
                    )),
                    new Constraints\Regex(array(
                        'pattern' => '/^[ -a-zA-Z0-9\w]+$/',
                        'message' => 'Please enter alphanumeric characters and spaces only',
                    ))
                )
            ))
            ->add('otherName', 'text', array(
                'required' => false,
                'constraints' => array(
                    new Constraints\Regex(array(
                        'pattern' => '/^[a-zA-Z0-9\w]*$/',
                        'message' => 'Please enter alphanumeric characters and spaces only',
                    ))
                ),
            ))
            ->add('birthDate', 'birthday', array(
                'placeholder' => '--',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new DateRange(array(
                        'min' => '-121 YEARS',
                        'max' => '-18 YEARS',
                        'maxMessage' => 'Applicant must be older than 18 years of age',
                    )),
                ),
                'attr' => array(
                    'data-provide' => 'datepicker'
                ),
            ))
            ->add($builder->create('email', 'email', array(
                'label' => 'Email Address',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Email(array(
                        'message' => 'Please provide a valid email address'
                    )),
                    $this->notProspectiveLandlordEmailConstraintSubscriber->getConstraint(),
                )
            ))->addEventSubscriber($this->notApplicantEmailBridgeSubscriber))
            ->add($builder->create('rentShare', 'money', array(
                'label' => 'Share of Rent',
                'currency' => 'GBP',
                'constraints' => array(
                    $this->rentShareConstraintSubscriber->getRentShareConstraint()
                ),
            ))->addEventSubscriber($this->rentShareConstraintSubscriber))
            ->add('completionMethod', 'choice', array(
                'choice_list' => $this->completionMethodLookup,
                'empty_value' => '- Please Select -',
                'attr' => array(
                    'class' => 'form-refresh'
                ),
                'constraints' => array(
                    new Constraints\NotBlank()
                )
            ))
            ->add('guarantors', 'collection', array(
                'type' => $this->guarantorType,
                'prototype_name' => '__guarantorname__',
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'cascade_validation' => true,
                'options' => array(
                    'label' => false,
                    'guarantor_decorator' => $guarantorDecoratorBridgeSubscriber->getGuarantorDecorator()
                ),
            ))
            ->addEventSubscriber($this->applicationDecorateSubscriber)
            ->addEventSubscriber($this->notProspectiveLandlordEmailConstraintSubscriber)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingApplication',
            'allow_extra_fields' => true,
            'validation_groups' => function(FormInterface $form) {
                $validationGroup = new ReferencingApplicationValidationGroupSelector();
                return $validationGroup->chooseGroups($form);
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'referencing_application';
    }
}
