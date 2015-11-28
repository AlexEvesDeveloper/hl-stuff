<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Type;

use Barbon\HostedApi\AppBundle\Form\Reference\EventListener\RentShareConstraintSubscriber;
use Barbon\HostedApi\AppBundle\Form\Reference\ValidationGroup\ReferencingApplicationValidationGroupSelector;
use Barbon\HostedApi\AppBundle\Form\Common\EventListener\ApplicantEmailConstraintSubscriber;
use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\DateRange;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ReferencingGuarantorType extends AbstractType
{
    /**
     * @var EventSubscriberInterface
     */
    private $notApplicantEmailBridgeSubscriber;

    /**
     * @var ApplicantEmailConstraintSubscriber
     */
    private $notProspectiveLandlordEmailConstraintSubscriber;

    /**
     * @var RentShareConstraintSubscriber
     */
    private $rentShareConstraintSubscriber;

    /**
     * @var ChoiceListInterface
     */
    private $titlesLookup;

    /**
     * @var ChoiceListInterface
     */
    private $completionMethodLookup;

    /**
     * @var RequestStack
     */
    private $requestStack;


    /**
     * Constructor
     *
     * @param EventSubscriberInterface $notApplicantEmailBridgeSubscriber
     * @param ApplicantEmailConstraintSubscriber $notProspectiveLandlordEmailConstraintSubscriber
     * @param RentShareConstraintSubscriber $rentShareConstraintSubscriber
     * @param ChoiceListInterface $titlesLookup
     * @param ChoiceListInterface $completionMethodLookup
     * @param RequestStack $requestStack
     */
    public function __construct(
        EventSubscriberInterface $notApplicantEmailBridgeSubscriber,
        ApplicantEmailConstraintSubscriber $notProspectiveLandlordEmailConstraintSubscriber,
        RentShareConstraintSubscriber $rentShareConstraintSubscriber,
        ChoiceListInterface $titlesLookup,
        ChoiceListInterface $completionMethodLookup,
        RequestStack $requestStack
    )
    {
        // Choice field lookup
        $this->titlesLookup = $titlesLookup;
        $this->completionMethodLookup = $completionMethodLookup;

        $this->rentShareConstraintSubscriber = $rentShareConstraintSubscriber;
        $this->notApplicantEmailBridgeSubscriber = $notApplicantEmailBridgeSubscriber;
        $this->notProspectiveLandlordEmailConstraintSubscriber = $notProspectiveLandlordEmailConstraintSubscriber;
        $this->requestStack = $requestStack;
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
                        'groups' => array('fullValidation'),
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
                    'data-provide' => 'datepicker',
                    'data-end-date' => date('d/m/Y')
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
            ->addEventSubscriber($options['guarantor_decorator'])
            ->addEventSubscriber($this->notProspectiveLandlordEmailConstraintSubscriber)
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                // Disable form validation on ajax requests by stopping all listeners listening to POST_SUBMIT with a lower weight priority than this, including the validationListener
                // Note: any listeners listening to POST_SUBMIT for AJAX requests, after this listener, are disabled as a result of this call
                if ($this->requestStack->getCurrentRequest()->isXmlHttpRequest()) {
                    $event->stopPropagation();
                }
            }, 1)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingGuarantor',
            'allow_extra_fields' => true,
            'validation_groups' => function(FormInterface $form) {
                $validationGroup = new ReferencingApplicationValidationGroupSelector();
                return $validationGroup->chooseGroups($form);
            },
            'guarantor_decorator' => null,
        ));
        
        $resolver->setAllowedTypes(array(
            'guarantor_decorator' => array('null', 'Barbon\HostedApi\AppBundle\Form\Reference\EventListener\ReferencingGuarantorDecoratorSubscriber'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'referencing_guarantor';
    }
}
