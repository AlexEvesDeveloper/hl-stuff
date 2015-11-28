<?php

namespace Barbondev\IRISSDK\Common\Utility;

use Barbondev\IRISSDK\Common\Utility\Exception\CannotHandleKeyCollisionException;
use Guzzle\Common\Exception\InvalidArgumentException;

/**
 * Class ValidationErrorParser
 *
 * @package Barbondev\IRISSDK\Common\Utility
 * @author Paul Swift <paul.swift@barbon.com>
 */
class ValidationErrorParser implements ValidationErrorParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse(array $errors)
    {
        $parsedOutput = array();

        // Pre-process keys that include square bracket indices by naively
        // converting them to dot notation
        $bracketProcessedErrors = array();
        foreach ($errors as $errorKey => $errorMessage) {
            // Open square brackets become dots
            $temporaryErrorKey = str_replace('[', '.', $errorKey);
            // Close square brackets are stripped
            $temporaryErrorKey = str_replace(']', '', $temporaryErrorKey);
            $bracketProcessedErrors[$temporaryErrorKey] = $errorMessage;
        }
        $errors = $bracketProcessedErrors;

        // Run through each error-message key-pair and build into structured
        // array
        foreach ($errors as $errorKey => $errorMessage) {
            // Convert to an n-depth one-key array
            $parseIntoArray = $this->iterativeParse(
                $errorKey,
                $errorMessage
            );

            // Merge new structured data into bigger structure so far
            $parsedOutput = $this->recursiveMerge(
                $parsedOutput,
                $parseIntoArray
            );
        }

        return $parsedOutput;
    }

    /**
     * @param $key
     * @param $val
     * @throws \Guzzle\Common\Exception\InvalidArgumentException
     * @return array
     */
    private function iterativeParse($key, $val)
    {
        // Ensure value is a non-empty string
        if (!is_string($val) || '' == $val) {
            throw new InvalidArgumentException('Value is non-string or empty');
        }

        $parsedOutput = array();

        // Check if dot notation is present
        if (strpos($key, '.') !== false) {

            $subKeys = explode('.', $key);

            $subArray = $val;
            while (count($subKeys) > 1) {
                $thisSubKey = array_pop($subKeys);

                $temporarySubArray = array();
                $temporarySubArray[$thisSubKey] = $subArray;
                $subArray = $temporarySubArray;
            }
            $thisSubKey = array_pop($subKeys);

            // Add to parsed output
            $parsedOutput[$thisSubKey] = $subArray;
        }
        else {
            // No dot notation, this is a top-level key-pair
            $parsedOutput[$key] = $val;
        }

        return $parsedOutput;
    }

    /**
     * @param array $existingArray
     * @param array $insertionArray Array of a single key to insert into the
     *                              existing array.
     *
     * @throws \Guzzle\Common\Exception\InvalidArgumentException
     * @throws \Barbondev\IRISSDK\Common\Exception\RuntimeException
     *
     * @return array
     */
    private function recursiveMerge(array $existingArray, array $insertionArray)
    {
        if (count($insertionArray) !== 1) {
            throw new InvalidArgumentException('Insertion array does not have exactly one key');
        }

        // Get the topmost array key from insertion array
        $keys = array_keys($insertionArray);
        $topKey = $keys[0];

        // See if there's a key collision
        if (isset($existingArray[$topKey])) {
            // Handle key collision

            // If the insertion array has an array as its value and so does the
            // existing array then recurse deeper
            if (
                is_array($existingArray[$topKey]) &&
                is_array($insertionArray[$topKey]))
            {
                // Recurse deeper
                $existingArray[$topKey] = $this->recursiveMerge(
                    $existingArray[$topKey],
                    $insertionArray[$topKey]
                );
            }
            else {
                // Something very bad happened
                throw new CannotHandleKeyCollisionException('Cannot handle key collision');
            }
        }
        else {
            // No key collision, do insertion and return
            $existingArray[$topKey] = $insertionArray[$topKey];
        }

        return $existingArray;
    }
}