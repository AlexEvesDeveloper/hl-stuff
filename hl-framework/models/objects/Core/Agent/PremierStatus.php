<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that
 * represent the premier status of Agent objects.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_PremierStatus extends Model_Abstract {

    /**#@+
     * Consts which represent the premier status of Agent objects.
     */
    const STANDARD = 1;
    const PREMIER = 2;
    /**#@-*/

    public function toString($const) {
        switch($const) {
            case self::STANDARD:
                return 'Standard';
                break;
            case self::PREMIER:
                return 'Premier';
                break;
        }
    }
}
