<?php

namespace RRP\Form;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class EventAwareTextType
 *
 * @package RRP\Form
 * @author Alex Eves <alex.eves@barbon.com>
 */
class EventAwareTextType extends TextType
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

        if (isset($events['view_transformers'])) {
            if ( ! is_array($events['view_transformers'])) {
                $events['view_transformers'] = array($events['view_transformers']);
            }

            $this->dataTransformers = $events['view_transformers'];
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
            $builder->addViewTransformer($dataTransformer);
        }
    }
}