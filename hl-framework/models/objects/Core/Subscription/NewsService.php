<?php

/**
 * Represents a subscribable news service in the system.
 *
 * @todo docblock parameters.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Subscription
 */
class Model_Core_Subscription_NewsService extends Model_Abstract {

    public $id;
    public $type;
    public $description;

    /**
     * Who to notify of successful subscribe/unsubscribe requests.
     *
     * @var array of strings containing e-mail addresses.
     */
    public $notifySubscriberChangesTo;
}