<?php

namespace Barbon\HostedApi\AppBundle\Event\Listener;

use Barbon\HostedApi\AppBundle\Event\ConfirmMultiReferenceEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Kick off payment for an individual guarantor
 *
 * Class GuarantorPaymentListener
 * @package Barbon\HostedApi\AppBundle\Event\Listener
 */
class MultiPaymentListener extends AbstractPaymentListener
{
    /**
     * @param ConfirmMultiReferenceEvent $event
     */
    public function determineRedirectToPayment(ConfirmMultiReferenceEvent $event)
    {
        parent::generatePaymentOrder();

        // Retrieve the payment gateway url from IRIS...
        $this->irisEntityManager->persist($this->paymentOrder, array(
            'caseId' => $event->getCase()->getCaseId()
        ));

        $response = new RedirectResponse($this->paymentOrder->getPaymentPortalStartUrl());
        $event->setResponse($response);
    }
}