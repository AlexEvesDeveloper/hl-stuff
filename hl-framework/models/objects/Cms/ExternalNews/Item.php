<?php

/**
 * Represents an external news item in the system.
 *
 * @category   Model
 * @package    Model_Cms
 * @subpackage ExternalNews
 */
class Model_Cms_ExternalNews_Item extends Model_Abstract {

    /**
     * A globally unique identifier hash.
     *
     * @var string
     */
    public $guidHash;

    /**
     * The ID of the category the item belongs to.
     *
     * @var int
     */
    public $categoryId;

    /**
     * The name of the category the item belongs to.
     *
     * @var string
     */
    public $categoryName;

    /**
     * The ID of the source the item comes from.
     *
     * @var int
     */
    public $sourceId;

    /**
     * The name of the source the item comes from.
     *
     * @var string
     */
    public $sourceName;

    /**
     * The date and time the news item was published.
     *
     * @var string|Zend_Date
     *
     * @todo Should always be Zend_Date with no default, except it's just TOO
     * SLOW in practice.
     */
	public $publishDate = '0000-00-00 00:00:00';

    /**
     * The news item title.
     *
     * @var string
     */
	public $title;

    /**
     * The news item summary.
     *
     * @var string
     */
    public $summary;

    /**
     * The news item link URL.
     *
     * @var string
     */
	public $linkUrl;

    /**
     * The news item thumbnail URL.
     *
     * @var string
     */
    public $thumbnailUrl;
}