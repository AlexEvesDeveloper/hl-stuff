<?php

namespace Barbon\HostedApi\SecurityBundle\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\ExceptionListener as BaseExceptionListener;

/**
 * Authorization Exception Listener.
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ExceptionListener extends BaseExceptionListener
{
    /**
     * @param $request Request
     *
     * @return void
     */
    protected function setTargetPath(Request $request)
    {
        // Do not save target path for non HTML requests
        // Note that non-GET requests are already ignored
        $acceptableContentTypes = array_map('strtolower', $request->getAcceptableContentTypes());

        if ( ! in_array('text/html', $acceptableContentTypes) || $request->isXmlHttpRequest()) {
            return;
        }

        parent::setTargetPath($request);
    }
}