<?php

namespace RRP\Utility;

/**
 * Class PolicyOptionsManager
 *
 * @package RRP\Utility
 * @author April Portus <april.portus@barbon.com>
 */
class PolicyOptionsManager
{
    /**
     * Option delimiter
     */
    const DELIMITER = '|';

    /**
     * Gets the option value from the string
     *
     * @param string $policyOptionString
     * @param string $optionName
     * @param string $optionString
     * @return $string
     * @throws \RuntimeException
     */
    public static function getOption($policyOptionString, $optionName, $optionString)
    {
        $policyOptions = array_flip(explode(self::DELIMITER, $policyOptionString));
        $options =  explode(self::DELIMITER, $optionString);
        if (count($policyOptions) != count($options)) {
            throw new \RuntimeException('Mis-match in option count');
        }
        if ( ! array_key_exists($optionName, $policyOptions)) {
            throw new \RuntimeException('Option name not set : ' . $optionName);
        }
        $index = $policyOptions[$optionName];
        if ( ! array_key_exists($index, $options)) {
            throw new \RuntimeException('Option value not set');
        }
        return $options[$index];
    }

    /**
     * Adds the option to the list (or initialises it if the existing list is null)
     *
     * @param mixed $optionValue
     * @param string|null $policyOption
     * @return string
     */
    public static function addPolicyOption($optionValue, $policyOption=null)
    {
        if ($policyOption !== null) {
            return $optionValue . self::DELIMITER . $policyOption;
        }
        return $optionValue;
    }

    /**
     * Returns true if the option is set
     *
     * @param string $policyOptionString
     * @param string $optionName
     * @param string $optionString
     * @return bool
     */
    public static function isOptionSet($policyOptionString, $optionName, $optionString)
    {
        $policyOptions = array_flip(explode(self::DELIMITER, $policyOptionString));
        if ( ! array_key_exists($optionName, $policyOptions)) {
            return false;
        }
        $optionValue = self::getOption($policyOptionString, $optionName, $optionString);
        return ($optionValue != 0);


    }
}