<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that
 * represent the status of Agent objects.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_Status extends Model_Abstract {

    /**#@+
     * Consts which represent the status of Agent objects.
     */
    const LIVE = 1;
    const ON_STOP = 2;
    const CANCELLED = 3;
    const ON_HOLD = 4;
    /**#@-*/
}