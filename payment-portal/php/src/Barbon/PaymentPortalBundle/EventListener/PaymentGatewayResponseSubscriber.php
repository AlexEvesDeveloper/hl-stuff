<?php

namespace Barbon\PaymentPortalBundle\EventListener;

use Barbon\PaymentPortalBundle\Entity\CustomerOrder;
use Barbon\PaymentPortalBundle\Model\OrderStatus;
use Barbon\PaymentPortalBundle\Model\PaymentStatusResponse;
use Barbondev\Payment\PayPointHostedBundle\Event\Events;
use Barbondev\Payment\PayPointHostedBundle\Event\GatewayCallbackEvent;
use Barbondev\Payment\PayPointHostedBundle\Plugin\PayPointResponseCodes;
use JMS\Payment\CoreBundle\Plugin\Exception\FinancialException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Barbon\PaymentPortalBundle\CallbackNotifier\CallbackNotifierInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class PaymentGatewayResponseSubscriber
 *
 * @package Barbon\PaymentPortalBundle\EventListener
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @DI\Service
 * @DI\Tag("kernel.event_subscriber")
 */
class PaymentGatewayResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CallbackNotifierInterface
     */
    private $callbackNotifier;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Constructor
     *
     * @param ObjectManager $em
     * @param LoggerInterface $logger
     * @param CallbackNotifierInterface $callbackNotifier
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @DI\InjectParams({
     *     "em"=@DI\Inject("doctrine.orm.entity_manager"),
     *     "logger"=@DI\Inject("logger"),
     *     "callbackNotifier"=@DI\Inject("barbon.payment_portal_bundle.callback_notifier.callback_notifier"),
     *     "eventDispatcher"=@DI\Inject("event_dispatcher")
     * })
     */
    public function __construct(ObjectManager $em, LoggerInterface $logger, CallbackNotifierInterface $callbackNotifier,
                                EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->callbackNotifier = $callbackNotifier;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::ON_GATEWAY_CALLBACK => 'onGatewayCallbackResponse',
        );
    }

    /**
     * barbondev.payment.paypoint_hosted.on_gateway_callback event handler
     *
     * @param GatewayCallbackEvent $event
     * @throws \JMS\Payment\CoreBundle\Plugin\Exception\FinancialException
     */
    public function onGatewayCallbackResponse(GatewayCallbackEvent $event)
    {
        $params = $event->getGatewayResponseParams();

        $paymentStatus = new OrderStatus();

        if (isset($params['code']) && PayPointResponseCodes::AUTHORISED == $params['code']) {
            $paymentStatus
                ->setStatus(OrderStatus::STATUS_SUCCESS)
                ->setCode(OrderStatus::CODE_PAYMENT_CAPTURED)
                ->setMessage('Payment captured')
            ;
        }
        else {
            $paymentStatus
                ->setStatus(OrderStatus::STATUS_FAILURE)
                ->setCode(OrderStatus::CODE_PAYMENT_FAILED_TO_CAPTURE) // todo: need more granular detail here
                ->setMessage('Payment failed')
            ;
        }

        $order = $this->em->getRepository('BarbonPaymentPortalBundle:CustomerOrder')->findOneBy(array(
            'paymentInstruction' => $event->getTransaction()->getPayment()->getPaymentInstruction(),
        ));

        if ( ! $order) {
            $this->logger->error('Could not find order for financial transaction reference number {transactionReferenceNumber}', array(
                'transactionReferenceNumber' => $params['trans_id'],
            ));
            throw new FinancialException('Could not find order');
        }

        if ( ! $order->getPaymentStatusCallbackUrl()) {
            return;
        }

        $paymentStatusResponse = new PaymentStatusResponse();

        $paymentStatusResponse
            ->setTransactionUuId($params['trans_id'])
            ->setOrderUuId($order->getUuid())
            ->setAmount((float) $params['amount'])
            ->setCurrency($order->getCurrency())
            ->setPaymentStatus($paymentStatus)
            ->setProcessor($event->getTransaction()->getPayment()->getPaymentInstruction()->getPaymentSystemName())
            ->setPaymentType(CustomerOrder::PAYMENT_TYPE_CARD_PAYMENT)
            ->setPayload($order->getPaymentStatusCallbackPayload())
        ;

        $order
            ->setStatusResponse($paymentStatusResponse)
            ->setTransId($params['trans_id'])
        ;
        $this->em->persist($order);
        $this->em->flush();

        $paymentStatusResponse
            ->setPayer($order->getPayer())
        ;

        $this->callbackNotifier->notifyCallback($order->getPaymentStatusCallbackUrl(), $paymentStatusResponse);

        $redirectUrl = null;

        if (OrderStatus::STATUS_SUCCESS == $paymentStatus->getStatus()) {
            $redirectUrl = $order->getRedirectOnSuccessUrl();
        }
        elseif (OrderStatus::STATUS_FAILURE == $paymentStatus->getStatus()) {
            $redirectUrl = $order->getRedirectOnFailureUrl();
        }

        if ($redirectUrl) {
            $this->eventDispatcher->addListener(KernelEvents::RESPONSE, function (FilterResponseEvent $event) use ($redirectUrl) {
                /* Now, before you go blabbering "what the hell was Dawson thinking when he did this" - I have an explanation
                for you, you jumpy-to-conclusiony developer punk. The reason is that PayPoint does not like it when the post back
                returns anything other than a 200 response - making a 30* redirect at the end of the process like pulling teeth
                or talking to Paul Swift about Toki Pona. It'd be nice not to have JS run the show here - but I'll leave it for you
                to find a better solution as you seem to know everything anyway. Or, you could ask Simon Paulger for the solution.
                If this is Simon Paulger then get Paul Swift to show you a card trick and forget what you've seen here. */
                $event->setResponse(new Response("<html><head><script>(function () { window.location.replace('{$redirectUrl}') })()</script></head><body><a href=\"{$redirectUrl}\">Proceed to last step&hellp;</a></body></html>"));
            });
        }
    }
}