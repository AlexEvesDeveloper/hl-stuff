<?php

namespace RRP\Event;

use RRP\Referral\RentRecoveryPlusReferral;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ReferredEvent
 *
 * @package RRP\Event
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferredEvent extends Event
{
    /**
     * @var RentRecoveryPlusReferral
     */
    protected $referral;

    /**
     * ReferredEvent Constructor.
     *
     * @param RentRecoveryPlusReferral $referral
     */
    public function __construct(RentRecoveryPlusReferral $referral)
    {
        $this->referral = $referral;
    }

    /**
     * Get $referral.
     *
     * @return mixed
     */
    public function getReferral()
    {
        return $this->referral;
    }
}