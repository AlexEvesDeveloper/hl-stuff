<?php

namespace Barbondev\IRISSDK\Agent\Agent\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;

/**
 * Class AgentUser
 *
 * @package Barbondev\IRISSDK\Agent\Agent\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class AgentUser extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $agentUserUuId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var bool
     */
    private $hasReports;

    /**
     * @var bool
     */
    private $hasAccounts;

    /**
     * @var int
     */
    private $status;

    /**
     * @var bool
     */
    private $isExternalNewsEnabled;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        return self::hydrateModelProperties(
            new self(),
            $data,
            array(
                'agentUserId' => 'agentUserUuId',
            )
        );
    }

    /**
     * Set agentUserUuId
     *
     * @param string $agentUserUuId
     * @return $this
     */
    public function setAgentUserUuId($agentUserUuId)
    {
        $this->agentUserUuId = $agentUserUuId;
        return $this;
    }

    /**
     * Get agentUserUuId
     *
     * @return string
     */
    public function getAgentUserUuId()
    {
        return $this->agentUserUuId;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set hasAccounts
     *
     * @param boolean $hasAccounts
     * @return $this
     */
    public function setHasAccounts($hasAccounts)
    {
        $this->hasAccounts = $hasAccounts;
        return $this;
    }

    /**
     * Get hasAccounts
     *
     * @return boolean
     */
    public function getHasAccounts()
    {
        return $this->hasAccounts;
    }

    /**
     * Set hasReports
     *
     * @param boolean $hasReports
     * @return $this
     */
    public function setHasReports($hasReports)
    {
        $this->hasReports = $hasReports;
        return $this;
    }

    /**
     * Get hasReports
     *
     * @return boolean
     */
    public function getHasReports()
    {
        return $this->hasReports;
    }

    /**
     * Set isExternalNewsEnabled
     *
     * @param boolean $isExternalNewsEnabled
     * @return $this
     */
    public function setIsExternalNewsEnabled($isExternalNewsEnabled)
    {
        $this->isExternalNewsEnabled = $isExternalNewsEnabled;
        return $this;
    }

    /**
     * Get isExternalNewsEnabled
     *
     * @return boolean
     */
    public function getIsExternalNewsEnabled()
    {
        return $this->isExternalNewsEnabled;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}