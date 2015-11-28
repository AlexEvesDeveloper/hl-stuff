<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that
 * represent the status of Agent_User objects.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_UserStatus extends Model_Abstract {

    /**#@+
     * Consts which represent the status of Agent_User objects.
     */
    const ACTIVATED = 1;
    const DEACTIVATED = 2;
    /**#@-*/

    public function toString($const) {
        switch($const) {
            case self::ACTIVATED:
                return 'Activated';
                break;
            case self::DEACTIVATED:
                return 'Deactivated';
                break;
        }
    }
}