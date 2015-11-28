<?php

namespace Barbon\PaymentPortalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;
use Symfony\Component\Validator\Constraints as Assert;
use Barbon\PaymentPortalBundle\Model\PaymentStatusResponse;
use Barbon\PaymentPortalBundle\Model\RefundStatusResponse;
use Barbon\PaymentPortalBundle\Model\RepeatStatusResponse;

/**
 * Class CustomerOrder
 *
 * @package Barbon\PaymentPortalBundle\Entity
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @ORM\Table(name="customer_orders")
 * @ORM\Entity
 */
class CustomerOrder
{
    /**
     * Identifier for transactions of type payment
     */
    const TRANSACTION_TYPE_PAYMENT = 1;

    /**
     * Identifier for transactions of type refund
     */
    const TRANSACTION_TYPE_REFUND = 2;

    /**
     * Identifier for transactions of type repeat payment
     */
    const TRANSACTION_TYPE_REPEAT = 3;

    /**
     * Identifier for payments of type credit card
     */
    const PAYMENT_TYPE_CARD_PAYMENT = 1;

    /**
     * Identifier for payments of type direct debit
     */
    const PAYMENT_TYPE_DIRECT_DEBIT = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, unique=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=5)
     * @Assert\NotBlank
     * @Assert\GreaterThan(value=0)
     * @Assert\Type(type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3)
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    private $currency;

    /**
     * @var array
     *
     * @ORM\Column(name="payment_types", type="json_array")
     * @Assert\Type(type="array")
     */
    private $paymentTypes;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_status_callback_url", type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    private $paymentStatusCallbackUrl;

    /**
     * @var array
     *
     * @ORM\Column(name="payment_status_callback_payload", type="json_array")
     * @Assert\NotBlank
     * @Assert\Type(type="array")
     */
    private $paymentStatusCallbackPayload;

    /**
     * @var string
     *
     * @ORM\Column(name="redirect_on_success_url", type="string", length=255, nullable=true)
     */
    private $redirectOnSuccessUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="redirect_on_failure_url", type="string", length=255, nullable=true)
     */
    private $redirectOnFailureUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var PaymentInstruction
     *
     * @ORM\OneToOne(targetEntity="JMS\Payment\CoreBundle\Entity\PaymentInstruction")
     * @ORM\JoinColumn(name="payment_instruction_id")
     */
    private $paymentInstruction;

    /**
     * @var Payer
     *
     * @ORM\OneToOne(targetEntity="Barbon\PaymentPortalBundle\Entity\Payer", cascade={"all"})
     * @ORM\JoinColumn(name="payer_id", nullable=true)
     */
    private $payer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Barbon\PaymentPortalBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", nullable=false)
     */
    private $user;

    /**
     * @var PaymentStatusResponse|RefundStatusResponse|RepeatStatusResponse
     *
     * As the status on an order is one-shot it must be backed into the order
     *
     * @ORM\Column(name="status_response", type="object", nullable=true)
     */
    private $statusResponse;

    /**
     * @var string
     *
     * The trans_id extracted from the status response
     *
     * @ORM\Column(name="trans_id", type="string", length=255, nullable=true)
     */
    private $transId;

    /**
     * @var string
     *
     * The transaction type (1=payment, 2=refund)
     *
     * @ORM\Column(name="transaction_type", type="integer")
     */
    private $transactionType;

    /**
     * @var string
     *
     * The id of the parent record if a refund
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     * @return $this
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return CustomerOrder
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get paymentInstruction
     *
     * @return PaymentInstruction
     */
    public function getPaymentInstruction()
    {
        return $this->paymentInstruction;
    }

    /**
     * Set paymentInstruction
     *
     * @param PaymentInstruction|null $paymentInstruction
     * @return $this
     */
    public function setPaymentInstruction(PaymentInstruction $paymentInstruction = null)
    {
        $this->paymentInstruction = $paymentInstruction;
        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get paymentStatusCallbackPayload
     *
     * @return array
     */
    public function getPaymentStatusCallbackPayload()
    {
        return $this->paymentStatusCallbackPayload;
    }

    /**
     * Set paymentStatusCallbackPayload
     *
     * @param array $paymentStatusCallbackPayload
     * @return $this
     */
    public function setPaymentStatusCallbackPayload(array $paymentStatusCallbackPayload)
    {
        $this->paymentStatusCallbackPayload = $paymentStatusCallbackPayload;
        return $this;
    }

    /**
     * Get paymentStatusCallbackUrl
     *
     * @return string
     */
    public function getPaymentStatusCallbackUrl()
    {
        return $this->paymentStatusCallbackUrl;
    }

    /**
     * Set paymentStatusCallbackUrl
     *
     * @param string $paymentStatusCallbackUrl
     * @return $this
     */
    public function setPaymentStatusCallbackUrl($paymentStatusCallbackUrl)
    {
        $this->paymentStatusCallbackUrl = $paymentStatusCallbackUrl;
        return $this;
    }

    /**
     * Get paymentTypes
     *
     * @return array
     */
    public function getPaymentTypes()
    {
        return $this->paymentTypes;
    }

    /**
     * Set paymentTypes
     *
     * @param array $paymentTypes
     * @return $this
     */
    public function setPaymentTypes(array $paymentTypes)
    {
        $this->paymentTypes = $paymentTypes;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get payer
     *
     * @return Payer
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * Set payer
     *
     * @param Payer|null $payer
     * @return $this
     */
    public function setPayer(Payer $payer = null)
    {
        $this->payer = $payer;
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get statusResponse
     *
     * @return PaymentStatusResponse|RefundStatusResponse|RepeatStatusResponse
     */
    public function getStatusResponse()
    {
        return $this->statusResponse;
    }

    /**
     * Set statusResponse
     *
     * @param PaymentStatusResponse|RefundStatusResponse|RepeatStatusResponse $statusResponse
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setStatusResponse($statusResponse = null)
    {
	    if (
            $statusResponse instanceof PaymentStatusResponse ||
            $statusResponse instanceof RefundStatusResponse ||
            $statusResponse instanceof RepeatStatusResponse
        ) {
            $this->statusResponse = $statusResponse;
            return $this;
	    }
        throw new \InvalidArgumentException('Invalid status response');
    }

    /**
     * Get transId
     *
     * @return string
     */
    public function getTransId()
    {
        return $this->transId;
    }

    /**
     * Set transId
     *
     * @param string $transId
     * @return $this
     */
    public function setTransId($transId)
    {
        $this->transId = $transId;
        return $this;
    }

    /**
     * Gets transactionType
     *
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * Sets transactionType
     *
     * @param string $transactionType
     * @return $this
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
        return $this;
    }

    /**
     * Gets the parentId
     *
     * @return string
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Sets the parentId
     *
     * @param string $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * Get redirectOnFailureUrl
     *
     * @return string
     */
    public function getRedirectOnFailureUrl()
    {
        return $this->redirectOnFailureUrl;
    }

    /**
     * Set redirectOnFailureUrl
     *
     * @param string $redirectOnFailureUrl
     * @return $this
     */
    public function setRedirectOnFailureUrl($redirectOnFailureUrl)
    {
        $this->redirectOnFailureUrl = $redirectOnFailureUrl;
        return $this;
    }

    /**
     * Get redirectOnSuccessUrl
     *
     * @return string
     */
    public function getRedirectOnSuccessUrl()
    {
        return $this->redirectOnSuccessUrl;
    }

    /**
     * Set redirectOnSuccessUrl
     *
     * @param string $redirectOnSuccessUrl
     * @return $this
     */
    public function setRedirectOnSuccessUrl($redirectOnSuccessUrl)
    {
        $this->redirectOnSuccessUrl = $redirectOnSuccessUrl;
        return $this;
    }
}
