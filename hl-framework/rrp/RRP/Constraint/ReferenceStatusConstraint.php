<?php

namespace RRP\Constraint;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\ApplicationStatuses;
use RRP\Utility\SessionReferenceHolder;

/**
 * Class ReferenceStatusConstraint
 *
 * @package RRP\Constraint
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceStatusConstraint implements ConstraintInterface
{
    /**
     * @var SessionReferenceHolder
     */
    protected $sessionHolder;

    /**
     * @var string
     */
    protected $status;

    /**
     * ReferenceStatusConstraint constructor
     *
     * @param SessionReferenceHolder $sessionHolder
     */
    public function __construct(SessionReferenceHolder $sessionHolder)
    {
        $this->sessionHolder = $sessionHolder;
    }

    /**
     * {@inheritdoc}
     */
    public function verify($referenceNumber, $data = array())
    {
        if ( ! array_key_exists('current_asn', $data)) {
            throw new \LogicException('current_asn key does not exist in $data array');
        }

        $this->status = $this->sessionHolder->getReferenceFromSession($referenceNumber, $data['current_asn'])->getStatus();

        if (ApplicationStatuses::COMPLETE != $this->status) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorText()
    {
        switch($this->status) {
            case ApplicationStatuses::CANCELLED:
            case ApplicationStatuses::DECLINED:
                return 'The reference you have added has been declined or cancelled. You cannot include this reference on this policy.';
                break;
            case ApplicationStatuses::AWAITING_AGENT_REVIEW:
            case ApplicationStatuses::AWAITING_APPLICATION_DETAILS:
            case ApplicationStatuses::IN_PROGRESS:
            case ApplicationStatuses::INCOMPLETE:
            default:
                return 'The reference you have added has not been completed. Please try again when the reference is complete.';
                break;
        }
    }
}