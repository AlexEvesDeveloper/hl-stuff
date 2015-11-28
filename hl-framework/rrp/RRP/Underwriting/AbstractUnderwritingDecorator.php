<?php

namespace RRP\Underwriting;

use RRP\DependencyInjection\LegacyContainer;
use RRP\Underwriting\Exception\InvalidQuestionNumberException;
use RRP\Underwriting\Exception\UnansweredQuestionException;
use RRP\Underwriting\Exception\UnderwritingSourceNotSetException;
use RRP\Underwriting\Exception\UnhandledQuestionException;

/**
 * Class AbstractUnderwritingDecorator
 *
 * @package RRP\Underwriting
 * @author April Portus <april.portus@barbon.com>
 */
abstract class AbstractUnderwritingDecorator implements UnderwritingDecoratorInterface
{
    /**
     * Used to identify that the data has no source yet
     */
    const SOURCE_NOT_SET = 0;

    /**
     * Used to identify that the source of the data is from the setAnswers function as opposed to a decorator's function
     */
    const SOURCE_INTERNAL = 1;

    /**
     * Used to identify that the source of the data is from rent recovery plus
     */
    const SOURCE_RENT_RECOVERY_PLUS = 2;

    /**
     * Used to identify that the source of the data is from a mid-term adjustment
     */
    const SOURCE_MID_TERM = 3;

    /**
     * Identifier for the YES response in the database
     */
    const YES_IDENTIFIER = 'yes';

    /**
     * Identifier for the NO response in the database
     */
    const NO_IDENTIFIER = 'no';

    /**
     * @var LegacyContainer
     */
    private $container;

    /**
     * @var \Datasource_Insurance_Answers (containerised)
     */
    private $underwritingAnswers;

    /**
     * @var array of question Ids within the database
     */
    private $databaseQuestionIds;

    /**
     * @var int one of the SOURCE_* constants
     */
    protected $source;

    /**
     * @var array
     */
    protected $questionList;

    /**
     * @var array
     */
    protected $answers;

    /**
     * @var string
     */
    protected $policyNumber;

    /**
     * @inheritdoc
     */
    public function __construct($questionSetId, $dateAnswered, $policyNumber, $policyName)
    {
        $this->source = self::SOURCE_NOT_SET;
        $this->container = new LegacyContainer();
        $this->underwritingAnswers = $this->container->get('rrp.legacy.datasource.underwriting_answers');
        $underwritingQuestions = $this->container->get('rrp.legacy.datasource.underwriting_questions');
        $this->databaseQuestionIds = $underwritingQuestions->getQuestionIdList($questionSetId, $policyName);
        $this->questionList = $this->getQuestionList();

        // Check the database question numbers match that of the child class
        if (count(array_diff(array_keys($this->databaseQuestionIds), $this->questionList)) > 0) {
            throw new UnansweredQuestionException();
        }
        $this->dateAnswered = new \Zend_Date($dateAnswered);
        $this->policyNumber = $policyNumber;
    }

    /**
     * @inheritdoc
     */
    public function setAnswers($underwritingAnswers)
    {
        $this->answers = array();
        foreach ($this->questionList as $questionNumber) {
            if (array_key_exists($questionNumber, $underwritingAnswers)) {
                // Allow for a 'null' answer which indicates that there is no change
                if ($underwritingAnswers[$questionNumber]) {
                    $this->answers[$questionNumber] = $underwritingAnswers[$questionNumber];
                }
                else {
                    $this->answers[$questionNumber] = $this->populateAnswer($questionNumber);
                }
            }
            else {
                throw new UnhandledQuestionException('questionNumber:'.$questionNumber);
            }
        }
        if (count($this->answers) != count($this->questionList)) {
            throw new UnansweredQuestionException();
        }
        $this->source = self::SOURCE_INTERNAL;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function populateAnswer($questionNumber)
    {
        if ( ! array_key_exists($questionNumber, $this->databaseQuestionIds)) {
            throw new InvalidQuestionNumberException();
        }

        $questionId = $this->databaseQuestionIds[$questionNumber];

        /** @var \Model_Insurance_Answer $answer */
        $answer = $this->container->get('rrp.legacy.underwriting_answers');

        $answer->setPolicyNumber($this->policyNumber);
        $answer->setQuestionNumber($questionId);
        $answer->setDateAnswered($this->dateAnswered);
        $existingAnswer = $this->underwritingAnswers->getExistingAnswer($answer);

        return $this->isYes($existingAnswer);
    }

    /**
     * @inheritdoc
     */
    public function getAllAnswers()
    {
        $answerList = array();
        foreach (array_keys($this->databaseQuestionIds) as $questionNumber) {
            $answerList[$questionNumber] = $this->populateAnswer($questionNumber);
        }
        return $answerList;
    }

    /**
     * @inheritdoc
     */
    public function saveAnswers()
    {
        if ($this->source == self::SOURCE_NOT_SET) {
            throw new UnderwritingSourceNotSetException();
        }

        $answers = array();
        $isUpdateNeeded = false;
        foreach ($this->databaseQuestionIds as $questionNumber => $questionId) {
            /** @var \Model_Insurance_Answer $answer */
            $answer = $this->container->get('rrp.legacy.underwriting_answers');

            $answer->setPolicyNumber($this->policyNumber);
            $answer->setQuestionNumber($questionId);
            $answer->setDateAnswered($this->dateAnswered);
            $existingAnswer = $this->underwritingAnswers->getExistingAnswer($answer);

            if ($this->isUpdateNeeded($questionNumber)) {
                $answer->setAnswer($this->getYesNoAnswer($questionNumber));
                if ( ! $existingAnswer) {
                    $isUpdateNeeded = true;
                }
                else if ($existingAnswer != $this->getYesNoAnswer($questionNumber)) {
                    $isUpdateNeeded = true;
                }
            }
            else {
                $answer->setAnswer($existingAnswer);
            }
            $answers[] = clone $answer;
        }

        if ($isUpdateNeeded) {
            $this->underwritingAnswers->removeAllAnswers($this->policyNumber);
            $this->underwritingAnswers->insertUnderwritingAnswers($answers);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function changeQuoteToPolicy($newPolicyNumber=null)
    {
        $this->underwritingAnswers->changeQuoteToPolicy($this->policyNumber, $newPolicyNumber);
        $this->policyNumber = $newPolicyNumber;
        return $this;
    }

    /**
     * Gets a yes or no response to the question
     *
     * @param int $questionNumber
     * @return string
     */
    private function getYesNoAnswer($questionNumber)
    {
        if ($this->answers[$questionNumber]) {
            return self::YES_IDENTIFIER;
        }
        return self::NO_IDENTIFIER;
    }

    /**
     * Translates the database string to a boolean
     *
     * @param string $answerString
     * @return bool
     */
    private function isYes($answerString)
    {
        if ($answerString == self::YES_IDENTIFIER) {
            return true;
        }
        return false;
    }

    /**
     * Checks to see if an update of the answer is required
     *
     * @param int $questionNumber
     * @return bool
     */
    private function isUpdateNeeded($questionNumber)
    {
        if ($this->answers[$questionNumber] === null) {
            return false;
        }
        return true;
    }

}