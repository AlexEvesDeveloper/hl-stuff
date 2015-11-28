<?php

/**
 * Represents an external news category in the system.
 *
 * @category   Model
 * @package    Model_Cms
 * @subpackage ExternalNews
 */
class Model_Cms_ExternalNews_Category extends Model_Abstract {

    /**
     * The category's unique ID.
     *
     * @var int
     */
    public $id;

	/**
	 * The category name.
	 *
	 * @var string
	 */
	public $name;

    /**
     * The date and time the category was first added.
     *
     * @var Zend_Date
     */
	public $added;

    /**
     * The date and time the category was last updated.
     *
     * @var Zend_Date
     */
	public $updated;

    /**
     * Flag for if the category is permanent.
     *
     * @var bool True means the category is permanent, false indicates the
     * category may be removed during cleanup if empty for a certain length of
     * time.
     */
	public $permanent = false;

    /**
     * The news items in the category.
     *
     * @var array Array of Model_Cms_ExternalNews_Item objects.
     */
	public $items;

    /**
     * The source of the news in the category.
     *
     * @var Model_Cms_ExternalNews_Source
     */
	public $source;

    /**
     * The source ID of the news in the category.  Redundant if $this->source is populated.
     *
     * @var int
     */
	public $sourceId;
}