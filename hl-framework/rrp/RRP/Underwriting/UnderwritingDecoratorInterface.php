<?php

namespace RRP\Underwriting;

use RRP\Underwriting\Exception\UnhandledQuestionException;
use RRP\Underwriting\Exception\UnansweredQuestionException;
use RRP\Underwriting\Exception\InvalidQuestionNumberException;
use RRP\Underwriting\Exception\UnderwritingSourceNotSetException;

/**
 * Interface UnderwritingDecoratorInterface
 *
 * @package RRP\Underwriting
 * @author April Portus <april.portus@barbon.com>
 */
interface UnderwritingDecoratorInterface
{
    /**
     * Constructor
     *
     * @param int $questionSetId
     * @param string $dateAnswered
     * @param string $policyName
     * @param string $policyNumber
     */
    public function __construct($questionSetId, $dateAnswered, $policyName, $policyNumber);

    /**
     * Gets an array of the question Ids
     *
     * @return array
     */
    public function getQuestionList();

    /**
     * Sets the answers
     *
     * @param array $underwritingAnswers keyed on question number
     * @return $this
     * @throws UnhandledQuestionException
     * @throws UnansweredQuestionException
     */
    public function setAnswers($underwritingAnswers);

    /**
     * Polulates the answer from the database
     *
     * @param int $questionNumber
     * @return bool
     * @throws InvalidQuestionNumberException
     */
    public function populateAnswer($questionNumber);

    /**
     * Gets an array containing all the answers for the given policy number
     *
     * @return bool
     */
    public function getAllAnswers();

    /**
     * Saves the answers
     *
     * @return $this
     * @throws UnderwritingSourceNotSetException
     */
    public function saveAnswers();

    /**
     * Changes the quote number to be the policy number in the database
     *
     * @param null|string $newPolicyNumber
     * @return $this
     */
    public function changeQuoteToPolicy($newPolicyNumber=null);

}
