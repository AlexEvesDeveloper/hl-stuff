<?php

namespace Barbon\HostedApi\AppBundle\Validator\Common\Constraints;

use Barbon\HostedApi\AppBundle\Form\Common\Model\PreviousAddress;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AddressHistoryDurationValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $years = 0;
        $count = 0;

        if (null !== $value && is_array($value) && count($value) > 0) {
            /** @var PreviousAddress $lastAddress */
            $lastAddress = null;
            $now = new DateTime();

            // Get addresses up to the first foreign addresses, or all the last address if none are foreign
            // and diff the start date to the current date
            /** @var PreviousAddress $previousAddress */
            foreach ($value as $previousAddress) {
                $lastAddress = $previousAddress;
                $count++;

                // Once we have found a foreign address, stop considering addresses beyond this.
                if ($previousAddress->isForeign()) {
                    break;
                }
            }

            if (null !== $lastAddress) {
                $startDate = $lastAddress->getStartDate();

                if (null !== $startDate) {
                    $years = $startDate->diff($now)->y;
                }
            }
        }

        // Check min number of years and count met
        if ($years < $constraint->minYears && $count < $constraint->minNum) {
            $this->context->addViolation($constraint->message, array(
                '{{ minYears }}' => $constraint->minYears,
                '{{ minNum }}' => $constraint->minNum,
            ));
        }
    }
}