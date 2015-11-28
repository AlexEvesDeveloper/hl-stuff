<?php

namespace Barbondev\IRISSDK\IndividualApplication\Activity\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Common\Collection;

/**
 * Class Activity
 *
 * @package Barbondev\IRISSDK\IndividualApplication\Activity\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Activity extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $note;

    /**
     * @var string
     */
    private $recordedAt;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $activities = new Collection();

        foreach ($command->getResponse()->json() as $key => $activity) {
            $activities->add($key, self::hydrateModelProperties(
                new self(),
                $activity
            ));
        }

        return $activities;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return $this
     */
    public function setNote($note)
    {
        $this->note = $note;
        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set recordedAt
     *
     * @param string $recordedAt
     * @return $this
     */
    public function setRecordedAt($recordedAt)
    {
        $this->recordedAt = $recordedAt;
        return $this;
    }

    /**
     * Get recordedAt
     *
     * @return string
     */
    public function getRecordedAt()
    {
        return $this->recordedAt;
    }
}