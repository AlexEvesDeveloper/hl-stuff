<?php

namespace Iris\Authentication;

/**
 * Class AgentAuthenticator
 *
 * @package Iris\Authentication
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class AgentAuthenticator extends AbstractAuthenticator
{
    /**
     * Authenticate an agent, passing back the authorisation token
     *
     * @param string $agentSchemeNumber
     * @param string $username
     * @param string $password
     * @return AuthorizationTokenInterface
     */
    public function authenticate($agentSchemeNumber, $username, $password)
    {
        $authorization = $this
            ->irisClientRegistry
            ->getSystemContext()
            ->getAgentClient()
            ->authenticate(array(
                'agentSchemeNumber' => $agentSchemeNumber,
                'username' => $username,
                'password' => $password,
            ))
        ;

        return new AgentAuthorizationToken(
            $authorization->getConsumerKey(),
            $authorization->getConsumerSecret(),
            $authorization->getAgentBranchUuid()
        );
    }
}