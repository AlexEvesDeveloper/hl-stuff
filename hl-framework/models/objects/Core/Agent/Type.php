<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that
 * represent the type of Agent objects.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_Type extends Model_Abstract {

    /**#@+
     * Consts which represent the type of Agent objects.
     */
    const HISTORIC_ACCOUNT = 1;
    const NEW_CUSTOMER = 2;
    const RETURNING_CUSTOMER = 3;
    const LEGAL_ENTITY_CHANGE = 4;
    /**#@-*/
}