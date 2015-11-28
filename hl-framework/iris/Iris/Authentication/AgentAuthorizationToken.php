<?php

namespace Iris\Authentication;

/**
 * Class AgentAuthorizationToken
 *
 * @package Iris\Authentication
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class AgentAuthorizationToken extends AbstractAuthorizationToken
{
    /**
     * @var string
     */
    private $agentBranchUuid;

    /**
     * Constructor
     *
     * @param string|null $consumerKey
     * @param string|null $consumerSecret
     * @param string|null $agentBranchUuid
     */
    public function __construct($consumerKey = null, $consumerSecret = null, $agentBranchUuid = null)
    {
        parent::__construct($consumerKey, $consumerSecret);

        $this->agentBranchUuid = $agentBranchUuid;

        if (null === $agentBranchUuid) {

            $sessionData = $this->read();

            if (isset($sessionData[$this->getName()])) {

                $sessionData = $sessionData[$this->getName()];

                if ($sessionData instanceof self) {

                    $this->agentBranchUuid = $sessionData->getAgentBranchUuid();
                }
            }
        }
    }

    /**
     * Set the agent branch UUID
     *
     * @param string $agentBranchUuid
     * @return $this
     */
    public function setAgentBranchUuid($agentBranchUuid)
    {
        $this->agentBranchUuid = $agentBranchUuid;

        return $this;
    }

    /**
     * Get the agent branch UUID
     *
     * @return string
     */
    public function getAgentBranchUuid()
    {
        return $this->agentBranchUuid;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'agent';
    }
}