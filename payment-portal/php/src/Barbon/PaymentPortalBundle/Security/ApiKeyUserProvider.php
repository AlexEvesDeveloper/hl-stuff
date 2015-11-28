<?php

namespace Barbon\PaymentPortalBundle\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class ApiKeyUserProvider
 *
 * @package Barbon\PaymentPortalBundle\Security
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @DI\Service
 */
class ApiKeyUserProvider implements UserProviderInterface
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * Constructor
     *
     * @param ObjectManager $em
     *
     * @DI\InjectParams({
     *     "em"=@DI\Inject("doctrine.orm.entity_manager")
     * })
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository('BarbonPaymentPortalBundle:User')->findOneBy(array(
            'apiKey' => $username,
        ));

        if ( ! $user) {
            throw new UsernameNotFoundException(sprintf('User could not be found for API key "%s"', $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException('Authentication is stateless, session token is not available');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return ('Barbon\PaymentPortalBundle\Entity\User' === $class);
    }
}