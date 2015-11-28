<?php

namespace RRP\Common;

/**
 * Class Titles
 *
 * @package RRP\Common
 * @author April Portus <april.portus@barbon.com>
 */
class Titles
{
    /**
     * Identifier for other titles
     */
    const TITLE_OTHER = 'Other';

    /**
     * Get titles
     *
     * @return array
     */
    public static function getTitles()
    {
        return array(
            'Mr' => 'Mr',
            'Mrs' => 'Mrs',
            'Ms' => 'Ms',
            'Miss' => 'Miss',
            'Mr and Mrs' => 'Mr and Mrs',
            'Dr' => 'Dr',
            'Rev' => 'Rev',
            'Sir' => 'Sir',
            'Professor' => 'Professor',
            self::TITLE_OTHER => self::TITLE_OTHER,
        );
    }

    /**
     * Returns true if title is other
     *
     * @param string $title
     * @return bool
     */
    public static function isOther($title)
    {
        return (self::TITLE_OTHER == $title);
    }

    /**
     * Returns true if other title is required
     *
     * @param string $title
     * @return bool
     */
    public static function isOtherRequired($title)
    {
        return ! array_key_exists($title, self::getTitles());
    }
}
