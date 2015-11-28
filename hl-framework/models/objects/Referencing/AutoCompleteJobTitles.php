<?php

/**
 * Copyright 2015 Barbon Insurance Group.
 *
 * Closed source, all rights reserved.
 *
 * Unless required by applicable law or agreed to in writing, 
 * distribution is prohibited.
 */

/**
 * Model definition for the autocomplete_job_titles datasource
 * representing the job title classifications that a user can select from
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class Model_Referencing_AutoCompleteJobTitles
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var bool
     */
    protected $fastTrack;

    /**
     * Set $id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Get $id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set $title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;

        return $this;
    }

    /**
     * Get $id
     *
     * @return int
     */
    public function getTitle()
    {
            return $this->title;
    }

    /**
     * Set $fastTrack
     *
     * @param bool $fastTrack
     *
     * @return $this
     */
    public function setFastTrack($fastTrack)
    {
        $this->fastTrack = (boolean) $fastTrack;

        return $this;
    }

    /**
     * Get $fastTrack
     *
     * @return bool
     */
    public function getFastTrack()
    {
        return $this->fastTrack;
    }
}