<?php

namespace Barbondev\IRISSDK\Common\Utility;

/**
 * Interface ValidationErrorParserInterface
 *
 * @package Barbondev\IRISSDK\Common\Utility
 * @author Paul Swift <paul.swift@barbon.com>
 */
interface ValidationErrorParserInterface
{
    /**
     * Takes a flat array with keys like "addressHistories[0].address.town" and
     * parses into a nested structure.
     *
     * @param array $errors Errors from response
     * @return array Nested set of errors from response
     */
    public function parse(array $errors);
}