<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Type;

use Barbon\HostedApi\AppBundle\Form\Common\Type\AddressType;
use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\DateRange;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ReferencingCaseType extends AbstractType
{
    /**
     * @var FormTypeInterface
     */
    private $applicationType;

    /**
     * @var FormTypeInterface
     */
    private $prospectiveLandlordType;

    /**
     * @var EventSubscriberInterface
     */
    private $rentShareBridgeSubscriber;

    /**
     * @var EventSubscriberInterface
     */
    private $rentGuaranteeOfferingTypeBridgeSubscriber;

    /**
     * @var EventSubscriberInterface
     */
    private $propertyLetTypeBridgeSubscriber;

    /**
     * @var ChoiceListInterface
     */
    private $rentGuaranteeOfferingLookup;

    /**
     * @var ChoiceListInterface
     */
    private $propertyLetTypeLookup;

    /**
     * @var ChoiceListInterface
     */
    private $propertyTypeLookup;

    /**
     * @var ChoiceListInterface
     */
    private $propertyBuildInRangeLookup;


    /**
     * Constructor
     *
     * @param FormTypeInterface $applicationType
     * @param FormTypeInterface $prospectiveLandlordType
     * @param EventSubscriberInterface $rentShareBridgeSubscriber
     * @param EventSubscriberInterface $rentGuaranteeOfferingTypeBridgeSubscriber
     * @param EventSubscriberInterface $propertyLetTypeBridgeSubscriber
     * @param ChoiceListInterface $rentGuaranteeOfferingLookup
     * @param ChoiceListInterface $propertyLetTypeLookup
     * @param ChoiceListInterface $propertyTypeLookup
     * @param ChoiceListInterface $propertyBuildInRangeLookup
     */
    public function __construct(
        FormTypeInterface $applicationType,
        FormTypeInterface $prospectiveLandlordType,
        EventSubscriberInterface $rentShareBridgeSubscriber,
        EventSubscriberInterface $rentGuaranteeOfferingTypeBridgeSubscriber,
        EventSubscriberInterface $propertyLetTypeBridgeSubscriber,
        ChoiceListInterface $rentGuaranteeOfferingLookup,
        ChoiceListInterface $propertyLetTypeLookup,
        ChoiceListInterface $propertyTypeLookup,
        ChoiceListInterface $propertyBuildInRangeLookup
    )
    {
        $this->applicationType = $applicationType;
        $this->prospectiveLandlordType = $prospectiveLandlordType;

        $this->rentShareBridgeSubscriber = $rentShareBridgeSubscriber;
        $this->rentGuaranteeOfferingTypeBridgeSubscriber = $rentGuaranteeOfferingTypeBridgeSubscriber;
        $this->propertyLetTypeBridgeSubscriber = $propertyLetTypeBridgeSubscriber;

        $this->rentGuaranteeOfferingLookup = $rentGuaranteeOfferingLookup;
        $this->propertyLetTypeLookup = $propertyLetTypeLookup;
        $this->propertyTypeLookup = $propertyTypeLookup;
        $this->propertyBuildInRangeLookup = $propertyBuildInRangeLookup;
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
            ->add('address', new AddressType())
            ->add(
                $builder->create('totalRent', 'money', array(
                    'currency' => 'GBP',
                    'constraints' => array(
                        new Constraints\GreaterThan(array('value' => 0)),
                        new Constraints\NotBlank(),
                        new Constraints\Regex(array(
                            'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                            'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                        ))
                    ),
                    'label' => 'Total Rent (£ Per Calendar Month)',
                ))->addEventSubscriber($this->rentShareBridgeSubscriber)
            )
            ->add(
                $builder->create('rentGuaranteeOfferingType', 'choice', array(
                    'choice_list' => $this->rentGuaranteeOfferingLookup,
                    'empty_value' => '- Please Select -',
                    'constraints' => array(
                        new Constraints\NotBlank(),
                    ),
                    'attr' => array(
                        'class' => 'form-refresh'
                    ),
                    'label' => 'How is Rent Guarantee offered to your landlord?',
                ))->addEventSubscriber($this->rentGuaranteeOfferingTypeBridgeSubscriber)
            )
            ->add(
                $builder->create('propertyLetType', 'choice', array(
                    'choice_list' => $this->propertyLetTypeLookup,
                    'empty_value' => '- Please Select -',
                    'constraints' => array(
                        new Constraints\NotBlank(),
                    ),
                    'attr' => array(
                        'class' => 'form-refresh'
                    ),
                    'label' => 'Property Let Type',
                ))->addEventSubscriber($this->propertyLetTypeBridgeSubscriber)
            )
            ->add('numberOfBedrooms', 'integer', array(
                'constraints' => array(
                    new Constraints\GreaterThanOrEqual(array('value' => 0)),
                    new Constraints\NotBlank(),
                ),
                'label' => 'How many bedrooms does the property have?',
                'attr' => array(
                    'min' => 0,
                ),
            ))
            ->add('propertyType', 'choice', array(
                'choice_list' => $this->propertyTypeLookup,
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'label' => 'Property Type',
            ))
            ->add('propertyBuiltInRangeType', 'choice', array(
                'choice_list' => $this->propertyBuildInRangeLookup,
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'label' => 'When was the property built?',
            ))
            ->add('tenancyTerm', 'choice', array(
                'choices' => array(
                    '6' => '6',
                    '12' => '12',
                    '18' => '18',
                    '24' => '24',
                ),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'label' => 'Tenancy Term in Months',
            ))
            ->add('numberOfTenants', 'choice', array(
                'choices' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                ),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'label' => 'Number of Tenants',
            ))
            ->add('tenancyStartDate', 'date', array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new DateRange(array(
                        'min' => '00:00:00',
                        'max' => '+200 DAY',
                        'minMessage' => 'The tenancy start date cannot be in the past.',
                        'maxMessage' => 'The tenancy start date cannot be more than 200 days in the future.',
                        'invalidMessage' => 'The tenancy start date must be a valid date',
                    )),
                ),
                'data' => new \DateTime(),
                'label' => 'Tenancy Start Date',
                'attr' => array(
                    'data-provide' => 'datepicker',
                    'data-start-date' => date('d/m/Y')
                ),
            ))
            ->add('prospectiveLandlord', $this->prospectiveLandlordType)
            ->add('applications', 'collection', array(
                'type' => $this->applicationType,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'cascade_validation' => true,
                'prototype_name' => '__applicationname__',
                'options' => array(
                    'label' => false,
                ),
                'constraints' => array(
                    new Constraints\Count(array(
                        'min' => 1,
                        'minMessage' => 'Submission of a reference requires at least one applicant',
                    )),
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
            'data_class' => 'Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'referencing_case';
    }
}
