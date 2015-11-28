<?php

namespace Barbon\HostedApi\AppBundle\Event;

final class NewReferenceEvents
{
    /**
     * Thrown each time a new multi reference form has been successfully submitted.
     *
     * The event listener receives a Barbon\HostedApi\AppBundle\Event\NewMultiReferenceEvent instance.
     *
     * @var string.
     */
    const MULTI_REFERENCE_CREATED = 'new_multi_reference_event';

    /**
     * Thrown each time a new guarantor reference form has been successfully submitted.
     *
     * The event listener receives a Barbon\HostedApi\AppBundle\Event\NewGuarantorReferenceEvent instance.
     *
     * @var string.
     */
    const GUARANTOR_REFERENCE_CREATED = 'new_guarantor_reference_event';

    /**
     * Thrown each time a new multi reference form has been successfully confirmed.
     *
     * The event listener receives a Barbon\HostedApi\AppBundle\Event\ConfirmMultiReferenceEvent instance.
     *
     * @var string.
     */
    const MULTI_REFERENCE_CONFIRMED = 'confirm_multi_reference_event';

    /**
     * Thrown each time a new guarantor reference form has been successfully confirmed.
     *
     * The event listener receives a Barbon\HostedApi\AppBundle\Event\ConfirmGuarantorReferenceEvent instance.
     *
     * @var string.
     */
    const INDIVIDUAL_REFERENCE_CONFIRMED = 'confirm_guarantor_reference_event';

    /**
     * Thrown each time a reference (multi or individual) has been successfully submitted and paid.
     *
     * The event listener receives a Barbon\HostedApi\AppBundle\Event\ConfirmReferenceSuccessEvent instance.
     *
     * @var string.
     */
    const NEW_REFERENCE_SUCCESS = 'new_reference_success_event';

    /**
     * Thrown each time a reference (multi or individual) has failed at payment.
     *
     * The event listener receives a Barbon\HostedApi\AppBundle\Event\ConfirmReferenceFailureEvent instance.
     *
     * @var string.
     */
    const NEW_REFERENCE_FAILURE = 'new_reference_failure_event';

    /**
     * Thrown each time a reference (multi or individual) has finished, success or failure.
     *
     * The event listener receives a Barbon\HostedApi\AppBundle\Event\ConfirmReferenceFinishEvent instance.
     *
     * @var string.
     */
    const NEW_REFERENCE_FINISH = 'new_reference_finish_event';
}