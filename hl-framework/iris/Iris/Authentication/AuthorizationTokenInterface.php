<?php

namespace Iris\Authentication;

/**
 * Class AuthorizationTokenInterface
 *
 * @package Iris\Authentication
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface AuthorizationTokenInterface
{
    /**
     * Persist this token in session
     *
     * @return void
     */
    public function persist();

    /**
     * Get the name of this authorization token
     *
     * @return string
     */
    public function getName();

    /**
     * Set consumerKey
     *
     * @param string $consumerKey
     * @return $this
     */
    public function setConsumerKey($consumerKey);

    /**
     * Get consumerKey
     *
     * @return string
     */
    public function getConsumerKey();

    /**
     * Set consumerSecret
     *
     * @param string $consumerSecret
     * @return $this
     */
    public function setConsumerSecret($consumerSecret);

    /**
     * Get consumerSecret
     *
     * @return string
     */
    public function getConsumerSecret();

    /**
     * Set apiBaseUrl
     *
     * @param string $apiBaseUrl
     * @return $this
     */
    public function setApiBaseUrl($apiBaseUrl);

    /**
     * Get apiBaseUrl
     *
     * @return string
     */
    public function getApiBaseUrl();

    /**
     * Set apiVersion
     *
     * @param string $apiVersion
     * @return $this
     */
    public function setApiVersion($apiVersion);

    /**
     * Get apiVersion
     *
     * @return string
     */
    public function getApiVersion();

    /**
     * Get API registry context parameters
     *
     * @return array
     */
    public function getContextParameters();
}