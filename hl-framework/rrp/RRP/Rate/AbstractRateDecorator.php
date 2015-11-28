<?php

namespace RRP\Rate;

/**
 * Class AbstractRateDecorator
 *
 * @package RRP\Rate
 * @author April Portus <april.portus@barbon.com>
 */
abstract class AbstractRateDecorator implements RateDecoratorInterface
{
    /**
     * @var int
     */
    protected $agentRateId;

    /**
     * @var int
     */
    protected $riskArea;

    /**
     * @var float
     */
    protected $iptPercent;

    /**
     * @var int
     */
    protected $rateSetId;

    /**
     * @var float
     */
    protected $premium;

    /**
     * @var float
     */
    protected $nilExcessOption;

    /**
     * @inheritdoc
     */
    public function getPremium()
    {
        return round($this->premium, 2);
    }

    /**
     * @inheritdoc
     */
    public function getIpt()
    {
        return round($this->premium * $this->getIptPercent() / 100.0, 2);
    }

    /**
     * @inheritdoc
     */
    public function getQuote()
    {
        return $this->getPremium() + $this->getIpt();
    }

    /**
     * @inheritdoc
     */
    public function getNilExcessOption()
    {
        return round($this->nilExcessOption, 2);
    }

    /**
     * @inheritdoc
     */
    public function getRateSetId()
    {
        return $this->rateSetId;
    }

    /**
     * @inheritdoc
     */
    public function getIptPercent()
    {
        return $this->iptPercent;
    }
}