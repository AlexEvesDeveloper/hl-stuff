<?php

namespace Iris\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class AddressHistory
 *
 * @package Iris\Validator\Constraints
 * @author Paul Swift <paul.swift@barbon.com>
 */
class AddressHistory extends Constraint
{
    /**
     * @var int Maximum number of addresses until this constraint returns true
     */
    public $maxAddresses = 3;

    /**
     * @var int Maximum address duration, in months, until this constraint returns true
     */
    public $maxDuration = 36;

    /**
     * @var bool If true, a foreign address makes this constraint return true
     */
    public $stopAtForeign = true;

    /**
     * @var string Constraint failure message
     */
    public $message = 'Please add previous address history spanning up to three addresses or three years.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'Iris\Validator\Constraints\AddressHistoryValidator';
    }
}