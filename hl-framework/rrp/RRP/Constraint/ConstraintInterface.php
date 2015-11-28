<?php

namespace RRP\Constraint;

use Symfony\Component\Form\FormEvent;

/**
 * Interface ConstraintInterface
 *
 * @package RRP\Constraint
 * @author Alex Eves <alex.eves@barbon.com>
 */
interface ConstraintInterface
{
    /**
     * Verify the reference number against a constraint.
     *
     * @param string $referenceNumber
     * @param array $data
     * @return mixed
     */
    public function verify($referenceNumber, $data = array());

    /**
     * Get the error text from a constraint.
     *
     * @return string
     */
    public function getErrorText();
}