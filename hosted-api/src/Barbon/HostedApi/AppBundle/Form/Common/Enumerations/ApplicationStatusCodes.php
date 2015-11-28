<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Enumerations;

/**
 * List of Application status constants
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
final class ApplicationStatusCodes
{
    const INCOMPLETE = 1;
    const IN_PROGRESS = 2;
    const COMPLETE = 3;
    const AWAITING_APPLICATION_DETAILS = 4;
    const CANCELLED = 5;
    const DECLINED = 6;
    const AWAITING_AGENT_REVIEW = 7;
}
