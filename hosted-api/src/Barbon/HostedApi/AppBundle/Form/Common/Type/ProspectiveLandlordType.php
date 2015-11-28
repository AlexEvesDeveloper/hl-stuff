<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Type;

use Barbon\HostedApi\AppBundle\Form\Common\ValidationGroup\ProspectiveLandlordValidationGroupSelector;
use Barbon\HostedApi\AppBundle\Validator\Common\Constraints\NotInArray;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

final class ProspectiveLandlordType extends AbstractType
{
    /**
     * @var ChoiceListInterface
     */
    private $titlesLookup;

    /**
     * @var FormTypeInterface
     */
    private $addressType;

    /**
     * @var EventSubscriberInterface
     */
    private $notProspectiveLandlordEmailBridgeSubscriber;

    /**
     * Constructor
     *
     * @param ChoiceListInterface $titlesLookup
     * @param FormTypeInterface $addressType
     * @param EventSubscriberInterface $notProspectiveLandlordEmailBridgeSubscriber
     */
    public function __construct(
        ChoiceListInterface $titlesLookup,
        FormTypeInterface $addressType,
        EventSubscriberInterface $notProspectiveLandlordEmailBridgeSubscriber
    )
    {
        $this->titlesLookup = $titlesLookup;
        $this->addressType = $addressType;
        $this->notProspectiveLandlordEmailBridgeSubscriber = $notProspectiveLandlordEmailBridgeSubscriber;
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
                        'message' => 'Please enter alphanumeric and spaces only',
                    ))
                )
            ))
            ->add('lastName', 'text', array(
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Please enter last name',
                    )),
                    new Constraints\Regex(array(
                        'pattern' => '/^[ -a-zA-Z0-9\w]+$/',
                        'message' => 'Please enter alphanumeric and spaces only',
                    ))
                )
            ))
            ->add('address', $this->addressType)
            ->add('dayPhone', 'text', array(
                'label' => 'Telephone (day)',
                'constraints' => array(
                    new Constraints\Length(array(
                        'max' => 14
                    )),
                    new Constraints\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}$/',
                        'message' => 'Phone number is invalid',
                    )),
                    new Constraints\NotBlank(array(
                        'groups' => 'dayPhone',
                        'message' => 'Please enter either a daytime or evening telephone number',
                    )),
                ),
            ))
            ->add('eveningPhone', 'text', array(
                'label' => 'Telephone (evening)',
                'constraints' => array(
                    new Constraints\Length(array(
                        'max' => 14
                    )),
                    new Constraints\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}$/',
                        'message' => 'Phone number is invalid',
                    )),
                    new Constraints\NotBlank(array(
                        'groups' => 'eveningPhone',
                        'message' => 'Please enter either a daytime or evening telephone number',
                    )),
                ),
            ))
            ->add('fax', 'text', array(
                'constraints' => array(
                    new Constraints\Regex(array(
                        'pattern' => '/^[0-9+\(\)#\.\s\/ext-]{1,20}$/',
                        'message' => 'Fax number is invalid',
                    )),
                ),
            ))
            ->add($builder->create('email', 'email', array(
                'constraints' => array(
                    new Constraints\Email(),
                )
            ))->addEventSubscriber($this->notProspectiveLandlordEmailBridgeSubscriber))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Barbon\HostedApi\AppBundle\Form\Common\Model\ProspectiveLandlord',
            
            'validation_groups' => function(FormInterface $form) {
                $validationGroup = new ProspectiveLandlordValidationGroupSelector();
                return $validationGroup->chooseGroups($form);
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'prospective_landlord';
    }
}
