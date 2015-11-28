<?php

/**
 * Represents a question that could be asked of a salesperson in the system.
 * This is separate from Model_Core_Salesperson_Answer as there could be
 * questions that exist with no answer.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Salesperson
 */
class Model_Core_Salesperson_Question extends Model_Abstract {

    /**
     * The question.
     *
     * @var string Indicates the question.
     */
    public $question;
}