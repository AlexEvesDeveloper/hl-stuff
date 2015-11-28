<?php

namespace Barbon\PaymentPortalBundle\UuId;

/**
 * Interface UuIdGeneratorInterface
 *
 * @package Barbon\PaymentPortalBundle\UuId
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface UuIdGeneratorInterface
{
    /**
     * Generate and return 36 char space/time UUID
     *
     * @return string
     */
    public function generate();
}