<?php

namespace Barbon\HostedApi\AppBundle\Event\Listener;

use Barbon\HostedApi\AppBundle\Form\Common\Model\AbstractPaymentOrder;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class AbstractPaymentListener
 * @package Barbon\HostedApi\AppBundle\Event\Listener
 */
class AbstractPaymentListener
{
    /**
     * @var IrisEntityManager
     */
    protected $irisEntityManager;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var AbstractPaymentOrder
     */
    protected $paymentOrder;

    /**
     * @param IrisEntityManager $irisEntityManager
     * @param Router $router
     * @param AbstractPaymentOrder $paymentOrder
     */
    public function __construct(IrisEntityManager $irisEntityManager, Router $router, AbstractPaymentOrder $paymentOrder)
    {
        $this->irisEntityManager = $irisEntityManager;
        $this->router = $router;
        $this->paymentOrder = $paymentOrder;
    }

    /**
     * Initialise the PaymentOrder Model to be posted to IRIS
     */
    protected function generatePaymentOrder()
    {
        // Set the necessary data to POST to the endpoint
        // TODO: the payment types are currently hardcoded, but aren't likely to change in the near future. This will eventually need refactoring.
        $this->paymentOrder->setPaymentTypes(array(1));

        // Success: redirect to the view page of the new case
        $this->paymentOrder->setRedirectOnSuccessUrl($this->router->generate('barbon_hostedapi_landlord_reference_newreference_success_index', array(), true));

        // Failure: redirect to generic failed payment page
        $this->paymentOrder->setRedirectOnFailureUrl($this->router->generate('barbon_hostedapi_landlord_reference_newreference_failure_index', array(), true));
    }
}