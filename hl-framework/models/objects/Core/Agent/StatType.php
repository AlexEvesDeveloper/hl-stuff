<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that
 * represent a stat type for Agent Stat objects.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_StatType extends Model_Abstract {

    /**#@+
     * Consts which represent the stat type of Agent Stat objects.
     */
    const NEW_REFS_BY_DAY = 1;
    const OPEN_REFS_BY_PRODUCT = 2;
    const OPEN_REFS_BY_PROGRESS = 3;
    const NEW_POLICIES_BY_DAY = 4;
    const OPEN_POLICIES_BY_PRODUCT = 5;
    const OPEN_POLICIES_BY_INCEPTION = 6;
    /**#@-*/

    static public function toString($const) {
        switch($const) {
            case self::NEW_REFS_BY_DAY:
                return 'new-refs-by-day';
                break;
            case self::OPEN_REFS_BY_PRODUCT:
                return 'open-refs-by-product';
                break;
            case self::OPEN_REFS_BY_PROGRESS:
                return 'open-refs-by-progress';
                break;
            case self::NEW_POLICIES_BY_DAY:
                return 'new-policies-by-day';
                break;
            case self::OPEN_POLICIES_BY_PRODUCT:
                return 'open-policies-by-product';
                break;
            case self::OPEN_POLICIES_BY_INCEPTION:
                return 'open-policies-by-inception';
                break;
        }
    }
}