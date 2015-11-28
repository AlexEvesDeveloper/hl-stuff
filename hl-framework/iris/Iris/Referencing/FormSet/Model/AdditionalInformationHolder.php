<?php

namespace Iris\Referencing\FormSet\Model;

/**
 * Class AdditionalInformationHolder
 *
 * @package Iris\Referencing\FormSet\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class AdditionalInformationHolder
{
    /**
     * @var int
     */
    private $noteId;

    /**
     * @var string
     */
    private $message;

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
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}