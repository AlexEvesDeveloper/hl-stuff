<?php

namespace Barbon\PaymentPortalBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

/**
 * Class ApiKeyAuthenticator
 *
 * @package Barbon\PaymentPortalBundle\Security
 * @author Ashley Dawson <ashley.dawson@barbon.co.uk>
 *
 * @DI\Service
 */
class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var ApiKeyUserProvider
     */
    private $userProvider;

    /**
     * Constructor
     *
     * @param ApiKeyUserProvider $userProvider
     *
     * @DI\InjectParams({
     *     "userProvider"=@DI\Inject("barbon.payment_portal_bundle.security.api_key_user_provider")
     * })
     */
    public function __construct(ApiKeyUserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function createToken(Request $request, $providerKey)
    {
        $apiKey = $request->query->get('apiKey');

        if ( ! $apiKey) {
            throw new BadCredentialsException('No API key found');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }

    /**
     * {@inheritdoc}
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $apiKey = $token->getCredentials();

        if ( ! $apiKey) {
            throw new AuthenticationException(sprintf('API key "%s" does not exist', $apiKey));
        }

        $user = $this->userProvider->loadUserByUsername($apiKey);

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return (($token instanceof PreAuthenticatedToken) && ($token->getProviderKey() === $providerKey));
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response(json_encode(array(
            'code' => 403,
            'message' => 'Authentication Failed',
        )), 403, array(
            'Content-Type' => 'application/json',
        ));
    }
}