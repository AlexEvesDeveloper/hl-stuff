<?php

/**
 * Represents a salesperson's answer to a question in the system.  Maps to the
 * question asked.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Salesperson
 */
class Model_Core_Salesperson_Answer extends Model_Abstract {

    /**
     * The question.
     *
     * @var Model_Core_Salesperson_Question Indicates the question asked.
     */
    public $question;

    /**
     * The answer.
     *
     * @var string Indicates the answer given.
     */
    public $answer;
}