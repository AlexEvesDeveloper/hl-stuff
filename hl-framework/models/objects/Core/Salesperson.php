<?php

/**
 * Represents a salesperson in the system.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Salesperson
 */
class Model_Core_Salesperson extends Model_Abstract {

    /**
     * The salesperson's name.
     *
     * @var Model_Core_Name Indicates the salesperson's name.
     */
    public $name;

    /**
     * The salesperson's contact details.
     *
     * @var Model_Core_ContactDetails Indicates the salesperson's phone numbers.
     */
    public $contactDetails;

    /**
     * The salesperson's commission rate.
     *
     * @var float Indicates the salesperson's commission rate.
     */
    public $commissionRate;

    /**
     * The salesperson's code.
     *
     * @var string Indicates the salesperson's code.
     */
    public $code;

    /**
     * The salesperson's answers to a set of questions.
     *
     * @var array Array of Model_Core_Salesperson_Answer.
     */
    public $questionAnswers;
}