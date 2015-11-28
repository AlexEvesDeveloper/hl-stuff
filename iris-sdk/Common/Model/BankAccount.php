<?php

namespace Barbondev\IRISSDK\Common\Model;

/**
 * Class BankAccount
 * @todo Should implement \JsonSerializable and have private properties.  Currently doesn't for PHP <5.4 compatibility.
 *
 * @package Barbondev\IRISSDK\Common\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class BankAccount
{
    /**
     * @var string
     */
    public $accountNumber;

    /**
     * @var string
     */
    public $accountSortcode;

    /**
     * Set accountNumber
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
     * Get accountNumber
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set accountSortcode
     *
     * @param string $accountSortcode
     * @return $this
     */
    public function setAccountSortCode($accountSortcode)
    {
        $this->accountSortcode = $accountSortcode;
        return $this;
    }

    /**
     * Get accountSortcode
     *
     * @return string
     */
    public function getAccountSortCode()
    {
        return $this->accountSortcode;
    }
}