<?php

namespace Barbon\PaymentPortalBundle\EventListener;

use Barbon\PaymentPortalBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserEventSubscriber
 *
 * @package Barbon\PaymentPortalBundle\EventListener
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @DI\Service
 * @DI\Tag("doctrine.event_subscriber")
 */
class UserEventSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     *
     * @DI\InjectParams({
     *     "container"=@DI\Inject("service_container")
     * })
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
     * prePersist handler
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {

            if ($entity->getApiKey()) {
                return;
            }

            $em = $this->container->get('doctrine.orm.entity_manager');

            do {
                $key = sha1(mt_rand() . time() . uniqid(null, true));
                $user = $em->getRepository(get_class($entity))->findOneBy(array(
                    'apiKey' => $key,
                ));
            }
            while ($user);

            $entity->setApiKey($key);
        }
    }
}