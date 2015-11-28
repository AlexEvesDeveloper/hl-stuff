<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that
 * represent the role of Agent_User objects.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_UserRole extends Model_Abstract
{
    /**#@+
     * Consts which represent the role of Agent_User objects.
     */
    const BASIC = 1;
    const MASTER = 2;
    const TESTER = 3;
    /**#@-*/

    public static function toString($const)
    {
        switch($const)
        {
            case self::BASIC:   return 'Basic';
            case self::MASTER:  return 'Master';
            case self::TESTER:  return 'Tester';
        }
    }
}
