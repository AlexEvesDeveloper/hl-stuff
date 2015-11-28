<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class ReferenceType extends AbstractType
{
    /**
     * @var FormTypeInterface
     */
    private $caseType;

    /**
     * @var RequestStack
     */
    private $requestStack;


    /**
     * Constructor
     *
     * @param FormTypeInterface $caseType
     * @param RequestStack $requestStack
     */
    public function __construct(FormTypeInterface $caseType, RequestStack $requestStack)
    {
        $this->caseType = $caseType;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('case', $this->caseType)
            ->add('order', 'submit', array(
                'label' => 'Order reference',
            ))
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
            'csrf_protection' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'reference';
    }
}
