<?php

namespace Iris\Utility\Validation;

use Symfony\Component\Form\FormInterface;
use Guzzle\Service\Exception\ValidationException;

/**
 * Class FormValidationErrorBinderInterface
 *
 * @package Iris\Utility\Validation
 * @author Ashley Dawson <ashley.dawson@barbon.com>
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