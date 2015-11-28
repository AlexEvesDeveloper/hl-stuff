<?php

namespace RRP\Criteria;

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;

/**
 * Class AbstractCriteria
 *
 * @package RRP\Criteria
 * @author Alex Eves <alex.eves@barbon.com>
 */
abstract class AbstractCriteria implements CriteriaInterface
{
    /**
     * @var bool
     */
    protected $isSatisfied = true;

    /**
     * @var string
     */
    protected $notSatisfiedText;

    /**
     * Return a new Plus instance.
     *
     * @param CriteriaInterface $criteria
     * @return Plus
     */
    public function plus(CriteriaInterface $criteria)
    {
        return new Plus($this, $criteria);
    }

    /**
     * Set $isSatisfied.
     *
     * @param $isSatisfied
     * @return $this
     */
    public function setIsSatisfied($isSatisfied)
    {
        $this->isSatisfied = $isSatisfied;

        return $this;
    }

    /**
     * Get $isSatisfied.
     *
     * @return bool
     */
    public function isSatisfied()
    {
        return $this->isSatisfied;
    }

    /**
     * Set $notSatisfiedText.
     *
     * @param $text
     * @return $this
     */
    public function setNotSatisfiedText($text)
    {
        $this->notSatisfiedText = $text;

        return $this;
    }

    /**
     * Get $notSatisfiedText.
     *
     * @return string
     */
    public function getNotSatisfiedText()
    {
        return $this->notSatisfiedText;
    }
}