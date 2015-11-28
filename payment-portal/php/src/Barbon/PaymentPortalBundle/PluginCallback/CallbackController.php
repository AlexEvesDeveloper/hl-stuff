<?php

namespace Barbon\PaymentPortalBundle\PluginCallback;

use Barbon\PaymentPortalBundle\Entity\CustomerOrder;
use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;

/**
 * Class CurlPostCallbackNotifier
 *
 * @package Barbon\PaymentPortalBundle\PluginCallback
 * @author April Portus <april.portus@barbon.com>
 */
class CallbackController
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Gets the data value from the extended data of the parent
     *
     * @param PaymentInstructionInterface $paymentInstruction
     * @param string $key
     * @return null|string
     */
    public function getDataValue(PaymentInstructionInterface $paymentInstruction, $key)
    {
        $repository = $this->em->getRepository('Barbon\PaymentPortalBundle\Entity\CustomerOrder');
        $customerOrder = $repository->findOneBy(array('paymentInstruction' => $paymentInstruction));
        if (null === $customerOrder) {
            return null;
        }

        $parentId = $customerOrder->getParentId();
        /** @var CustomerOrder $parentOrder */
        $parentOrder = $repository->find($parentId);
        if (null === $parentOrder) {
            return null;
        }
        $parentPaymentInstruction = $parentOrder->getPaymentInstruction();
        $extendedData = $parentPaymentInstruction->getExtendedData();
        if (!$extendedData->has($key)) {
            return null;
        }
        return $extendedData->get($key);
    }

}
