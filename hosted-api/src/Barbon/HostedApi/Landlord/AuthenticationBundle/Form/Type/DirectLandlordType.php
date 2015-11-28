<?php

namespace Barbon\HostedApi\Landlord\AuthenticationBundle\Form\Type;

use Barbon\HostedApi\AppBundle\Form\Common\Type\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class DirectLandlordType extends AbstractType
{
    /**
     * @var ChoiceListInterface
     */
    private $titlesLookup;

    /**
     * @var ChoiceListInterface
     */
    private $securityQuestionsLookup;

    /**
     * Constructor
     *
     * @param ChoiceListInterface $titlesLookup
     * @param ChoiceListInterface $securityQuestionsLookup
     */
    public function __construct(ChoiceListInterface $titlesLookup, ChoiceListInterface $securityQuestionsLookup)
    {
        $this->titlesLookup = $titlesLookup;
        $this->securityQuestionsLookup = $securityQuestionsLookup;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'choice', array(
                'choice_list' => $this->titlesLookup,
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Please select a title',
                    ))
                )
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
            ->add($builder->create('email', 'email', array(
                'label' => 'Email Address',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Email(array(
                        'message' => 'Please provide a valid email address'
                    ))
                )
            )))
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
            ->add('address', new AddressType())
            ->add('password', 'password', array(
                'constraints' => array(
                    new Constraints\Length(array(
                       'min' => 8
                    )),
                    new Constraints\Regex(array(
                        'pattern' => '/^(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]/',
                        'message' => 'Password must contain at least 1 upper case character and at least 1 numeric digit',
                    ))
                ),
            ))
        /**
         * The security questions have been removed, but may return in a later version.
         *
            ->add('securityQuestion', 'choice', array(
                'choice_list' => $this->securityQuestionsLookup,
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Constraints\NotBlank(array(
                        'message' => 'Please select a security question',
                    ))
                )
            ))
            ->add('securityAnswer', 'text')
         */
            // todo: Add options, give a nice label - unsure if this field refers to the person or their address:
            ->add('foreigner', 'hidden')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'direct_landlord';
    }
}
