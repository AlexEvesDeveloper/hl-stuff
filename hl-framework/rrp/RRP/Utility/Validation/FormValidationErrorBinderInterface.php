<?php

namespace RRP\Utility\Validation;

use Symfony\Component\Form\FormInterface;
use Guzzle\Service\Exception\ValidationException;

/**
 * Interface FormValidationErrorBinderInterface
 *
 * @package RRP\Utility\Validation
 * @author April Portus <april.portus@barbon.com>
 */
interface FormValidationErrorBinderInterface
{
    /**
     * Bind validation errors to forms
     *
     * @param FormInterface $form
     * @param ValidationException $validationException
     * @return void
     */
    public function bind(FormInterface $form, ValidationException $validationException);
}