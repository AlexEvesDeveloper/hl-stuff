<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Enumerations;

/**
 * List of payment status constants
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
final class PaymentStatusCodes
{
    const NULL = 0;
    const PENDING = 1;
    const SUCCESS = 2;
    const FAILURE = 3;
    const NOT_REQUIRED = 4;
    const EXEMPTED = 5;
}