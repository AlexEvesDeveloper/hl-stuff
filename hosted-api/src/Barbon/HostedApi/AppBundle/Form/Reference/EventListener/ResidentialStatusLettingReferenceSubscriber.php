<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\EventListener;

use Barbon\HostedApi\AppBundle\Form\Common\Enumerations\ResidentialStatus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ResidentialStatusLettingReferenceSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'filter',
            FormEvents::PRE_SUBMIT => 'filter',
        );
    }

    /**
     * PRE_SET_DATA & PRE_SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function filter(FormEvent $event)
    {
        $residentialStatus = $event->getData();

        if (ResidentialStatus::HOME_OWNER == $residentialStatus || ResidentialStatus::LIVING_WITH_RELATIVES == $residentialStatus) {
            // Home Owner or Living With Relatives, don't ask for letting reference
            $event->getForm()->getParent()->remove('lettingReferee');
        }
    }
}
