<?php

namespace RRP\Event;

/**
 * Class RRPEvents
 *
 * @package RRP\Event
 * @author Alex Eves <alex.eves@barbon.com>
 */
final class RRPEvents
{
    /**
     * The policy.referred event is thrown each time a reference and/or application does not meet the policy criteria.
     *
     * The event listener receives an RRP\Event\ReferredEvent instance.
     *
     * @var string
     */
    const POLICY_REFERRED = 'policy.referred';
}