<?php

namespace RRP\Common;

/**
 * Class PropertyLetTypes
 *
 * @package RRP\Common
 * @author April Portus <april.portus@barbon.com>
 */
class PropertyLetTypes
{
    /**
     * Get Reference Types
     *
     * @return array
     */
    public static function getPropertyLetTypes()
    {
        return array(
            'Let Only'     => 'Let Only',
            'Managed'      => 'Managed',
            'Rent Collect' => 'Rent Collect',
        );
    }

    /**
     * Returns true if landlord permission is required
     *
     * @param $propertyLetType
     * @return bool
     */
    public static function isLandlordPermissionRequired($propertyLetType)
    {
        if ($propertyLetType == 'Let Only') {
            return true;
        }
        else {
            return false;
        }
    }
}
