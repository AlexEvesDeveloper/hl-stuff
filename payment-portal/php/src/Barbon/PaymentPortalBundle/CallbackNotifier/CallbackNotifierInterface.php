<?php

namespace Barbon\PaymentPortalBundle\CallbackNotifier;

/**
 * Interface CallbackNotifierInterface
 *
 * @package Barbon\PaymentPortalBundle\CallbackNotifier
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface CallbackNotifierInterface
{
    /**
     * Notify a supplied client callback URL via HTTP POST
     *
     * @param string $callbackUrl
     * @param object $statusResponse
     * @return void
     */
    public function notifyCallback($callbackUrl, $statusResponse);
}