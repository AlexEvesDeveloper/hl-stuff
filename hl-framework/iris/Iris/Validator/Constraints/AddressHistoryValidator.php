<?php

namespace Iris\Validator\Constraints;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\AddressHistory as AddressHistoryModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class AddressHistoryValidator
 *
 * @package Iris\Validator\Constraints
 * @author Paul Swift <paul.swift@barbon.com>
 */
class AddressHistoryValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        $validAddressHistory = false;

        // If the maximum number addresses has been reached, the address history is automatically valid
        if (count($value) >= $constraint->maxAddresses) {
            $validAddressHistory = true;
        }

        // Count up the total address duration and look for foreign addresses
        $totalDuration = 0;
        $foreignAddress = false;
        foreach ($value as $address) {

            if (!($address instanceof AddressHistoryModel)) {
                throw new UnexpectedTypeException($address, 'AddressHistory');
            }

            $totalDuration += $address->getDurationMonths();

            if ($address->getIsForeign()) {
                $foreignAddress = true;
            }

        }

        // Check maximum duration constraint
        if ($totalDuration >= $constraint->maxDuration) {
            $validAddressHistory = true;
        }

        // Check stopping at foreign addresses constraint, if enabled
        if ($constraint->stopAtForeign && $foreignAddress) {
            $validAddressHistory = true;
        }

        // Add constraint violation if any of the above aren't met
        if (!$validAddressHistory) {
            $this->context->addViolation($constraint->message, array(), null, null, 'ADDRESSHISTORYVIOLATION');
        }

    }
}
