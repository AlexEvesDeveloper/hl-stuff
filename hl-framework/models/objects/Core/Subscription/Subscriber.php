<?php

/**
 * Represents a subscriber and their subscribed news services in the system.
 *
 * @todo docblock parameters.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Subscription
 */
class Model_Core_Subscription_Subscriber extends Model_Abstract {

    /**
     * The subscriber's unique ID.
     *
     * @var int
     */
    public $id;

    public $email;
    public $realName;
    public $added;
    public $updated;

    /**
     * News service subscriptions.
     *
     * @var array Array of Model_Core_Subscription_NewsService objects.
     */
    public $newsServices;
}