<?php

namespace RRP\EventListener;

use RRP\Event\ReferredEvent;
use RRP\Referral\RentRecoveryPlusReferral;

/**
 * Class LogReferralReasonsListener
 *
 * @package RRP\EventListener
 * @author Alex Eves <alex.eves@barbon.com>
 */
class LogReferralReasonsListener
{
    /**
     * @var Datasource_Insurance_PolicyNotes
     */
    protected $policyNote;

    /**
     * LogReferralReasonsListener Constructor.
     *
     * @param $policyNote
     */
    public function __construct($policyNote)
    {
        $this->policyNote = $policyNote;
    }

    /**
     * Dispatched each time the listener is triggered.
     *
     * @param ReferredEvent $event
     */
    public function onReferral(ReferredEvent $event)
    {
        $referral = $event->getReferral();

        $note = sprintf(
            "This policy has been referred for the following reason(s)\n\n%s",
            implode("\n", $referral->getAllReferralReasons())
        );

        $this->policyNote->addNote($referral->getPolicyNumber(), $note);
    }
}