<?php

namespace Barbon\PaymentPortalBundle\Http;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class PublicCacheResponse
 *
 * @package Barbon\PaymentPortalBundle\Http
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class PublicCacheResponse extends Response
{
    /**
     * Constructor
     *
     * @param int $ttlInSeconds
     * @param string $content
     * @param int $status
     * @param array $headers
     */
    public function __construct($ttlInSeconds, $content = '', $status = 200, $headers = array())
    {
        $now = gmdate('D, d M Y H:i:s \G\M\T', time());
        $expires = gmdate('D, d M Y H:i:s \G\M\T', time() + $ttlInSeconds);

        $headers = array_merge(array(
            'Content-Type' => 'text/css',
            'Pragma' => 'cache',
            'Cache-Control' => 'public',
            'Date' => $now,
            'Last-Modified' => $now,
            'Expires' => $expires,
        ), $headers);

        parent::__construct($content, $status, $headers);
    }
}