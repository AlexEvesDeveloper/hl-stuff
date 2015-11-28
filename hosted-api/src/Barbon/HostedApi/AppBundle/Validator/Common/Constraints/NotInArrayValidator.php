<?php

namespace Barbon\HostedApi\AppBundle\Validator\Common\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotInArrayValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (is_array($constraint->notInArray) && in_array($value, $constraint->notInArray)) {
            $this->context->addViolation($constraint->notInArrayMessage, array(
                '{{ value }}' => $value,
                '{{ values }}' => implode(', ', $constraint->notInArray)
            ));
        }
    }
}
