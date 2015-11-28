<?php

namespace RRP\Constraint;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Utility\SessionReferenceHolder;

/**
 * Class ReferenceExpiryConstraint
 *
 * @package RRP\Constraint
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceExpiryConstraint implements ConstraintInterface
{
    /**
     * The maximum number days allowed between now and completion date
     *
     * @var int
     */
    const MAX_DAYS_ALLOWED = 60;

    /**
     * @var SessionReferenceHolder
     */
    protected $sessionHolder;

    /**
     * ReferenceExpiryConstraint constructor
     *
     * @param SessionReferenceHolder $sessionHolder
     */
    public function __construct(SessionReferenceHolder $sessionHolder)
    {
        $this->sessionHolder = $sessionHolder;
    }

    /**
     * Verify that the number of days between now and date reference was completed does not exceed the limit.
     *
     * {@inheritdoc}
     */
    public function verify($referenceNumber, $data = array())
    {
        if ( ! array_key_exists('current_asn', $data)) {
            throw new \LogicException('current_asn key does not exist in $data array');
        }

        $reference = $this->sessionHolder->getReferenceFromSession($referenceNumber, $data['current_asn']);

        if (self::MAX_DAYS_ALLOWED < $this->calculateDaysSinceCompletion($reference)) {
            return false;
        }

        return true;
    }

    /**
     * Calculate and return number of days between now and the date the reference was completed.
     *
     * @param ReferencingApplication $reference
     * @return int
     */
    private function calculateDaysSinceCompletion(ReferencingApplication $reference)
    {
        // Millisecond timestamp of the date completed.
        $dateCompletedMilliseconds = $reference->getFirstCompletionAt();

        // Milliseconds to seconds, then into DateTime.
        $dateCompletedTimestamp = $dateCompletedMilliseconds / 1000;
        $dateCompleted = new \DateTime();
        $dateCompleted->setTimestamp($dateCompletedTimestamp);

        // Now DateTime.
        $now = new \DateTime();
        $now->setTimestamp(time());

        // Return the difference between the two.
        return $dateCompleted->diff($now)->days;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorText()
    {
        return 'The reference you have entered has expired, and cannot be used on this policy.';
    }
}