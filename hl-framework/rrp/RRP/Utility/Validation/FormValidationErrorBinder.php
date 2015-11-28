<?php

namespace RRP\Utility\Validation;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Guzzle\Service\Exception\ValidationException;

/**
 * Class FormValidationErrorBinder
 *
 * @package RRP\Utility\Validation
 * @author April Portus <april.portus@barbon.com>
 */
class FormValidationErrorBinder implements FormValidationErrorBinderInterface
{
    /**
     * Step form name
     */
    const STEP_FORM_NAME = 'step';

    /**
     * {@inheritdoc}
     * @todo Needs to recurse over nested errors
     */
    public function bind(FormInterface $form, ValidationException $validationException)
    {
        // Attach exception message to root form
        // todo: general error message is non user-friendly, removing for now
        //$form->addError(new FormError($validationException->getMessage()));

        $errors = $validationException->getErrors();

        // Iterate over errors and bind to form
        foreach ($errors as $field => $message) {
            if ($form->get(self::STEP_FORM_NAME)->has($field)) {

                if (is_array($message)) {
                    foreach ($message as $subField => $subMessage) {
                        if ($form->get(self::STEP_FORM_NAME)->get($field)->has($subField)) {
                            $form->get(self::STEP_FORM_NAME)->get($field)->get($subField)->addError(new FormError($subMessage));
                        }
                    }
                }
                else {
                    $form->get(self::STEP_FORM_NAME)->get($field)->addError(new FormError($message));
                }
                unset($errors[$field]);
            }
            elseif (is_array($message)) {
                foreach ($message as $key => $error) {
                    if (is_numeric($key)) {
                        if ($form->get(self::STEP_FORM_NAME)->has($field)) {
                            if ($form->get(self::STEP_FORM_NAME)->get($field)->has($key)) {
                                $this->recurseErrors($form->get(self::STEP_FORM_NAME)->get($field)->get($key), $message);
                            }
                        }
                    }
                }
            }
            elseif (preg_match('/^\[([a-z0-9_]+)\].*$/i', $message, $matches)) {
                if (isset($matches[1]) && $form->get(self::STEP_FORM_NAME)->has($matches[1])) {
                    $form->get(self::STEP_FORM_NAME)->get($matches[1])->addError(new FormError($message));
                    unset($errors[$field]);
                }
            }
        }

        // Iterate over errors and bind remaining errors to form
        foreach ($errors as $message) {
            if (is_string($message)) {
                $form->get(self::STEP_FORM_NAME)->addError(new FormError($message));
            }
        }
    }

    /**
     * Recurse over nested errors
     *
     * @param FormInterface $form
     * @param array $errors
     */
    private function recurseErrors(FormInterface $form, array $errors)
    {
        foreach ($errors as $field => $error) {
            if (is_array($error)) {
                $this->recurseErrors($form->has($field) ? $form->get($field) : $form, $error);
            }
            else {

                // todo: this fix should not be in here - needs lifting out into mapping
                if ('addressLine1' == $field) {
                    $field = 'street';
                }

                if ($form->has($field)) {
                    $form->get($field)->addError(new FormError($error));
                    unset($errors[$field]);
                }
            }
        }
    }
}
