<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that
 * represent the resources available to Agent_User objects.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_UserResources extends Model_Abstract {

    /**#@+
     * Consts which represent the resources available to Agent_User objects.
     */
    const ADD_USER = 1;
    const REPORTS = 2;
    const NEW_REFERENCE = 3;
    const REFERENCE_SUITE = 4;
    const MODIFY_COVER = 5;
    const ACCOUNTS = 6;
    /**#@-*/

    public function toString($const) {
        switch($const) {
            case self::ADD_USER:
                return 'Add user';
                break;
            case self::REPORTS:
                return 'Reports';
                break;
            case self::NEW_REFERENCE:
                return 'New reference';
                break;
            case self::REFERENCE_SUITE:
                return 'Reference suite';
                break;
            case self::MODIFY_COVER:
                return 'Modify cover';
                break;
            case self::ACCOUNTS:
                return 'Accounts';
                break;
        }
    }
}