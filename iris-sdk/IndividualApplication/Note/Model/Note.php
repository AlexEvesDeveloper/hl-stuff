<?php

namespace Barbondev\IRISSDK\IndividualApplication\Note\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Common\Collection;

/**
 * Class Note
 *
 * @package Barbondev\IRISSDK\IndividualApplication\Note\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Note extends AbstractResponseModel
{
    /**
     * @var int
     */
    private $noteId;

    /**
     * @var string
     */
    public $creatorType;

    /**
     * @var string
     */
    public $createdBy;

    /**
     * @var string
     */
    public $recordedAt;

    /**
     * @var string
     */
    public $note;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        // Collection of notes
        if (self::isResponseDataIndexedArray($data)) {

            $notes = new Collection();

            foreach ($data as $key => $noteData) {
                $notes->add($key, self::hydrateModelProperties(
                    new self(),
                    $noteData
                ));
            }

            return $notes;
        }

        // Single note
        else {

            return self::hydrateModelProperties(
                new self(),
                $data
            );
        }
    }

    /**
     * Get noteId
     *
     * @return int
     */
    public function getNoteId()
    {
        return $this->noteId;
    }

    /**
     * Set noteId
     *
     * @param int $noteId
     * @return $this
     */
    public function setNoteId($noteId)
    {
        $this->noteId = $noteId;
        return $this;
    }

    /**
     * Set creatorType
     *
     * @param string $creatorType
     * @return $this
     */
    public function setCreatorType($creatorType)
    {
        $this->creatorType = $creatorType;
        return $this;
    }

    /**
     * Get creatorType
     *
     * @return string
     */
    public function getCreatorType()
    {
        return $this->creatorType;
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

    /**
     * Set createdBy
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}