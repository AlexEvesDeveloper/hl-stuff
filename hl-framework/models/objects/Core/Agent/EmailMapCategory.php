<?php

/**
 * An enumerated type (or the PHP equivalent), providing constants that
 * represent the category of Agent_EmailMap objects.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
  */
class Model_Core_Agent_EmailMapCategory extends Model_Abstract {

    /**#@+
     * Consts which represent the category of Agent_EmailMap objects.
     */
    const GENERAL = 1;
    const REFERENCING = 2;
    const INSURANCE = 3;
    const UPDATES = 6;
    /**#@-*/

    static public function toString($const) {
        switch($const) {
            case self::GENERAL:
                return 'General';
                break;
            case self::REFERENCING:
                return 'Referencing';
                break;
            case self::INSURANCE:
                return 'Insurance';
                break;
            case self::UPDATES:
                return 'HomeLet updates';
                break;
        }
    }
}
