<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that
 * represent the "Absolute" type of Agent objects.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_AbsoluteType extends Model_Abstract {

    /**#@+
     * Consts which represent the "Absolute" type of Agent objects.
     */
    const ABSOLUTE = 1;
    const PROMISE = 2;
    const ESSENTIAL = 3;
    /**#@-*/

    static public function toString($const) {
        switch($const) {
            case self::ABSOLUTE:
                return 'Absolute';
                break;
            case self::PROMISE:
                return 'Promise';
                break;
            case self::ESSENTIAL:
                return 'Essential';
                break;
        }
    }
}