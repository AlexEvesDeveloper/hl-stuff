<?php

namespace Iris\FormFlow;

/**
 * Class FormFlowStep
 *
 * @package Iris\FormFlow
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class FormFlowStep implements FormFlowStepInterface
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var \Closure
     */
    protected $backUrlBuilder;

    /**
     * @var \Closure
     */
    protected $nextUrlBuilder;

    /**
     * @var \Closure
     */
    protected $skip;

    /**
     * Constructor
     *
     * @param string|null $url
     * @param callable $backUrlBuilder
     * @param callable $nextUrlBuilder
     * @param callable $skip
     */
    public function __construct(
        $url = null,
        \Closure $backUrlBuilder = null,
        \Closure $nextUrlBuilder = null,
        \Closure $skip = null
    ) {
        $this->url = $url;
        $this->backUrlBuilder = $backUrlBuilder;
        $this->nextUrlBuilder = $nextUrlBuilder;
        $this->skip = $skip;
    }

    /**
     * {@inheritdoc}
     */
    public function setBackUrlBuilder(\Closure $backUrlBuilder)
    {
        $this->backUrlBuilder = $backUrlBuilder;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBackUrlBuilder()
    {
        return $this->backUrlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextUrlBuilder(\Closure $nextUrlBuilder)
    {
        $this->nextUrlBuilder = $nextUrlBuilder;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextUrlBuilder()
    {
        return $this->nextUrlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set skip
     *
     * @param callable $skip
     * @return $this
     */
    public function setSkip(\Closure $skip)
    {
        $this->skip = $skip;
        return $this;
    }

    /**
     * Get skip
     *
     * @return callable
     */
    public function getSkip()
    {
        return $this->skip;
    }
}