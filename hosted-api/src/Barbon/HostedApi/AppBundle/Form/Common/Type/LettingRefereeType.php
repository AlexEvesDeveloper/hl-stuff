<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Type;

use Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup\LettingRefereeValidationGroupSelector;
use Barbon\HostedApi\AppBundle\Form\Common\EventListener\LettingRefereeEmailConstraintSubscriber;
use Barbon\HostedApi\AppBundle\Form\Common\EventListener\LettingRefereePhoneConstraintSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

final class LettingRefereeType extends AbstractType
{
    /**
     * @var ChoiceListInterface
     */
    private $lettingRefereeLookup;

    /**
     * @var FormTypeInterface
     */
    private $addressType;

    /**
     * @var LettingRefereePhoneConstraintSubscriber
     */
    private $notApplicantPhoneConstraintSubscriber;

    /**
     * @var LettingRefereeEmailConstraintSubscriber
     */
    private $notApplicantEmailConstraintSubscriber;

    /**
     * Constructor
     *
     * @param ChoiceListInterface $lettingRefereeLookup
     * @param FormTypeInterface $addressType
     * @param LettingRefereePhoneConstraintSubscriber $notApplicantPhoneConstraintSubscriber
     * @param LettingRefereeEmailConstraintSubscriber $notApplicantEmailConstraintSubscriber
     */
    public function __construct(
        ChoiceListInterface $lettingRefereeLookup,
        FormTypeInterface $addressType,
        LettingRefereePhoneConstraintSubscriber $notApplicantPhoneConstraintSubscriber,
        LettingRefereeEmailConstraintSubscriber $notApplicantEmailConstraintSubscriber
    )
    {
        $this->lettingRefereeLookup = $lettingRefereeLookup;
        $this->addressType = $addressType;
        $this->notApplicantPhoneConstraintSubscriber = $notApplicantPhoneConstraintSubscriber;
        $this->notApplicantEmailConstraintSubscriber = $notApplicantEmailConstraintSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'choice_list' => $this->lettingRefereeLookup,
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Constraints\NotBlank(),
                )
            ))
            ->add('name', 'text', array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array(
                        'min' => 2,
                    ))
                )
            ))
            ->add('address', $this->addressType, array(
                'is_international_type' => true,
            ))
            ->add($builder->create('dayPhone', 'text', array(
                // Note: Not a required field, unless additional conditions are met.
                // Though if a value is supplied, it must be valid.
                'required' => false,
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'groups' => 'dayphone',
                        'message' => 'Please enter either a daytime or evening phone number',
                    )),
                    new Constraints\Length(array(
                        'groups' => 'dayphone',
                        'min' => 9,
                    )),
                    new Constraints\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}$/',
                        'message' => 'Phone number is invalid',
                    )),

                    $this->notApplicantPhoneConstraintSubscriber->getConstraint(),
                ),
                'label' => 'Telephone (day)',
            ))->addEventSubscriber($this->notApplicantPhoneConstraintSubscriber))
            ->add($builder->create('eveningPhone', 'text', array(
                // Note: Not a required field, unless additional conditions are met.
                // Though if a value is supplied, it must be valid.
                'required' => false,
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'groups' => 'eveningphone',
                        'message' => 'Please enter either a daytime or evening phone number',
                    )),
                    new Constraints\Length(array(
                        'groups' => 'eveningphone',
                        'min' => 9,
                    )),
                    new Constraints\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}$/',
                        'message' => 'Phone number is invalid',
                    )),

                    $this->notApplicantPhoneConstraintSubscriber->getConstraint(),
                ),
                'label' => 'Telephone (evening)',
            ))->addEventSubscriber($this->notApplicantPhoneConstraintSubscriber))
            ->add('fax', 'text', array(
                'required' => false,
                'constraints' => array(
                    new Constraints\Length(array(
                        'min' => 9,
                    )),
                    new Constraints\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}$/',
                        'message' => 'Fax number is invalid',
                    )),
                )
            ))
            ->add('email', 'email', array(
                // Note: Not a required field, unless additional conditions are met.
                // Though if a value is supplied, it must be valid.
                'required' => false,
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'groups' => 'email',
                    )),

                    $this->notApplicantEmailConstraintSubscriber->getConstraint(),
                )
            ))
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
            'data_class' => 'Barbon\HostedApi\AppBundle\Form\Common\Model\LettingReferee',

            'validation_groups' => function(FormInterface $form) {
                $validationGroup = new LettingRefereeValidationGroupSelector();
                return $validationGroup->chooseGroups($form);
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'letting_referee';
    }
}
