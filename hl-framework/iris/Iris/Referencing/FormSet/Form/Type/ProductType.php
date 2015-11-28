<?php

namespace Iris\Referencing\FormSet\Form\Type;

use Barbondev\IRISSDK\Common\Enumeration\CompletionMethodsOptions;
use Barbondev\IRISSDK\Common\Enumeration\LookupCategoryOptions;
use Barbondev\IRISSDK\Common\Enumeration\SignaturePreferenceOptions;
use Barbondev\IRISSDK\IndividualApplication\Product\Model\Product;
use Iris\ProgressiveStore\ProgressiveStoreInterface;
use Iris\Referencing\Form\Type\StepTypeInterface;
use Iris\Referencing\FormSet\ProgressiveStore\AgentGuarantorProgressiveStore;
use Iris\Utility\Lookup\Lookup;
use Symfony\Component\Form\FormBuilderInterface;
use Iris\Common\Titles;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Zend_Registry;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Iris\Referencing\Form\Type\MoneyWithoutStringTransformerType;

/**
 * Class ProductType
 *
 * @package Iris\Referencing\FormSet\Form\Type
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class ProductType extends AbstractFormStepType implements StepTypeInterface
{
    /**
     * @var ProgressiveStoreInterface
     */
    protected $progressiveStore;

    /**
     * Constructor
     *
     * @param ProgressiveStoreInterface $progressiveStore
     */
    public function __construct(ProgressiveStoreInterface $progressiveStore = null)
    {
        if ($progressiveStore instanceof ProgressiveStoreInterface) {
            $this->progressiveStore = $progressiveStore;
        }
        else {
            $this->progressiveStore = $this->getAgentProgressiveStore();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userLabel = 'Tenant';
        if (
            isset($options['userLabel']) &&
            is_string($options['userLabel'])
        ) {
            $userLabel = $options['userLabel'];
        }

        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase $case */
        $case = $this->progressiveStore->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase');

        $builder
            ->add('signaturePreference', 'hidden', array(
                'data' => SignaturePreferenceOptions::SCANNED_DECLARATION, // Always scanned declaration for now
            ))
            ->add('productId', 'choice', array(
                'label' => 'Product',
                'choices' => $this->getProductChoices(),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Please select a product',
                    )),
                ),
            ))
            ->add('update', 'submit', array(
                'attr' => array(
                    'value' => '1',
                ),
            ))
            ->add('title', 'choice', array(
                'choices' => Titles::getTitles(),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Please select a title',
                        'groups' => array('fullValidation'),
                    )),
                ),
            ))
            ->add('firstName', 'text', array(
                'label' => $userLabel . ' First Name',
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => array('fullValidation'),
                        'message' => 'Please enter a first name',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[-a-zA-Z0-9\w]+$/',
                        'message' => 'Please enter alphanumeric characters and spaces only',
                    ))
                )
            ))
            ->add('middleName', 'text', array(
                'label' => $userLabel . ' Middle Name',
                'required' => false,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/^[ -a-zA-Z0-9\w]*$/',
                        'message' => 'Please enter alphanumeric characters and spaces only',
                    ))
                ),
            ))
            ->add('lastName', 'text', array(
                'label' => $userLabel . ' Last Name',
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => array('fullValidation'),
                        'message' => 'Please enter a last name',
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[ -a-zA-Z0-9\w]+$/',
                        'message' => 'Please enter alphanumeric characters and spaces only',
                    ))
                )
            ))
            ->add('email', 'repeated', array(
                // The tenant details page does not require an email address
                'type' => 'email',
                'options' => array(
                    'required' => true,
                    'constraints' => array(
                        new Assert\NotBlank(array(
                            'groups' => array('fullValidation'),
                            'message' => 'Please enter an email address',
                        )),
                        new Assert\Email(array(
                            'groups' => array('fullValidation'),
                            'message' => 'Please enter a valid email address',
                        )),
                    )
                ),
                'first_options' => array('label' => $userLabel . ' Email'),
                'second_options' => array('label' => 'Confirm Email')
            ))
            ->add('rentShare', new MoneyWithoutStringTransformerType(), array(
                'label' => 'Share of Rent',
                'currency' => 'GBP',
                'constraints' => array(
                    new Assert\GreaterThanOrEqual(array(
                        'value' => 0,
                        'groups' => array('fullValidation'),
                        'message' => 'Please enter a positive numeric value for share of rent',
                    )),
                    new Assert\NotBlank(array(
                        'groups' => array('fullValidation'),
                        'message' => 'Please enter a share of rent',
                    )),
                    new Assert\Regex(array(
                            'pattern' => '/^([0-9]+\.[0-9]{0,2}|[0-9]+)$/',
                            'message' => 'Amount must either have 2 decimal places or a whole number, e.g. "15000.00" or "15000"'
                    )),
                    new Assert\LessThanOrEqual(array(
                        'value' => $case->getTotalRent(),
                        'groups' => array('fullValidation'),
                        'message' => 'This amount must be less than the total rent',
                    )),
                ),
            ))
            ->add('completionMethod', 'choice', array(
                'choices' => Lookup::getInstance()->getCategoryAsChoices(LookupCategoryOptions::COMPLETION_METHODS),
                'empty_value' => '- Please Select -',
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'groups' => array('fullValidation'),
                        'message' => 'Please select a completion method',
                    )),
                ),
            ))
        ;

        $self = $this;

        /**
         * Form modifier callback - adds policyLength field if rent guarantee product and adds helper notes depending on
         * the product.
         *
         * @param FormInterface $form
         * @param \Barbondev\IRISSDK\IndividualApplication\Product\Model\Product $product
         * @return void
         */
        $policyLengthFormModifier = function (FormInterface $form, Product $product = null) {

            if (null !== $product) {

                // Check if this is a rent guarantee product
                if ($product->getHasRentGuarantee()) {
                    // Check for international products by product code
                        $form
                            ->add('policyLength', 'choice', array(
                                'choices' => array(
                                    6 => '6 Months',
                                    12 => '12 Months',
                                ),
                                'expanded' => true,
                                'constraints' => array(
                                    new Assert\NotBlank(array(
                                        'message' => 'Please enter a policy length for rent guarantee',
                                    )),
                                    new Assert\Choice(array(
                                        'choices' => array(6, 12),
                                    ))
                                ),
                            ))
                        ;
                }
                else {
                    // Not a rent guarantee product
                }

            }
            elseif ((null !== $product) && !$product->getHasRentGuarantee()) {
                if ($form->has('policyLength')) {
                    $form->remove('policyLength');
                }
            }
        };

        if (!($this->progressiveStore instanceof AgentGuarantorProgressiveStore)) {

            $builder
                ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($self, $policyLengthFormModifier) {

                    /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
                    $application = $event->getData();

                    if ($application->getProductId()) {

                        // Get the current product and if has rent guarantee, add a policy length field
                        $product = $self->getProductById($application->getProductId());

                        $policyLengthFormModifier($event->getForm(), $product);
                    }
                })
            ;

            $builder
                ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($self, $policyLengthFormModifier) {

                    /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
                    $application = $event->getData();

                    // Get the current product and if has rent guarantee, add a policy length field
                    $product = $self->getProductById($application->getProductId());

                    $policyLengthFormModifier($event->getForm(), $product);
                })
            ;

            $builder
                ->get('productId')
                ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($self, $policyLengthFormModifier) {

                    $productId = $event->getForm()->getData();

                    // Get the current product and if has rent guarantee, add a policy length field
                    $product = $self->getProductById($productId);

                    $policyLengthFormModifier($event->getForm()->getParent(), $product);
                })
            ;

        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setOptional(array(
                'userLabel',
            ))
            ->setDefaults(array(
                'userLabel' => 'Tenant',
                'data_class' => 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication',
                'validation_groups' => function (FormInterface $form)
                    {
                        $update = false;

                        if ($form->has('update')) {
                            $update = $form->get('update')->isClicked();
                        }

                        if ($update) {
                            $groups = array('Default');
                        }
                        else {
                            $groups = array('Default', 'fullValidation');
                        }

                        if ($form->get('completionMethod')->getData() == CompletionMethodsOptions::COMPLETE_BY_EMAIL) {
                            $groups[] = 'email';
                        }

                        return $groups;
                    }
        ));
    }


    /**
     * Get an array of products for form display
     *
     * @return array Assoc list of id => product name pairs
     */
    private function getProductChoices()
    {
        $productChoices = array();
        foreach ($this->getProducts() as $product) {
            $productChoices[$product->getId()] = $product->getName();
        }

        return $productChoices;
    }

    /**
     * Get products
     *
     * @return \Guzzle\Common\Collection
     */
    private function getProducts()
    {
        $case = $this->getCaseFromSession();

        return \Zend_Registry::get('iris_container')
            ->get('iris.product')
            ->getProducts($case->getRentGuaranteeOfferingType(), $case->getPropertyLetType())
        ;
    }

    /**
     * Get product by ID
     *
     * @param int $productId
     * @return \Barbondev\IRISSDK\IndividualApplication\Product\Model\Product|null
     */
    public function getProductById($productId)
    {
        foreach ($this->getProducts() as $product) {
            if ($product->getId() == $productId) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Get case from session
     *
     * @return \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase
     */
    private function getCaseFromSession()
    {
        $case = $this
            ->progressiveStore
            ->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase')
        ;

        return $case;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'product';
    }
}
