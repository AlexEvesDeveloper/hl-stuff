<?php

namespace RRP\Rate;

/**
 * Interface RateDecorator
 *
 * @package RRP\Rate
 * @author April Portus <april.portus@barbon.com>
 */
interface RateDecoratorInterface
{
    /**
     * Gets the Premium
     *
     * @return float
     */
    public function getPremium();

    /**
     * Gets the Quote (premium + ipt)
     *
     * @return float
     */
    public function getQuote();

    /**
     * Gets the ipt
     *
     * @return float
     */
    public function getIpt();

    /**
     * Gets the Nil Excess Option
     *
     * @return float
     */
    public function getNilExcessOption();

    /**
     * Gets the rate set id
     *
     * @return int
     */
    public function getRateSetId();

    /**
     * Gets the ipt percent
     *
     * @return int
     */
    public function getIptPercent();
}