<?php

/**
 * Represents an agent statistic.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Agent
 */
class Model_Core_Agent_Stat extends Model_Abstract {

    /**
     * The type of stat being represented.
     *
     * @var Model_Core_Agent_StatType Indicates the stat type.
     */
    public $statTypeId;

    /**
     * The agent user's agency's unique identifier.
     *
     * @var mixed Value may be an integer or a string.
     */
    public $agentSchemeNumber;

    /**
     * The stat's date - optional.
     *
     * @var string Indicates the date in 'YYYY-MM-DD' format that the stat
     * applies.
     */
    public $dateApplicable = null;

    /**
     * The stat's variant - optional.  Used like an application-level
     * subcategory.
     *
     * @var string Indicates the variant, if one is needed.
     */
    public $variant = null;

    /**
     * The stat's actual value.
     *
     * @var float Indicates the actual value of the stat.
     */
    public $value = 0;
}