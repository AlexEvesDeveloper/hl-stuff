<?php

namespace Barbon\HostedApi\SecurityBundle\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use GuzzleHttp\Exception\RequestException;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;

abstract class AbstractUserProvider implements UserProviderInterface
{
    /**
     * @var IrisEntityManager
     */
    protected $irisEntityManager;

    /**
     * {@inheritdoc}
     */
    abstract public function loadUserByUsername($credentials);

    /**
     * Constructor
     *
     * @param IrisEntityManager $irisEntityManager
     */
    public function __construct(IrisEntityManager $irisEntityManager)
    {
        $this->irisEntityManager = $irisEntityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        // set the consumer key and secret into the OAuth client
        $this->irisEntityManager->getClient()->resume(array(
            'consumerKey' => $user->getConsumerKey(),
            'consumerSecret' => $user->getConsumerSecret()
        ));

        return $user;
    }
}
