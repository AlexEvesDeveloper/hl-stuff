<?php

namespace RRP\Model;

/**
 * Class RentRecoveryPlusCancellation
 *
 * @package RRP\Model
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusCancellation
{
    /**
     * @var string
     */
    private $policyNumber;

    /**
     * Absolute end data allowed (start date + policy length)
     *
     * @var string
     */
    private $policyExpiresAt;

    /**
     * The date from which the policy is to be cancelled from
     *
     * @var string
     */
    private $policyEndAt;

    /**
     * Converts the date to database format
     *
     * @param $inputDate
     * @return string
     */
    private function transformDate($inputDate)
    {
        if (
            null === $inputDate ||
            '00-00-0000' == $inputDate ||
            '0000-00-00' == $inputDate ||
            '' == $inputDate
        ) {
            return '0000-00-00';
        }
        else if ($inputDate instanceof \DateTime) {
            $returnDate = $inputDate->format('Y-m-d');
        }
        else {
            $returnDate = date('Y-m-d', strtotime(str_replace('/', '-', $inputDate)));
        }
        return $returnDate;
    }

    /**
     * Gets the policy expires at string
     *
     * @return string
     */
    public function getPolicyExpiresAt()
    {
        return $this->policyExpiresAt;
    }

    /**
     * Sets the policy expires at
     *
     * @param mixed $policyExpiresAt
     * @return $this
     */
    public function setPolicyExpiresAt($policyExpiresAt)
    {
        $this->policyExpiresAt = $this->transformDate($policyExpiresAt);
        return $this;
    }

    /**
     * Gets the policy end at string
     *
     * @return mixed
     */
    public function getPolicyEndAt()
    {
        return $this->policyEndAt;
    }

    /**
     * Sets the policy end at
     *
     * @param mixed $policyEndAt
     * @return $this
     */
    public function setPolicyEndAt($policyEndAt)
    {
        $this->policyEndAt = $this->transformDate($policyEndAt);
        return $this;
    }

    /**
     * Gets the policy number
     *
     * @return string
     */
    public function getPolicyNumber()
    {
        return $this->policyNumber;
    }

    /**
     * Sets the policy number
     *
     * @param string $policyNumber
     * @return $this
     */
    public function setPolicyNumber($policyNumber)
    {
        $this->policyNumber = $policyNumber;
        return $this;
    }
}