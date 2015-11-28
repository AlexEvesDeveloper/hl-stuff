<?php

namespace Barbondev\IRISSDK\System\Agent\Model;

use Barbondev\IRISSDK\Common\Model\AbstractAuthorisation;
use Guzzle\Service\Command\OperationCommand;

/**
 * Class BranchAuthorisation
 *
 * @package Barbondev\IRISSDK\System\Agent\Model
 * @author Paul Swift <paul.swift@barbon.com>
 */
class BranchAuthorisation extends AbstractAuthorisation
{
    /**
     * @var string
     */
    private $agentBranchUuid;

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
     * Create a response model object from a completed command
     *
     * @param OperationCommand $command That serialized the request
     *
     * @return self
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        return self::hydrateModelProperties(
            new self(),
            $data,
            array(
                'agentBranchId' => 'agentBranchUuId'
            ),
            array()
        );
    }
}