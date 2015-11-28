<?php

namespace Barbondev\IRISSDK\Common\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Barbondev\IRISSDK\Common\Exception\ExceptionFactoryInterface;
use Guzzle\Common\Event;

/**
 * Class ExceptionEventSubscriber
 *
 * @package Barbondev\IRISSDK\Common\EventListener
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ExceptionEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ExceptionFactoryInterface
     */
    protected $factory;

    /**
     * Constructor
     *
     * @param ExceptionFactoryInterface $factory
     */
    public function __construct(ExceptionFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'request.error' => array(
                'onRequestError',
                -1,
            ),
        );
    }

    /**
     * Throw an IRIS exception
     *
     * @param Event $event
     * @throws \Barbondev\IRISSDK\Common\Exception\IRISExceptionInterface
     */
    public function onRequestError(Event $event)
    {
        $exception = $this
            ->factory
            ->fromResponse($event['request'], $event['response'])
        ;

        $event->stopPropagation();

        throw $exception;
    }
}