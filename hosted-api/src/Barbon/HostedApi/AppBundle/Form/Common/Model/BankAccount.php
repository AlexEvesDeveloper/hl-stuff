<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\BankAccount
 */
class BankAccount
{
    /**
     * @Iris\Field
     * @var string
     */
    private $accountNumber;

    /**
     * @Iris\Field
     * @var string
     */
    private $accountSortcode;


    /**
     * Get account number
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set account number
     *
     * @param string $accountNumber
     * @return $this
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    /**
     * Get account sort code
     *
     * @return string
     */
    public function getAccountSortcode()
    {
        return $this->accountSortcode;
    }

    /**
     * Set account sort code
     *
     * @param string $accountSortcode
     * @return $this
     */
    public function setAccountSortcode($accountSortcode)
    {
        $this->accountSortcode = $accountSortcode;
        return $this;
    }
}