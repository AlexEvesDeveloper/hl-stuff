<?php

/**
 * Manager class for end user subscriptions to bulk e-mail news services.  All
 * new subscriptions must be verified by e-mail.
 *
 * @category   Manager
 * @package    Manager_Core
 * @subpackage Subscription
 */

class Manager_Core_Subscription {

    /**#@+
     * References to common aspects of a subscription stored in the datasources.
     */
    protected $_subscriptionDatasource;
    protected $_subscriberObject = null;
    /**#@-*/

    public function __construct($subscriber = null) {

        $this->_subscriptionDatasource = new Datasource_Core_Subscription();

        // Has a subscriber object, a subscriber ID, or subscriber e-mail
        // address string been passed in?
        if ($subscriber != null) {
            return $this->_createSubscriberObject($subscriber);
        }
    }

    protected function _createSubscriberObject($subscriber) {

        if (is_a($subscriber, 'Model_Core_Subscription_Subscriber')) {
            // Object passed in, set internal private var to it
            $this->_subscriberObject = $subscriber;
        } elseif (is_numeric($subscriber)) {
            // Check to see if there's a matching account already by ID
            $findSubscriber = $this->_subscriptionDatasource->fetchById($subscriber);
            if ($findSubscriber !== null) {
                // Exists, fetch existing user object
                $this->_subscriberObject = $findSubscriber;
            } else {
                // Failed lookup by ID and can't create from supplied info
                throw new Zend_Exception('Failed lookup by ID');
            }
        } elseif (is_string($subscriber)) {
            // Check to see if there's a matching account already by e-mail
            // address
            $findSubscriber = $this->_subscriptionDatasource->fetchByEmail($subscriber);
            if ($findSubscriber !== null) {
                // Already exists, fetch existing user object
                $this->_subscriberObject = $findSubscriber;
            } else {
                // New subscriber, set up user object
                $this->_subscriberObject = new Model_Core_Subscription_Subscriber();
                $this->_subscriberObject->email = $subscriber;
                $this->_upsertSubscriber();
            }
        } else {
            throw new Zend_Exception('Invalid type of subscriber');
        }

        return true;
    }

    /**
     * Create a new subscription to a news service.  Must be verified separately
     * using verify() before it will be returned by getSubscribers().
     */
    public function subscribe($service, $subscriber = null) {

        // Subscriber object, ID or e-mail might be passed in, if so, validate
        if (!is_null($subscriber)) {
            try {
                $this->_createSubscriberObject($subscriber);
            } catch (Exception $e) {
                throw new Zend_Exception('Invalid subscriber: ' . $e->getMessage());
            }
        }

        // Create subscription
        try {
            $this->_subscriptionDatasource->subscribe($this->_subscriberObject, $service);
            // Send subscription verification to end user
            // TODO: as above
            return true;
        } catch (Exception $e) {
            throw new Zend_Exception('Subscription unsuccessful: ' . $e->getMessage());
        }

    }

    /**
     * Unsubscribe.  Verification should be implicit as the trigger should only
     * be present in a bulk e-mail received by the unsubscribing user.
     */
    public function unsubscribe() {

    }

    /**
     * Verify an application for subscription or unsubscription.  Helps prevent
     * abusive subscriptions and unsubscriptions.
     */
    public function verify() {

    }

    /**
     * Get a detailed list of users subscribed to a particular service.
     */
    public function getSubscribers() {

    }

    /**
     * Update or insert current subscriber object.
     */
    protected function _upsertSubscriber() {

        $result = null;

        if (!is_null($this->_subscriberObject)) {

            $this->_subscriptionDatasource->upsertSubscriber($this->_subscriberObject);

            if ($result !== null) {
                $this->_subscriberObject = $result;
            }
        }

        return $result;
    }

    /**
     * Add a new subscribable news service.
     */
    public function addNewsService() {

    }

    /**
     * Remove an existing subscribable news service.
     */
    public function removeNewsService() {

    }

    /**
     * Get list of all news services.
     */
    public function getNewsServices() {

    }
}