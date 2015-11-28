<?php

namespace Barbon\PaymentPortalBundle\CallbackNotifier;

use JMS\Serializer\SerializerInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class CurlPostCallbackNotifier
 *
 * @package Barbon\PaymentPortalBundle\CallbackNotifier
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @DI\Service("barbon.payment_portal_bundle.callback_notifier.callback_notifier")
 */
class CurlPostCallbackNotifier implements CallbackNotifierInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     *
     * @DI\InjectParams({
     *     "serializer"=@DI\Inject("jms_serializer")
     * })
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function notifyCallback($callbackUrl, $statusResponse)
    {
        $requestBody = $this->serializer->serialize($statusResponse, 'json');

        $request = new \cURL\Request($callbackUrl);
        $request
            ->getOptions()
            ->set(CURLOPT_FILE, fopen('/dev/null', 'w'))
            ->set(CURLOPT_TIMEOUT, 5)
            ->set(CURLOPT_RETURNTRANSFER, false)
            ->set(CURLOPT_CUSTOMREQUEST, 'POST')
            ->set(CURLOPT_POSTFIELDS, $requestBody)
            ->set(CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                sprintf('Content-Length: %d', strlen($requestBody)),
            ))
        ;

        $request->send();
    }
}
