<?php

namespace Barbon\PaymentPortalBundle\EventListener;

use Barbon\PaymentPortalBundle\Entity\CustomerOrder;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Barbon\PaymentPortalBundle\UuId\UuIdGeneratorInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class CustomerOrderSubscriber
 *
 * @package Barbon\PaymentPortalBundle\EventListener
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @DI\Service
 * @DI\Tag("doctrine.event_subscriber")
 */
class CustomerOrderSubscriber implements EventSubscriber
{
    /**
     * @var UuIdGeneratorInterface
     */
    private $uuIdGenerator;

    /**
     * Constructor
     *
     * @param UuIdGeneratorInterface $uuIdGenerator
     *
     * @DI\InjectParams({
     *     "uuIdGenerator"=@DI\Inject("barbon.payment_portal_bundle.uu_id.uu_id_generator")
     * })
     */
    public function __construct(UuIdGeneratorInterface $uuIdGenerator)
    {
        $this->uuIdGenerator = $uuIdGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
        );
    }

    /**
     * prePersist lifecycle event handler
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof CustomerOrder) {

            // todo: assuring universal uniqueness should be the responsibility of the UUID generator service
            do {
                $uuid = $this->uuIdGenerator->generate();
                $customerOrder = $args->getEntityManager()->getRepository('BarbonPaymentPortalBundle:CustomerOrder')->findBy(array(
                    'uuid' => $uuid,
                ));
            } while ($customerOrder);

            $entity->setUuid($uuid);
        }
    }
}