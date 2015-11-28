<?php

namespace Barbon\HostedApi\AppBundle\Event\Listener;

use Barbon\HostedApi\AppBundle\Event\ConfirmGuarantorReferenceEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Kick off payment for an individual guarantor
 *
 * Class IndividualPaymentListener
 * @package Barbon\HostedApi\AppBundle\Event\Listener
 */
class IndividualPaymentListener extends AbstractPaymentListener
{
    /**
     * @param ConfirmGuarantorReferenceEvent $event
     */
    public function determineRedirectToPayment(ConfirmGuarantorReferenceEvent $event)
    {
        parent::generatePaymentOrder();

        // Retrieve the payment gateway url from IRIS...
        $this->irisEntityManager->persist($this->paymentOrder, array(
            'applicationId' => $event->getReference()->getApplicationId()
        ));

        $response = new RedirectResponse($this->paymentOrder->getPaymentPortalStartUrl());
        $event->setResponse($response);
    }
}