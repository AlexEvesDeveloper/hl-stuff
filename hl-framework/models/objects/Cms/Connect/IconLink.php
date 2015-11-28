<?php

/**
 * Represents a Connect icon link.
 *
 * @category   Model
 * @package    Model_Cms
 * @subpackage IconLink
 */
class Model_Cms_Connect_IconLink extends Model_Abstract {

    /**
     * The icon link's type, eg, 'standard' (just a link) or 'special' (like a
     * calculator).
     *
     * @var string
     *
     * @todo Normalise.
     */
    public $type = 'standard';

    /**
     * The icon link's title.
     *
     * @var string
     */
    public $title;

    /**
     * The name of the icon to display, if any.
     *
     * @var string
     */
    public $icon;

    /**
     * The URL of the icon, if any.  Can be relative or absolute.
     *
     * @var string
     */
    public $url;

    /**
     * Optional JavaScript, triggered by an onclick event.  'return false;' is
     * automatically appended at runtime if $this->url is empty.
     *
     * @var string
     */
    public $javaScript;

}