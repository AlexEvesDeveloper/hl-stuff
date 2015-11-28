<?php

namespace Barbondev\IRISSDK\Common\Enumeration;

/**
 * Class ClientOptions
 *
 * @package Barbondev\IRISSDK\Common\Enumeration
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ClientOptions
{
    /**
     * Absolute base URL for the API
     */
    const BASE_URL = 'base_url';

    /**
     * API version
     */
    const VERSION = 'version';

    /**
     * OAuth 1.0 consumer key
     */
    const CONSUMER_KEY = 'consumer_key';

    /**
     * OAuth 1.0 consumer secret
     */
    const CONSUMER_SECRET = 'consumer_secret';

    /**
     * Guzzle service description
     */
    const SERVICE_DESCRIPTION = 'service_description';
}