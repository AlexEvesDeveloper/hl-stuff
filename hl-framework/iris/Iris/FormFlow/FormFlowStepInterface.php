<?php

namespace Iris\FormFlow;

/**
 * Interface FormFlowStepInterface
 *
 * @package Iris\FormFlow
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface FormFlowStepInterface
{
    /**
     * Set backUrlBuilder
     * The closure must return a URL
     *
     * <code>
     * $formFlowStep->setBackUrlBuilder(function (ProgressiveStoreInterface $store, FormInterface $form, AbstractFormFlow $flow) {
     *     return '/some/url/path';
     * });
     * </code>
     *
     * @param callable $backUrlBuilder
     * @return $this
     */
    public function setBackUrlBuilder(\Closure $backUrlBuilder);

    /**
     * Get backUrlBuilder
     *
     * @return callable
     */
    public function getBackUrlBuilder();

    /**
     * Set nextUrlBuilder
     * The closure must return a URL
     *
     * <code>
     * $formFlowStep->setNextUrlBuilder(function (ProgressiveStoreInterface $store, FormInterface $form, AbstractFormFlow $flow) {
     *     return '/some/url/path';
     * });
     * </code>
     *
     * @param callable $nextUrlBuilder
     * @return $this
     */
    public function setNextUrlBuilder(\Closure $nextUrlBuilder);

    /**
     * Get nextUrlBuilder
     *
     * @return callable
     */
    public function getNextUrlBuilder();

    /**
     * Set url
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Set skip
     *
     * @param callable $skip
     * @return $this
     */
    public function setSkip(\Closure $skip);

    /**
     * Get skip
     *
     * @return callable
     */
    public function getSkip();
}