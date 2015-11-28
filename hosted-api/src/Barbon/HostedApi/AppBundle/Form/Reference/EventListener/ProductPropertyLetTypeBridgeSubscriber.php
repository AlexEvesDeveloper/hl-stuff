<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\EventListener;

use Barbon\HostedApi\AppBundle\Form\Common\Lookup\Product;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Bridge the property let type field with the product lookup
 */
final class ProductPropertyLetTypeBridgeSubscriber implements EventSubscriberInterface
{
    /**
     * @var Product
     */
    private $productLookup;

    /**
     * Constructor
     *
     * @param Product $productLookup
     */
    public function __construct(Product $productLookup)
    {
        $this->productLookup = $productLookup;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'setFilter',
            FormEvents::PRE_SUBMIT => 'setFilter',
        );
    }

    /**
     * PRE_SET_DATA & PRE_SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function setFilter(FormEvent $event)
    {
        $propertyLetType = $event->getData();
        $this->productLookup->setPropertyLettingType($propertyLetType);
    }
}
