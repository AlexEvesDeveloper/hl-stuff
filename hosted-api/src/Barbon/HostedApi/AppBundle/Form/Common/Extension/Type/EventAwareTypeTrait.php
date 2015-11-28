<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Extension\Type;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

trait EventAwareTypeTrait
{
    /**
     * @var EventSubscriberInterface[]
     */
    private $eventSubscribers = array();

    /**
     * @var DataTransformerInterface[]
     */
    private $dataTransformers = array();

    /**
     * Constructor
     *
     * @param array $events
     */
    public function __construct(array $events)
    {
        if (isset($events['subscribers'])) {
            if ( ! is_array($events['subscribers'])) {
                $events['subscribers'] = array($events['subscribers']);
            }

            $this->eventSubscribers = $events['subscribers'];
        }

        if (isset($events['model_transformers'])) {
            if ( ! is_array($events['model_transformers'])) {
                $events['model_transformers'] = array($events['model_transformers']);
            }

            $this->dataTransformers = $events['model_transformers'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        foreach ($this->eventSubscribers as $eventSubscriber) {
            $builder->addEventSubscriber($eventSubscriber);
        }

        foreach ($this->dataTransformers as $dataTransformer) {
            $builder->addModelTransformer($dataTransformer);
        }
    }
}