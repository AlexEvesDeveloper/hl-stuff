<?php

/**
 * Represents an external news source in the system.
 *
 * @category   Model
 * @package    Model_Cms
 * @subpackage ExternalNews
 */
class Model_Cms_ExternalNews_Source extends Model_Abstract {

    /**
     * The source's unique ID.
     *
     * @var int
     */
    public $id;

	/**
	 * The source name.
	 *
	 * @var string
	 */
	public $name;

    /**
     * The source default category name, used when category names are missing
     * from the data coming from the source.
     *
     * @var string
     */
    public $defaultCategory;

    /**
     * The source type.
     *
     * @var string
     */
    public $type = 'rss';

    /**
     * The date and time the source was last updated.
     *
     * @var Zend_Date
     */
	public $updated;

    /**
     * How frequently, in minutes, the source should be updated.  This is a
     * minimum value to wait between refreshing the source, there is no
     * maximum.
     *
     * @var int
     */
    public $updateFrequency = 15;

    /**
     * The source URL of the news.
     *
     * @var string
     */
	public $sourceUrl;

    /**
     * The info URL of the news.
     *
     * @var string
     */
    public $infoUrl;

    /**
     * The source description.
     *
     * @var string
     */
    public $description;

    /**
     * The source copyright information.
     *
     * @var string
     */
    public $copyright;

    /**
     * The source image URL.
     *
     * @var string
     */
    public $imageUrl;
}