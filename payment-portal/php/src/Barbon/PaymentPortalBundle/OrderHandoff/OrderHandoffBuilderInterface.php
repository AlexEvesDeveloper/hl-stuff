<?php

namespace Barbon\PaymentPortalBundle\OrderHandoff;

use Barbon\PaymentPortalBundle\Entity\CustomerOrder;

/**
 * Interface OrderHandoffBuilderInterface
 *
 * @package Barbon\PaymentPortalBundle\OrderHandoff
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface OrderHandoffBuilderInterface
{
    /**
     * Builds an order handoff object from a persisted order
     *
     * @param CustomerOrder $order
     * @return \Barbon\PaymentPortalBundle\Model\OrderHandoff
     */
    public function buildOrderHandoff(CustomerOrder $order);
}