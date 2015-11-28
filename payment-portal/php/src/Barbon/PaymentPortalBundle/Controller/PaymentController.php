<?php

namespace Barbon\PaymentPortalBundle\Controller;

use Barbon\PaymentPortalBundle\Entity\CustomerOrder;
use Barbon\PaymentPortalBundle\Entity\User;
use Barbon\PaymentPortalBundle\Http\PublicCacheResponse;
use Barbon\PaymentPortalBundle\Model\OrderStatus;
use Barbon\PaymentPortalBundle\Model\RefundStatusResponse;
use Barbon\PaymentPortalBundle\Model\RepeatStatusResponse;
use Barbon\PaymentPortalBundle\CallbackNotifier\CallbackNotifierInterface;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Payment\CoreBundle\Entity\ExtendedData;
use JMS\Payment\CoreBundle\Entity\FinancialTransaction;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;
use JMS\Payment\CoreBundle\Model\CreditInterface;
use JMS\Payment\CoreBundle\Model\PaymentInterface;
use JMS\Payment\CoreBundle\Plugin\Exception\FinancialException;
use JMS\Payment\CoreBundle\PluginController\Result;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Framework;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class PaymentController
 *
 * @package Barbon\PaymentPortalBundle\Controller
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @Framework\Route("/payments")
 */
class PaymentController
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     * @DI\Inject
     */
    private $request;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     * @DI\Inject("form.factory")
     */
    private $formFactory;

    /**
     * @var \JMS\Payment\CoreBundle\PluginController\EntityPluginController
     * @DI\Inject("payment.plugin_controller")
     */
    private $paymentPluginController;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    private $em;

    /**
     * @var RouterInterface
     * @DI\Inject
     */
    private $router;

    /**
     * @var CallbackNotifierInterface
     * @DI\Inject("barbon.payment_portal_bundle.callback_notifier.callback_notifier")
     */
    private $callbackNotifier;

    /**
     * Action to present the initial payment selection form, or jump to approval if only one payment type
     *
     * @param CustomerOrder $order
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Framework\Route("/order/{uuid}/payment-type")
     * @Framework\Template
     */
    public function paymentTypeSelectionAction(CustomerOrder $order)
    {
        $canSkipPaymentSelection = (1 == count($order->getPaymentTypes()));
        $instruction = null;
        if ($canSkipPaymentSelection) {
            $instruction = new PaymentInstruction($order->getAmount(), 'GBP', 'paypoint_hosted');
        }
        else {
            $form = $this->formFactory->create('jms_choose_payment_method', null, array(
                'amount' => $order->getAmount(),
                'currency' => 'GBP',
                'predefined_data' => array(
                    'paypoint_hosted' => array(/* Note: you can add custom parameters here to be passed to the payment processor
                        they can be picked up the in the custom data array that's serialized along
                        with the payment instruction */
                    ),
                ),
            ));

            if ($this->request->isMethod('POST')) {

                $form->submit($this->request);

                if ($form->isValid()) {
                    $instruction = $form->getData();
                }
            }

            if (null === $instruction) {
                return array(
                    'form' => $form->createView(),
                    'order' => $order,
                );
            }
        }

        $this->paymentPluginController->createPaymentInstruction($instruction);
        $order->setPaymentInstruction($instruction);
        $this->em->persist($instruction);
        $this->em->flush();

        return new RedirectResponse(
            $this->router->generate('barbon_paymentportal_payment_approvepayment',
                array('uuid' => $order->getUuid()))
        );
    }

    /**
     * Action to deposit the payment
     *
     * @param CustomerOrder $order
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Framework\Route("/order/{uuid}/approve")
     */
    public function approvePaymentAction(CustomerOrder $order)
    {
        $instruction = $order->getPaymentInstruction();
        $pendingTransaction = $instruction->getPendingTransaction();

        if (null === $pendingTransaction) {
            $payment = $this->paymentPluginController->createPayment(
                $instruction->getId(),
                $instruction->getAmount() - $instruction->getDepositedAmount()
            );
        }
        else {
            $payment = $pendingTransaction->getPayment();
        }

        $this->paymentPluginController->approve($payment->getId(), $payment->getTargetAmount());

        return new Response();
    }

    /**
     * Action to refund the payment
     *
     * @param CustomerOrder $order
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     *
     * @Framework\Route("/order/{uuid}/refund")
     * @Framework\Template
     */
    public function requestRefundAction(CustomerOrder $order)
    {
        $instruction = new PaymentInstruction($order->getAmount(), 'GBP', 'paypoint_hosted');

        $this->paymentPluginController->createPaymentInstruction($instruction);
        $order->setPaymentInstruction($instruction);
        $this->em->persist($instruction);
        $this->em->flush();

        $pendingTransaction = $instruction->getPendingTransaction();
        if (null === $pendingTransaction) {
            $payment = $this->paymentPluginController->createPayment(
                $instruction->getId(),
                $instruction->getAmount()
            );
        }
        else {
            $payment = $pendingTransaction->getPayment();
        }
        $oldState = $payment->getState();
        $this->setPaymentState($payment, PaymentInterface::STATE_APPROVED);

        $orderId = $order->getId();
        $amount = -abs($order->getAmount());
        try {
            /** @var CreditInterface $credit */
            $credit = $this->paymentPluginController->createDependentCredit($payment->getId(), $amount);

            /** @var Result $result */
            $result = $this->paymentPluginController->credit($credit->getId(), $amount);
        }
        catch (\Exception $e) {
            $this->setPaymentState($payment, $oldState);
            throw $e;
        }

        /** @var FinancialTransaction $transaction */
        $transaction = $result->getFinancialTransaction();
        if (null === $transaction) {
            throw new FinancialException('No financial transaction for refund');
        }

        /** @var CustomerOrder $order */
        $order = $this->em->getRepository('BarbonPaymentPortalBundle:CustomerOrder')->find($orderId);
        if ( ! $order) {
            throw new FinancialException('Could not find order');
        }

        /** @var CustomerOrder $parentOrder */
        $parentOrder = $this->em->getRepository('BarbonPaymentPortalBundle:CustomerOrder')->find($order->getParentId());
        if ( ! $parentOrder) {
            throw new FinancialException('Could not find parent order');
        }

        if ($order->getPaymentStatusCallbackUrl()) {

            $refundStatus = new OrderStatus();

            if (Result::STATUS_SUCCESS == $result->getStatus() && ! $result->isAttentionRequired()) {
                $refundStatus
                    ->setStatus(OrderStatus::STATUS_SUCCESS)
                    ->setCode(OrderStatus::CODE_PAYMENT_CAPTURED)
                    ->setMessage('Refund generated')
                ;
            }
            else {
                $message = 'Refund failed';
                /** @var ExtendedData $extendedData */
                $extendedData = $transaction->getExtendedData();
                if ($extendedData) {
                    if ($extendedData->has('message')) {
                        $message = $extendedData->get('message');
                    }
                }

                $refundStatus
                    ->setStatus(OrderStatus::STATUS_FAILURE)
                    ->setCode(OrderStatus::CODE_PAYMENT_FAILED_TO_CAPTURE) // todo: need more granular detail here, but I can't find any details on PayPoint's resp_code
                    ->setMessage($message)
                ;
            }

            $refundStatusResponse = new RefundStatusResponse();

            $refundStatusResponse
                ->setOriginalTransactionUuId($parentOrder->getTransId())
                ->setRefundTransactionUuId($transaction->getReferenceNumber())
                ->setOrderUuId($order->getUuid())
                ->setAmount($transaction->getProcessedAmount())
                ->setCurrency($order->getCurrency())
                ->setRefundStatus($refundStatus)
                ->setProcessor($result->getPaymentInstruction()->getPaymentSystemName())
                ->setPaymentType(CustomerOrder::PAYMENT_TYPE_CARD_PAYMENT)
                ->setPayload($order->getPaymentStatusCallbackPayload())
                ->setPayer($order->getPayer())
            ;

            $order
                ->setStatusResponse($refundStatusResponse)
                ->setTransId($transaction->getReferenceNumber())
            ;
            $this->em->persist($order);
            $this->em->flush();

            $refundStatusResponse
                ->setPayer($order->getPayer())
            ;

            $this->callbackNotifier->notifyCallback($order->getPaymentStatusCallbackUrl(), $refundStatusResponse);

            $redirectUrl = null;

            if (Result::STATUS_SUCCESS == $result->getStatus()) {
                $redirectUrl = $order->getRedirectOnSuccessUrl();
            }
            else if (Result::STATUS_FAILED == $result->getStatus()) {
                $redirectUrl = $order->getRedirectOnFailureUrl();
            }

            if ($redirectUrl) {
                return new Response("<html><head><script>(function () { window.location.replace('{$redirectUrl}') })()</script></head><body><a href=\"{$redirectUrl}\">Proceed to last step&hellp;</a></body></html>");
            }
        }

        return new Response();
    }

    /**
     * Action to repeat the payment
     *
     * @param CustomerOrder $order
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     *
     * @Framework\Route("/order/{uuid}/repeat")
     * @Framework\Template
     */
    public function requestRepeatAction(CustomerOrder $order)
    {
        $instruction = new PaymentInstruction($order->getAmount(), 'GBP', 'paypoint_hosted');

        $this->paymentPluginController->createPaymentInstruction($instruction);
        $order->setPaymentInstruction($instruction);
        $this->em->persist($instruction);
        $this->em->flush();

        $pendingTransaction = $instruction->getPendingTransaction();
        if (null === $pendingTransaction) {
            $payment = $this->paymentPluginController->createPayment(
                $instruction->getId(),
                $instruction->getAmount()
            );
        }
        else {
            $payment = $pendingTransaction->getPayment();
        }

        $orderId = $order->getId();
        $amount = -abs($order->getAmount());

        /** @var Result $result */
        $result = $this->paymentPluginController->approveAndDeposit($payment->getId(), $amount);

        /** @var FinancialTransaction $transaction */
        $transaction = $result->getFinancialTransaction();
        if (null === $transaction) {
            throw new FinancialException('No financial transaction for repeat');
        }

        /** @var CustomerOrder $order */
        $order = $this->em->getRepository('BarbonPaymentPortalBundle:CustomerOrder')->find($orderId);
        if ( ! $order) {
            throw new FinancialException('Could not find order');
        }

        /** @var CustomerOrder $parentOrder */
        $parentOrder = $this->em->getRepository('BarbonPaymentPortalBundle:CustomerOrder')->find($order->getParentId());
        if ( ! $parentOrder) {
            throw new FinancialException('Could not find parent order');
        }

        if ($order->getPaymentStatusCallbackUrl()) {

            $repeatStatus = new OrderStatus();

            if (Result::STATUS_SUCCESS == $result->getStatus() && ! $result->isAttentionRequired()) {
                $repeatStatus
                    ->setStatus(OrderStatus::STATUS_SUCCESS)
                    ->setCode(OrderStatus::CODE_REPEAT_PAYMENT_CAPTURED)
                    ->setMessage('Repeat generated')
                ;
            }
            else {
                $message = 'Repeat failed';
                /** @var ExtendedData $extendedData */
                $extendedData = $transaction->getExtendedData();
                if ($extendedData) {
                    if ($extendedData->has('message')) {
                        $message = $extendedData->get('message');
                    }
                }

                $repeatStatus
                    ->setStatus(OrderStatus::STATUS_FAILURE)
                    ->setCode(OrderStatus::CODE_REPEAT_PAYMENT_FAILED_TO_CAPTURE) // todo: need more granular detail here, but I can't find any details on PayPoint's resp_code
                    ->setMessage($message)
                ;
            }

            $repeatStatusResponse = new RepeatStatusResponse();

            $repeatStatusResponse
                ->setOriginalTransactionUuId($parentOrder->getTransId())
                ->setRepeatTransactionUuId($transaction->getReferenceNumber())
                ->setOrderUuId($order->getUuid())
                ->setAmount($transaction->getProcessedAmount())
                ->setCurrency($order->getCurrency())
                ->setRepeatStatus($repeatStatus)
                ->setProcessor($result->getPaymentInstruction()->getPaymentSystemName())
                ->setPaymentType(CustomerOrder::PAYMENT_TYPE_CARD_PAYMENT)
                ->setPayload($order->getPaymentStatusCallbackPayload())
                ->setPayer($order->getPayer())
            ;

            $order
                ->setStatusResponse($repeatStatusResponse)
                ->setTransId($transaction->getReferenceNumber())
            ;
            $this->em->persist($order);
            $this->em->flush();

            $repeatStatusResponse
                ->setPayer($order->getPayer())
            ;

            $this->callbackNotifier->notifyCallback($order->getPaymentStatusCallbackUrl(), $repeatStatusResponse);

            $redirectUrl = null;

            if (Result::STATUS_SUCCESS == $result->getStatus()) {
                $redirectUrl = $order->getRedirectOnSuccessUrl();
            }
            else if (Result::STATUS_FAILED == $result->getStatus()) {
                $redirectUrl = $order->getRedirectOnFailureUrl();
            }

            if ($redirectUrl) {
                return new Response("<html><head><script>(function () { window.location.replace('{$redirectUrl}') })()</script></head><body><a href=\"{$redirectUrl}\">Proceed to last step&hellp;</a></body></html>");
            }
        }

        return new Response();
    }

    /**
    * Set state on a given Payment.
    *
    * @param PaymentInterface $payment Payment entity
    * @param integer $state New state
    */
    private function setPaymentState($payment, $state)
    {
        $payment->setState($state);
        $this->em->persist($payment);
        $this->em->flush();
    }

    /**
     * Action to get user stylesheet
     *
     * @param \Barbon\PaymentPortalBundle\Entity\User $user
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Framework\Route("/user/{id}/payment-portal-stylesheet.css")
     */
    public function getUserPaymentPortalStylesheetAction(User $user)
    {
        // todo: need to handle this using the cache kernel
        return new PublicCacheResponse(600, $user->getPaymentPortalCss());
    }
}
