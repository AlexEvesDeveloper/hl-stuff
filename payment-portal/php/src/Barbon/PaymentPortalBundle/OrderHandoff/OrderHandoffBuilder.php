<?php

namespace Barbon\PaymentPortalBundle\OrderHandoff;

use Barbon\PaymentPortalBundle\Entity\CustomerOrder;
use Barbon\PaymentPortalBundle\Model\OrderHandoff;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class OrderHandoffBuilder
 *
 * @package Barbon\PaymentPortalBundle\OrderHandoff
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @DI\Service
 */
class OrderHandoffBuilder implements OrderHandoffBuilderInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $handoffUrlRouteName;

    /**
     * Constructor
     *
     * @param RouterInterface $router
     * @param string $handoffUrlRouteName
     */
    public function __construct(RouterInterface $router, $handoffUrlRouteName)
    {
        $this->router = $router;
        $this->handoffUrlRouteName = $handoffUrlRouteName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOrderHandoff(CustomerOrder $order)
    {
        $orderUuId = $order->getUuid();

        if ( ! $orderUuId) {
            throw new \InvalidArgumentException(
                'Order does not exist in entity manager, make sure that it is persisted before handing off');
        }

        $handoff = new OrderHandoff(
            $orderUuId,
            $this->router->generate($this->handoffUrlRouteName, array('uuid' => $orderUuId), true)
        );

        return $handoff;
    }
}