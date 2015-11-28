<?php

namespace Barbon\PaymentPortalBundle\Controller;

use Barbon\PaymentPortalBundle\Entity\CustomerOrder;
use Barbon\PaymentPortalBundle\OrderHandoff\OrderHandoffBuilder;
use JMS\Serializer\Exception\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Framework;
use FOS\RestBundle\Controller\Annotations as REST;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ApiController
 *
 * @package Barbon\PaymentPortalBundle\Controller
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @Framework\Route("/api/v1")
 */
class ApiController
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     * @DI\Inject
     */
    private $request;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject
     */
    private $doctrine;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     * @DI\Inject("form.factory")
     */
    private $formFactory;

    /**
     * @var \Barbon\PaymentPortalBundle\OrderHandoff\OrderHandoffBuilderInterface
     */
    private $paymentHandoffBuilder;

    /**
     * @var \Barbon\PaymentPortalBundle\OrderHandoff\OrderHandoffBuilderInterface
     */
    private $refundHandoffBuilder;

    /**
     * @var \Barbon\PaymentPortalBundle\OrderHandoff\OrderHandoffBuilderInterface
     */
    private $repeatHandoffBuilder;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     * @DI\Inject("security.context")
     */
    private $security;

    /**
     * Constructor
     *
     * @param RouterInterface $router
     * @param string $orderHandoffUrlRouteName
     * @param string $refundHandoffUrlRouteName
     * @param string $repeatHandoffUrlRouteName
     *
     * @DI\InjectParams({
     *     "router"=@DI\Inject("router"),
     *     "orderHandoffUrlRouteName"=@DI\Inject("%payment_handoff_url_route_name%"),
     *     "refundHandoffUrlRouteName"=@DI\Inject("%refund_handoff_url_route_name%"),
     *     "repeatHandoffUrlRouteName"=@DI\Inject("%repeat_handoff_url_route_name%")
     * })
     */
    public function __construct(
        RouterInterface $router,
        $orderHandoffUrlRouteName,
        $refundHandoffUrlRouteName,
        $repeatHandoffUrlRouteName
    ) {
        $this->router = $router;
        $this->paymentHandoffBuilder = new OrderHandoffBuilder($router, $orderHandoffUrlRouteName);
        $this->refundHandoffBuilder = new OrderHandoffBuilder($router, $refundHandoffUrlRouteName);
        $this->repeatHandoffBuilder = new OrderHandoffBuilder($router, $repeatHandoffUrlRouteName);
    }

    /**
     * Create a new order and return the start URL of the
     * payment process
     *
     * @REST\Post("/orders", defaults={"_format"="json"})
     * @REST\View
     */
    public function postOrderAction()
    {
        $form = $this->formFactory->create('order');

        $form->submit($this->request);

        if ($form->isValid()) {

            /** @var \Barbon\PaymentPortalBundle\Entity\CustomerOrder $order */
            $order = $form->getData();
            $order
                ->setUser($this->security->getToken()->getUser())
                ->setTransactionType($order::TRANSACTION_TYPE_PAYMENT)
            ;

            $em = $this->doctrine->getManager();
            $em->persist($order);
            $em->flush();

            return $this->paymentHandoffBuilder->buildOrderHandoff($order);
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Generate a refund
     *
     * @REST\Post("/refunds", defaults={"_format"="json"})
     * @REST\View
     */
    public function postRefundAction()
    {
        $form = $this->formFactory->create('refund');

        $form->submit($this->request);

        if ($form->isValid()) {

            /** @var \Barbon\PaymentPortalBundle\Entity\CustomerOrder $refund */
            $refund = $form->getData();

            /** @var \Barbon\PaymentPortalBundle\Entity\CustomerOrder $parentOrder */
            $parentOrder = $this->doctrine
                ->getRepository('BarbonPaymentPortalBundle:CustomerOrder')
                ->findOneBy(array('transId' => $refund->getTransId()))
            ;
            if ( ! $parentOrder) {
                $form->get('transId')->addError(new FormError('Transaction not found'));
            }
            else if (CustomerOrder::TRANSACTION_TYPE_PAYMENT != $parentOrder->getTransactionType()) {
                $form->get('transId')->addError(new FormError('Invalid transaction, it must be a payment'));
            }
            else {
                $refundOrder = new CustomerOrder();
                $refundOrder
                    ->setUser($this->security->getToken()->getUser())
                    ->setPayer($parentOrder->getPayer())
                    ->setAmount($refund->getAmount())
                    ->setCurrency($refund->getCurrency())
                    ->setPaymentTypes($parentOrder->getPaymentTypes())
                    ->setPaymentStatusCallbackUrl($refund->getPaymentStatusCallbackUrl())
                    ->setPaymentStatusCallbackPayload($refund->getPaymentStatusCallbackPayload())
                    ->setRedirectOnSuccessUrl($refund->getRedirectOnSuccessUrl())
                    ->setRedirectOnFailureUrl($refund->getRedirectOnFailureUrl())
                    ->setTransactionType($refundOrder::TRANSACTION_TYPE_REFUND)
                    ->setParentId($parentOrder->getId())
                ;

                $em = $this->doctrine->getManager();
                $em->persist($refundOrder);
                $em->flush();

                return $this->refundHandoffBuilder->buildOrderHandoff($refundOrder);
            }
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Generate a repeat payment
     *
     * @REST\Post("/repeats", defaults={"_format"="json"})
     * @REST\View
     */
    public function postRepeatAction()
    {
        $form = $this->formFactory->create('repeat');

        $form->submit($this->request);

        if ($form->isValid()) {

            /** @var \Barbon\PaymentPortalBundle\Entity\CustomerOrder $repeat */
            $repeat = $form->getData();

            /** @var \Barbon\PaymentPortalBundle\Entity\CustomerOrder $parentOrder */
            $parentOrder = $this->doctrine
                ->getRepository('BarbonPaymentPortalBundle:CustomerOrder')
                ->findOneBy(array('transId' => $repeat->getTransId()))
            ;
            if ( ! $parentOrder) {
                $form->get('transId')->addError(new FormError('Transaction not found'));
            }
            else if (CustomerOrder::TRANSACTION_TYPE_PAYMENT != $parentOrder->getTransactionType()) {
                $form->get('transId')->addError(new FormError('Invalid transaction, it must be a payment'));
            }
            else {
                $repeatOrder = new CustomerOrder();
                $repeatOrder
                    ->setUser($this->security->getToken()->getUser())
                    ->setPayer($parentOrder->getPayer())
                    ->setAmount($repeat->getAmount())
                    ->setCurrency($repeat->getCurrency())
                    ->setPaymentTypes($parentOrder->getPaymentTypes())
                    ->setPaymentStatusCallbackUrl($repeat->getPaymentStatusCallbackUrl())
                    ->setPaymentStatusCallbackPayload($repeat->getPaymentStatusCallbackPayload())
                    ->setRedirectOnSuccessUrl($repeat->getRedirectOnSuccessUrl())
                    ->setRedirectOnFailureUrl($repeat->getRedirectOnFailureUrl())
                    ->setTransactionType($repeatOrder::TRANSACTION_TYPE_REPEAT)
                    ->setParentId($parentOrder->getId())
                ;

                $em = $this->doctrine->getManager();
                $em->persist($repeatOrder);
                $em->flush();

                return $this->repeatHandoffBuilder->buildOrderHandoff($repeatOrder);
            }
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Get the order payment status
     *
     * @REST\Get("/orders/{uuid}/paymentStatus", defaults={"_format"="json"})
     * @REST\View
     */
    public function getOrderPaymentStatusAction(CustomerOrder $order)
    {
        if ($order->getUser()->getId() != $this->security->getToken()->getUser()->getId()) {
            throw new AccessDeniedException('Access denied');
        }
        else if (CustomerOrder::TRANSACTION_TYPE_PAYMENT != $order->getTransactionType()) {
            throw new InvalidArgumentException('Order was not a payment');
        }

        $paymentStatusResponse = $order->getStatusResponse();

        if ( ! $paymentStatusResponse) {
            throw new NotFoundHttpException(
                sprintf('Payment status for the order "%s" could not be found', $order->getUuid())
            );
        }

        return $paymentStatusResponse;
    }

    /**
     * Get the refund status
     *
     * @REST\Get("/orders/{uuid}/refundStatus", defaults={"_format"="json"})
     * @REST\View
     */
    public function getRefundStatusAction(CustomerOrder $order)
    {
        if ($order->getUser()->getId() != $this->security->getToken()->getUser()->getId()) {
            throw new AccessDeniedException('Access denied');
        }
        else if (CustomerOrder::TRANSACTION_TYPE_REFUND != $order->getTransactionType()) {
            throw new InvalidArgumentException('Order was not a refund');
        }

        $refundResponse = $order->getStatusResponse();

        if ( ! $refundResponse) {
            throw new NotFoundHttpException(
                sprintf('Payment status for the order "%s" could not be found', $order->getUuid())
            );
        }

        return $refundResponse;
    }
}