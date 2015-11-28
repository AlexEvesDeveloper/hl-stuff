<?php

namespace Barbondev\IRISSDK\Common\Enumeration;

/**
 * Class AgentBranchStatusOptions
 *
 * @package Barbondev\IRISSDK\Common\Enumeration
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class AgentBranchStatusOptions
{
    /**
     * Live status
     */
    const LIVE = 'live';

    /**
     * On stop status
     */
    const ON_STOP = 'onstop';

    /**
     * On hold status
     */
    const ON_HOLD = 'onhold';

    /**
     * Cancelled status
     */
    const CANCELLED = 'cancelled';
}