<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\EventListener;

use Barbon\HostedApi\AppBundle\Form\Common\Model\PreviousAddress;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class AddressHistoryDurationCalculationSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => 'postSubmit'
        );
    }

    /**
     * POST_SUBMIT handler
     *
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $endDate = new DateTime();

        foreach($data as $value) {
            if ($value instanceof PreviousAddress) {
                // Calculate difference between dates and update model
                $startDate = $value->getStartDate();

                if (null !== $startDate) {
                    $diff = $startDate->diff($endDate);
                    $value->setDurationMonths($diff->m + (12 * $diff->y));

                    $endDate = $startDate;
                }
            }
        }
    }
}
